<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}
if (isset($_POST['delete_supplier'])) {
    $supplierCode = $_POST['supplier_code'];
    
    $checkQuery = "SELECT COUNT(*) as count FROM orders WHERE supplier_code = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $supplierCode);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $error = "Cannot delete supplier: There are orders associated with this supplier";
    } else {
        $deleteQuery = "DELETE FROM supplier WHERE code = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("s", $supplierCode);
        
        if ($stmt->execute()) {
            $message = "Supplier deleted successfully";
        } else {
            $error = "Error deleting supplier";
        }
    }
}

if (isset($_POST['update_supplier'])) {
    $code = $_POST['supplier_code'];
    $name = $_POST['supplier_name'];
    $tel = !empty($_POST['telephone']) ? $_POST['telephone'] : null;
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    
    $updateQuery = "UPDATE supplier SET name = ?, tel = ?, email = ? WHERE code = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("siss", $name, $tel, $email, $code);
    
    if ($stmt->execute()) {
        $message = "Supplier updated successfully";
    } else {
        $error = "Error updating supplier: " . $conn->error;
    }
}

$query = "SELECT * FROM supplier ORDER BY code";
$suppliers = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Suppliers | Foody</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/viewSupplier.css">
    <link rel="stylesheet" href="../css/static/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
</head>
<body>
<header>
        <div class="menu">
            <a href="homeDiningRoom.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Log off</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <aside class="sidebar">
        <a href="homeDiningRoom.php" class="logo">Foody</a>
        <nav class="sidebar-nav">
            <ul>
            <li><a href="homeDiningRoom.php"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="viewOrderEmplo.php"><i class="fas fa-users"></i>Employees Orders</a></li>
                <li><a href="createDish.php"><i class="fas fa-utensils"></i>Create Dish</a></li>
                <li><a href="createmenu.php"><i class="fas fa-book"></i>Create Menu</a></li>
                <li><a href="inventoryIngredients.php"><i class="fas fa-warehouse"></i>Inventory</a></li>
                <li><a href="orderSupplier.php"><i class="fas fa-truck"></i>Order Supplier</a></li>
                <li><a href="createSupplier.php"><i class="fas fa-store"></i>Register Supplier</a></li>
                <li><a href="createReport.php"><i class="fas fa-chart-bar"></i>Create Reports</a></li>
                <li><a href="reports.php"><i class="fas fa-chart-bar"></i>View Reports</a></li>
                <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i>Log off</a></li>
            </ul>
        </nav>
    </aside>
    <div class="hamburger-btn" onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <section class="create-menu-container">
        <h2>Manage Suppliers</h2>

        <?php if (isset($message)): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <table class="suppliers-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($supplier['code']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['tel'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($supplier['email'] ?? 'N/A'); ?></td>
                        <td class="action-buttons">
                            <button class="btn-edit" onclick="editSupplier('<?php echo $supplier['code']; ?>', 
                                '<?php echo htmlspecialchars($supplier['name']); ?>', 
                                '<?php echo htmlspecialchars($supplier['tel'] ?? ''); ?>', 
                                '<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>')">
                                Edit
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="supplier_code" value="<?php echo $supplier['code']; ?>">
                                <button type="submit" name="delete_supplier" class="btn-delete" 
                                        onclick="return confirm('Are you sure you want to delete this supplier?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <h3>Edit Supplier</h3>
            <form method="POST" class="edit-form">
                <input type="hidden" id="edit_supplier_code" name="supplier_code">
                
                <div class="form-group">
                    <label for="supplier_name">Name:</label>
                    <input type="text" id="edit_supplier_name" name="supplier_name" required maxlength="25">
                </div>

                <div class="form-group">
                    <label for="telephone">Phone:</label>
                    <input type="number" id="edit_telephone" name="telephone" maxlength="15">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="edit_email" name="email" maxlength="30">
                </div>

                <div class="edit-form-buttons">
                    <button type="submit" name="update_supplier" class="btn-update">Update</button>
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="buttons">
        <a href="createSupplier.php" class="btn-create">Create New Supplier</a>
        <a href="homeDiningRoom.php" class="btn-home">Back to Home</a>
    </div>

    <script>
       function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        function editSupplier(code, name, tel, email) {
            document.getElementById('edit_supplier_code').value = code;
            document.getElementById('edit_supplier_name').value = name;
            document.getElementById('edit_telephone').value = tel;
            document.getElementById('edit_email').value = email;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>