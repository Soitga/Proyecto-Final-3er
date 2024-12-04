<?php
require_once __DIR__ . '/../php/conectbd.php';
require_once __DIR__ . '/../php/login.php';

session_start();
checkSession();

class MenuSystem {
    private $conn;
    private $employeeDiningRoom;
    private $errorMessage;

    public function __construct($conn) {
        date_default_timezone_set('America/Los_Angeles');

        $this->conn = $conn;

        if (!isset($_SESSION['user_num']) || !$_SESSION['is_employee']) {
            $this->errorMessage = "Unauthorized access";
            return;
        }

        $this->setEmployeeDiningRoom();
    }

    public function getCurrentTime() {
        return date('h:i A');
    }

    private function setEmployeeDiningRoom() {
        if (!isset($_SESSION['user_num'])) {
            $this->errorMessage = "Unauthenticated user";
            return;
        }

        $query = "SELECT dr.num 
                 FROM employee e
                 INNER JOIN factory f ON e.factory = f.code
                 INNER JOIN diningRoom dr ON dr.factory = f.code
                 WHERE e.userNum = ?";

        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparing query: " . $this->conn->error);
            }

            $stmt->bind_param("i", $_SESSION['user_num']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $this->employeeDiningRoom = $row['num'];
            } else {
                $this->errorMessage = "No assigned dining room found";
            }
        } catch (Exception $e) {
            $this->errorMessage = "Error: " . $e->getMessage();
        }
    }

    public function getAvailableMenus() {
        if (!$this->employeeDiningRoom) {
            return [];
        }

        $currentTime = date('H:i:s');

        $query = "SELECT DISTINCT
                    m.code as menu_code,
                    m.name as menu_name,
                    m.description as menu_description,
                    mt.description as menu_type,
                    mt.start_time,
                    mt.end_time
                 FROM menu m
                 INNER JOIN menu_type mt ON m.menu_type = mt.num
                 INNER JOIN dining_menu dm ON m.code = dm.menu
                 WHERE dm.diningRoom = ?
                 AND (
                    (mt.start_time <= ? AND mt.end_time >= ?)
                    OR (mt.start_time > mt.end_time AND 
                        (mt.start_time <= ? OR mt.end_time >= ?))
                 )";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issss", 
                $this->employeeDiningRoom,
                $currentTime,
                $currentTime,
                $currentTime,
                $currentTime
            );

            $stmt->execute();
            $result = $stmt->get_result();

            $menus = [];
            while ($row = $result->fetch_assoc()) {
                $menus[] = $row;
            }

            return $menus;
        } catch (Exception $e) {
            $this->errorMessage = "Error fetching menus: " . $e->getMessage();
            return [];
        }
    }

    public function getDishesForMenu($menuCode) {
        $query = "SELECT 
                    d.code, 
                    d.name, 
                    d.description, 
                    d.price, 
                    c.name AS category_name
                 FROM dish d
                 JOIN category c ON d.category = c.code
                 WHERE d.menu = ?
                 ORDER BY c.name, d.price";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $menuCode);
            $stmt->execute();
            $result = $stmt->get_result();

            $categorizedDishes = [];
            while ($dish = $result->fetch_assoc()) {
                $categorizedDishes[$dish['category_name']][] = $dish;
            }

            return $categorizedDishes;
        } catch (Exception $e) {
            $this->errorMessage = "Error fetching dishes: " . $e->getMessage();
            return [];
        }
    }

    public function hasError() {
        return !empty($this->errorMessage);
    }

    public function getError() {
        return $this->errorMessage;
    }
}

$menuSystem = new MenuSystem($conn);

$availableMenus = $menuSystem->getAvailableMenus();

$selectedDishes = [];
if (isset($_GET['menu']) && !empty($_GET['menu'])) {
    $selectedDishes = $menuSystem->getDishesForMenu($_GET['menu']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foody - Menu</title>
    <link rel="stylesheet" href="/css/Employe/selectMenu.css">
    <script src="https://kit.fontawesome.com/9c68e1ecda.js" crossorigin="anonymous"></script>
</head>
<body>
    <div id="notification" class="notification"></div>

    <header>
    <div class="menu">
        <a href="inicioEmpleado.php" class="logo">Foody</a>
        <div class="user-info">
            <p>Welcome, <?= htmlspecialchars($_SESSION['user_email']) ?></p>
            <p>Current time: <?= date('h:i A') ?> PST</p>
        </div>
        <nav class="navbar">
            <ul>
                <li>
                    <a href="#" id="view-orders" class="view-order-btn">
                        See Order
                    </a>
                </li>
                <li><a href="../index.php" class="log-off">Log Out</a></li>
            </ul>
        </nav>
    </div>
</header>

    <main>
        <?php if ($menuSystem->hasError()): ?>
            <div class="error-message">
                <?= htmlspecialchars($menuSystem->getError()) ?>
            </div>
        <?php else: ?>
            <?php if (!isset($_GET['menu'])): ?>
                <section class="menus-section">
                    <h2>Available Menus</h2>
                    <div class="menu-grid">
                        <?php foreach ($availableMenus as $menu): ?>
                            <div class="menu-card" onclick="window.location.href='?menu=<?= htmlspecialchars($menu['menu_code']) ?>'">
                                <div class="menu-header">
                                    <h3><?= htmlspecialchars($menu['menu_name']) ?></h3>
                                    <span class="menu-type"><?= htmlspecialchars($menu['menu_type']) ?></span>
                                </div>
                                <p><?= htmlspecialchars($menu['menu_description']) ?></p>
                                <div class="menu-time">
                                    <p>Available: <?= date('h:i A', strtotime($menu['start_time'])) ?> PST - 
                                                  <?= date('h:i A', strtotime($menu['end_time'])) ?> PST</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php else: ?>
                <?php if (empty($selectedDishes)): ?>
                    <div class="no-dishes">
                        <p>No dishes available for this menu.</p>
                        <a href="?">Return to menus</a>
                    </div>
                <?php else: ?>
                    <a href="?" class="back-button">← Return to menus</a>
                    <?php foreach ($selectedDishes as $categoryName => $dishes): ?>
                        <section class="category-container">
                            <h2><?= htmlspecialchars($categoryName) ?></h2>
                            <div class="container-products">
                                <?php foreach ($dishes as $dish): ?>
                                    <div class="card-product" data-dish-id="<?= htmlspecialchars($dish['code']) ?>">
                                        <div class="content-card-product">
                                            <h3><?= htmlspecialchars($dish['name']) ?></h3>
                                            <p class="description"><?= htmlspecialchars($dish['description']) ?></p>
                                            <p class="price">
                                                $<?= number_format($dish['price'], 2) ?>
                                            </p>
                                            <button class="add-order">
                                                <i class="fa-solid fa-plus"></i> Add to order
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </main>
   <script>
    document.addEventListener('DOMContentLoaded', function() {
    function showNotification(message, isSuccess = true) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${isSuccess ? 'success' : 'error'}`;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    document.querySelectorAll('.add-order').forEach(button => {
        button.addEventListener('click', function(e) {
            const dishCard = e.target.closest('.card-product');
            const dishId = dishCard.dataset.dishId;
            
            fetch('../php/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `dish_id=${dishId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Chick added to the order!');
                } else {
                    showNotification(data.message || 'Error when adding to order', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al procesar la solicitud', false);
            });
        });
    });

    document.getElementById('view-orders').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = 'orderEmplo.php';
    });
});

   </script>                                          

</body>
</html>