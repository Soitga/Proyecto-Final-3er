<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $sql = "DELETE FROM diningRoom WHERE num = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_POST['delete']);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $sql = "UPDATE diningRoom SET name = ?, ubication = ? WHERE num = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $_POST['name'], $_POST['ubication'], $_POST['num']);
    $stmt->execute();
}

$sql = "SELECT d.*, f.name as factory_name 
        FROM diningRoom d 
        JOIN factory f ON d.factory = f.code 
        ORDER BY d.num";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Dining Rooms</title>
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/generalManagerCss/listDining.css">
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
        <h2>Dining Rooms List</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Factory</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row-<?php echo $row['num']; ?>">
                        <td>
                            <span class="display-value"><?php echo htmlspecialchars($row['name']); ?></span>
                            <input type="text" class="edit-input form-control d-none" 
                                   value="<?php echo htmlspecialchars($row['name']); ?>" maxlength="30">
                        </td>
                        <td>
                            <span class="display-value"><?php echo htmlspecialchars($row['ubication']); ?></span>
                            <input type="text" class="edit-input form-control d-none" 
                                   value="<?php echo htmlspecialchars($row['ubication']); ?>" maxlength="30">
                        </td>
                        <td><?php echo htmlspecialchars($row['factory_name']); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-btn" 
                                    onclick="toggleEdit(<?php echo $row['num']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-success btn-sm save-btn d-none" 
                                    onclick="saveChanges(<?php echo $row['num']; ?>)">
                                <i class="fas fa-save"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" 
                                    onclick="deleteDiningRoom(<?php echo $row['num']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <script>
        function toggleEdit(id) {
            const row = document.getElementById(`row-${id}`);
            row.querySelectorAll('.display-value').forEach(el => el.classList.toggle('d-none'));
            row.querySelectorAll('.edit-input').forEach(el => el.classList.toggle('d-none'));
            row.querySelector('.edit-btn').classList.toggle('d-none');
            row.querySelector('.save-btn').classList.toggle('d-none');
        }

        function saveChanges(id) {
            const row = document.getElementById(`row-${id}`);
            const name = row.querySelector('.edit-input').value;
            const ubication = row.querySelectorAll('.edit-input')[1].value;

            const formData = new FormData();
            formData.append('update', '1');
            formData.append('num', id);
            formData.append('name', name);
            formData.append('ubication', ubication);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            }).then(() => window.location.reload());
        }

        function deleteDiningRoom(id) {
            if (confirm('Are you sure you want to delete this dining room?')) {
                const formData = new FormData();
                formData.append('delete', id);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                }).then(() => window.location.reload());
            }
        }

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>