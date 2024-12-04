<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();
if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

function handleDishCreation($conn) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['add_dish'])) {
        return null;
    }

    $code = trim(htmlspecialchars($_POST['code'] ?? ''));
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $description = trim(htmlspecialchars($_POST['description'] ?? ''));
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $discountPercentage = filter_input(INPUT_POST, 'discountPercentage', FILTER_VALIDATE_FLOAT);
    $menu = trim(htmlspecialchars($_POST['menu'] ?? ''));
    $ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];
    $portions = isset($_POST['portions']) ? $_POST['portions'] : [];

    if (!$code || !$name || !$description || $price === false || $discountPercentage === false || !$menu) {
        return "Please fill all required fields with valid data.";
    }

    try {
        mysqli_begin_transaction($conn);
        
        $stmt = $conn->prepare("CALL createDish(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdds", 
            $code,
            $name,
            $description,
            $price,
            $discountPercentage,
            $menu
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating dish: " . $stmt->error);
        }
        $stmt->close();

        $stmtIngredient = $conn->prepare("CALL addDishIngredient(?, ?, ?)");
        foreach ($ingredients as $ingredientNum) {
            $portion = isset($portions[$ingredientNum]) ? intval($portions[$ingredientNum]) : 1;
            
            $stmtIngredient->bind_param("sii", 
                $code,
                $ingredientNum,
                $portion
            );
            
            if (!$stmtIngredient->execute()) {
                throw new Exception("Error adding ingredient: " . $stmtIngredient->error);
            }
        }
        $stmtIngredient->close();
        
        mysqli_commit($conn);
        header("Location: viewDish.php");
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return "Error: " . $e->getMessage();
    }
}
$message = handleDishCreation($conn);

$menus = $conn->query("SELECT * FROM menu");
$ingredients = $conn->query("SELECT * FROM ingredients");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Dish | Foody</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css" >
    <link rel="stylesheet" href="../css/DiningRoMCSS/createDish.css">
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

    <section class="create-dish-container">
        <h2>Create Dish</h2>

        <?php if (isset($message)): ?>
            <div class="message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="dish-form">
            <div class="form-group">
                <label for="code">Dish Code:</label>
                <input type="text" id="code" name="code" maxlength="5" pattern="[A-Za-z0-9]{1,5}" required>
            </div>

            <div class="form-group">
                <label for="name">Dish Name:</label>
                <input type="text" id="name" name="name" maxlength="50" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" maxlength="50" required></textarea>
            </div>

            <div class="form-group">
                <label for="menu">Menu:</label>
                <select id="menu" name="menu" required>
                    <option value="">Select a menu</option>
                    <?php while ($menu = $menus->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($menu['code']); ?>">
                            <?php echo htmlspecialchars($menu['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="discountPercentage">Discount Percentage:</label>
                <select id="discountPercentage" name="discountPercentage" required>
                    <?php for($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i/100; ?>"><?php echo $i; ?>%</option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ingredients">Ingredients:</label>
                <div class="ingredients-container">
                    <select id="ingredients-select">
                        <?php 
                        $ingredients->data_seek(0);
                        while ($ingredient = $ingredients->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($ingredient['num']); ?>">
                                <?php echo htmlspecialchars($ingredient['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="selected-ingredients" class="selected-ingredients"></div>
                </div>
            </div>

            <button type="submit" name="add_dish" class="btn-edit">Add Dish</button>
       

    <div><button class="btn-edit" ><a href="homeDiningRoom.php">Back to Home</a></button>
    </div>
    
    <button class="btn-edit"><a href="viewDish.php" class="btn-edit">See Dishes</a></button> 
    
    </form>
    </section>

   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ingredientsSelect = document.getElementById('ingredients-select');
            const selectedIngredientsContainer = document.getElementById('selected-ingredients');
            const selectedIngredients = new Set();

            ingredientsSelect.addEventListener('change', function() {
                Array.from(this.selectedOptions).forEach(option => {
                    if (!selectedIngredients.has(option.value)) {
                        addIngredientTag(option.value, option.text);
                        selectedIngredients.add(option.value);
                    }
                });
                this.selectedIndex = -1;
            });

            function addIngredientTag(value, text) {
                const tag = document.createElement('div');
                tag.className = 'ingredient-tag';
                tag.innerHTML = `
                    ${text}
                    <input type="number" 
                           name="portions[${value}]" 
                           value="1" 
                           min="1" 
                           class="portions-input" 
                           placeholder="Portions"
                           required>
                    <input type="hidden" name="ingredients[]" value="${value}">
                    <span class="remove-ingredient" onclick="removeIngredient(this, '${value}')">&times;</span>
                `;
                selectedIngredientsContainer.appendChild(tag);
            }

            window.removeIngredient = function(element, value) {
                selectedIngredients.delete(value);
                element.parentElement.remove();
            };
        });
    </script>
 <script src="/Foody/javascript/createdish.js"></script>
    

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