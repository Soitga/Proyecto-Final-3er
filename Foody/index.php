<?php
require_once __DIR__ . '/php/conectbd.php';
require_once __DIR__ . '/php/login.php';

define('EMPLOYEE_HOME', '/InterfacesEmpleadohtml/inicioEmpleado.php');
define('DINING_MANAGER_HOME', '/DiningRoomManager/homeDiningRoom.php');
define('GENERAL_MANAGER_HOME', '/generalManager/homeGeneralManager.php');

ini_set('session.gc_maxlifetime', 7200);
ini_set('session.cookie_lifetime', 7200);

// Iniciar la sesión
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
    <title>Foody</title>
    <style>
        :root {
            --brown: #774c20;
            --olive: #969044;
            --vanilla: #e5d2b8;
            --palmoil: #5e2611;
            --midnight: #3d3211ef;
            --text: "Quicksand", sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            box-sizing: border-box;
        }

        body {
            font-family: var(--text);
            background-color: var(--midnight);
            display: flex;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .brand-title {
            position: absolute;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: var(--vanilla);
            font-size: 3.5rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 10;
        }

        .container {
            margin: 0 auto;
            max-width: 1200px;
            width: 95%;
        }

        .container-form {
            display: flex;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(0,0,0,0.3);
        }

        .error-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b6b, #c92a2a);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease-out;
            max-width: 400px;
            z-index: 1000;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .form {
            flex: 1;
            padding: 3rem 2rem;
            background-color: var(--palmoil);
        }

        .text-form {
            text-align: center;
            margin-bottom: 2rem;
        }

        .text-form h2 {
            color: var(--vanilla);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .text-form p {
            color: var(--vanilla);
            font-size: 1.1rem;
        }

        .input {
            margin-bottom: 1.5rem;
        }

        .input label {
            display: block;
            color: var(--vanilla);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .input input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid transparent;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: rgba(229, 210, 184, 0.1);
            color: var(--vanilla);
        }

        .input input::placeholder {
            color: rgba(229, 210, 184, 0.5);
        }

        .input input:focus {
            outline: none;
            border-color: var(--olive);
            background-color: rgba(229, 210, 184, 0.2);
        }

        .input input[type="submit"] {
            background-color: var(--olive);
            color: white;
            font-weight: bold;
            cursor: pointer;
            padding: 1rem;
            margin-top: 1rem;
            border: none;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .input input[type="submit"]:hover {
            background-color: var(--brown);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .img-form {
            flex: 1.2;
            background-image: url('../css/imagenes/bg.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .img-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                to right,
                rgba(94, 38, 17, 0.9),
                rgba(94, 38, 17, 0.3)
            );
        }

        @media(max-width: 768px) {
            .container-form {
                flex-direction: column;
            }

            .img-form {
                height: 200px;
            }

            .form {
                padding: 2rem;
            }

            .brand-title {
                font-size: 2.5rem;
                position: relative;
                margin-bottom: 2rem;
            }
        }
    </style>
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
