<?php
require_once 'conectbd.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_num']) || !isset($_POST['dish_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
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
        UPDATE ord_dish 
        SET numberDishes = ?,
            amount = (SELECT price FROM dish WHERE code = ?) * ?
        WHERE orderEmp = ? AND dish = ?
    ");
    
    $quantity = (int)$_POST['quantity'];
    $dishId = $_POST['dish_id'];
    $stmt->bind_param("issis", $quantity, $dishId, $quantity, $orderId, $dishId);
    
    if (!$stmt->execute()) {
        throw new Exception("Error updating quantity");
    }

    $stmt = $conn->prepare("
        SELECT SUM(amount - COALESCE(dishDiscount, 0)) as total
        FROM ord_dish
        WHERE orderEmp = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];

    echo json_encode(['success' => true, 'total' => $total]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
