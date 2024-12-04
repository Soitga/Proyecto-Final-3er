<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;
session_start();

function getUserInfo($conn, $user_id) {
    $userInfo = [];
    
    $baseQuery = "SELECT u.email, u.rol FROM users u WHERE u.num = ?";
    $stmt = $conn->prepare($baseQuery);
    
    if (!$stmt) {
        throw new Exception("Error preparing base query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing base query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $baseInfo = $result->fetch_assoc();
    
    if (!$baseInfo) {
        throw new Exception("User not found");
    }
    
    $userInfo = $baseInfo;
    
    $roleSpecificQuery = "";
    switch ($baseInfo['rol']) {
        case 'employee':
            $roleSpecificQuery = "
                SELECT e.firstName, e.middleName, e.lastName, e.tel, 
                       e.factory, j.description as jobPosition
                FROM employee e
                JOIN jobPosition j ON e.jobPosition = j.code
                WHERE e.userNum = ?";
            break;
            
        case 'diningRoomManager':
            $roleSpecificQuery = "
                SELECT d.firstName, d.middleName, d.lastName, d.tel,
                       dr.name as diningRoomName
                FROM diningRoomManager d
                JOIN diningRoom dr ON d.diningRoom = dr.num
                WHERE d.userNumber = ?";
            break;
            
        case 'generalManager':
            $roleSpecificQuery = "
                SELECT f.firstName, f.middleName, f.lastName, f.tel,
                       fac.name as factoryName
                FROM factoryAdmin f
                JOIN factory fac ON f.factory = fac.code
                WHERE f.user_num = ?";
            break;
            
        default:
            throw new Exception("Invalid role type");
    }
    
    $stmt = $conn->prepare($roleSpecificQuery);
    if (!$stmt) {
        throw new Exception("Error preparing role specific query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing role specific query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $roleSpecificInfo = $result->fetch_assoc();
    
    if (!$roleSpecificInfo) {
        throw new Exception("Role specific information not found");
    }
    
    return array_merge($userInfo, $roleSpecificInfo);
}

try {
    if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
        throw new Exception("User ID not provided");
    }
    
    $user_id = filter_var($_GET['user_id'], FILTER_VALIDATE_INT);
    if ($user_id === false) {
        throw new Exception("Invalid user ID format");
    }
    
    $user = getUserInfo($conn, $user_id);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css" >
    <link rel="stylesheet" href="/css/generalManagerCss/viewUser.css">
</head>
<body>
    <header>
        <div class="menu">
            <a href="/generalManager/homeGeneralManager.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.html">Log off</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="homeGeneralManager.php" class="logo">Foody</a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="userInfo.php" data-section="profile-section">
                    <i class="fas fa-user"></i>
                    <span>View Profile</span>
                </a></li>
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
                <li><a href="../index.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log off</span>
                </a></li>
            </ul>
        </nav>
    </aside>

    <section class="user-info">
        <h1>User Information</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (isset($user)): ?>
            <div class="user-details">
                <p><strong>Name:</strong> 
                    <?php echo htmlspecialchars($user['firstName'] . ' ' . 
                                              $user['middleName'] . ' ' . 
                                              $user['lastName']); ?>
                </p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['rol']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['tel']); ?></p>
                
                <?php switch($user['rol']): 
                    case 'employee': ?>
                        <p><strong>Factory:</strong> <?php echo htmlspecialchars($user['factory']); ?></p>
                        <p><strong>Position:</strong> <?php echo htmlspecialchars($user['jobPosition']); ?></p>
                        <?php break; ?>
                        
                    <?php case 'diningRoomManager': ?>
                        <p><strong>Dining Room:</strong> <?php echo htmlspecialchars($user['diningRoomName']); ?></p>
                        <?php break; ?>
                        
                    <?php case 'generalManager': ?>
                        <p><strong>Factory:</strong> <?php echo htmlspecialchars($user['factoryName']); ?></p>
                        <?php break; ?>
                <?php endswitch; ?>
            </div>
        <?php endif; ?>

        <div class="buttons">
            <a href="/generalManager/homeGeneralManager.php" class="btn-home">Return to Home</a>
        </div>
        
        <div class="buttons">
        <a href="/generalManager/listUsers.php" class="btn-home">See All Users</a>
        </div>
    </section>
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>

</body>
</html>