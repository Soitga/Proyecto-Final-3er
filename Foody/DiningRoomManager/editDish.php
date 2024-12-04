<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;
session_start();

if (!$conn) {
    die("Connection Error: " . mysqli_connect_error());
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    $stmt = $conn->prepare("SELECT * FROM dish WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $dish = $stmt->get_result()->fetch_assoc();
    
    if (!$dish) {
        $_SESSION['message'] = "Dish not found.";
        header("Location: viewDish.php");
        exit();
    }
    
    $stmt = $conn->prepare("SELECT i.num, i.name, di.numberIngred 
                           FROM ingredients i 
                           JOIN dish_ingred di ON i.num = di.ingredients 
                           WHERE di.dish = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $dishIngredients = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    try {
        mysqli_begin_transaction($conn);
        
        $stmt = $conn->prepare("UPDATE dish SET 
            name = ?, 
            description = ?, 
            price = ?, 
            discountPercentage = ?, 
            category = ?,
            menu = ?
            WHERE code = ?");
            
        $stmt->bind_param("ssddsss", 
            $_POST['name'], 
            $_POST['description'], 
            $_POST['price'], 
            $_POST['discountPercentage'], 
            $_POST['category'],
            $_POST['menu'],
            $_POST['code']
        );
        
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM dish_ingred WHERE dish = ?");
        $stmt->bind_param("s", $_POST['code']);
        $stmt->execute();
        
        if (isset($_POST['ingredients']) && isset($_POST['quantities'])) {
            $stmtIngred = $conn->prepare("INSERT INTO dish_ingred (dish, ingredients, numberIngred) VALUES (?, ?, ?)");
            
            foreach ($_POST['ingredients'] as $index => $ingredientId) {
                $quantity = $_POST['quantities'][$index];
                $stmtIngred->bind_param("sii", $_POST['code'], $ingredientId, $quantity);
                $stmtIngred->execute();
            }
        }
        
        mysqli_commit($conn);
        $_SESSION['message'] = "Dish updated successfully.";
        header("Location: viewDish.php");
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = "Error: " . $e->getMessage();
    }
}

$categories = $conn->query("SELECT * FROM category ORDER BY name");
$menus = $conn->query("SELECT * FROM menu ORDER BY name");
$ingredients = $conn->query("SELECT * FROM ingredients ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dish | Foody</title>
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

    <div class="dishes-container">
        <h2>Edit Dish</h2>

        <form method="POST" class="edit-form">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="code" value="<?php echo htmlspecialchars($dish['code']); ?>">
            
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($dish['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Price:</label>
                <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($dish['price']); ?>" required>
            </div>

            <div class="form-group">
                <label>Discount (%):</label>
                <input type="number" name="discountPercentage" step="0.01" min="0" max="1" 
                       value="<?php echo htmlspecialchars($dish['discountPercentage']); ?>" required>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <select name="category" required>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($category['code']); ?>"
                                <?php if ($category['code'] === $dish['category']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Menu:</label>
                <select name="menu" required>
                    <?php while ($menu = $menus->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($menu['code']); ?>"
                                <?php if ($menu['code'] === $dish['menu']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($menu['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Current Ingredients:</label>
                <div id="current-ingredients">
                    <?php foreach ($dishIngredients as $ing): ?>
                        <div class="ingredient-tag">
                            <span><?php echo htmlspecialchars($ing['name']); ?></span>
                            <input type="hidden" name="ingredients[]" value="<?php echo htmlspecialchars($ing['num']); ?>">
                            <input type="number" name="quantities[]" value="<?php echo htmlspecialchars($ing['numberIngred']); ?>" 
                                   min="1" required>
                            <button type="button" onclick="this.parentElement.remove()">&times;</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Add Ingredient:</label>
                <select id="ingredient-select">
                    <option value="">Select ingredient...</option>
                    <?php while ($ingredient = $ingredients->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($ingredient['num']); ?>"
                                data-name="<?php echo htmlspecialchars($ingredient['name']); ?>">
                            <?php echo htmlspecialchars($ingredient['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="buttons">
                <button type="submit" class="btn btn-add">Save Changes</button>
                <a href="viewDish.php" class="btn btn-delete">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('ingredient-select').addEventListener('change', function() {
            if (this.value) {
                const option = this.options[this.selectedIndex];
                const container = document.getElementById('current-ingredients');
                
                const div = document.createElement('div');
                div.className = 'ingredient-tag';
                div.innerHTML = `
                    <span>${option.text}</span>
                    <input type="hidden" name="ingredients[]" value="${this.value}">
                    <input type="number" name="quantities[]" value="1" min="1" required>
                    <button type="button" onclick="this.parentElement.remove()">&times;</button>
                `;
                container.appendChild(div);
                this.value = '';
            }
        });

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const hamburgerBtn = document.querySelector('.hamburger-btn');
            sidebar.classList.toggle('open');
            hamburgerBtn.classList.toggle('active');
        }
    </script>
</body>
</html>