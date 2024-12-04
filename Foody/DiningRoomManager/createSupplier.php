<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_supplier'])) {
    try {
        $code = trim($_POST['code']);
        $name = trim($_POST['name']);
        $tel = trim($_POST['tel']);
        $email = trim($_POST['email']);

        $errors = [];
        
        if (empty($code)) {
            $errors[] = "Supplier code is required";
        }
        
        if (empty($name)) {
            $errors[] = "Supplier name is required";
        }
        
        if (!empty($tel) && !preg_match("/^\d{10}$/", $tel)) {
            $errors[] = "Telephone number must be 10 digits";
        }
        
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }

        $check_query = "SELECT * FROM supplier WHERE code = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $errors[] = "Supplier code already exists";
        }

        if (empty($errors)) {
            $tel = !empty($tel) ? (int)$tel : NULL;
            $email = !empty($email) ? $email : NULL;
            
            $stmt = $conn->prepare("CALL registerSupplier(?, ?, ?, ?)");
            $stmt->bind_param("ssis", $code, $name, $tel, $email);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Supplier created successfully";
                header("Location: viewSuppliers.php");
                exit();
            } else {
                $errors[] = "Error creating supplier: " . $stmt->error;
            }
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Supplier | Foody</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/createMenu.css">
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

    <section class="create-menu-container">
        <h2>Create Supplier</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="menu-form" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="code">Supplier Code:</label>
                <input type="text" id="code" name="code" required maxlength="5" 
                       value="<?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="name">Supplier Name:</label>
                <input type="text" id="name" name="name" required maxlength="25"
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="tel">Phone:</label>
                <input type="tel" id="tel" name="tel" maxlength="10" 
                       pattern="\d{10}" title="10 digit telephone number"
                       value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" maxlength="30"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <button type="submit" name="create_supplier" class="btn-create">Create Supplier</button>
        </form>
    </section>

    <div class="buttons">
        <a href="viewSuppliers.php" class="btn-view">View Suppliers</a>
        <a href="homeDiningRoom.php" class="btn-home">Back to Home</a>
    </div>

    <script>
        function validateForm() {
            const code = document.getElementById('code').value.trim();
            const name = document.getElementById('name').value.trim();
            const tel = document.getElementById('tel').value.trim();
            const email = document.getElementById('email').value.trim();

            if (code.length === 0 || code.length > 7) {
                alert('Supplier code is required and must be max 7 characters');
                return false;
            }

            if (name.length === 0 || name.length > 25) {
                alert('Supplier name is required and must be max 25 characters');
                return false;
            }

            if (tel.length > 0 && !/^\d{10}$/.test(tel)) {
                alert('Telephone must be 10 digits');
                return false;
            }

            if (email.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Invalid email format');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>