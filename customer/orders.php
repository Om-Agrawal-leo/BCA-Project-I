<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_user_data();
$orders = get_user_orders(get_user_id());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <audio id="readySound" src="../assets/sounds/se.mp3" preload="auto"></audio>
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
        .navbar-menu { display: flex; gap: 20px; align-items: center; }
        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .orders-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .order-card {
            background: #fff5f8;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid #F26076;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand">🍔 My Orders</div>
            <div class="navbar-menu">
                <button id="soundToggle" onclick="toggleSound()" style="padding: 5px 10px; background: #ff5722; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;">
                    🔇 Enable Sound
                </button>
                <a href="dashboard.php">Home</a>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="orders-container">
        <?php display_message(); ?>

        <?php if (empty($orders)): ?>
            <div class="card text-center">
                <h2>No orders yet</h2>
                <p>Start ordering some delicious food!</p>
                <a href="dashboard.php" class="btn btn-primary">Browse Menu</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p><?php echo format_date($order['order_time']); ?></p>
                        </div>
                        <div>
                            <div class="order-status" style="background-color: <?php echo get_status_color($order['status']); ?>; color: white;">
                                <?php echo $order['status']; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4>Items:</h4>
                        <?php
                        $items = get_order_items($order['id']);
                        foreach ($items as $item):
                        ?>
                            <p>• <?php echo htmlspecialchars($item['food_name']); ?> x <?php echo $item['quantity']; ?> - <?php echo format_price($item['price'] * $item['quantity']); ?></p>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (!empty($order['notes'])): ?>
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                    <?php endif; ?>
                    
                    <p><strong>Total:</strong> <?php echo format_price($order['total_amount']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        // ================================
        // CUSTOMER SOUND MANAGEMENT - AUTOMATIC
        // ================================
        
        // Sound enabled state - Default to TRUE (sound ALWAYS ON by default)
        let storedSound = localStorage.getItem('customerSoundEnabled');
        let soundEnabled = storedSound === null ? true : (storedSound === 'true');
        
        // Initialize sound on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSoundButtonUI();
            
            // Unlock audio context on first user interaction
            document.body.addEventListener('click', function unlockAudio() {
                const audio = document.getElementById('readySound');
                if (audio && soundEnabled) {
                    audio.load();
                    console.log('✓ Customer audio context unlocked');
                }
                document.body.removeEventListener('click', unlockAudio);
            }, { once: true });
            
            // Start monitoring for order status changes
            startOrderStatusMonitoring();
        });
        
        function updateSoundButtonUI() {
            const btn = document.getElementById('soundToggle');
            if (soundEnabled) {
                btn.innerHTML = '🔊 Sound ON';
                btn.style.background = '#4CAF50';
            } else {
                btn.innerHTML = '🔇 Enable Sound';
                btn.style.background = '#ff5722';
            }
        }
        
        function toggleSound() {
            if (!soundEnabled) {
                // Enable sound
                soundEnabled = true;
                localStorage.setItem('customerSoundEnabled', 'true');
                updateSoundButtonUI();
                playOrderReadySound(); // Test sound
                console.log('✓ Customer sound enabled');
            } else {
                // Disable sound
                soundEnabled = false;
                localStorage.setItem('customerSoundEnabled', 'false');
                updateSoundButtonUI();
                console.log('✓ Customer sound disabled');
            }
        }
        
        // Play sound when order is READY - AUTOMATIC
        function playOrderReadySound() {
            if (soundEnabled) {
                const audio = document.getElementById('readySound');
                if (audio) {
                    try {
                        audio.currentTime = 0;
                        audio.volume = 1.0;
                        
                        const playPromise = audio.play();
                        
                        if (playPromise !== undefined) {
                            playPromise
                                .then(() => {
                                    console.log('🔔 Order ready notification sound played');
                                })
                                .catch(e => {
                                    console.warn('⚠️ Order sound play failed:', e.name);
                                    if (e.name === 'NotAllowedError') {
                                        console.log('(Autoplay blocked - will resume after user interaction)');
                                    }
                                });
                        }
                    } catch (error) {
                        console.error('Customer audio error:', error);
                    }
                }
            }
        }

        // Track order statuses for changes
        let lastStatuses = <?php echo json_encode(array_column($orders, 'status', 'id')); ?>;
        
        // Ensure we have an object with string keys
        let normalizedStatuses = {};
        for (let key in lastStatuses) {
            normalizedStatuses[String(key)] = lastStatuses[key];
        }
        lastStatuses = normalizedStatuses;
        
        // Auto-refresh every 5 seconds to check for order status updates
        function startOrderStatusMonitoring() {
            setInterval(function() {
                fetch('../api/get_customer_orders.php?t=' + Date.now())
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.orders) {
                            let statusChanged = false;
                            let shouldPlaySound = false;
                            
                            data.orders.forEach(order => {
                                const orderId = String(order.id);
                                const newStatus = order.status;
                                const oldStatus = lastStatuses[orderId];
                                
                                // Check if status changed
                                if (oldStatus && oldStatus !== newStatus) {
                                    console.log('📝 Order #' + orderId + ' status: ' + oldStatus + ' → ' + newStatus);
                                    statusChanged = true;
                                    
                                    // Play sound only when order becomes READY
                                    if (newStatus === 'Ready') {
                                        console.log('✅ Order #' + orderId + ' is READY!');
                                        shouldPlaySound = true;
                                    }
                                }
                                
                                lastStatuses[orderId] = newStatus;
                            });
                            
                            // PLAY SOUND AUTOMATICALLY when order is ready
                            if (shouldPlaySound && soundEnabled) {
                                playOrderReadySound();
                            }
                            
                            // Reload page if status changed to show updates
                            if (statusChanged) {
                                console.log('Reloading page to show updated statuses...');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            }
                        }
                    })
                    .catch(error => {
                        console.warn('⚠️ Customer order check error:', error);
                    });
            }, 5000); // Check every 5 seconds
        }
    </script>
</body>
</html>
