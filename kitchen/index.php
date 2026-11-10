<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Get pending and cooking orders
$query = "SELECT o.*, u.name as customer_name, u.phone 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.status IN ('Pending', 'Received', 'Cooking', 'Preparing', 'Ready') 
          ORDER BY o.order_time ASC";
$result = mysqli_query($conn, $query);
$orders = array();
$order_ids = array();
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
    $order_ids[] = $row['id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Dashboard - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/kitchen.css">
    <audio id="orderSound" src="../assets/sounds/se.mp3" preload="auto"></audio>
</head>
<body>
    <nav class="kitchen-navbar">
        <div class="navbar-content">
            <div style="font-size: 1.5em; font-weight: bold;">Kitchen Dashboard</div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <button id="soundToggle" onclick="toggleSound()" style="padding: 8px 15px; background: #ff5722; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    🔇 Enable Sound
                </button>
                <a href="../index.html" style="color: white; text-decoration: none; padding: 8px 15px; background: rgba(255,255,255,0.2); border-radius: 5px;">Home</a>
            </div>
        </div>
    </nav>

    <div class="kitchen-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Active Orders</h1>
            <div>
                <span class="loading" id="refreshIndicator" style="display: none;"></span>
                <span id="lastUpdate"></span>
            </div>
        </div>

        <div class="order-grid" id="ordersGrid">
            <?php if (empty($orders)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <h2>No active orders</h2>
                    <p>All orders have been completed!</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="kitchen-order-card">
                        <div class="order-number">Order #<?php echo $order['id']; ?></div>
                        <div class="order-time"><?php echo format_date($order['order_time']); ?></div>
                        
                        <div style="margin: 15px 0;">
                            <strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?><br>
                            <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?>
                        </div>
                        
                        <div style="margin: 15px 0;">
                            <strong>Items:</strong>
                            <?php
                            $items = get_order_items($order['id']);
                            foreach ($items as $item):
                            ?>
                                <div>• <?php echo htmlspecialchars($item['food_name']); ?> (x<?php echo $item['quantity']; ?>)</div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (!empty($order['notes'])): ?>
                            <div style="background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0;">
                                <strong>Note:</strong> <?php echo htmlspecialchars($order['notes']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <strong>Status:</strong>
                            <span class="badge" style="background-color: <?php echo get_status_color($order['status']); ?>;">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>
                        
                        <div class="status-buttons">
                            <?php if ($order['status'] == 'Pending'): ?>
                                <button class="btn btn-info btn-sm" onclick="updateStatus(<?php echo $order['id']; ?>, 'Preparing')">Start Preparing</button>
                            <?php elseif ($order['status'] == 'Preparing'): ?>
                                <button class="btn btn-success btn-sm" onclick="updateStatus(<?php echo $order['id']; ?>, 'Ready')">Mark Ready</button>
                            <?php elseif ($order['status'] == 'Ready'): ?>
                                <button class="btn btn-success btn-sm" onclick="updateStatus(<?php echo $order['id']; ?>, 'Served')">Mark Served</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // ================================
        // KITCHEN SOUND MANAGEMENT - AUTOMATIC
        // ================================
        
        // Sound enabled state - Default to TRUE (sound ALWAYS ON by default)
        let storedSound = localStorage.getItem('kitchenSoundEnabled');
        let soundEnabled = storedSound === null ? true : (storedSound === 'true');
        
        // Initialize sound button state on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSoundButtonUI();
            
            // Unlock audio context on first user interaction (required by browsers)
            document.body.addEventListener('click', function unlockAudio() {
                const audio = document.getElementById('orderSound');
                if (audio && soundEnabled) {
                    audio.load();
                    console.log('✓ Kitchen audio context unlocked');
                }
                document.body.removeEventListener('click', unlockAudio);
            }, { once: true });
            
            // Start auto-refresh immediately
            refreshOrders();
        });
        
        // Update sound button UI based on state
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
        
        // Toggle sound on/off
        function toggleSound() {
            const audio = document.getElementById('orderSound');
            
            if (!soundEnabled) {
                // Enable sound
                soundEnabled = true;
                localStorage.setItem('kitchenSoundEnabled', 'true');
                updateSoundButtonUI();
                playNewOrderSound(); // Test sound
                console.log('✓ Kitchen sound enabled');
            } else {
                // Disable sound
                soundEnabled = false;
                localStorage.setItem('kitchenSoundEnabled', 'false');
                updateSoundButtonUI();
                console.log('✓ Kitchen sound disabled');
            }
        }
        
        // Play notification sound for NEW ORDERS - AUTOMATIC
        function playNewOrderSound() {
            if (soundEnabled) {
                const audio = document.getElementById('orderSound');
                if (audio) {
                    try {
                        audio.currentTime = 0;
                        audio.volume = 1.0;
                        
                        // Use promise API for better control
                        const playPromise = audio.play();
                        
                        if (playPromise !== undefined) {
                            playPromise
                                .then(() => {
                                    console.log('🔔 Kitchen notification sound played');
                                })
                                .catch(e => {
                                    console.warn('⚠️ Kitchen sound play failed:', e.name);
                                    if (e.name === 'NotAllowedError') {
                                        console.log('(Autoplay blocked - will resume after user interaction)');
                                    }
                                });
                        }
                    } catch (error) {
                        console.error('Kitchen audio error:', error);
                    }
                }
            }
        }

        // Auto-refresh every 5 seconds to check for new orders
        let knownOrderIds = <?php echo json_encode($order_ids); ?>;
        knownOrderIds = knownOrderIds.map(String);
        let lastUpdateTime = new Date();

        function refreshOrders() {
            const indicator = document.getElementById('refreshIndicator');
            if (indicator) {
                indicator.style.display = 'inline-block';
            }
            
            fetch('get_orders.php?t=' + Date.now())
                .then(response => response.json())
                .then(data => {
                    if (indicator) {
                        indicator.style.display = 'none';
                    }
                    
                    if (data.success) {
                        const currentOrders = data.data;
                        const currentOrderIds = currentOrders.map(o => String(o.id));
                        
                        // Check for NEW orders (not in known list)
                        const newOrders = currentOrderIds.filter(id => !knownOrderIds.includes(id));
                        
                        if (newOrders.length > 0) {
                            console.log('🆕 New order(s) detected - IDs:', newOrders);
                            knownOrderIds = currentOrderIds;
                            playNewOrderSound(); // ← AUTOMATIC SOUND
                            
                            // Reload page to show new orders
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else if (JSON.stringify(knownOrderIds.sort()) !== JSON.stringify(currentOrderIds.sort())) {
                            // Orders changed (completed/removed)
                            console.log('📋 Orders list changed, reloading...');
                            knownOrderIds = currentOrderIds;
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        } else {
                            // No changes, update timestamp
                            lastUpdateTime = new Date();
                        }
                    }
                })
                .catch(e => {
                    console.warn('⚠️ Kitchen refresh error:', e);
                    if (indicator) {
                        indicator.style.display = 'none';
                    }
                });
        }
        
        // Start auto-refresh every 5 seconds
        setInterval(refreshOrders, 5000);

        // Update last refresh time display
        function updateTime() {
            const now = new Date();
            const seconds = Math.floor((now - lastUpdateTime) / 1000);
            const updateElement = document.getElementById('lastUpdate');
            if (updateElement) {
                if (seconds < 60) {
                    updateElement.textContent = 'Updated ' + seconds + 's ago';
                } else {
                    const minutes = Math.floor(seconds / 60);
                    updateElement.textContent = 'Updated ' + minutes + 'm ago';
                }
            }
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Update order status
        function updateStatus(orderId, newStatus) {
            console.log('📝 Updating order:', orderId, '→', newStatus);
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/update_order_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            console.log('✓ Status updated');
                            location.reload();
                        } else {
                            console.log('✗ Update failed:', response.message);
                            alert('Error: ' + response.message);
                        }
                    } catch (e) {
                        console.error('Invalid JSON response');
                        alert('Error parsing response');
                    }
                } else {
                    console.log('✗ HTTP error:', xhr.status);
                    alert('HTTP error: ' + xhr.status);
                }
            };
            
            xhr.onerror = function() {
                console.log('✗ Network error');
                alert('Network error');
            };
            
            xhr.send('order_id=' + orderId + '&status=' + newStatus);
        }
    </script>
</body>
</html>
