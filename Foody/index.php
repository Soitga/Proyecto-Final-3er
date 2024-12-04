<?php
require_once __DIR__ . '/php/conectbd.php';
require_once __DIR__ . '/php/login.php';

define('EMPLOYEE_HOME', '/InterfacesEmpleadohtml/inicioEmpleado.php');
define('DINING_MANAGER_HOME', '/DiningRoomManager/homeDiningRoom.php');
define('GENERAL_MANAGER_HOME', '/generalManager/homeGeneralManager.php');

ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 7200);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $loginResult = loginUser($_POST['email'], $_POST['password']);
        if ($loginResult['success']) {
            switch($_SESSION['user_role']) {
                case 'employee':
                    $redirect_url = EMPLOYEE_HOME;
                    break;
                case 'diningRoomManager':
                    $redirect_url = DINING_MANAGER_HOME;
                    break;
                case 'generalManager':
                    $redirect_url = GENERAL_MANAGER_HOME;
                    break;
                default:
                    throw new Exception("Invalid role");
            }
            header("Location: " . $redirect_url);
            exit();
        } else {
            throw new Exception($loginResult['message']);
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        error_log("Error de login: " . $e->getMessage());
        header('Location: index.php?error=login_failed');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Foody</title>
</head>
<body>
    <h1 class="brand-title">Foody</h1>
    <div class="container-form container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="form">
            <div class="text-form">
                <h2>Login</h2>
                <p>Log in with your account</p>
            </div>
            
            <div class="input">
                <label for="email">Email</label>
                <input 
                    placeholder="User123@gmail.com" 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                >
            </div>
            
            <div class="input">
                <label for="password">Password</label>
                <input 
                    placeholder="Password" 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                >
            </div>
            
            <div class="input">
                <input type="submit" value="Login">
            </div>
        </form>
        
        <div class="img-form"></div>
    </div>
</body>
</html>
