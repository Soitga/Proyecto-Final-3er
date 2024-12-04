<?php
session_start();
require_once __DIR__ . '/../php/conectbd.php';
require_once __DIR__ . '/../php/login.php';
checkSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Foody</title>
    <link rel="stylesheet" href="/css/Employe/histortyOrders.css">
</head>
<body>
    <header>
        <div class="menu">
            <a href="inicioEmpleado.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <h2>My Orders History</h2>
        <a href="inicioEmpleado.php" class="back-button">Return to Menu</a>
        <div class="orders-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Order Number</th>
                        <th>Order Date</th>
                        <th>Ticket Number</th>
                        <th>Dish</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>Final Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(isset($_SESSION['employee_num'])) {
                    $empNum = $_SESSION['employee_num'];
                    try {
                        $stmt = $conn->prepare("CALL SP_infoRecibo(?)");
                        $stmt->bind_param("i", $empNum);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['NumeroOrden']) . "</td>";
                            echo "<td>" . htmlspecialchars(date('Y-m-d H:i', strtotime($row['FechaOrden']))) . "</td>";
                            echo "<td>" . htmlspecialchars($row['NumeroTicket']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['NombrePlatillo']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['CantPlatillos']) . "</td>";
                            echo "<td>$" . number_format($row['PrecioPlatillo'], 2) . "</td>";
                            echo "<td>$" . number_format($row['DescuentoPlatillo'], 2) . "</td>";
                            echo "<td>$" . number_format($row['PrecioConDescuento'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } catch (Exception $e) {
                        echo "<div class='message error'>Error: " . $e->getMessage() . "</div>";
                    }
                } else {
                    echo "<div class='message error'>Error: Employee session not found.</div>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>