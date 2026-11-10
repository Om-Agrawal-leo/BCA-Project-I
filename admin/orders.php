<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$orders = get_all_orders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <audio id="readySound" src="../assets/sounds/se.mp3" preload="auto"></audio>
    <style>
        .admin-navbar {
            background: #1D546D;
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
        .navbar-menu { display: flex; gap: 15px; flex-wrap: wrap; }
        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .navbar-menu a:hover { background: rgba(255,255,255,0.2); }
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        select.form-control {
            width: auto;
            display: inline-block;
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="navbar-content">
            <div class="navbar-brand" style="font-size: 1.5em; font-weight: bold;">👨‍💼 Admin Panel</div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <button id="soundToggle" onclick="toggleSound()" style="padding: 8px 15px; background: #ff5722; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    🔇 Enable Sound
                </button>
                <div class="navbar-menu">
                    <a href="index.php">Dashboard</a>
                    <a href="categories.php">Categories</a>
                    <a href="foods.php">Food Items</a>
                    <a href="orders.php">Orders</a>
                    <a href="customers.php">Customers</a>
                    <a href="../index.html">Home</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Manage Orders</h1>

        <?php display_message(); ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No orders found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                            <td><?php echo format_price($order['total_amount']); ?></td>
                            <td>
                                <select class="form-control" onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)">
                                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Preparing" <?php echo $order['status'] == 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                    <option value="Cooking" <?php echo $order['status'] == 'Cooking' ? 'selected' : ''; ?>>Cooking</option>
                                    <option value="Ready" <?php echo $order['status'] == 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                    <option value="Served" <?php echo $order['status'] == 'Served' ? 'selected' : ''; ?>>Served</option>
                                    <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td><?php echo format_date($order['order_time']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">View</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sound enabled state - load from localStorage
        let soundEnabled = localStorage.getItem('adminSoundEnabled') === 'true';
        
        // Initialize sound button state on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSoundButtonUI();
            // Start polling for updates
            setInterval(checkForUpdates, 5000);
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
            const audio = document.getElementById('readySound');
            const btn = document.getElementById('soundToggle');
            
            if (!soundEnabled) {
                // Enable sound - play a short test sound
                audio.currentTime = 0;
                audio.play().then(() => {
                    soundEnabled = true;
                    localStorage.setItem('adminSoundEnabled', 'true');
                    updateSoundButtonUI();
                    console.log('Sound enabled');
                }).catch(e => {
                    console.warn('Sound enable failed:', e);
                    alert('Could not enable sound. Please check your browser settings.');
                });
            } else {
                // Disable sound
                soundEnabled = false;
                localStorage.setItem('adminSoundEnabled', 'false');
                updateSoundButtonUI();
                audio.pause();
                audio.currentTime = 0;
                console.log('Sound disabled');
            }
        }
        
        // Play notification sound
        function playReadySound() {
            if (soundEnabled) {
                const audio = document.getElementById('readySound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(e => {
                        console.warn('Audio play failed:', e);
                    });
                }
            }
        }

        // Track known ready orders to detect new ones
        let knownReadyOrders = <?php 
            $ready_ids = [];
            foreach($orders as $o) {
                if($o['status'] == 'Ready') {
                    $ready_ids[] = $o['id'];
                }
            }
            echo json_encode($ready_ids); 
        ?>;
        // Ensure IDs are strings
        knownReadyOrders = knownReadyOrders.map(String);

        function checkForUpdates() {
            fetch('../api/get_orders.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const currentOrders = data.data;
                        const currentReadyOrders = currentOrders
                            .filter(o => o.status === 'Ready')
                            .map(o => String(o.id));
                        
                        // Check for new ready orders
                        const newReadyOrders = currentReadyOrders.filter(id => !knownReadyOrders.includes(id));
                        
                        if (newReadyOrders.length > 0) {
                            console.log('New ready orders detected:', newReadyOrders);
                            playReadySound();
                            // Reload to update display
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            // Check if we need to reload due to other status changes (simple check: count mismatch or different IDs)
                            // For admin panel, we might want to reload if ANY status changes, but let's focus on Ready for now
                            // Or if the user manually changed status, the page reloads anyway.
                            // Let's just update our known list if it changed without us noticing (e.g. another admin changed it)
                            if (JSON.stringify(knownReadyOrders.sort()) !== JSON.stringify(currentReadyOrders.sort())) {
                                // List changed (maybe some were served), just update local list or reload
                                // location.reload(); // Optional: auto-reload on any change
                                knownReadyOrders = currentReadyOrders;
                            }
                        }
                    }
                })
                .catch(e => console.warn('Check failed:', e));
        }

        function updateOrderStatus(orderId, newStatus) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/update_order_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Status updated successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            };
            
            xhr.send('order_id=' + orderId + '&status=' + newStatus);
        }

        function viewOrderDetails(orderId) {
            alert('Order details view - Order ID: #' + orderId);
            // You can implement a modal to show order details
        }
    </script>
</body>
</html>
