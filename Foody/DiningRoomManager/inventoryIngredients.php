<?php
require_once __DIR__ . '/../php/conectbd.php';
global $conn;

session_start();

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $expirationDate = !empty($_POST['expirationDate']) ? $_POST['expirationDate'] : null;
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    
    try {
        mysqli_begin_transaction($conn);
        
        $stmt = $conn->prepare("CALL registerIngred(?, ?, ?, ?)");
        $stmt->bind_param("ssid", $name, $expirationDate, $stock, $price);
        
        if ($stmt->execute()) {
            mysqli_commit($conn);
            $message = "Product added successfully.";
        } else {
            throw new Exception("Error when adding the product");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $num = $_POST['num'];
    $name = $_POST['name'];
    $expirationDate = !empty($_POST['expirationDate']) ? $_POST['expirationDate'] : null;
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    
    try {
        mysqli_begin_transaction($conn);
        
        $updateQuery = "UPDATE ingredients SET name = ?, experitionDate = ?, stock = ?, price = ? WHERE num = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssidi", $name, $expirationDate, $stock, $price, $num);
        
        if ($stmt->execute()) {
            mysqli_commit($conn);
            $message = "Product updated successfully.";
        } else {
            throw new Exception("Error when updating the product");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productNum = $_POST['delete_product'];
    
    try {
        mysqli_begin_transaction($conn);
        
        $deleteQuery = "DELETE FROM ingredients WHERE num = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $productNum);
        
        if ($stmt->execute()) {
            mysqli_commit($conn);
            $message = "Product successfully eliminated.";
        } else {
            throw new Exception("Error when deleting the product");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error: " . $e->getMessage();
    }
}

$query = "SELECT * FROM ingredients ORDER BY num DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="/css/DiningRoMCSS/inventory.css">
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

    <section class="inventory-list">
        <h1>Inventory Management</h1>

        <?php if (isset($message)): ?>
            <div class="message">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="add-form">
            <h2>Add New Product</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" maxlength="20" required>
                </div>
                
                <div class="form-group">
                    <label>Expiration Date:</label>
                    <input type="date" name="expirationDate">
                    <span class="clear-date" onclick="clearDate(this)">X</span>
                </div>

                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" required min="0">
                </div>

                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" name="price" required min="0" step="0.01">
                </div>
                
                <button type="submit" name="add_product" class="btn btn-primary">
                    Add Product
                </button>
            </form>
        </div>

        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Expiration Date</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['num']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo $product['experitionDate'] ? htmlspecialchars($product['experitionDate']) : 'Not specified'; ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <button onclick="toggleEditForm('<?php echo $product['num']; ?>')" class="btn btn-edit">
                                Edit
                            </button>
                            <form method="POST" style="display:inline;">
                                <button type="submit" name="delete_product" 
                                        value="<?php echo $product['num']; ?>" 
                                        class="btn btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div id="edit-form-<?php echo $product['num']; ?>" class="edit-form">
                                <form method="POST">
                                    <input type="hidden" name="num" value="<?php echo htmlspecialchars($product['num']); ?>">
                                    
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <input type="text" name="name" maxlength="20" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Expiration Date:</label>
                                        <input type="date" name="expirationDate" value="<?php echo $product['experitionDate'] ? htmlspecialchars($product['experitionDate']) : ''; ?>">
                                        <span class="clear-date" onclick="clearDate(this)">X</span>
                                    </div>

                                    <div class="form-group">
                                        <label>Stock:</label>
                                        <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required min="0">
                                    </div>

                                    <div class="form-group">
                                        <label>Price:</label>
                                        <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required min="0" step="0.01">
                                    </div>
                                    
                                    <button type="submit" name="update_product" class="btn btn-primary">
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
        <a href="homeDiningRoom.php" class="btn-home">Back to Home</a>
    </div>

    <script>
        function toggleEditForm(num) {
            const form = document.getElementById(`edit-form-${num}`);
            if (form.classList.contains('active')) {
                form.classList.remove('active');
            } else {
                document.querySelectorAll('.edit-form.active').forEach(f => {
                    f.classList.remove('active');
                });
                form.classList.add('active');
            }
        }

        function clearDate(element) {
            const dateInput = element.previousElementSibling;
            dateInput.value = '';
        }
    </script>
</body>
</html>