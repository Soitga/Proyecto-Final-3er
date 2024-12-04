<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

function getOrders($startDate = null, $endDate = null) {
    global $conn;
    
    if ($startDate === null) {
        $startDate = date('Y-m-d', strtotime('-30 days'));
    }
    if ($endDate === null) {
        $endDate = date('Y-m-d');
    }
    
    $sql = "SELECT 
            oe.num as order_id,
            oe.dateOrde as order_date,
            oe.paymentAmount as final_amount,
            oe.totalDiscount,
            s.description as status,
            s.code as status_code,
            CONCAT(e.firstName, ' ', e.middleName, ' ', COALESCE(e.lastName, '')) as employee_name,
            jp.description as job_position,
            f.name as factory_name,
            GROUP_CONCAT(
                CONCAT(od.numberDishes, 'x ', d.name)
                SEPARATOR ', '
            ) as order_items
            FROM orderEmp oe
            JOIN status s ON oe.status = s.code
            JOIN employee e ON oe.employee = e.num
            JOIN factory f ON e.factory = f.code
            JOIN jobPosition jp ON e.jobPosition = jp.code
            JOIN ord_dish od ON oe.num = od.orderEmp
            JOIN dish d ON od.dish = d.code
            WHERE DATE(oe.dateOrde) BETWEEN ? AND ?
            GROUP BY oe.num
            ORDER BY oe.dateOrde DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['error' => "No orders found between the dates $startDate and $endDate"];
    }
    
    return $result;
}

$filterStartDate = isset($_GET['filter_start_date']) ? $_GET['filter_start_date'] : date('Y-m-d', strtotime('-30 days'));
$filterEndDate = isset($_GET['filter_end_date']) ? $_GET['filter_end_date'] : date('Y-m-d');

$orders = getOrders($filterStartDate, $filterEndDate);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Orders</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/DiningRoMCSS/report.css">
</head>
<body>
    <nav class="top-nav">
        <div class="brand">
            <a href="homeDiningRoom.php">Foody</a>
        </div>
        <div class="user-menu">
            <a href="../index.php"><i class="fas fa-sign-out-alt"></i> Log off</a>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-nav">
            <ul>
                <li><a href="homeDiningRoom.php"><i class="fas fa-home"></i>Home</a></li>
                <li><a href="#"><i class="fas fa-users"></i>Employees Orders</a></li>
                <li><a href="createDish.php"><i class="fas fa-utensils"></i>Create Dish</a></li>
                <li><a href="createmenu.php"><i class="fas fa-book"></i>Create Menu</a></li>
                <li><a href="inventoryIngredients.php"><i class="fas fa-warehouse"></i>Inventory</a></li>
                <li><a href="orderSupplier.php"><i class="fas fa-truck"></i>Order Supplier</a></li>
                <li><a href="createSupplier.php"><i class="fas fa-store"></i>Register Supplier</a></li>
                <li><a href="createReport.php"><i class="fas fa-chart-bar"></i>Create Reports</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i>View Reports</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Log off</a></li>
            </ul>
        </div>
    </nav>

    <div class="hamburger-btn">
        <i class="fas fa-bars"></i>
    </div>

    <header class="header">
        <h1>Employee Orders</h1>
    </header>

    <main class="main-content">
        <div class="filter-section">
            <form method="GET" class="d-flex gap-3">
                <div class="flex-grow-1">
                    <label for="filter_start_date" class="form-label">Start Date:</label>
                    <input type="date" id="filter_start_date" name="filter_start_date" 
                           class="form-control" value="<?php echo $filterStartDate; ?>"
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="filter_end_date" class="form-label">End Date:</label>
                    <input type="date" id="filter_end_date" name="filter_end_date" 
                           class="form-control" value="<?php echo $filterEndDate; ?>"
                           max="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="align-self-end">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>

        <div class="reports-table">
            <?php if (is_array($orders) && isset($orders['error'])): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($orders['error']); ?>
                </div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Factory</th>
                            <th>Order Items</th>
                            <th>Total Discount</th>
                            <th>Payment Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['order_date'])); ?></td>
                                <td>
                                    <span class="status-<?php echo htmlspecialchars($row['status_code']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['job_position']); ?></td>
                                <td><?php echo htmlspecialchars($row['factory_name']); ?></td>
                                <td class="order-items"><?php echo htmlspecialchars($row['order_items']); ?></td>
                                <td>$<?php echo number_format($row['totalDiscount'], 2); ?></td>
                                <td>$<?php echo number_format($row['final_amount'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('filter_start_date').value);
            const endDate = new Date(document.getElementById('filter_end_date').value);
            
            if (startDate > endDate) {
                e.preventDefault();
                alert('The start date cannot be later than the end date');
            }
        });

        document.querySelector('.hamburger-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>