<?php
require_once 'conectbd.php';
require_once 'login.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_num'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT e.num as emp_num, 
               (SELECT num FROM orderEmp WHERE employee = e.num AND status = 'PND' ORDER BY num DESC LIMIT 1) as order_num
        FROM employee e 
        WHERE e.userNum = ?
    ");
    
    $stmt->bind_param("i", $_SESSION['user_num']);
    $stmt->execute();
    $result = $stmt->get_result();
    $employeeInfo = $result->fetch_assoc();
    
    if (!$employeeInfo) {
        throw new Exception("Employee not found");
    }

    $employeeNum = $employeeInfo['emp_num'];
    $orderNum = $employeeInfo['order_num'];

    $action = $_POST['action'] ?? '';
    $dishId = $_POST['dish_id'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);

    if (!$dishId) {
        throw new Exception("Invalid dish ID");
    }

    if (!$orderNum && $action !== 'remove') {
        $createOrderStmt = $conn->prepare("
            INSERT INTO orderEmp (employee, status, date) 
            VALUES (?, 'PND', NOW())
        ");
        $createOrderStmt->bind_param("i", $employeeNum);
        if (!$createOrderStmt->execute()) {
            throw new Exception("Failed to create order");
        }
        $orderNum = $conn->insert_id;
    } elseif (!$orderNum) {
        throw new Exception("No active order found");
    }

    $conn->begin_transaction();

    try {
        switch ($action) {
            case 'update':
                if ($quantity <= 0) {
                    $stmt = $conn->prepare("
                        DELETE FROM ord_dish 
                        WHERE orderEmp = ? AND dish = ?
                    ");
                    $stmt->bind_param("is", $orderNum, $dishId);
                } else {
                    $priceStmt = $conn->prepare("
                        SELECT price, discountPercentage 
                        FROM dish 
                        WHERE code = ?
                    ");
                    $priceStmt->bind_param("s", $dishId);
                    $priceStmt->execute();
                    $dishInfo = $priceStmt->get_result()->fetch_assoc();
                    
                    if (!$dishInfo) {
                        throw new Exception("Dish not found");
                    }

                    $price = $dishInfo['price'];
                    $discount = $dishInfo['discountPercentage'];
                    $discountedPrice = $price * (1 - ($discount/100));
                    $totalAmount = $discountedPrice * $quantity;
                    $dishDiscount = ($price * $quantity) - $totalAmount;

                    $stmt = $conn->prepare("
                        UPDATE ord_dish 
                        SET numberDishes = ?,
                            amount = ?,
                            dishDiscount = ?
                        WHERE orderEmp = ? AND dish = ?
                    ");
                    $stmt->bind_param("idiss", $quantity, $totalAmount, $dishDiscount, $orderNum, $dishId);
                }
                break;

            case 'remove':
                $checkTicketStmt = $conn->prepare("
                    SELECT 1 FROM ticket WHERE orderEmp = ? LIMIT 1
                ");
                $checkTicketStmt->bind_param("i", $orderNum);
                $checkTicketStmt->execute();
                $hasTicket = $checkTicketStmt->get_result()->num_rows > 0;

                if ($hasTicket) {
                    $stmt = $conn->prepare("
                        UPDATE ord_dish 
                        SET numberDishes = 0,
                            amount = 0,
                            dishDiscount = 0
                        WHERE orderEmp = ? AND dish = ?
                    ");
                    $stmt->bind_param("is", $orderNum, $dishId);
                } else {
                    $stmt = $conn->prepare("
                        DELETE FROM ord_dish 
                        WHERE orderEmp = ? AND dish = ?
                    ");
                    $stmt->bind_param("is", $orderNum, $dishId);
                }
                break;

            default:
                throw new Exception("Invalid action");
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to update cart");
        }

        $totalStmt = $conn->prepare("
            SELECT COALESCE(SUM(amount - IFNULL(dishDiscount, 0)), 0) as total 
            FROM ord_dish 
            WHERE orderEmp = ? AND numberDishes > 0
        ");
        $totalStmt->bind_param("i", $orderNum);
        $totalStmt->execute();
        $total = $totalStmt->get_result()->fetch_assoc()['total'];

        if ($total == 0) {
            $checkTicketStmt = $conn->prepare("
                SELECT 1 FROM ticket WHERE orderEmp = ? LIMIT 1
            ");
            $checkTicketStmt->bind_param("i", $orderNum);
            $checkTicketStmt->execute();
            
            if ($checkTicketStmt->get_result()->num_rows === 0) {
                $deleteOrderStmt = $conn->prepare("
                    DELETE FROM orderEmp 
                    WHERE num = ? AND status = 'PND'
                ");
                $deleteOrderStmt->bind_param("i", $orderNum);
                $deleteOrderStmt->execute();
            }
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'total' => number_format($total, 2),
            'message' => 'Cart updated successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>