<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

$suppliers = $conn->query("SELECT * FROM supplier");
if (!$suppliers) {
    die("Error fetching suppliers: " . $conn->error);
}

$ingredients = $conn->query("SELECT * FROM ingredients");
if (!$ingredients) {
    die("Error fetching ingredients: " . $conn->error);
}

$diningRooms = $conn->query("SELECT dr.num as dining_room_num, dr.name as dining_room_name, 
                            drm.num as manager_id 
                            FROM diningRoom dr 
                            INNER JOIN diningRoomManager drm ON dr.num = drm.diningRoom");
if (!$diningRooms) {
    die("Error fetching dining rooms: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = $_POST['supplier'] ?? '';
    $diningRoom = $_POST['dining_room'] ?? '';
    $date = $_POST['date'] ?? '';
    $selectedIngredients = $_POST['ingredients'] ?? [];
    $quantities = $_POST['quantities'] ?? [];

    if ($supplier && $diningRoom && $date && !empty($selectedIngredients)) {
        try {
            $managerQuery = $conn->prepare("SELECT num FROM diningRoomManager WHERE diningRoom = ?");
            $managerQuery->bind_param("i", $diningRoom);
            $managerQuery->execute();
            $managerResult = $managerQuery->get_result();
            
            if ($managerRow = $managerResult->fetch_assoc()) {
                $managerId = $managerRow['num'];
                
                $stmt = $conn->prepare("CALL createPurchaseOrder(?, ?, ?)");
                $stmt->bind_param("sis", $date, $managerId, $supplier);
                $stmt->execute();
                
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $orderId = $row['orderId'];
                $stmt->close();

                if ($orderId) {
                    foreach ($selectedIngredients as $index => $ingredientId) {
                        if (!empty($quantities[$index])) {
                            $amount = 0;
                            
                            $stmtIngred = $conn->prepare("CALL addPurchaseOrderIngredient(?, ?, ?, ?)");
                            $quantity = $quantities[$index];
                            $stmtIngred->bind_param("iiid", $orderId, $ingredientId, $quantity, $amount);
                            $stmtIngred->execute();
                            $stmtIngred->close();
                        }
                    }

                    header("Location: viewPurchaseOrder.php?order_id=" . $orderId);
                    exit();
                } else {
                    throw new Exception("Error getting purchase order ID.");
                }
            } else {
                throw new Exception("No dining room manager found for the selected dining room.");
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Purchase Order</title>
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
    <?php if (isset($errorMessage)): ?>
        <p class="error"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="supplier">Select Supplier:</label>
        <select id="supplier" name="supplier" required>
            <option value="">Select</option>
            <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                <option value="<?php echo $supplier['code']; ?>"><?php echo $supplier['name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="dining_room">Select Dining Room:</label>
        <select id="dining_room" name="dining_room" required>
            <option value="">Select</option>
            <?php while ($room = $diningRooms->fetch_assoc()): ?>
                <option value="<?php echo $room['dining_room_num']; ?>">
                    <?php echo $room['dining_room_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="date">Order Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="ingredients">Select Ingredients:</label>
        <div id="ingredients-container">
            <?php 
            mysqli_data_seek($ingredients, 0);
            while ($ingredient = $ingredients->fetch_assoc()): 
            ?>
                <div>
                    <label>
                        <input type="checkbox" name="ingredients[]" value="<?php echo $ingredient['num']; ?>">
                        <?php echo $ingredient['name']; ?>
                    </label>
                    <input type="number" name="quantities[]" min="1" placeholder="Quantity">
                </div>
            <?php endwhile; ?>
        </div>

        <button type="submit">Create Order</button>
    </form>
</main>
</body>
</html>