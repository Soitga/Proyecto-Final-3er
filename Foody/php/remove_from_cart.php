<?php
require_once 'conectbd.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_num']) || !isset($_POST['dish_id'])) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data']);
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

    $stmt = $conn->prepare("
        SELECT num 
        FROM orderEmp 
        WHERE employee = ? AND status = 'PND'
    ");
    
    $stmt->bind_param("i", $employeeNum);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("No active order found");
    }
    
    $order = $result->fetch_assoc();
    $orderId = $order['num'];

    $stmt = $conn->prepare("
        DELETE FROM ord_dish 
        WHERE orderEmp = ? AND dish = ?
    ");
    
    $dishId = $_POST['dish_id'];
    $stmt->bind_param("is", $orderId, $dishId);
    
    if (!$stmt->execute()) {
        throw new Exception("Error when eliminating the saucer");
    }

    $stmt = $conn->prepare("
        SELECT SUM(amount - COALESCE(dishDiscount, 0)) as total
        FROM ord_dish
        WHERE orderEmp = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'] ?? 0;

    $stmt = $conn->prepare("
        UPDATE orderEmp 
        SET paymentAmount = ?,
            totalDiscount = (
                SELECT COALESCE(SUM(dishDiscount), 0)
                FROM ord_dish
                WHERE orderEmp = ?
            )
        WHERE num = ?
    ");
    $stmt->bind_param("dii", $total, $orderId, $orderId);
    $stmt->execute();

    echo json_encode([
        'success' => true, 
        'total' => $total,
        'message' => 'Saucer removed correctly'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>