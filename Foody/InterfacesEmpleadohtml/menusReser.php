<?php
require_once __DIR__ . '/../php/conectbd.php';
require_once __DIR__ . '/../php/login.php';
global $conn;

session_start();

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}
$stmt = $conn->prepare("
    SELECT e.num as employee_num, e.factory, f.code as factory_code
    FROM employee e 
    JOIN factory f ON e.factory = f.code
    WHERE e.userNum = ?");

$stmt->bind_param("i", $_SESSION['user_num']);
$stmt->execute();
$result_employee = $stmt->get_result();
$employee_info = $result_employee->fetch_assoc();

if (!$employee_info) {
    die("Error: Employee information not found");
}

$stmt = $conn->prepare("
    SELECT num, name 
    FROM diningRoom 
    WHERE factory = ?
    LIMIT 1");

$stmt->bind_param("s", $employee_info['factory_code']);
$stmt->execute();
$result_dining = $stmt->get_result();
$employee_dining = $result_dining->fetch_assoc();

if (!$employee_dining) {
    die("Error: No dining room found for this factory");
}

$currentDate = date('Y-m-d');
$current_time = date('H:i:s'); 

$query = "SELECT m.*, mt.description as schedule_type, 
          mt.start_time, mt.end_time,
          GROUP_CONCAT(DISTINCT dr.name SEPARATOR ', ') as dining_rooms,
          GROUP_CONCAT(DISTINCT d.code SEPARATOR '||') as dish_codes,
          GROUP_CONCAT(DISTINCT d.name SEPARATOR '||') as dishes,
          GROUP_CONCAT(DISTINCT d.description SEPARATOR '||') as dish_descriptions,
          GROUP_CONCAT(DISTINCT d.price SEPARATOR '||') as dish_prices
          FROM menu m
          LEFT JOIN menu_type mt ON m.menu_type = mt.num
          LEFT JOIN dining_menu dm ON m.code = dm.menu
          LEFT JOIN diningRoom dr ON dm.diningRoom = dr.num
          LEFT JOIN dish d ON m.code = d.menu
          WHERE dm.diningRoom = ?
          GROUP BY m.code
          ORDER BY mt.start_time";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_dining['num']);
$stmt->execute();
$result = $stmt->get_result();

$_SESSION['employee_num'] = $employee_info['employee_num'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Menus | Foody</title>
    <link rel="stylesheet" href="/css/Employe/menuReser.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   
</head>
<body>
    <header>
        <div class="menu">
            <a href="inicioEmpleado.php" class="logo">Foody</a>
            <nav class="navbar">
                <ul>
                    <li><a href="../index.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>Available Menus</h1>
        
        <div class="error-message" id="errorMessage"></div>
        
        <form id="orderForm" action="make_reservation.php" method="POST">
        <div class="menus-grid">
    <?php while ($menu = $result->fetch_assoc()): ?>
        <div class="menu-card">
            <h3><?php echo htmlspecialchars($menu['name']); ?></h3>
            <div class="menu-info">
                <p><strong>Schedule:</strong> <?php echo htmlspecialchars($menu['schedule_type']); ?></p>
                <p><strong>Time:</strong> <?php echo htmlspecialchars($menu['start_time']); ?> - <?php echo htmlspecialchars($menu['end_time']); ?></p>
                <?php
                if ($current_time < $menu['start_time'] || $current_time > $menu['end_time']) {
                    echo '<p class="discount-notice">Outside menu hours - Discounts will be applied!</p>';
                }
                ?>
                
                <div class="dishes-section">
                    <h4>Available Dishes:</h4>
                    <?php
                    if ($menu['dishes']) {
                        $dish_codes = explode('||', $menu['dish_codes']);
                        $dishes = explode('||', $menu['dishes']);
                        $descriptions = explode('||', $menu['dish_descriptions']);
                        $prices = explode('||', $menu['dish_prices']);
                        
                        $total_items = min(count($dish_codes), count($dishes), count($descriptions), count($prices));
                        
                        for ($i = 0; $i < $total_items; $i++) {
                            echo '<div class="dish-item" data-menu-time="' . htmlspecialchars($menu['start_time']) . ',' . htmlspecialchars($menu['end_time']) . '">';
                            echo '<div class="dish-info">';
                            echo '<strong>' . htmlspecialchars($dishes[$i] ?? '') . '</strong><br>';
                            echo htmlspecialchars($descriptions[$i] ?? '') . '<br>';
                            echo '<span class="dish-price">$' . htmlspecialchars($prices[$i] ?? '0.00') . '</span>';
                            echo '</div>';
                            echo '<div class="quantity-controls">';
                            echo '<button type="button" class="quantity-btn decrease" onclick="updateQuantity(\'' . htmlspecialchars($dish_codes[$i]) . '\', -1)">-</button>';
                            echo '<input type="number" name="dishes[' . htmlspecialchars($dish_codes[$i] ?? '') . ']" ';
                            echo 'class="quantity-input" min="0" value="0" data-price="' . htmlspecialchars($prices[$i] ?? '0.00') . '" ';
                            echo 'data-name="' . htmlspecialchars($dishes[$i] ?? '') . '" readonly>';
                            echo '<button type="button" class="quantity-btn increase" onclick="updateQuantity(\'' . htmlspecialchars($dish_codes[$i]) . '\', 1)">+</button>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No dishes available</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="cart">
    <h3>Current Order</h3>
    <div class="reservation-details">
        <h3>Order Details</h3>
        <div class="form-group">
            <label for="reservation_datetime">Select Date and Time:</label>
            <input type="datetime-local" 
                   id="reservation_datetime" 
                   name="reservation_date" 
                   min="<?php echo date('Y-m-d\TH:i'); ?>" 
                   required
                   class="form-control">
            <small class="form-text text-muted">
                Note: Orders outside menu hours will receive a discount.
            </small>
        </div>
    </div>
    
    <div id="cartItems"></div>
    <div id="cartTotal"></div>
    <input type="hidden" name="dining_room" value="<?php echo htmlspecialchars($employee_dining['num']); ?>">
    <button type="submit" class="proceed-button" id="submitOrder" style="display: none;">Complete Order</button>
</div>

    <script>
    let cart = new Map();

    function updateQuantity(dishCode, change) {
        const input = document.querySelector(`input[name="dishes[${dishCode}]"]`);
        const currentValue = parseInt(input.value) || 0;
        const newValue = Math.max(0, currentValue + change);
        input.value = newValue;
        
        const dishName = input.dataset.name;
        const price = parseFloat(input.dataset.price);
        
        if (newValue > 0) {
            cart.set(dishCode, {
                name: dishName,
                quantity: newValue,
                price: price
            });
        } else {
            cart.delete(dishCode);
        }
        
        updateCart();
    }

    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const submitButton = document.getElementById('submitOrder');
        let total = 0;
        let html = '';

        for (const [dishCode, item] of cart) {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            html += `
                <div class="cart-item">
                    <span>${item.quantity}x ${item.name}</span>
                    <span>$${subtotal.toFixed(2)}</span>
                </div>
            `;
        }

        cartItems.innerHTML = html;
        cartTotal.innerHTML = cart.size > 0 ? `<h4>Total: $${total.toFixed(2)}</h4>` : '';
        submitButton.style.display = cart.size > 0 ? 'block' : 'none';
    }

    document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (cart.size === 0) {
        document.getElementById('errorMessage').textContent = 'Please select at least one dish';
        document.getElementById('errorMessage').style.display = 'block';
        return;
    }

    const formData = new FormData(this);
    const reservationDate = document.getElementById('reservation_datetime').value;
    const formattedDate = new Date(reservationDate).toISOString().slice(0, 19).replace('T', ' ');
    formData.set('reservation_date', formattedDate);
    
    fetch('../php/confirm_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = `ticket.php?order_id=${data.order_id}`;
        } else {
            throw new Error(data.message || 'Failed to create order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('errorMessage').textContent = error.message || 'Error creating order. Please try again.';
        document.getElementById('errorMessage').style.display = 'block';
    });
});
    updateCart();
    </script>
</body>
</html>