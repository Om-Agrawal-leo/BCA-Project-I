<?php
// Include configuration and functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get user data
$user = get_user_data();

// Get all categories
$categories = get_categories();

// Get search query if exists
$search_query = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// Get selected category
$selected_category = isset($_GET['category']) ? clean_input($_GET['category']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        /* Navigation Bar */
        .navbar {
            background: #F26076;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .navbar-brand {
            font-size: 1.5em;
            font-weight: bold;
        }

        .navbar-menu {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
            position: relative;
        }

        .navbar-menu a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Search Bar */
        .search-container {
            max-width: 1200px;
            margin: 30px auto 20px;
            padding: 0 20px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-bar input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Categories */
        .categories {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .category-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .category-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 25px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }

        .category-tab:hover,
        .category-tab.active {
            background: #F26076;
            color: white;
            border-color: #F26076;
        }

        /* Food Items Grid */
        .food-grid {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .food-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .food-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #F26076 0%, #E04A60 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 4em;
            overflow: hidden !important;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .food-image img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
        }

        .food-details {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .food-name {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .food-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .food-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .food-price {
            font-size: 1.3em;
            font-weight: bold;
            color: #667eea;
        }

        .add-to-cart-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .add-to-cart-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        }

        .add-to-cart-btn.added {
            background: #4CAF50;
            animation: buttonPulse 0.6s ease;
        }

        @keyframes buttonPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes cartFloat {
            0% { opacity: 0; transform: translate(0, 0); }
            50% { opacity: 1; }
            100% { opacity: 0; transform: translate(calc(100vw - 100px), -calc(100vh - 100px)); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 5em;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-content {
                flex-direction: column;
                gap: 15px;
            }

            .navbar-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .search-bar {
                flex-direction: column;
            }

            .food-grid {
                grid-template-columns: 1fr;
            }
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
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand">Food Ordering</div>
            <div class="navbar-menu">
                <a href="dashboard.php">Home</a>
                <a href="cart.php">
                    Cart
                    <?php if (get_cart_count() > 0): ?>
                        <span class="notification-badge"><?php echo get_cart_count(); ?></span>
                    <?php endif; ?>
                </a>
                <a href="orders.php">My Orders</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <?php display_message(); ?>

    <!-- Order Status Summary -->
    <div id="orderStatusSummary" style="max-width:400px;margin:20px auto 0;background:#fff5f7;border-radius:10px;padding:18px 22px;box-shadow:0 2px 10px rgba(0,0,0,0.07);color:#333;">
        <h3 style="margin-top:0;">Order Status</h3>
        <?php $orders = get_user_orders(get_user_id()); ?>
        <?php if (!empty($orders)): ?>
            <?php $latest = $orders[0]; ?>
            <div><strong>Order #<?php echo $latest['id']; ?>:</strong> <span style="color:#F26076;font-weight:bold;"><?php echo $latest['status']; ?></span></div>
            <div><small><?php echo format_date($latest['order_time']); ?></small></div>
        <?php else: ?>
            <div>No active orders.</div>
        <?php endif; ?>
    </div>

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search for food items..." 
                   value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-primary" onclick="searchFood()">Search</button>
            <button class="btn btn-info" onclick="clearSearch()">Clear</button>
        </div>
    </div>

    <!-- Categories -->
    <div class="categories">
        <div class="category-tabs">
            <a href="dashboard.php" class="category-tab <?php echo empty($selected_category) ? 'active' : ''; ?>">
                All Items
            </a>
            <?php foreach ($categories as $category): ?>
                <a href="dashboard.php?category=<?php echo $category['id']; ?>" 
                   class="category-tab <?php echo $selected_category == $category['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Food Items Grid -->
    <div class="food-grid" id="foodGrid">
        <?php
        // Get foods based on filters
        if (!empty($selected_category)) {
            $foods = get_foods_by_category($selected_category);
        } else {
            $foods = get_all_foods();
        }

        // Apply search filter
        if (!empty($search_query)) {
            $foods = array_filter($foods, function($food) use ($search_query) {
                return stripos($food['name'], $search_query) !== false || 
                       stripos($food['description'], $search_query) !== false;
            });
        }

        if (empty($foods)):
        ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <div class="empty-state-icon">🍽️</div>
                <h3>No items found</h3>
                <p>Try adjusting your search or filters</p>
            </div>
        <?php else: ?>
            <?php foreach ($foods as $food): ?>
                <div class="food-card">
                    <div class="food-image">
                        <?php if (!empty($food['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($food['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($food['name']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f0f0f0; font-size: 2.5em; border: none;">🍔</div>
                        <?php endif; ?>
                    </div>
                    <div class="food-details">
                        <div class="food-name"><?php echo htmlspecialchars($food['name']); ?></div>
                        <div class="food-description">
                            <?php echo htmlspecialchars($food['description']); ?>
                        </div>
                        <div class="food-footer">
                            <div class="food-price"><?php echo format_price($food['price']); ?></div>
                            <button class="add-to-cart-btn" 
                                    onclick="addToCart(<?php echo $food['id']; ?>, '<?php echo htmlspecialchars($food['name']); ?>', <?php echo $food['price']; ?>)">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>

        function searchFood() {
            const searchValue = document.getElementById('searchInput').value;
            window.location.href = 'dashboard.php?search=' + encodeURIComponent(searchValue);
        }

        function clearSearch() {
            window.location.href = 'dashboard.php';
        }

        // Add to cart function
        function addToCart(foodId, foodName, foodPrice) {
            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Add to cart response:', xhr.responseText);
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Add to floating cart as well
                        addToFloatCart(foodId, foodName, foodPrice);
                    } else {
                        let errorDiv = document.getElementById('cartErrorMsg');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.id = 'cartErrorMsg';
                            errorDiv.style.background = '#f8d7da';
                            errorDiv.style.color = '#721c24';
                            errorDiv.style.padding = '10px';
                            errorDiv.style.margin = '10px 0';
                            errorDiv.style.borderRadius = '5px';
                            document.body.insertBefore(errorDiv, document.body.firstChild);
                        }
                        errorDiv.innerHTML = response.message + (response.message.toLowerCase().includes('login') ? ' <a href=\"login.php\" style=\"color:#721c24;text-decoration:underline;\">Login</a>' : '');
                        window.scrollTo({top: 0, behavior: 'smooth'});
                    }
                } else {
                    console.log('Add to cart request failed:', xhr.status, xhr.statusText);
                }
            };
            xhr.send('food_id=' + foodId);
        }

        // Floating cart functionality
        let floatCart = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Fetch cart from server instead of localStorage
            fetchServerCart();
        });

        function fetchServerCart() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '../api/get_cart.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            floatCart = response.items || [];
                        }
                    } catch (e) {
                        console.log('Cart fetch error');
                    }
                }
                updateFloatingIcon();
            };
            xhr.send();
        }

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
            
            btn.innerHTML = '🛒';
            const itemCount = floatCart.reduce((sum, item) => sum + item.quantity, 0);
            
            if (itemCount > 0) {
                badge.style.display = 'flex';
                badge.textContent = itemCount;
            } else {
                badge.style.display = 'none';
            }
        }

        function updateFloatPanelContent() {
            const body = document.getElementById('floatPanelBody');
            body.innerHTML = generateCartHTML();
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

        function addToFloatCart(id, name, price) {
            const existingItem = floatCart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                floatCart.push({ id, name, price: parseFloat(price), quantity: 1 });
            }
            
            // Don't store in localStorage - server session is source of truth
            updateFloatingIcon();
            showFloatNotification('✓ Added to cart!');
        }

        function removeFloatItem(index) {
            floatCart.splice(index, 1);
            updateFloatingIcon();
            updateFloatPanelContent();
        }

        function goToCheckout() {
            window.location.href = 'cart.php';
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