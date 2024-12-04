<<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

$menuTypesQuery = "SELECT * FROM menu_type";
$menuTypes = $conn->query($menuTypesQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_menu'])) {
    $menuName = $_POST['menu_name'];
    $description = $_POST['description'];
    $menuType = $_POST['menu_type'];
    $selectedDiningRooms = isset($_POST['diningRoom']) ? $_POST['diningRoom'] : [];
    
    try {
        mysqli_begin_transaction($conn);
        
        $tableCheck = $conn->query("SHOW TABLES LIKE 'diningRoom'");
        if ($tableCheck->num_rows === 0) {
            throw new Exception("Table diningRoom does not exist. Please create the table first.");
        }
        
        $result = $conn->query("SELECT MAX(CAST(SUBSTRING(code, 2) AS UNSIGNED)) as max_code FROM menu");
        $row = $result->fetch_assoc();
        $nextCode = 'M' . str_pad(($row['max_code'] + 1), 3, '0', STR_PAD_LEFT);
        
        $stmt = $conn->prepare("CALL createMenu(?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nextCode, $menuName, $description, $menuType);
        $stmt->execute();
        
        $diningMenuStmt = $conn->prepare("INSERT INTO dining_menu (diningRoom, menu) VALUES (?, ?)");
        foreach ($selectedDiningRooms as $diningRoom) {
            $diningMenuStmt->bind_param("is", $diningRoom, $nextCode);
            $diningMenuStmt->execute();
        }
        
        mysqli_commit($conn);
        header("Location: viewMenu.php");
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error: " . $e->getMessage();
    }
}

try {
    $diningRoomsQuery = "SELECT * FROM diningRoom"; 
    $diningRooms = $conn->query($diningRoomsQuery);
    
    if (!$diningRooms) {
        throw new Exception("Error in the diningRoom query: " . $conn->error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Menu | Foody</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/createMenu.css">
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <script src="/js/shared/shared-layout.js" defer></script>
</head>
<body>
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
        </nav>
    </aside>
    <div class="hamburger-btn" onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <section class="create-menu-container">
        <h2>Create Menu</h2>

        <?php if (isset($message)): ?>
            <div class="message">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="menu-form">
            <div class="form-group">
                <label for="menu_name">Menu Name:</label>
                <input type="text" id="menu_name" name="menu_name" required maxlength="30">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required maxlength="60"></textarea>
            </div>

            <div class="form-group">
                <label for="menu_type">Schedule:</label>
                <select id="menu_type" name="menu_type" required>
                    <option value="">Select Schedule</option>
                    <?php while ($type = $menuTypes->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($type['num']); ?>">
                            <?php echo htmlspecialchars($type['description']); ?> 
                            (<?php echo htmlspecialchars($type['start_time']); ?> - 
                             <?php echo htmlspecialchars($type['end_time']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="diningRoom">Select Dining Rooms:</label>
                <div class="dining-rooms-container">
                    <select id="dining-rooms-select" name="diningRoom[]" multiple required>
                        <?php 
                        if ($diningRooms && $diningRooms->num_rows > 0):
                            while ($diningRoom = $diningRooms->fetch_assoc()): 
                        ?>
                            <option value="<?php echo htmlspecialchars($diningRoom['num']); ?>">
                                <?php echo htmlspecialchars($diningRoom['name']); ?>
                            </option>
                        <?php 
                            endwhile;
                        endif; 
                        ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="create_menu" class="btn-create">Create Menu</button>
        </form>
    </section>

    <div class="buttons">
        <a href="viewMenu.php" class="btn-view">View Menus</a>
        <a href="homeDiningRoom.php" class="btn-home">Back to Home</a>
    </div>
</body>
</html>
