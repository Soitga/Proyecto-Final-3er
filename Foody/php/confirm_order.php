<?php
session_start();

require_once 'conectbd.php';
require_once 'login.php';
checkSession();

global $conn;

if (!isset($_SESSION['user_num'])) {
    header('Location: ../index.php');
    exit();
}

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

try {
    $stmt = $conn->prepare("SELECT e.num as emp_num FROM employee e WHERE e.userNum = ?");
    $stmt->bind_param("i", $_SESSION['user_num']);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    
    if (!$employee) {
        throw new Exception("Employee record not found");
    }

    mysqli_begin_transaction($conn);
    
    if (isset($_POST['dishes'])) {
        $reservation_date = $_POST['reservation_date'] ?? date('Y-m-d H:i:s');
            error_log("Reservation Date: " . $reservation_date);
            error_log("Employee Num: " . $employee['emp_num']);
        $stmt = $conn->prepare("
            INSERT INTO orderEmp (employee, dateOrde, status, paymentAmount, totalDiscount) 
            VALUES (?, ?, 'PND', 0, 0)
        ");
        $stmt->bind_param("is", $employee['emp_num'], $reservation_date);
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating order");
        }
        
        $order_num = $conn->insert_id;

        foreach ($_POST['dishes'] as $dish_code => $quantity) {
            if ($quantity > 0) {
                $stmt = $conn->prepare("INSERT INTO ord_dish (dish, orderEmp, numberDishes) VALUES (?, ?, ?)");
                $stmt->bind_param("sii", $dish_code, $order_num, $quantity);
                if (!$stmt->execute()) {
                    throw new Exception("Error adding dish to order");
                }
            }
        }
    } else {
        $stmt = $conn->prepare("SELECT num FROM orderEmp WHERE employee = ? AND status = 'PND' ORDER BY num DESC LIMIT 1");
        $stmt->bind_param("i", $employee['emp_num']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("No pending order found");
        }
        $order = $result->fetch_assoc();
        $order_num = $order['num'];
    }
    
    $stmt = $conn->prepare("CALL crearRecibo(?)");
    $stmt->bind_param("i", $order_num);
    if (!$stmt->execute()) {
        throw new Exception("Error executing stored procedure");
    }

    $stmt = $conn->prepare("SELECT num FROM ticket WHERE orderEmp = ? ORDER BY num DESC LIMIT 1");
    $stmt->bind_param("i", $order_num);
    $stmt->execute();
    $ticket = $stmt->get_result()->fetch_assoc();
    $_SESSION['last_ticket_num'] = $ticket['num'];

    mysqli_commit($conn);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Order and ticket created successfully',
        'order_id' => $order_num,
        'ticket_id' => $ticket['num']
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>