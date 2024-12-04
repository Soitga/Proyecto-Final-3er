<?php
require_once 'conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../InterfacesEmpleadohtml/menusReser.php');
    exit;
}

$dishes = $_POST['dishes'] ?? [];
$employee_num = $_SESSION['employee_num'];
$current_time = date('H:i:s');
$is_reservation = isset($_POST['isReservation']) ? $_POST['isReservation'] : false;

if (empty($dishes)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No dishes selected']);
    exit;
}

try {
    mysqli_begin_transaction($conn);
    
    $initial_status = $is_reservation ? 'RSV' : 'ETR';
    $stmt = $conn->prepare("INSERT INTO orderEmp (employee, dateOrde, status, paymentAmount, totalDiscount) 
                           VALUES (?, NOW(), ?, 0, 0)");
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("is", $employee_num, $initial_status);
    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }
    
    $order_num = $conn->insert_id;
    $_SESSION['last_order_num'] = $order_num;
    
    $total_amount = 0;
    $total_discount = 0;
    
    foreach ($dishes as $dish_code => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("
                SELECT d.price, d.discountPercentage, 
                       mt.start_time, mt.end_time
                FROM dish d
                JOIN menu m ON d.menu = m.code
                JOIN menu_type mt ON m.menu_type = mt.num
                WHERE d.code = ?
            ");
            $stmt->bind_param("s", $dish_code);
            $stmt->execute();
            $dish_info = $stmt->get_result()->fetch_assoc();
            
            $subtotal = $dish_info['price'] * $quantity;
            $discount = 0;
            
            if ($current_time < $dish_info['start_time'] || 
                $current_time > $dish_info['end_time']) {
                $discount = $subtotal * ($dish_info['discountPercentage'] / 100);
            }
            
            $total_amount += $subtotal;
            $total_discount += $discount;
            
            $stmt = $conn->prepare("
                INSERT INTO ord_dish (dish, orderEmp, numberDishes, amount, dishDiscount) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("siidi", $dish_code, $order_num, $quantity, $subtotal, $discount);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting dish: " . $stmt->error);
            }
        }
    }
    
    $stmt = $conn->prepare("
        UPDATE orderEmp 
        SET totalDiscount = ?,
            paymentAmount = ?
        WHERE num = ?
    ");
    $final_amount = $total_amount - $total_discount;
    $stmt->bind_param("ddi", $total_discount, $final_amount, $order_num);
    if (!$stmt->execute()) {
        throw new Exception("Error updating order totals: " . $stmt->error);
    }
    
    if (!$is_reservation) {
        $stmt = $conn->prepare("CALL crearRecibo(?)");
        if (!$stmt) {
            throw new Exception("Error preparing stored procedure call: " . $conn->error);
        }
        
        $stmt->bind_param("i", $order_num);
        if (!$stmt->execute()) {
            throw new Exception("Error executing stored procedure: " . $stmt->error);
        }
    }

    mysqli_commit($conn);
    
    http_response_code(200);
    echo json_encode([
        'status' => 'success', 
        'message' => 'Order ' . ($is_reservation ? 'reservation' : 'and ticket') . ' created successfully', 
        'order_id' => $order_num
    ]);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>