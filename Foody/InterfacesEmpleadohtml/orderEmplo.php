<?php
require_once '../php/conectbd.php';
require_once '../php/login.php';
global $conn;
session_start();

if (!isset($_SESSION['user_num'])) {
    header('Location: ../index.php');
    exit();
}

function debug_to_console($data) {
    echo "<script>console.log('Debug: " . json_encode($data) . "');</script>";
}

try {
    $stmt = $conn->prepare("
        SELECT e.num as emp_num
        FROM employee e
        WHERE e.userNum = ?
    ");
    
    $stmt->bind_param("i", $_SESSION['user_num']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Employee record not found");
    }
    
    $employee = $result->fetch_assoc();
    $employeeNum = $employee['emp_num'];
    
    debug_to_console("Employee Number: " . $employeeNum);

    $orderQuery = "
        SELECT num 
        FROM orderEmp 
        WHERE employee = ? AND status = 'PND'
        ORDER BY num DESC 
        LIMIT 1
    ";
    
    $orderStmt = $conn->prepare($orderQuery);
    $orderStmt->bind_param("i", $employeeNum);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    
    if ($orderResult->num_rows === 0) {
        debug_to_console("No pending order found");
        $cartItems = [];
        $totalAmount = 0;
    } else {
        $order = $orderResult->fetch_assoc();
        $orderNum = $order['num'];
        debug_to_console("Order Number: " . $orderNum);

        $cartQuery = "
        SELECT 
            od.dish, 
            d.name, 
            d.price, 
            od.numberDishes as amount,
            od.amount as total_amount
        FROM ord_dish od
        JOIN dish d ON od.dish = d.code
        WHERE od.orderEmp = ? 
        AND od.numberDishes > 0
    ";
    
    $cartStmt = $conn->prepare($cartQuery);
    $cartStmt->bind_param("i", $orderNum);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();

        $cartItems = [];
        $totalAmount = 0;

        while ($item = $cartResult->fetch_assoc()) {
            $itemTotal = $item['price'] * $item['amount'];
            $item['item_total'] = $itemTotal;
            $cartItems[] = $item;
            $totalAmount += $itemTotal;
        }
        
        debug_to_console("Cart Items Count: " . count($cartItems));
        debug_to_console("Total Amount: " . $totalAmount);
    }

} catch (Exception $e) {
    debug_to_console("Error: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order | Foody</title>
    <link rel="stylesheet" href="/css/Employe/orderEmplo.css">
</head>
<body>
    <div id="debug-info" style="display:none;"></div>
    
    <header>
        <div class="menu">
            <h1>Foody</h1>
            <nav>
                <a href="selectMenu.php" class="back-button">Back to Menu</a>
                <a href="../index.php">Logout</a>
            </nav>
        </div>
    </header>
    
    <main>
        <section class="cart-section">
            <h2>Your Order</h2>
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <p>There are no dishes in your order.</p>
                    <a href="selectMenu.php" class="return-btn">Back to Menu</a>
                </div>
            <?php else: ?>
                <div class="cart-container" id="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item" data-dish-id="<?php echo htmlspecialchars($item['dish']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price-info" data-base-price="<?php echo $item['price']; ?>">
                                Price: $<?php echo number_format($item['price'], 2); ?>
                            </p>
                            <div class="quantity">
                                <button class="quantity-btn decrease-btn">-</button>
                                <span class="quantity-value"><?php echo $item['amount']; ?></span>
                                <button class="quantity-btn increase-btn">+</button>
                                <button class="remove-btn">Delete</button>
                            </div>
                        </div>
                        <div class="item-total">
                            Subtotal: $<?php echo number_format($item['item_total'], 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-summary">
                    <h3>Total: $<span id="total-amount"><?php echo number_format($totalAmount, 2); ?></span></h3>
                    <button class="checkout-btn" id="order-btn">Place Order</button>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    function debugLog(message) {
        console.log('Debug:', message);
    }

    const orderBtn = document.getElementById('order-btn');
    if (orderBtn) {
        orderBtn.addEventListener('click', async function() {
            if (!confirm('Do you want to place your order?')) {
                return;
            }

            try {
                orderBtn.disabled = true;
                orderBtn.textContent = 'Processing...';

                const response = await fetch('../php/confirm_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    window.location.href = `ticket.php?ticket_id=${data.ticket_id}`;
                } else {
                    throw new Error(data.message || 'Failed to create order');
                }

            } catch (error) {
                console.error('Order Error:', error);
                alert('Error processing order: ' + error.message);
            } finally {
                orderBtn.disabled = false;
                orderBtn.textContent = 'Place Order';
            }
        });
    }

    const cartContainer = document.getElementById('cart-items');
    if (!cartContainer) {
        debugLog('Cart container not found');
        return;
    }

    cartContainer.addEventListener('click', async function(e) {
        const target = e.target;
        const cartItem = target.closest('.cart-item');
        
        if (!cartItem) return;
        
        const dishId = cartItem.dataset.dishId;
        const quantitySpan = cartItem.querySelector('.quantity-value');
        let currentQuantity = parseInt(quantitySpan.textContent);

        try {
            if (target.classList.contains('decrease-btn')) {
                if (currentQuantity > 1) {
                    const result = await updateCart(dishId, currentQuantity - 1);
                    if (result) {
                        currentQuantity--;
                        quantitySpan.textContent = currentQuantity;
                        updateItemDisplay(cartItem, currentQuantity);
                    }
                }
            } 
            else if (target.classList.contains('increase-btn')) {
                const result = await updateCart(dishId, currentQuantity + 1);
                if (result) {
                    currentQuantity++;
                    quantitySpan.textContent = currentQuantity;
                    updateItemDisplay(cartItem, currentQuantity);
                }
            }
            else if (target.classList.contains('remove-btn')) {
                if (confirm('Are you sure you want to remove this item?')) {
                    const success = await removeFromCart(dishId);
                    if (success) {
                        cartItem.remove();
                        
                        const remainingVisibleItems = cartContainer.querySelectorAll('.cart-item:not([style*="display: none"])');
                        if (remainingVisibleItems.length === 0) {
                            window.location.reload();
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error updating cart: ' + error.message);
        }
    });

    async function updateCart(dishId, quantity) {
        try {
            const response = await fetch('../php/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&dish_id=${dishId}&quantity=${quantity}`
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to update cart');
            }

            updateTotalDisplay(data.total);
            return true;
        } catch (error) {
            console.error('Update cart error:', error);
            throw error;
        }
    }

    async function removeFromCart(dishId) {
        try {
            const response = await fetch('../php/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&dish_id=${dishId}`
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to remove item');
            }

            updateTotalDisplay(data.total);
            return true;
        } catch (error) {
            console.error('Remove from cart error:', error);
            throw error;
        }
    }

    function updateItemDisplay(cartItem, newQuantity) {
        const priceElement = cartItem.querySelector('.price-info');
        const itemTotalElement = cartItem.querySelector('.item-total');
        
        if (priceElement && itemTotalElement) {
            const basePrice = parseFloat(priceElement.dataset.basePrice);
            const newTotal = (basePrice * newQuantity).toFixed(2);
            
            if (newQuantity <= 0) {
                cartItem.style.display = 'none';
                
                const remainingVisibleItems = cartContainer.querySelectorAll('.cart-item:not([style*="display: none"])');
                if (remainingVisibleItems.length === 0) {
                    window.location.reload();
                }
            } else {
                itemTotalElement.textContent = `Subtotal: $${newTotal}`;
            }
        }
    }

    function updateTotalDisplay(newTotal) {
        const totalElement = document.getElementById('total-amount');
        if (totalElement) {
            totalElement.textContent = newTotal;
        }
    }
});
    </script>
</body>
</html>