<?php
require_once __DIR__ . '/../php/conectbd.php'; 
global $conn;
session_start();

function validateFactoryData($data) {
    $errors = [];
    
    if (strlen($data['code']) > 8) {
        $errors[] = "The factory code must have a maximum of 8 characters";
    }
    if (strlen($data['name']) > 16) {
        $errors[] = "The name must have a maximum of 16 characters";
    }
    if (strlen($data['email']) > 25 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid or too long email";
    }
    if (strlen($data['street']) > 15) {
        $errors[] = "The street must have a maximum of 15 characters";
    }
    if (strlen($data['neighborhood']) > 20) {
        $errors[] = "The colony must have a maximum of 20 characters";
    }
    if (empty($data['city_name'])) {
        $errors[] = "City name is required";
    }
    if (!isset($data['employee_count']) || !is_numeric($data['employee_count']) || intval($data['employee_count']) <= 0) {
        $errors[] = "Employee count must be a positive number";
    }
    
    return $errors;
}

function registerFactory($data) {
    try {
        global $conn;
        
        $sql = "CALL createfactory(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        
        $params = [
            $data['code'],
            $data['name'],
            $phone,
            $data['email'],
            $data['street'],
            intval($data['street_number']),
            $data['neighborhood'],
            intval($data['employee_count']),
            $data['city_name']
        ];
        
        $stmt->execute($params);
        return true;
        
    } catch(PDOException $e) {
        error_log("Error en registro de fábrica: " . $e->getMessage());
        return false;
    }
}

$message = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $factoryData = [
        'code' => trim($_POST['code'] ?? ''),
        'name' => trim($_POST['name'] ?? ''),
        'phone' => $_POST['phone'] ?? '',
        'email' => trim($_POST['email'] ?? ''),
        'street' => trim($_POST['street'] ?? ''),
        'street_number' => $_POST['street_number'] ?? null,
        'neighborhood' => trim($_POST['neighborhood'] ?? ''),
        'employee_count' => $_POST['employee_count'] ?? '',
        'city_name' => trim($_POST['city_name'] ?? '')
    ];
    
    $errors = validateFactoryData($factoryData);
    
    if (empty($errors)) {
        if (registerFactory($factoryData)) {
            header("Location: listFactory.php");
            exit();
        } else {
            $message = "Error al registrar la fábrica. Por favor, intente nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Factory</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css" >
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/generalManagerCss/registFactory.css">
</head>
<body>
<header>
    <div class="menu">
        <a href="homeGeneralManager.php" class="logo">Foody</a>
        <nav class="navbar">
            <ul>
                <li><a href="../index.php">Log off</a></li>
            </ul>
        </nav>
    </div>
</header>

<button class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="homeGeneralManager.php" class="logo">Foody</a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="registerEmplo.php">
                <i class="fas fa-user-plus"></i>
                <span>Register Users</span>
            </a></li>
            <li><a href="listUsers.php">
                <i class="fas fa-users"></i>
                <span>See Created Users</span>
            </a></li>
            <li><a href="registerfactory.php">
                <i class="fas fa-industry"></i>
                <span>Register Factory</span>
            </a></li>
            <li><a href="listFactory.php">
                <i class="fas fa-building"></i>
                <span>See Factories</span>
            </a></li>
            <li><a href="registerDining.php">
                <i class="fas fa-utensils"></i>
                <span>Register Dining Room</span>
            </a></li>
            <li><a href="listDiningRooms.php">
                <i class="fas fa-utensils"></i>
                <span>See Dining Room</span>
            </a></li>
            <li><a href="../index.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log off</span>
            </a></li>
        </ul>
    </nav>
</aside>
    
    <section class="register-factory-container">
        <h2>Register Factory</h2>
        
        <?php if (!empty($errors)): ?>
            <div style="color: red; margin-bottom: 20px;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div style="color: red; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" maxlength="16" required 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="code">Factory Code</label>
                <input type="text" id="code" name="code" maxlength="8" required
                       value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="street">Street</label>
                <input type="text" id="street" name="street" maxlength="15" required
                       value="<?php echo htmlspecialchars($_POST['street'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="street_number">Street Number</label>
                <input type="number" id="street_number" name="street_number" required
                       value="<?php echo htmlspecialchars($_POST['street_number'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="neighborhood">Neighborhood</label>
                <input type="text" id="neighborhood" name="neighborhood" maxlength="20" required
                       value="<?php echo htmlspecialchars($_POST['neighborhood'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="employee_count">Employee Count</label>
                <input type="number" id="employee_count" name="employee_count" min="1" required
                       value="<?php echo htmlspecialchars($_POST['employee_count'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="25" required
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" maxlength="10" required
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="city_name">City Name</label>
                <input type="text" id="city_name" name="city_name" required
                       value="<?php echo htmlspecialchars($_POST['city_name'] ?? ''); ?>">
            </div>

            <button type="submit">Register Factory</button>
        </form>
    </section>

     
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>