<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;
session_start();

if (!$conn) {
    die("Connection Error: " . mysqli_connect_error());
}

function getDishIngredients($conn, $dishCode) {
    $query = "SELECT i.num, i.name, i.experitionDate, di.numberIngred 
              FROM ingredients i 
              JOIN dish_ingred di ON i.num = di.ingredients 
              WHERE di.dish = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $dishCode);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $ingredients = [];
    while ($row = $result->fetch_assoc()) {
        $ingredients[] = [
            'id' => $row['num'],
            'name' => $row['name'],
            'experitionDate' => $row['experitionDate'],
            'quantity' => $row['numberIngred']
        ];
    }
    return $ingredients;
}

if (isset($_POST['delete_dish'])) {
    try {
        mysqli_begin_transaction($conn);
        
        $stmt = $conn->prepare("DELETE FROM dish_ingred WHERE dish = ?");
        $stmt->bind_param("s", $_POST['dish_code']);
        $stmt->execute();
    
        $stmt = $conn->prepare("DELETE FROM dish WHERE code = ?");
        $stmt->bind_param("s", $_POST['dish_code']);
        $stmt->execute();
        
        mysqli_commit($conn);
        $_SESSION['message'] = "Dish deleted successfully.";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = "Error deleting dish: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$query = "SELECT d.*, c.name as category_name, m.name as menu_name 
          FROM dish d 
          JOIN category c ON d.category = c.code 
          JOIN menu m ON d.menu = m.code 
          ORDER BY d.name";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dish Management | Foody</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/view.Dish.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
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

    <div class="dishes-container">
        <h2>Dish Management</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="controls">
            <a href="createDish.php" class="btn btn-add">Add New Dish</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Category</th>
                    <th>Menu</th>
                    <th>Ingredients (Quantity)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($dish = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dish['code']); ?></td>
                        <td><?php echo htmlspecialchars($dish['name']); ?></td>
                        <td><?php echo htmlspecialchars($dish['description']); ?></td>
                        <td>$<?php echo number_format($dish['price'], 2); ?></td>
                        <td><?php echo ($dish['discountPercentage'] * 100) . '%'; ?></td>
                        <td><?php echo htmlspecialchars($dish['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($dish['menu_name']); ?></td>
                        <td>
                            <?php 
                            $ingredients = getDishIngredients($conn, $dish['code']);
                            foreach ($ingredients as $ing) {
                                echo htmlspecialchars($ing['name']) . ' (Quantity: ' . $ing['quantity'] . ')<br>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="editDish.php?code=<?php echo htmlspecialchars($dish['code']); ?>" class="btn btn-edit">Edit</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this dish?');">
                                <input type="hidden" name="dish_code" value="<?php echo htmlspecialchars($dish['code']); ?>">
                                <button type="submit" name="delete_dish" class="btn btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const hamburgerBtn = document.querySelector('.hamburger-btn');
            sidebar.classList.toggle('open');
            hamburgerBtn.classList.toggle('active');
        }
    </script>
</body>
</html>