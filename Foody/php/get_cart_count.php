<?php
require_once 'conectbd.php';
session_start();

if (!isset($_SESSION['user_num'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$employee_num = $_SESSION['user_num'];

try {
    $stmt = $conn->prepare("
        SELECT SUM(od.numberDishes) as count
        FROM orderEmp o
        JOIN ord_dish od ON o.num = od.orderEmp
        WHERE o.employee = ? AND o.status = 'CART'
    ");
    
    $stmt->bind_param("i", $employee_num);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode(['count' => (int)($row['count'] ?? 0)]);
} catch (Exception $e) {
    echo json_encode(['count' => 0]);
}