<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

$menuCode = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($menuCode)) {
    die("Menu code is required");
}

$menuQuery = "SELECT m.*, mt.description as type_description, mt.start_time, mt.end_time 
              FROM menu m 
              JOIN menu_type mt ON m.menu_type = mt.num 
              WHERE m.code = ?";
              
$stmt = $conn->prepare($menuQuery);
$stmt->bind_param("s", $menuCode);
$stmt->execute();
$menuResult = $stmt->get_result();
$menuData = $menuResult->fetch_assoc();

if (!$menuData) {
    die("Menu not found");
}

$menuTypesQuery = "SELECT * FROM menu_type ORDER BY start_time";
$menuTypes = $conn->query($menuTypesQuery);

$diningRoomsQuery = "SELECT dr.* 
                     FROM diningRoom dr 
                     JOIN dining_menu dm ON dr.num = dm.diningRoom 
                     WHERE dm.menu = ?";
$stmt = $conn->prepare($diningRoomsQuery);
$stmt->bind_param("s", $menuCode);
$stmt->execute();
$currentDiningRooms = $stmt->get_result();

$dishesQuery = "SELECT * FROM dish WHERE menu = ? ORDER BY name";
$stmt = $conn->prepare($dishesQuery);
$stmt->bind_param("s", $menuCode);
$stmt->execute();
$currentDishes = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_menu'])) {
    try {
        $conn->begin_transaction();
        
        $updateMenuQuery = "UPDATE menu 
                           SET name = ?, 
                               description = ?, 
                               menu_type = ? 
                           WHERE code = ?";
        
        $stmt = $conn->prepare($updateMenuQuery);
        $stmt->bind_param("ssis", 
            $_POST['menu_name'],
            $_POST['description'],
            $_POST['menu_type'],
            $menuCode
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating menu");
        }
        
        $conn->commit();
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?code=" . $menuCode . "&success=1");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['success'])) {
    $message = "Menu updated successfully";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_dish') {
    header('Content-Type: application/json');
    
    $dishCode = isset($_POST['dish_code']) ? trim($_POST['dish_code']) : '';
    
    if (empty($dishCode)) {
        echo json_encode(['success' => false, 'error' => 'Missing dish code']);
        exit();
    }
    
    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("UPDATE dish SET menu = NULL WHERE code = ? AND menu = ?");
        $stmt->bind_param("ss", $dishCode, $menuCode);
        
        if (!$stmt->execute()) {
            throw new Exception("Error removing dish from menu");
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Dish not found or already removed");
        }
        
        $conn->commit();
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu | Foody</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/editMenu.css">
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
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
            </ul>
        </nav>
    </aside>
    <div class="hamburger-btn" onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="edit-menu-container">
        <h2>Edit Menu</h2>

        <?php if (isset($message)): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="menu_name">Menu Name:</label>
                <input type="text" id="menu_name" name="menu_name" required maxlength="30" 
                       value="<?php echo htmlspecialchars($menuData['name']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required maxlength="60"><?php echo htmlspecialchars($menuData['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="menu_type">Schedule:</label>
                <select id="menu_type" name="menu_type" required>
                    <?php while ($type = $menuTypes->fetch_assoc()): ?>
                        <option value="<?php echo $type['num']; ?>" 
                                <?php echo ($type['num'] == $menuData['menu_type']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['description']); ?> 
                            (<?php echo htmlspecialchars($type['start_time']); ?> - 
                             <?php echo htmlspecialchars($type['end_time']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="dining-rooms-display">
                <h3>Assigned Dining Rooms:</h3>
                <?php while ($diningRoom = $currentDiningRooms->fetch_assoc()): ?>
                    <div class="dining-room-item">
                        <?php echo htmlspecialchars($diningRoom['name']); ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="current-dishes">
                <h3>Current Dishes:</h3>
                <?php while ($dish = $currentDishes->fetch_assoc()): ?>
                    <div class="dish-item" data-dish-code="<?php echo htmlspecialchars($dish['code']); ?>">
                        <span><?php echo htmlspecialchars($dish['name']); ?> - $<?php echo htmlspecialchars($dish['price']); ?></span>
                        <span class="remove-dish" title="Remove dish">×</span>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="buttons">
                <button type="submit" name="update_menu" class="btn btn-update">Update Menu</button>
                <a href="viewMenu.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-dish').forEach(button => {
                button.addEventListener('click', function() {
                    const dishItem = this.parentElement;
                    const dishCode = dishItem.dataset.dishCode;
                    
                    if (confirm('Are you sure you want to remove this dish from the menu?')) {
                        const formData = new FormData();
                        formData.append('action', 'remove_dish');
                        formData.append('dish_code', dishCode);
                        
                        fetch(window.location.href, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                dishItem.remove();
                                alert('Dish removed successfully');
                            } else {
                                throw new Error(data.error || 'Error removing dish');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error: ' + error.message);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>