<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = mysqli_real_escape_string($conn, $_POST['delete_user']);
    
    mysqli_begin_transaction($conn);
    
    try {
        $roleQuery = "SELECT rol FROM users WHERE num = ?";
        $stmt = $conn->prepare($roleQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        
        if (!$userData) {
            throw new Exception("User not found");
        }
        
        $userRole = $userData['rol'];
        
        $deleteRoleQuery = "";
        switch ($userRole) {
            case 'employee':
                $deleteRoleQuery = "DELETE FROM employee WHERE userNum = ?";
                break;
            case 'diningRoomManager':
                $deleteRoleQuery = "DELETE FROM diningRoomManager WHERE userNumber = ?";
                break;
            case 'generalManager':
                $deleteRoleQuery = "DELETE FROM factoryAdmin WHERE user_num = ?";
                break;
            default:
                throw new Exception("Invalid user role");
        }
        
        if ($deleteRoleQuery) {
            $stmt = $conn->prepare($deleteRoleQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
        
        $deleteUserQuery = "DELETE FROM users WHERE num = ?";
        $stmt = $conn->prepare($deleteUserQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        mysqli_commit($conn);
        $message = "User successfully deleted.";
        $messageType = "success";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error deleting user: " . $e->getMessage();
        $messageType = "error";
    }
}

$employeeQuery = "SELECT * FROM infoEmployee ORDER BY id DESC";
$diningRoomManagerQuery = "SELECT * FROM infoDinningRoomManager ORDER BY id DESC";
$generalManagerQuery = "SELECT * FROM infoGeneralManager ORDER BY id DESC";

$employees = $conn->query($employeeQuery);
$diningRoomManagers = $conn->query($diningRoomManagerQuery);
$generalManagers = $conn->query($generalManagerQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users List</title>
    <link rel="stylesheet" href="/css/generalManagerCss/listview.css">
</head>
<body>
    <header>
        <div class="menu">
            <a href="homeGeneralManager.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="user-list">
        <h1>Registered Users List</h1>

        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="user-section">
            <h2>Employees</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Factory</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($employee = $employees->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($employee['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($employee['Name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($employee['E-mail'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($employee['phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($employee['factory'] ?? 'N/A'); ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <button type="submit" class="btn-delete" name="delete_user" value="<?php echo htmlspecialchars($employee['id'] ?? ''); ?>">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="user-section">
            <h2>Dining Room Managers</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Dining Room</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($manager = $diningRoomManagers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($manager['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($manager['Name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($manager['E-mail'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($manager['phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($manager['diningRoom'] ?? 'N/A'); ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <button type="submit" class="btn-delete" name="delete_user" value="<?php echo htmlspecialchars($manager['id'] ?? ''); ?>">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="user-section">
            <h2>General Managers</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Factory</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($generalManager = $generalManagers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($generalManager['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($generalManager['Name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($generalManager['E-mail'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($generalManager['phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($generalManager['factory'] ?? 'N/A'); ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <button type="submit" class="btn-delete" name="delete_user" value="<?php echo htmlspecialchars($generalManager['id'] ?? ''); ?>">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="buttons">
        <a href="/generalManager/homeGeneralManager.php" class="btn btn-primary">Back to Home</a>
        <a href="/generalManager/editUsers.php" class="btn btn-primary">Edit Users</a>
    </div>
</body>
</html>