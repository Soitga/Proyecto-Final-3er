<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_factory'])) {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $streetAddr = $_POST['streetAddr'];
    $numAddr = $_POST['numAddr'];
    $colonyAddr = $_POST['colonyAddr'];
    $numberEmp = $_POST['numberEmp'];
    $city = $_POST['city'];
    
    try {
        mysqli_begin_transaction($conn);
        
        $updateQuery = "UPDATE factory SET 
            name = ?, 
            tel = ?, 
            email = ?, 
            streetAddr = ?, 
            numAddr = ?, 
            colonyAddr = ?, 
            numberEmp = ?, 
            city = ? 
            WHERE code = ?";
            
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sissiisss", 
            $name, 
            $tel, 
            $email, 
            $streetAddr, 
            $numAddr, 
            $colonyAddr, 
            $numberEmp, 
            $city, 
            $code
        );
        
        if ($stmt->execute()) {
            mysqli_commit($conn);
            $message = "Factory updated correctly.";
        } else {
            throw new Exception("Error when updating the factory");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_factory'])) {
    $factoryCode = $_POST['delete_factory'];
    
    try {
        mysqli_begin_transaction($conn);
        
        $deleteQuery = "DELETE FROM factory WHERE code = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("s", $factoryCode);
        
        if ($stmt->execute()) {
            mysqli_commit($conn);
            $message = "Factory successfully eliminated.";
        } else {
            throw new Exception("Error when deleting the factory");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error: " . $e->getMessage();
    }
}

$query = "SELECT f.*, c.name as city_name 
          FROM factory f 
          LEFT JOIN city c ON f.city = c.code 
          ORDER BY f.code";
$result = $conn->query($query);

$cityQuery = "SELECT code, name FROM city ORDER BY name";
$cityResult = $conn->query($cityQuery);
$cities = $cityResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Management</title>
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

    <section class="factory-list">
        <h1>Factory Management</h1>

        <?php if (isset($message)): ?>
            <div class="message">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <table border="1">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Employee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($factory = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($factory['code']); ?></td>
                        <td><?php echo htmlspecialchars($factory['name']); ?></td>
                        <td><?php echo htmlspecialchars($factory['tel']); ?></td>
                        <td><?php echo htmlspecialchars($factory['email']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($factory['streetAddr'] . ' ' . 
                                                      $factory['numAddr'] . ', ' . 
                                                      $factory['colonyAddr']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($factory['city_name']); ?></td>
                        <td><?php echo htmlspecialchars($factory['numberEmp']); ?></td>
                        <td>
                            <button onclick="toggleEditForm('<?php echo $factory['code']; ?>')" class="btn-edit">
                            Edit
                            </button>
                            <form method="POST" style="display:inline;">
                                <button type="submit" name="delete_factory" 
                                        value="<?php echo $factory['code']; ?>" 
                                        class="btn-edit btn-delete"
                                        onclick="return confirm('¿Está seguro de eliminar esta fábrica?')">
                                        Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <div id="edit-form-<?php echo $factory['code']; ?>" class="edit-form">
                                <form method="POST">
                                    <input type="hidden" name="code" value="<?php echo htmlspecialchars($factory['code']); ?>">
                                    
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <input type="text" name="name" value="<?php echo htmlspecialchars($factory['name']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Phone:</label>
                                        <input type="number" name="tel"  value="<?php echo htmlspecialchars($factory['tel']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Email:</label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($factory['email']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Street:</label>
                                        <input type="text" name="streetAddr" value="<?php echo htmlspecialchars($factory['streetAddr']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Number:</label>
                                        <input type="number" name="numAddr" value="<?php echo htmlspecialchars($factory['numAddr']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Colony:</label>
                                        <input type="text" name="colonyAddr" value="<?php echo htmlspecialchars($factory['colonyAddr']); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Número de Empleados:</label>
                                        <input type="number" name="numberEmp" value="<?php echo htmlspecialchars($factory['numberEmp']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>City:</label>
                                        <select name="city" required>
                                            <?php foreach ($cities as $city): ?>
                                                <option value="<?php echo htmlspecialchars($city['code']); ?>"
                                                    <?php echo $city['code'] === $factory['city'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($city['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" name="update_factory" class="btn-edit">
                                    Save Changes
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <div class="buttons">
        <a href="/generalManager/homeGeneralManager.php" class="btn-home">Back to Home</a>
    </div>

    <script>
        function toggleEditForm(code) {
            const form = document.getElementById(`edit-form-${code}`);
            if (form.classList.contains('active')) {
                form.classList.remove('active');
            } else {
                document.querySelectorAll('.edit-form.active').forEach(f => {
                    f.classList.remove('active');
                });
                form.classList.add('active');
            }
        }
    </script>
    
</body>
</html>