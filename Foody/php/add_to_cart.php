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
    $stmt = $conn->prepare("SELECT num FROM employee WHERE userNum = ?");
    $stmt->bind_param("i", $_SESSION['user_num']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Employee not found");
    }
    
    $employee = $result->fetch_assoc();
    $employeeNum = $employee['num'];

    $dishId = $_POST['dish_id'] ?? '';
    if (empty($dishId)) {
        throw new Exception("No dish specified");
    }

    $orderStmt = $conn->prepare("
        SELECT num 
        FROM orderEmp 
        WHERE employee = ? AND status = 'PND'
        ORDER BY num DESC 
        LIMIT 1
    ");
    $orderStmt->bind_param("i", $employeeNum);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    
    $conn->begin_transaction();

    try {
        if ($orderResult->num_rows === 0) {
            $createOrderStmt = $conn->prepare("
                INSERT INTO orderEmp (employee, status, dateOrde, paymentAmount, totalDiscount) 
                VALUES (?, 'PND', CURRENT_TIMESTAMP(), 0, 0)
            ");
            $createOrderStmt->bind_param("i", $employeeNum);
            $createOrderStmt->execute();
            $orderNum = $conn->insert_id;
        } else {
            $order = $orderResult->fetch_assoc();
            $orderNum = $order['num'];
        }

        $dishStmt = $conn->prepare("
            SELECT price, discountPercentage 
            FROM dish 
            WHERE code = ?
        ");
        $dishStmt->bind_param("s", $dishId);
        $dishStmt->execute();
        $dishResult = $dishStmt->get_result();

        if ($dishResult->num_rows === 0) {
            throw new Exception("Dish not found");
        }

        $dish = $dishResult->fetch_assoc();
        $price = $dish['price'];
        $discount = $dish['discountPercentage'];
        $discountedPrice = $price * (1 - ($discount/100));
        
        $checkStmt = $conn->prepare("
            SELECT numberDishes 
            FROM ord_dish 
            WHERE orderEmp = ? AND dish = ?
        ");
        $checkStmt->bind_param("is", $orderNum, $dishId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $currentDish = $checkResult->fetch_assoc();
            $newQuantity = $currentDish['numberDishes'] + 1;
            $newTotal = $discountedPrice * $newQuantity;
            $dishDiscount = ($price * $newQuantity) - $newTotal;

            $updateStmt = $conn->prepare("
                UPDATE ord_dish 
                SET numberDishes = ?,
                    amount = ?,
                    dishDiscount = ?
                WHERE orderEmp = ? AND dish = ?
            ");
            $updateStmt->bind_param("idiss", $newQuantity, $newTotal, $dishDiscount, $orderNum, $dishId);
            $updateStmt->execute();
        } else {
            $quantity = 1;
            $totalAmount = $discountedPrice * $quantity;
            $dishDiscount = $price - $discountedPrice;

            $insertStmt = $conn->prepare("
                INSERT INTO ord_dish (orderEmp, dish, numberDishes, amount, dishDiscount) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $insertStmt->bind_param("isidi", $orderNum, $dishId, $quantity, $totalAmount, $dishDiscount);
            $insertStmt->execute();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Item added to cart successfully'
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