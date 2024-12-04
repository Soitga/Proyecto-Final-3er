<?php
require_once __DIR__ . '/../php/conectbd.php'; 
global $conn;
session_start();

$diningRooms = [];
$queryDiningRooms = "SELECT num, name FROM diningRoom";
$resultDiningRooms = mysqli_query($conn, $queryDiningRooms);
if ($resultDiningRooms) {
    while($row = mysqli_fetch_assoc($resultDiningRooms)) {
        $diningRooms[] = $row;
    }
}

$factories = [];
$queryFactories = "SELECT code, name FROM factory";
$resultFactories = mysqli_query($conn, $queryFactories);
if ($resultFactories) {
    while($row = mysqli_fetch_assoc($resultFactories)) {
        $factories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first-name'] ?? '');
    $lastName = trim($_POST['last-name'] ?? '');
    $maternalName = trim($_POST['middle-name'] ?? '');
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $factory = $_POST['factory'] ?? null;
    $position = $_POST['position'] ?? null;
    $rol = $_POST['rol'];
    $diningRoom = $_POST['diningroom'] ?? null;

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $regexName = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/";

    if (!preg_match($regexName, $firstName) || !preg_match($regexName, $lastName) || !preg_match($regexName, $maternalName)) {
        echo json_encode(['error' => 'The names contain invalid characters.']);
        exit;
    }

    $checkEmailQuery = "SELECT email FROM users WHERE email = ?";
    $stmtCheck = mysqli_prepare($conn, $checkEmailQuery);
    mysqli_stmt_bind_param($stmtCheck, 's', $email);
    
    if (!mysqli_stmt_execute($stmtCheck)) {
        echo json_encode(['error' => 'Error checking email: ' . mysqli_stmt_error($stmtCheck)]);
        exit;
    }
    
    mysqli_stmt_store_result($stmtCheck);

    if (mysqli_stmt_num_rows($stmtCheck) > 0) {
        echo json_encode(['error' => 'The e-mail address is already registered.']);
        exit;
    }
    mysqli_stmt_close($stmtCheck);

    $hash_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        mysqli_begin_transaction($conn);
        
        $userId = null;
        $error = null;

        if ($rol === 'employee') {
            $stmt = mysqli_prepare($conn, "CALL registerEmployee(?, ?, ?, ?, ?, ?, ?, ?, @new_user_id)");
            mysqli_stmt_bind_param($stmt, 'ssssssss', 
                $firstName,
                $maternalName,
                $lastName,
                $phone,
                $factory,
                $position,
                $email,
                $hash_password
            );
        } 
        elseif ($rol === 'diningRoomManager') {
            $stmt = mysqli_prepare($conn, "CALL regiterDininManager(?, ?, ?, ?, ?, ?, ?, @new_user_id)");
            mysqli_stmt_bind_param($stmt, 'sssssss',
                $firstName,
                $maternalName,
                $lastName,
                $phone,
                $diningRoom,
                $email,
                $hash_password
            );
        } 
        elseif ($rol === 'generalManager') {
            $stmt = mysqli_prepare($conn, "CALL registerGenManager(?, ?, ?, ?, ?, ?, ?, @new_user_id)");
            mysqli_stmt_bind_param($stmt, 'sssssss',
                $firstName,
                $maternalName,
                $lastName,
                $phone,
                $factory,
                $email,
                $hash_password
            );
        }

        if (!mysqli_stmt_execute($stmt)) {
            $errorCode = mysqli_errno($conn);
            if ($errorCode == 1644) { 
                $error = mysqli_stmt_error($stmt);
                if (strpos($error, "A manager is already assigned") !== false) {
                    throw new Exception("A manager is already assigned to this dining room.");
                } else if (strpos($error, "A general manager is already assigned") !== false) {
                    throw new Exception("A general manager is already assigned to this factory.");
                }
            } else {
                throw new Exception("Error registering user: " . mysqli_stmt_error($stmt));
            }
        }

        $result = mysqli_query($conn, "SELECT @new_user_id as user_id");
        $row = mysqli_fetch_assoc($result);
        $userId = $row['user_id'];

        if (!$userId) {
            throw new Exception("Error getting new user ID");
        }

        mysqli_commit($conn);
        mysqli_stmt_close($stmt);
        
        echo json_encode(['success' => true, 'userId' => $userId]);
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/generalManagerCss/registerEmploye.css">
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

    <section class="register-employee-container">
        <h2>Register User</h2>

        <div id="error-message" class="alert alert-danger hidden"></div>

        <form action="" method="POST" id="registerForm">
            <div class="form-group">
                <label for="first-name">First Name </label>
                <input type="text" id="first-name" name="first-name" required>
            </div>

            <div class="form-group">
                <label for="middle-name">Middle Name </label>
                <input type="text" id="middle-name" name="middle-name" required>
            </div>

            <div class="form-group">
                <label for="last-name">Last Name </label>
                <input type="text" id="last-name" name="last-name" required>
            </div>

            <div class="form-group">
                <label for="email">Email </label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password </label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone </label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" maxlength="10" required>
            </div>

            <div class="form-group">
                <label for="rol">Role </label>
                <select name="rol" id="rol" required>
                    <option value="">Select Role</option>
                    <option value="employee">Employee</option>
                    <option value="diningRoomManager">Dining Room Manager</option>
                    <option value="generalManager">General Manager</option>
                </select>
            </div>

            <div id="employeeFields" class="role-specific-fields hidden">
                <div class="form-group">
                    <label for="factory">Factory </label>
                    <select name="factory" id="factory">
                        <option value="">Select a Factory</option>
                        <?php foreach ($factories as $factory): ?>
                            <option value="<?php echo htmlspecialchars($factory['code']); ?>">
                                <?php echo htmlspecialchars($factory['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="position">Position </label>
                    <input type="text" id="position" name="position">
                </div>
            </div>

            <div id="diningRoomManagerFields" class="role-specific-fields hidden">
                <div class="form-group">
                    <label for="diningroom">Dining Room </label>
                    <select name="diningroom" id="diningroom">
                        <option value="">Select Dining Room</option>
                        <?php foreach ($diningRooms as $diningRoom): ?>
                            <option value="<?php echo htmlspecialchars($diningRoom['num']); ?>">
                                <?php echo htmlspecialchars($diningRoom['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="generalManagerFields" class="role-specific-fields hidden">
                <div class="form-group">
                    <label for="factory">Factory </label>
                    <select name="factory" id="factory-manager">
                        <option value="">Select a Factory</option>
                        <?php foreach ($factories as $factory): ?>
                            <option value="<?php echo htmlspecialchars($factory['code']); ?>">
                                <?php echo htmlspecialchars($factory['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit">Register User</button>
        </form>
    </section>

    <script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const roleSelect = document.getElementById('rol');
    const errorMessage = document.getElementById('error-message');
    const roleSpecificFields = document.querySelectorAll('.role-specific-fields');
    
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('hidden');
    }

    function hideError() {
        errorMessage.classList.add('hidden');
    }

    function hideAllRoleFields() {
        roleSpecificFields.forEach(field => {
            field.classList.add('hidden');
            field.querySelectorAll('input, select').forEach(input => {
                input.required = false;
                input.disabled = true;
            });
        });
    }

    function showRoleFields(role) {
        hideAllRoleFields();
        
        let fieldsToShow = null;
        switch(role) {
            case 'employee':
                fieldsToShow = document.getElementById('employeeFields');
                break;
            case 'diningRoomManager':
                fieldsToShow = document.getElementById('diningRoomManagerFields');
                break;
            case 'generalManager':
                fieldsToShow = document.getElementById('generalManagerFields');
                break;
        }

        if (fieldsToShow) {
            fieldsToShow.classList.remove('hidden');
            fieldsToShow.querySelectorAll('input, select').forEach(input => {
                input.required = true;
                input.disabled = false;
            });
        }
    }

    roleSelect.addEventListener('change', function() {
        hideError();
        if (this.value) {
            showRoleFields(this.value);
        } else {
            hideAllRoleFields();
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        hideError();

        if (!validateForm()) {
            return;
        }

        try {
            const formData = new FormData(this);
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) {
                showError(result.error);
            } else if (result.success) {
                window.location.href = 'userInfo.php?user_id=' + result.userId;
            }
        } catch (error) {
            showError('An error occurred while processing your request.');
        }
    });

    function validateForm() {
        const firstName = document.getElementById('first-name').value.trim();
        const middleName = document.getElementById('middle-name').value.trim();
        const lastName = document.getElementById('last-name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const phone = document.getElementById('phone').value.trim();
        const role = roleSelect.value;

        if (!firstName || !middleName || !lastName || !email || !password || !phone || !role) {
            showError('Please fill in all required fields.');
            return false;
        }

        if (!/^\d{10}$/.test(phone)) {
            showError('Phone number must be exactly 10 digits.');
            return false;
        }

        switch(role) {
            case 'employee':
                const empFactory = document.getElementById('factory').value;
                const position = document.getElementById('position').value.trim();
                if (!empFactory || !position) {
                    showError('Please complete all employee fields.');
                    return false;
                }
                break;
            case 'diningRoomManager':
                const diningRoom = document.getElementById('diningroom').value;
                if (!diningRoom) {
                    showError('Please select a dining room.');
                    return false;
                }
                break;
            case 'generalManager':
                const genFactory = document.getElementById('factory-manager').value;
                if (!genFactory) {
                    showError('Please select a factory.');
                    return false;
                }
                break;
        }

        return true;
    }
    hideAllRoleFields();
});

    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>