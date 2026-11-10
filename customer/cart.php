<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_user_data();

// Get cart items with details
$cart_items = array();
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $food_id => $quantity) {
        $food = get_food_by_id($food_id);
        if ($food) {
            $food['quantity'] = $quantity;
            $food['subtotal'] = $food['price'] * $quantity;
            $cart_items[] = $food;
            $total += $food['subtotal'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .navbar {
            background: #F26076;
            color: white;
            padding: 15px 0;
        }
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand { font-size: 1.5em; font-weight: bold; }
        .navbar-menu { display: flex; gap: 20px; }
        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .navbar-menu a:hover { background: rgba(255,255,255,0.2); }
        .cart-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .cart-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .quantity-controls {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: #667eea;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .cart-summary {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .summary-total {
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }

        /* Floating Icon Container */
        .floating-icon {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            cursor: pointer;
        }

        /* Icon Button */
        .float-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        /* Badge for count */
        .float-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #f44336;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
        }

        /* Popup Panel */
        .float-panel {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            max-height: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            display: none;
            overflow: hidden;
        }

        .float-panel.active {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Panel Header */
        .float-panel-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .float-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
        }

        /* Panel Body */
        .float-panel-body {
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Cart Item in Panel */
        .float-cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .float-cart-item:last-child {
            border-bottom: none;
        }

        .float-item-info {
            flex: 1;
        }

        .float-item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .float-item-price {
            color: #667eea;
            font-size: 14px;
        }

        /* Order Status Tracker */
        .float-status-tracker {
            padding: 10px 0;
        }

        .float-status-step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            position: relative;
        }

        .float-status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .float-status-step.completed .float-status-icon {
            background: #4CAF50;
        }

        .float-status-step.active .float-status-icon {
            background: #667eea;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
        }

        .float-status-text {
            flex: 1;
        }

        .float-status-label {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .float-status-time {
            font-size: 12px;
            color: #999;
        }

        /* Buttons */
        .float-btn-action {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: transform 0.2s;
        }

        .float-btn-action:hover {
            transform: translateY(-2px);
        }

        /* Empty State */
        .float-empty {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .float-empty-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .floating-icon {
                bottom: 20px;
                right: 20px;
            }
            
            .float-panel {
                width: calc(100vw - 40px);
                right: -10px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand"> Shopping Cart</div>
            <div class="navbar-menu">
                <a href="dashboard.php">Continue Shopping</a>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <?php display_message(); ?>

        <?php if (empty($cart_items)): ?>
            <div class="card text-center">
                <h2>Your cart is empty</h2>
                <p>Start adding some delicious items!</p>
                <a href="dashboard.php" class="btn btn-primary">Browse Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo format_price($item['price']); ?></td>
                            <td>
                                <div class="quantity-controls">
                                    <button class="quantity-btn" type="button" data-action="decrease" data-food-id="<?php echo $item['id']; ?>">-</button>
                                    <span id="qty-<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                                    <button class="quantity-btn" type="button" data-action="increase" data-food-id="<?php echo $item['id']; ?>">+</button>
                                </div>
                            </td>
                            <td><?php echo format_price($item['subtotal']); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm remove-btn" type="button" data-food-id="<?php echo $item['id']; ?>">Remove</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span><?php echo format_price($total); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-success" style="width: 100%; margin-top: 15px;">
                    Proceed to Checkout
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Event listeners for quantity buttons
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up event listeners');
            
            // Quantity adjustment buttons
            const quantityButtons = document.querySelectorAll('button[data-action]');
            console.log('Found ' + quantityButtons.length + ' quantity buttons');
            
            quantityButtons.forEach(function(btn, index) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const foodId = this.getAttribute('data-food-id');
                    const action = this.getAttribute('data-action');
                    const change = action === 'increase' ? 1 : -1;
                    
                    console.log('Quantity button clicked - index: ' + index + ', foodId: ' + foodId + ', action: ' + action + ', change: ' + change);
                    updateQuantity(foodId, change);
                });
            });
            
            // Remove buttons
            const removeButtons = document.querySelectorAll('button.remove-btn');
            console.log('Found ' + removeButtons.length + ' remove buttons');
            
            removeButtons.forEach(function(btn, index) {
                console.log('Setting up remove button ' + index + ' with data-food-id: ' + btn.getAttribute('data-food-id'));
                
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const foodId = this.getAttribute('data-food-id');
                    console.log('Remove button clicked - index: ' + index + ', foodId: ' + foodId);
                    
                    removeItem(foodId);
                });
            });
        });

        function updateQuantity(foodId, change) {
            console.log('updateQuantity called - foodId: ' + foodId + ', change: ' + change);
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/update_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                console.log('Update response status: ' + xhr.status);
                console.log('Update response text: ' + xhr.responseText);
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Parsed response: ', response);
                        
                        if (response.success) {
                            console.log('Quantity updated successfully, reloading...');
                            setTimeout(() => location.reload(), 500);
                        } else {
                            alert('Error updating cart: ' + (response.message || 'Unknown error'));
                            console.log('Update failed:', response);
                        }
                    } catch(e) {
                        console.log('JSON parse error: ' + e.message + ', reloading anyway');
                        setTimeout(() => location.reload(), 500);
                    }
                } else {
                    alert('Error updating cart. Status: ' + xhr.status);
                    console.log('Error status: ' + xhr.status);
                }
            };
            
            xhr.onerror = function() {
                alert('Error connecting to server');
                console.log('XHR error occurred');
            };
            
            const params = 'food_id=' + encodeURIComponent(foodId) + '&change=' + encodeURIComponent(change);
            console.log('Sending params: ' + params);
            xhr.send(params);
        }

        function removeItem(foodId) {
            console.log('removeItem called - foodId: ' + foodId);
            console.log('foodId type: ' + typeof foodId + ', value: ' + foodId);
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/remove_from_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                console.log('Remove response status: ' + xhr.status);
                console.log('Remove response text: ' + xhr.responseText);
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Parsed remove response: ', response);
                        
                        if (response.success) {
                            console.log('Item removed successfully, reloading...');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            alert('Error removing item: ' + (response.message || 'Unknown error'));
                            console.log('Remove failed:', response);
                        }
                    } catch(e) {
                        console.log('JSON parse error: ' + e.message + ', reloading anyway');
                        setTimeout(() => location.reload(), 800);
                    }
                } else {
                    alert('Error removing item. Status: ' + xhr.status);
                    console.log('Error status: ' + xhr.status);
                }
            };
            
            xhr.onerror = function() {
                alert('Error connecting to server');
                console.log('XHR error occurred');
            };
            
            const params = 'food_id=' + encodeURIComponent(foodId);
            console.log('Sending remove request with params: ' + params);
            console.log('Full URL: ../api/remove_from_cart.php with params: ' + params);
            xhr.send(params);
        }
    </script>

    <!-- Floating Cart Icon HTML -->
    <div class="floating-icon" id="floatingIcon">
        <button class="float-btn" id="floatBtn" onclick="toggleFloatPanel()">
            🛒
            <span class="float-badge" id="floatBadge" style="display: none;">0</span>
        </button>
        
        <div class="float-panel" id="floatPanel">
            <div class="float-panel-header">
                <span id="floatPanelTitle">Shopping Cart</span>
                <button class="float-close" onclick="toggleFloatPanel()">×</button>
            </div>
            <div class="float-panel-body" id="floatPanelBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Floating Cart & Order Tracker JavaScript -->
    <script>
        let floatCart = JSON.parse(localStorage.getItem('cart')) || [];
        let floatCurrentOrder = JSON.parse(localStorage.getItem('current_order')) || null;

        document.addEventListener('DOMContentLoaded', function() {
            updateFloatingIcon();
            
            if (floatCurrentOrder) {
                setInterval(refreshOrderStatus, 5000);
            }
        });

        function toggleFloatPanel() {
            const panel = document.getElementById('floatPanel');
            panel.classList.toggle('active');
            
            if (panel.classList.contains('active')) {
                updateFloatPanelContent();
            }
        }

        function updateFloatingIcon() {
            const badge = document.getElementById('floatBadge');
            const btn = document.getElementById('floatBtn');
            
            if (floatCurrentOrder) {
                btn.innerHTML = '📦';
                badge.style.display = 'flex';
                badge.textContent = '!';
                badge.style.background = '#4CAF50';
            } else {
                btn.innerHTML = '🛒';
                const itemCount = floatCart.reduce((sum, item) => sum + item.quantity, 0);
                
                if (itemCount > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = itemCount;
                    badge.style.background = '#f44336';
                } else {
                    badge.style.display = 'none';
                }
            }
        }

        function updateFloatPanelContent() {
            const title = document.getElementById('floatPanelTitle');
            const body = document.getElementById('floatPanelBody');
            
            if (floatCurrentOrder) {
                title.textContent = 'Order Tracking';
                body.innerHTML = generateOrderTrackingHTML();
            } else {
                title.textContent = 'Shopping Cart';
                body.innerHTML = generateCartHTML();
            }
        }

        function generateCartHTML() {
            if (floatCart.length === 0) {
                return `
                    <div class="float-empty">
                        <div class="float-empty-icon">🛒</div>
                        <p>Your cart is empty</p>
                    </div>
                `;
            }
            
            let html = '';
            let total = 0;
            
            floatCart.forEach((item, index) => {
                const subtotal = item.price * item.quantity;
                total += subtotal;
                
                html += `
                    <div class="float-cart-item">
                        <div class="float-item-info">
                            <div class="float-item-name">${item.name}</div>
                            <div class="float-item-price">
                                Rs. ${item.price.toFixed(2)} × ${item.quantity} = Rs. ${subtotal.toFixed(2)}
                            </div>
                        </div>
                        <button onclick="removeFloatItem(${index})" style="background: none; border: none; color: #f44336; cursor: pointer; font-size: 20px;">
                            ×
                        </button>
                    </div>
                `;
            });
            
            html += `
                <div style="padding-top: 15px; border-top: 2px solid #eee; margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; margin-bottom: 15px;">
                        <span>Total:</span>
                        <span style="color: #667eea;">Rs. ${total.toFixed(2)}</span>
                    </div>
                    <button class="float-btn-action" onclick="goToCheckout()">
                        Proceed to Checkout
                    </button>
                </div>
            `;
            
            return html;
        }

        function generateOrderTrackingHTML() {
            if (!floatCurrentOrder) return '';
            
            const statuses = [
                { key: 'Pending', label: 'Order Placed', icon: '📝' },
                { key: 'Received', label: 'Order Received', icon: '✅' },
                { key: 'Preparing', label: 'Preparing Food', icon: '👨‍🍳' },
                { key: 'Ready', label: 'Ready for Pickup', icon: '🎉' },
                { key: 'Served', label: 'Delivered', icon: '🍽️' }
            ];
            
            let html = `
                <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">
                        Order #${floatCurrentOrder.order_id}
                    </div>
                    <div style="color: #666; font-size: 14px;">
                        Total: Rs. ${parseFloat(floatCurrentOrder.total_amount).toFixed(2)}
                    </div>
                </div>
                
                <div class="float-status-tracker">
            `;
            
            const currentIndex = statuses.findIndex(s => s.key === floatCurrentOrder.status);
            
            statuses.forEach((status, index) => {
                let stepClass = '';
                if (index < currentIndex) stepClass = 'completed';
                if (index === currentIndex) stepClass = 'active';
                
                html += `
                    <div class="float-status-step ${stepClass}">
                        <div class="float-status-icon">
                            ${index < currentIndex ? '✓' : status.icon}
                        </div>
                        <div class="float-status-text">
                            <div class="float-status-label">${status.label}</div>
                            ${index === currentIndex ? '<div class="float-status-time">In Progress...</div>' : ''}
                            ${index < currentIndex ? '<div class="float-status-time">Completed</div>' : ''}
                        </div>
                    </div>
                `;
            });
            
            html += `
                </div>
                <button class="float-btn-action" onclick="clearOrderTracking()" style="background: #6c757d;">
                    Clear Tracking
                </button>
            `;
            
            return html;
        }

        function addToFloatCart(id, name, price) {
            const existingItem = floatCart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                floatCart.push({ id, name, price, quantity: 1 });
            }
            
            localStorage.setItem('cart', JSON.stringify(floatCart));
            updateFloatingIcon();
            showFloatNotification('Added to cart!');
        }

        function removeFloatItem(index) {
            const item = floatCart[index];
            const foodId = item.id;
            
            console.log('Removing float item at index: ' + index + ', foodId: ' + foodId);
            
            // Call server API to remove from session cart
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/remove_from_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                console.log('Float remove response status: ' + xhr.status);
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Float remove response: ', response);
                        
                        if (response.success) {
                            // Remove from local float cart
                            floatCart.splice(index, 1);
                            localStorage.setItem('cart', JSON.stringify(floatCart));
                            updateFloatingIcon();
                            updateFloatPanelContent();
                            showFloatNotification('Item removed!');
                        } else {
                            alert('Error removing item: ' + (response.message || 'Unknown error'));
                        }
                    } catch(e) {
                        console.log('JSON parse error: ' + e.message);
                    }
                }
            };
            
            xhr.onerror = function() {
                alert('Error connecting to server');
            };
            
            const params = 'food_id=' + encodeURIComponent(foodId);
            xhr.send(params);
        }

        function goToCheckout() {
            console.log('Navigating to checkout...');
            window.location.href = 'checkout.php';
        }

        function startOrderTracking(orderId, totalAmount, status = 'Pending') {
            floatCurrentOrder = {
                order_id: orderId,
                total_amount: totalAmount,
                status: status
            };
            
            localStorage.setItem('current_order', JSON.stringify(floatCurrentOrder));
            localStorage.removeItem('cart');
            floatCart = [];
            
            updateFloatingIcon();
            
            document.getElementById('floatPanel').classList.add('active');
            updateFloatPanelContent();
        }

        async function refreshOrderStatus() {
            if (!floatCurrentOrder) return;
            
            try {
                const response = await fetch(`../api/get_order_details.php?order_id=${floatCurrentOrder.order_id}`);
                const data = await response.json();
                
                if (data.success && data.order) {
                    floatCurrentOrder.status = data.order.status;
                    localStorage.setItem('current_order', JSON.stringify(floatCurrentOrder));
                    
                    if (document.getElementById('floatPanel').classList.contains('active')) {
                        updateFloatPanelContent();
                    }
                    
                    if (data.order.status === 'Served') {
                        setTimeout(() => {
                            clearOrderTracking();
                        }, 10000);
                    }
                }
            } catch (error) {
                console.error('Error refreshing order status:', error);
            }
        }

        function clearOrderTracking() {
            floatCurrentOrder = null;
            localStorage.removeItem('current_order');
            updateFloatingIcon();
            document.getElementById('floatPanel').classList.remove('active');
        }

        function showFloatNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                bottom: 110px;
                right: 30px;
                background: #4CAF50;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                z-index: 9998;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 2000);
        }
    </script>
</body>
</html>