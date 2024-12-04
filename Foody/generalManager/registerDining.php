<?php
require_once __DIR__ . '/../php/conectbd.php'; 
global $conn;
session_start();

function validateDiningRoomData($data) {
    $errors = [];
    if (strlen($data['name']) > 30) {
        $errors[] = "The name must have a maximum of 30 characters";
    }
    if (strlen($data['ubication']) > 30) {
        $errors[] = "The location must have a maximum of 30 characters";
    }
    if (empty($data['factory'])) {
        $errors[] = "Factory selection is required";
    }
    return $errors;
}

function registerDiningRoom($data) {
    try {
        global $conn;
        $sql = "CALL creatediningroom(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $params = [
            $data['name'],
            $data['ubication'],
            $data['factory']
        ];
        $stmt->bind_param("sss", ...$params);
        $stmt->execute();
        return true;
    } catch(Exception $e) {
        error_log("Error in dining room registration: " . $e->getMessage());
        return false;
    }
}

function getFactories() {
    try {
        global $conn;
        $stmt = $conn->query("SELECT code, name FROM factory");
        $factories = [];
        while ($row = $stmt->fetch_assoc()) {
            $factories[] = $row;
        }
        return $factories;
    } catch(Exception $e) {
        error_log("Error fetching factories: " . $e->getMessage());
        return [];
    }
}

$message = '';
$errors = [];
$factories = getFactories();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diningRoomData = [
        'name' => trim($_POST['name'] ?? ''),
        'ubication' => trim($_POST['location'] ?? ''),
        'factory' => $_POST['factory_id'] ?? ''
    ];

    
    $errors = validateDiningRoomData($diningRoomData);
    
    if (empty($errors)) {
        if (registerDiningRoom($diningRoomData)) {
            header("Location: listDiningRooms.php");
            exit();
        } else {
            $message = "Error registering dining room. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Dining Room</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
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
        <h2>Register Dining Room</h2>
        
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
                <label for="location">Location</label>
                <input type="text" id="location" name="location" maxlength="30" required
                       value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="factory_id">Factory</label>
                <select id="factory_id" name="factory_id" required>
    <option value="">Select a factory</option>
    <?php foreach ($factories as $factory): ?>
        <option value="<?php echo htmlspecialchars($factory['code']); ?>"
                <?php echo (isset($_POST['factory_id']) && $_POST['factory_id'] == $factory['code']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($factory['name']); ?>
        </option>
    <?php endforeach; ?>
</select>
            </div>

            <button type="submit">Register Dining Room</button>
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