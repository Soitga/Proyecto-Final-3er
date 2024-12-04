<?php
require_once '../php/conectbd.php';
require_once '../php/login.php';
session_start();

if (!isset($_SESSION['user_num'])) {
    header('Location: ../index.php');
    exit();
}

try {
    $stmt = $conn->prepare("
        SELECT e.num as emp_num, e.firstName, e.middleName, e.lastName
        FROM employee e
        WHERE e.userNum = ?
    ");
    
    $stmt->bind_param("i", $_SESSION['user_num']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Employee record not found");
    }
    
    $employee = $result->fetch_assoc();
    
    $ticket_id = $_GET['ticket_id'] ?? $_SESSION['last_ticket_num'] ?? null;
    
    if (!$ticket_id) {
        throw new Exception("No ticket ID provided");
    }
    
    $ticketQuery = "
    SELECT
        t.num as ticket_num,
        t.dateTick as dateTime, 
        t.total,
        oe.num as order_num,
        CONCAT(e.firstName, ' ', e.middleName, ' ', e.lastName) as employee_name
    FROM ticket t
    INNER JOIN orderEmp oe ON t.orderEmp = oe.num
    INNER JOIN employee e ON oe.employee = e.num
    WHERE t.num = ?";
    
    $ticketStmt = $conn->prepare($ticketQuery);
    $ticketStmt->bind_param("i", $ticket_id);
    $ticketStmt->execute();
    $ticketInfo = $ticketStmt->get_result()->fetch_assoc();
    
    if (!$ticketInfo) {
       throw new Exception("Ticket not found");
    }
    
    $itemsStmt = $conn->prepare("
        SELECT
            d.name,
            od.numberDishes as quantity,
            d.price,
            od.amount as subtotal,
            od.dishDiscount
        FROM ord_dish od
        JOIN dish d ON od.dish = d.code
        WHERE od.orderEmp = ? AND od.numberDishes > 0
        ORDER BY d.name
    ");
    
    $itemsStmt->bind_param("i", $ticketInfo['order_num']);
    $itemsStmt->execute();
    $items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $total = 0;
    foreach ($items as $item) {
        $total += $item['subtotal'] - $item['dishDiscount'];
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Ticket | Foody</title>
    <link rel="stylesheet" href="/css/Employe/ticket.css">
    <style>
        @media print {
            .actions {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .ticket-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <h1>Foody</h1>
        <div class="ticket-header">
            <p>Ticket #<?php echo htmlspecialchars($ticketInfo['ticket_num']); ?></p>
            <p>Date: <?php echo date('Y-m-d H:i:s', strtotime($ticketInfo['dateTime'])); ?></p>
            <p>Employee: <?php echo htmlspecialchars($ticketInfo['employee_name']); ?></p>
            <p>Order #<?php echo htmlspecialchars($ticketInfo['order_num']); ?></p>
        </div>

        <div class="ticket-items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['subtotal'] - $item['dishDiscount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="ticket-footer">
            <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
        </div>

        <div class="actions">
            <button onclick="window.print()">Print Ticket</button>
            <a href="inicioEmpleado.php" class="button">Back to Home</a>
        </div>
    </div>
</body>
</html>