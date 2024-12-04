<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

function getTicketsInRange($startDate, $endDate) {
    global $conn;
    $sql = "SELECT 
                t.num as ticket_num,
                t.dateTick,
                t.total as ticket_total,
                oe.num as order_num,
                oe.paymentAmount,
                oe.totalDiscount,
                CONCAT(e.firstName, ' ', e.lastName) as employee_name
            FROM ticket t
            INNER JOIN orderEmp oe ON t.orderEmp = oe.num
            INNER JOIN employee e ON oe.employee = e.num
            WHERE DATE(t.dateTick) BETWEEN ? AND ?
            AND t.report IS NULL
            ORDER BY t.dateTick DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    return $stmt->get_result();
}

if (isset($_GET['preview']) && isset($_GET['dateStart']) && isset($_GET['dateEnd'])) {
    $tickets = getTicketsInRange($_GET['dateStart'], $_GET['dateEnd']);
    $totalSales = 0;
    $ticketsData = [];
    
    while ($row = $tickets->fetch_assoc()) {
        $totalSales += $row['ticket_total'];
        $ticketsData[] = $row;
    }
    
    if (empty($ticketsData)) {
        $previewMessage = "No tickets found in the selected date range.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        $dateStart = $_POST['dateStart'];
        $dateEnd = $_POST['dateEnd'];
    
        $tickets = getTicketsInRange($dateStart, $dateEnd);
        $totalSales = 0;
        $ticketIds = [];
        
        while ($row = $tickets->fetch_assoc()) {
            $totalSales += $row['ticket_total'];
            $ticketIds[] = $row['ticket_num'];
        }
        
        if (empty($ticketIds)) {
            throw new Exception("No tickets found in the selected date range.");
        }
    
        $stmt = $conn->prepare("INSERT INTO report (dateStart, endDate, productionDate, totalSales) VALUES (?, ?, CURRENT_DATE(), ?)");
        $stmt->bind_param("ssd", $dateStart, $dateEnd, $totalSales);
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating report: " . $stmt->error);
        }
        
        $reportId = $conn->insert_id;
        
        $updateQuery = "UPDATE ticket SET report = ? WHERE num IN (" . str_repeat('?,', count($ticketIds) - 1) . '?)';
        $updateStmt = $conn->prepare($updateQuery);
        
        $params = array_merge([$reportId], $ticketIds);
        
        $types = str_repeat('i', count($params));
        
        $updateStmt->bind_param($types, ...$params);
        
        if (!$updateStmt->execute()) {
            throw new Exception("Error updating tickets: " . $updateStmt->error);
        }

        $conn->commit();
        $successMessage = "Report #$reportId successfully created with " . count($ticketIds) . " tickets and total sales of $" . number_format($totalSales, 2);
        
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = "Error creating report: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
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
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i>Reports</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Log off</a></li>
            </ul>
        </div>
    </nav>

    <div class="hamburger-btn">
        <i class="fas fa-bars"></i>
    </div>

    <header class="header">
        <h1>Create New Report</h1>
    </header>

    <main class="main-content">
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($successMessage); ?>
                <div class="mt-2">
                    <a href="reports.php" class="btn btn-sm btn-success">View Reports</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form method="GET" id="previewForm" class="mb-4">
            <div class="filter-section">
                <div class="d-flex gap-3">
                    <div class="flex-grow-1">
                        <label for="dateStart" class="form-label">Start Date:</label>
                        <input type="date" class="form-control" id="dateStart" name="dateStart" 
                               value="<?php echo $_GET['dateStart'] ?? ''; ?>" required>
                    </div>
                    <div class="flex-grow-1">
                        <label for="dateEnd" class="form-label">End Date:</label>
                        <input type="date" class="form-control" id="dateEnd" name="dateEnd" 
                               value="<?php echo $_GET['dateEnd'] ?? ''; ?>" required>
                    </div>
                    <div class="align-self-end">
                        <input type="hidden" name="preview" value="1">
                        <button type="submit" class="btn btn-secondary">Preview Tickets</button>
                    </div>
                </div>
            </div>
        </form>

        <?php if (isset($_GET['preview']) && !empty($ticketsData)): ?>
            <div class="preview-section">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Report Preview</h3>
                        <h4 class="mb-0">Total Sales: $<?php echo number_format($totalSales, 2); ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="reports-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Order #</th>
                                        <th>Payment Amount</th>
                                        <th>Discount</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ticketsData as $ticket): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ticket['ticket_num']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($ticket['dateTick'])); ?></td>
                                            <td><?php echo htmlspecialchars($ticket['employee_name']); ?></td>
                                            <td><?php echo htmlspecialchars($ticket['order_num']); ?></td>
                                            <td>$<?php echo number_format($ticket['paymentAmount'], 2); ?></td>
                                            <td>$<?php echo number_format($ticket['totalDiscount'], 2); ?></td>
                                            <td>$<?php echo number_format($ticket['ticket_total'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="dateStart" value="<?php echo htmlspecialchars($_GET['dateStart']); ?>">
                            <input type="hidden" name="dateEnd" value="<?php echo htmlspecialchars($_GET['dateEnd']); ?>">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Create Report with These Tickets</button>
                                <a href="reports.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php elseif (isset($previewMessage)): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($previewMessage); ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
        document.querySelector('.hamburger-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const hamburgerBtn = document.querySelector('.hamburger-btn');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !hamburgerBtn.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });

        document.getElementById('previewForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('dateStart').value);
            const endDate = new Date(document.getElementById('dateEnd').value);
            
            if (startDate > endDate) {
                e.preventDefault();
                alert('Start date cannot be after end date');
                return;
            }
        });

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('dateStart').setAttribute('max', today);
        document.getElementById('dateEnd').setAttribute('max', today);
    </script>
</body>
</html>