<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

$message = '';
$messageType = '';

function cleanInput($data) {
    if ($data === null || !isset($data)) {
        return '';
    }
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    try {
        $userId = intval($_POST['user_id']);
        $role = cleanInput($_POST['role']);
        $firstName = cleanInput($_POST['first_name']);
        $middleName = cleanInput($_POST['middle_name']);
        $lastName = cleanInput($_POST['last_name']);
        $phone = cleanInput($_POST['tel']);
        $extraField = cleanInput($_POST['extra_field']);
        $email = isset($_POST['email']) ? cleanInput($_POST['email']) : '';
        
        mysqli_begin_transaction($conn);

        if (!empty($email)) {
            $updateUserQuery = "UPDATE users SET email = ? WHERE num = ?";
            $stmtUser = $conn->prepare($updateUserQuery);
            $stmtUser->bind_param("si", $email, $userId);
            $stmtUser->execute();
        }

        switch ($role) {
            case 'employee':
                $updateQuery = "UPDATE employee SET 
                    firstName = ?, 
                    middleName = ?, 
                    lastName = ?, 
                    tel = ?, 
                    jobPosition = ? 
                    WHERE userNum = ?";
                break;
            
            case 'diningRoomManager':
                $updateQuery = "UPDATE diningRoomManager SET 
                    firstName = ?, 
                    middleName = ?, 
                    lastName = ?, 
                    tel = ?, 
                    diningRoom = ? 
                    WHERE userNumber = ?";
                break;
            
            case 'generalManager':
                $updateQuery = "UPDATE factoryAdmin SET 
                    firstName = ?, 
                    middleName = ?, 
                    lastName = ?, 
                    tel = ?, 
                    factory = ? 
                    WHERE user_num = ?";
                break;
            
            default:
                throw new Exception("Invalid role specified");
        }

        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssssi", $firstName, $middleName, $lastName, $phone, $extraField, $userId);
        
        if ($stmt->execute()) {
            mysqli_commit($conn);
            $message = "User updated successfully.";
            $messageType = "success";
        } else {
            throw new Exception("Error executing update query: " . $conn->error);
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error updating user: " . $e->getMessage();
        $messageType = "error";
    }
}

function fetchUsersByRole($role) {
    global $conn;
    
    try {
        $query = match ($role) {
            'employee' => "SELECT 
                e.userNum as num,
                u.email,
                e.firstName,
                e.middleName,
                e.lastName,
                e.tel,
                e.jobPosition as extra_field
                FROM employee e
                LEFT JOIN users u ON e.userNum = u.num
                WHERE u.rol = 'employee'",
                
            'diningRoomManager' => "SELECT 
                d.userNumber as num,
                u.email,
                d.firstName,
                d.middleName,
                d.lastName,
                d.tel,
                d.diningRoom as extra_field
                FROM diningRoomManager d
                LEFT JOIN users u ON d.userNumber = u.num
                WHERE u.rol = 'diningRoomManager'",
                
            'generalManager' => "SELECT 
                f.user_num as num,
                u.email,
                f.firstName,
                f.middleName,
                f.lastName,
                f.tel,
                f.factory as extra_field
                FROM factoryAdmin f
                LEFT JOIN users u ON f.user_num = u.num
                WHERE u.rol = 'generalManager'",
                
            default => throw new Exception("Invalid role specified")
        };

        $result = $conn->query($query);
        
        if ($result === false) {
            throw new Exception("Error fetching users: " . $conn->error);
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in fetchUsersByRole: " . $e->getMessage());
        return null;
    }
}

$employees = fetchUsersByRole('employee');
$diningManagers = fetchUsersByRole('diningRoomManager');
$generalManagers = fetchUsersByRole('generalManager');

$roleData = [
    ['title' => 'Employees', 'data' => $employees, 'role' => 'employee', 'extra_field_label' => 'Job Position'],
    ['title' => 'Dining Room Managers', 'data' => $diningManagers, 'role' => 'diningRoomManager', 'extra_field_label' => 'Dining Room'],
    ['title' => 'General Managers', 'data' => $generalManagers, 'role' => 'generalManager', 'extra_field_label' => 'Factory']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="/css/generalManagerCss/listview.css">
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

    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <main>
        <?php foreach ($roleData as $roleInfo): ?>
            <section class="role-section">
                <h2><?php echo $roleInfo['title']; ?></h2>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Phone</th>
                            <th><?php echo $roleInfo['extra_field_label']; ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($roleInfo['data'] && $roleInfo['data']->num_rows > 0):
                            while ($row = $roleInfo['data']->fetch_assoc()): 
                        ?>
                            <tr>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to save these changes?');">
                                    <td><?php echo htmlspecialchars($row['num']); ?></td>
                                    <td>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($row['firstName']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($row['middleName']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($row['lastName']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="tel" name="tel" value="<?php echo htmlspecialchars($row['tel']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="extra_field" value="<?php echo htmlspecialchars($row['extra_field']); ?>" required>
                                    </td>
                                    <td class="action-buttons">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['num']); ?>">
                                        <input type="hidden" name="role" value="<?php echo htmlspecialchars($roleInfo['role']); ?>">
                                        <button type="submit" name="edit_user" class="edit-button">Save</button>
                                    </td>
                                </form>
                            </tr>
                        <?php 
                            endwhile;
                        endif;
                        ?>
                    </tbody>
                </table>
            </section>
        <?php endforeach; ?>
    </main>

    <div class="buttons">
    <a href="/generalManager/homeGeneralManager.php" class="btn btn-primary">Back to Home</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.querySelector('.message');
            if (message) {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>