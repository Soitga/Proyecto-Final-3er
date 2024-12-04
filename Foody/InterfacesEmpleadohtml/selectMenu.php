<?php
session_start();

require_once __DIR__ . '/../php/conectbd.php';
require_once __DIR__ . '/../php/login.php';
checkSession();

class MenuSystem {
    private $conn;
    private $employeeDiningRoom;
    private $errorMessage;
    
    public function __construct($conn) {
        if (!$conn) {
            throw new Exception("Database connection not provided");
        }
        
        date_default_timezone_set('America/Los_Angeles');
        
        $this->conn = $conn;
        
        if (!isset($_SESSION['user_num'])) {
            throw new Exception("Session expired - please log in again");
        }
        
        if (!isset($_SESSION['is_employee']) || $_SESSION['is_employee'] !== true) {
            throw new Exception("This page is restricted to employees only");
        }
        
        if (!isset($_SESSION['employee_num'])) {
            throw new Exception("Employee information not found - please log in again");
        }

        $this->setEmployeeDiningRoom();
    }

    private function setEmployeeDiningRoom() {
        if (!isset($_SESSION['employee_num'])) {
            $this->errorMessage = "No employee number in session";
            error_log("No employee number in session");
            return;
        }

        $query = "SELECT dr.num 
                 FROM employee e
                 INNER JOIN factory f ON e.factory = f.code
                 INNER JOIN diningRoom dr ON dr.factory = f.code
                 WHERE e.num = ?";
                 
        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparing query: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $_SESSION['employee_num']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $this->employeeDiningRoom = $row['num'];
            } else {
                $this->errorMessage = "Assigned dining room not found";
            }
        } catch (Exception $e) {
            $this->errorMessage = "Error: " . $e->getMessage();
        }
    }

    public function getAvailableMenus() {
        if (!$this->employeeDiningRoom) {
            error_log("No dining room assigned");
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
                    CASE 
                        WHEN mt.start_time <= mt.end_time 
                        THEN ? BETWEEN mt.start_time AND mt.end_time
                        ELSE ? >= mt.start_time OR ? <= mt.end_time
                    END
                )
                ORDER BY mt.start_time";

        try {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparing query: " . $this->conn->error);
            }
            
            $stmt->bind_param("isss", 
                $this->employeeDiningRoom,
                $currentTime,
                $currentTime,
                $currentTime
            );
            
            $stmt->execute();
            if ($stmt->error) {
                error_log("SQL Error: " . $stmt->error);
                throw new Exception("Error executing query: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            if (!$result) {
                throw new Exception("Error getting result set: " . $this->conn->error);
            }
            
            $menus = [];
            while ($row = $result->fetch_assoc()) {
                $menus[] = $row;
            }
            
            error_log("Found " . count($menus) . " menus for time " . $currentTime);
            return $menus;
            
        } catch (Exception $e) {
            error_log("Error fetching menus: " . $e->getMessage());
            $this->errorMessage = "Error fetching menus: " . $e->getMessage();
            return [];
        }
    }

    public function debugInfo() {
        echo "<div class='debug-info'>";
        echo "<h3>Session Info:</h3>";
        echo "user_num: " . ($_SESSION['user_num'] ?? 'not set') . "<br>";
        echo "employee_num: " . ($_SESSION['employee_num'] ?? 'not set') . "<br>";
        echo "is_employee: " . ($_SESSION['is_employee'] ?? 'not set') . "<br>";
        echo "user_email: " . ($_SESSION['user_email'] ?? 'not set') . "<br>";
        
        echo "<h3>Database Info:</h3>";
        if (isset($_SESSION['employee_num']) && $this->conn) {
            $debug_query = "SELECT e.*, f.code as factory_code, dr.num as dining_room_num 
                          FROM employee e 
                          LEFT JOIN factory f ON e.factory = f.code 
                          LEFT JOIN diningRoom dr ON dr.factory = f.code 
                          WHERE e.num = ?";
            try {
                $stmt = $this->conn->prepare($debug_query);
                $stmt->bind_param("i", $_SESSION['employee_num']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    echo "Employee Database Info:<br>";
                    echo "Employee Num: " . $row['num'] . "<br>";
                    echo "Factory Code: " . $row['factory_code'] . "<br>";
                    echo "Dining Room Num: " . $row['dining_room_num'] . "<br>";
                }
            } catch (Exception $e) {
                echo "Error querying employee info: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<h3>Menu System Info:</h3>";
        echo "employeeDiningRoom: " . ($this->employeeDiningRoom ?? 'not set') . "<br>";
        echo "errorMessage: " . ($this->errorMessage ?? 'no error') . "<br>";
        echo "Current Time: " . date('H:i:s') . "<br>";
        echo "</div>";
    }

    public function hasError() {
        return !empty($this->errorMessage);
    }

    public function getError() {
        return $this->errorMessage;
    }
}

$menuSystem = null;
$availableMenus = [];
$error = null;

try {
    $menuSystem = new MenuSystem($conn);
    
    if (isset($_GET['debug'])) {
        $menuSystem->debugInfo();
    }
    
    $availableMenus = $menuSystem->getAvailableMenus();
    
} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("MenuSystem Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Menus - Foody</title>
    <link rel="stylesheet" href="/css/Employe/selectMenu.css">
</head>
<body>
<header>
    <div class="menu">
        <a href="inicioEmpleado.php" class="logo">Foody</a>
        <nav class="navbar">
            <ul>
                <li>
                    <a href="orderEmplo.php" id="view-orders" class="view-order-btn">See Order</a>
                </li>
                <li><a href="../index.php" class="log-off">Log Out</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="user-info">
            <p>Welcome, <?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'Guest' ?></p>
            <p>Current time: <?= date('h:i A') ?> PST</p>
        </div>

<main>
    <?php if ($error): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif ($menuSystem->hasError()): ?>
        <div class="error-message">
            <?= htmlspecialchars($menuSystem->getError()) ?>
        </div>
    <?php else: ?>
        <section class="menus-section">
            <h2>Available Menus</h2>
            <?php if (empty($availableMenus)): ?>
                <p>No menus available at this time.</p>
            <?php else: ?>
                <div class="menu-grid">
                    <?php foreach ($availableMenus as $menu): ?>
                        <div class="menu-card" onclick="window.location.href='categoryEmplo.php?menu=<?= htmlspecialchars($menu['menu_code']) ?>'">
                            <div class="menu-header">
                                <h3><?= htmlspecialchars($menu['menu_name']) ?></h3>
                                <span class="menu-type"><?= htmlspecialchars($menu['menu_type']) ?></span>
                            </div>
                            <p><?= htmlspecialchars($menu['menu_description']) ?></p>
                            <div class="menu-time">
                                <p>Available: <?= date('h:i A', strtotime($menu['start_time'])) ?> - 
                                   <?= date('h:i A', strtotime($menu['end_time'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<div id="notification" style="display: none;" class="notification"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('notification');

    function showNotification(message, isSuccess = true) {
        if (!notification) return;
        
        notification.textContent = message;
        notification.className = `notification ${isSuccess ? 'success' : 'error'}`;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    document.querySelectorAll('.add-order').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const dishCard = e.target.closest('.card-product');
            if (!dishCard) return;

            const dishId = dishCard.dataset.dishId;
            if (!dishId) {
                showNotification('Error: No dish ID found', false);
                return;
            }

            try {
                button.disabled = true;
                const response = await fetch('../php/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `dish_id=${dishId}`
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('Item added to cart successfully');
                } else {
                    showNotification(data.message || 'Error adding item to cart', false);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error processing request', false);
            } finally {
                button.disabled = false;
            }
        });
    });

    document.getElementById('view-orders')?.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = 'orderEmplo.php';
    });
});
</script>

</body>
</html>