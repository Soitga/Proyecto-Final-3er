<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

if (isset($_POST['delete_menu'])) {
    try {
        mysqli_begin_transaction($conn);
        
        $menuCode = $_POST['menu_code'];
        
        $stmt = $conn->prepare("SELECT code FROM dish WHERE menu = ?");
        $stmt->bind_param("s", $menuCode);
        $stmt->execute();
        $dishResult = $stmt->get_result();
        $dishCodes = [];
        while ($row = $dishResult->fetch_assoc()) {
            $dishCodes[] = $row['code'];
        }
        
        if (!empty($dishCodes)) {
            $placeholders = str_repeat('?,', count($dishCodes) - 1) . '?';
            $stmt = $conn->prepare("DELETE FROM dish_ingred WHERE dish IN ($placeholders)");
            $stmt->bind_param(str_repeat('s', count($dishCodes)), ...$dishCodes);
            $stmt->execute();
        }
        
        if (!empty($dishCodes)) {
            $placeholders = str_repeat('?,', count($dishCodes) - 1) . '?';
            $stmt = $conn->prepare("DELETE FROM ord_dish WHERE dish IN ($placeholders)");
            $stmt->bind_param(str_repeat('s', count($dishCodes)), ...$dishCodes);
            $stmt->execute();
        }
        
        $stmt = $conn->prepare("DELETE FROM dining_menu WHERE menu = ?");
        $stmt->bind_param("s", $menuCode);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM dish WHERE menu = ?");
        $stmt->bind_param("s", $menuCode);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM menu WHERE code = ?");
        $stmt->bind_param("s", $menuCode);
        $stmt->execute();
        
        mysqli_commit($conn);
        $message = "Menu deleted successfully";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Error deleting menu: " . $e->getMessage();
    }
}
$query = "SELECT m.*, mt.description as schedule_type, 
          mt.start_time, mt.end_time,
          GROUP_CONCAT(DISTINCT dr.name SEPARATOR ', ') as dining_rooms,
          GROUP_CONCAT(DISTINCT d.name SEPARATOR '||') as dishes,
          GROUP_CONCAT(DISTINCT d.description SEPARATOR '||') as dish_descriptions,
          GROUP_CONCAT(DISTINCT d.price SEPARATOR '||') as dish_prices
          FROM menu m
          LEFT JOIN menu_type mt ON m.menu_type = mt.num
          LEFT JOIN dining_menu dm ON m.code = dm.menu
          LEFT JOIN diningRoom dr ON dm.diningRoom = dr.num
          LEFT JOIN dish d ON m.code = d.menu
          GROUP BY m.code
          ORDER BY m.code";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Menus | Foody</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/viewMenu.css">
</head>
<body>
    <header>
        <div class="menu">
            <a href="homeDiningRoom.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="header-actions">
    <h1>Menu List</h1>
    </div>

    <div class="container">
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        

        <div class="menus-grid">
        <?php while ($menu = $result->fetch_assoc()): ?>
            <div class="menu-card">
                <h3><?php echo htmlspecialchars($menu['name']); ?></h3>
                <div class="menu-info">
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($menu['description']); ?></p>
                    <p><strong>Schedule:</strong> <?php echo htmlspecialchars($menu['schedule_type']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($menu['start_time']); ?> - <?php echo htmlspecialchars($menu['end_time']); ?></p>
                    <p><strong>Dining Rooms:</strong> <?php echo htmlspecialchars($menu['dining_rooms'] ?? 'None assigned'); ?></p>
                    
                    <div class="dishes-section">
    <h4>Dishes:</h4>
    <?php
    if (!empty($menu['dishes'])) {
        $dishes = explode('||', $menu['dishes']);
        $descriptions = !empty($menu['dish_descriptions']) ? explode('||', $menu['dish_descriptions']) : array();
        $prices = !empty($menu['dish_prices']) ? explode('||', $menu['dish_prices']) : array();
        
        for ($i = 0; $i < count($dishes); $i++) {
            echo '<div class="dish-item">';
            echo '<strong>' . htmlspecialchars($dishes[$i] ?? 'N/A') . '</strong><br>';
            
            if (isset($descriptions[$i])) {
                echo htmlspecialchars($descriptions[$i]) . '<br>';
            }
            
            if (isset($prices[$i])) {
                echo '<span class="dish-price">$' . htmlspecialchars($prices[$i]) . '</span>';
            }
            echo '</div>';
        }
    } else {
        echo '<p>No dishes available</p>';
    }
    ?>
</div>
                </div>
                <div class="menu-actions">
                    <a href="editMenu.php?code=<?php echo urlencode($menu['code']); ?>" class="btn btn-edit">Edit</a>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this menu?');">
                        <input type="hidden" name="menu_code" value="<?php echo htmlspecialchars($menu['code']); ?>">
                        <button type="submit" name="delete_menu" class="btn btn-delete">Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="header-actions">
            <div>
                <a href="createmenu.php" class="btn btn-create">Create New Menu</a>
                <a href="homeDiningRoom.php" class="btn btn-home">Back to Home</a>
            </div>
        </div>
</body>
</html>