<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

function getReportInfo($startDate = null, $endDate = null) {
    global $conn;
    
    if ($startDate === null) {
        $startDate = date('Y-m-d', strtotime('-30 days'));
    }
    if ($endDate === null) {
        $endDate = date('Y-m-d');
    }
    
    $sql = "CALL infoReporte(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return ['error' => "No reports found between the dates $startDate and $endDate"];
    }
    
    return $result;
}

$filterStartDate = isset($_GET['filter_start_date']) ? $_GET['filter_start_date'] : date('Y-m-d', strtotime('-30 days'));
$filterEndDate = isset($_GET['filter_end_date']) ? $_GET['filter_end_date'] : date('Y-m-d');

$reports = getReportInfo($filterStartDate, $filterEndDate);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Overview</title>
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
    <nav class="sidebar">
        <div class="sidebar-nav">
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
        </div>
    </nav>

    <div class="hamburger-btn">
        <i class="fas fa-bars"></i>
    </div>

    <header class="header">
        <h1>Reports Overview</h1>
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
            <?php if (is_array($reports) && isset($reports['error'])): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($reports['error']); ?>
                </div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Report Number</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Employee</th>
                            <th>Order Number</th>
                            <th>Payment Amount</th>
                            <th>Total Discount</th>
                            <th>Ticket Number</th>
                            <th>Ticket Date</th>
                            <th>Total Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $reports->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Numero del Reporte']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['Fecha de inicio'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['Fecha final'])); ?></td>
                                <td><?php echo htmlspecialchars($row['Empleado']); ?></td>
                                <td><?php echo htmlspecialchars($row['NumeroOrden']); ?></td>
                                <td>$<?php echo number_format($row['MontoPago'], 2); ?></td>
                                <td>$<?php echo number_format($row['DescuentoTotal'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['NumeroTicket']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['FechaTicket'])); ?></td>
                                <td>$<?php echo number_format($row['TotalTicket'], 2); ?></td>
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
    </script>
</body>
</html>
