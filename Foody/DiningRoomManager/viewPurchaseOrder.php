<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug: " . str_replace("'", "\\'", $output) . "' );</script>";
}

$viewCheck = $conn->query("SHOW TABLES LIKE 'purchaseOrderDetails'");
if ($viewCheck->num_rows == 0) {
    die("The view 'purchaseOrderDetails' does not exist. Please create it first.");
}

$ordersQuery = "SELECT * FROM purchaseOrderDetails ORDER BY orderDate DESC";
debug_to_console("Executing query: " . $ordersQuery);

$orders = $conn->query($ordersQuery);

if (!$orders) {
    debug_to_console("Query error: " . $conn->error);
    die("Error fetching orders: " . $conn->error);
}

$numRows = mysqli_num_rows($orders);
debug_to_console("Number of orders found: " . $numRows);

$checkOrdersQuery = "SELECT COUNT(*) as count FROM purchaseOrder";
$checkOrders = $conn->query($checkOrdersQuery);
$orderCount = $checkOrders->fetch_assoc()['count'];
debug_to_console("Total orders in purchaseOrder table: " . $orderCount);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Orders Details</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/orderSup.css">
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
</head>
<body>
    <main>
    <header>
        <div class="menu">
            <a href="homeDiningRoom.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Log off</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <aside class="sidebar">
        <a href="homeDiningRoom.php" class="logo">Foody</a>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="homeDiningRoom.php"><i class="fas fa-home"></i>Home</a></li>
                <li><a href="viewOrderEmplo.php"><i class="fas fa-users"></i>Employees Orders</a></li>
                <li><a href="createDish.php"><i class="fas fa-utensils"></i>Create Dish</a></li>
                <li><a href="createmenu.php"><i class="fas fa-book"></i>Create Menu</a></li>
                <li><a href="inventoryIngredients.php"><i class="fas fa-warehouse"></i>Inventory</a></li>
                <li><a href="orderSupplier.php"><i class="fas fa-truck"></i>Order Supplier</a></li>
                <li><a href="createSupplier.php"><i class="fas fa-store"></i>Register Supplier</a></li>
                <li><a href="createReport.php"><i class="fas fa-chart-bar"></i>Create Reports</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i>View Reports</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Log off</a></li>
            </ul>
        </nav>
    </aside>
    <div class="hamburger-btn" onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>

        <div></div>

        <h2>Purchase Orders Details</h2>
        
        <?php if ($numRows == 0): ?>
            <div class="alert alert-info">
                No purchase orders found. 
                <?php if ($orderCount > 0): ?>
                    There are <?php echo $orderCount; ?> orders in the database, but they may not be properly linked.
                <?php else: ?>
                    The orders table is empty.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <table class="orders-table">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Date</th>
            <th>Manager</th>
            <th>Dining Room</th>
            <th>Supplier</th>
            <th>Ingredients</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        while ($order = $orders->fetch_assoc()):
            $ingredientsQuery = "SELECT i.name, ip.numberIngred, ip.amount 
                              FROM ingred_purcha ip 
                              JOIN ingredients i ON ip.ingredients = i.num 
                              WHERE ip.purchaseOrder = " . $order['orderNumber'];
            $ingredientsResult = $conn->query($ingredientsQuery);
        ?>
            <tr>
                <td class="order-number">
                    #<?php echo htmlspecialchars($order['orderNumber']); ?>
                </td>
                <td class="order-date">
                    <?php echo date('M d, Y', strtotime($order['orderDate'])); ?>
                </td>
                <td class="manager-name">
                    <?php echo htmlspecialchars($order['managerFullName']); ?>
                </td>
                <td class="dining-room">
                    <?php echo htmlspecialchars($order['diningRoomName']); ?>
                </td>
                <td class="supplier-name">
                    <?php echo htmlspecialchars($order['supplierName']); ?>
                </td>
                <td class="ingredients-cell">
                    <?php 
                    if ($ingredientsResult && $ingredientsResult->num_rows > 0) {
                        while ($ingredient = $ingredientsResult->fetch_assoc()) {
                            echo '<div class="ingredient-item">';
                            echo '<strong>' . htmlspecialchars($ingredient['name']) . '</strong><br>';
                            echo 'Quantity: ' . $ingredient['numberIngred'] . '<br>';
                            echo 'Amount: $' . number_format($ingredient['amount'], 2);
                            echo '</div>';
                        }
                    } else {
                        echo htmlspecialchars($order['ingredients']);
                    }
                    ?>
                </td>
                <td class="total-amount">
                    $<?php echo number_format($order['totalAmount'], 2); ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

        <?php endif; ?>

        <div class="actions">
            <a href="orderSupplier.php" class="btn btn-create">Create New Order</a>
            <a href="homeDiningRoom.php" class="btn btn-back">Back to Home</a>
        </div>
    </main>
</body>
</html>