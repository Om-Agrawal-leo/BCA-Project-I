<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$total_sales = get_total_sales();
$today_sales = get_today_sales();
$total_customers = count_customers();
$pending_orders = count_pending_orders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            flex-wrap: wrap;
        }
        .navbar-menu {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s, color 0.3s;
        }
      
        .navbar-menu a:hover { background: rgba(255,255,255,0.2); }
        .navbar-menu a.active { background: rgba(255,255,255,0.3); font-weight: bold; }
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 1.1em;
        }
        #card {
            padding: 30px;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="navbar-content">
            <div class="navbar-brand" style="font-size: 1.5em; font-weight: bold;">Admin Panel</div>
            <div class="navbar-menu">
                <?php
                $nav_items = [
                    ['href' => 'index.php', 'text' => 'Dashboard'],
                    ['href' => 'categories.php', 'text' => 'Categories'],
                    ['href' => 'foods.php', 'text' => 'Food Items'],
                    ['href' => 'orders.php', 'text' => 'Orders'],
                    ['href' => 'customers.php', 'text' => 'Customers', 'class' => 'customers-link'],
                    ['href' => '../index.html', 'text' => 'Home']
                ];

                $current_page = basename($_SERVER['PHP_SELF']);
                foreach ($nav_items as $item) {
                    $href = htmlspecialchars($item['href']);
                    $text = htmlspecialchars($item['text']);
                    $class = isset($item['class']) ? htmlspecialchars($item['class']) : '';
                    $active = ($current_page === basename($item['href'])) ? ' active' : '';
                    echo "<a href=\"$href\" class=\"$class$active\">$text</a>";
                }
                ?>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Dashboard Overview</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Sales</div>
                <div class="stat-value"><?php echo format_price($total_sales); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Today's Sales</div>
                <div class="stat-value"><?php echo format_price($today_sales); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value"><?php echo $total_customers; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Pending Orders</div>
                <div class="stat-value" style="color: #ff9800;"><?php echo $pending_orders; ?></div>
            </div>
        </div>

        <div class="card">
            <h2>Quick Actions</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="categories.php" class="btn btn-primary">Manage Categories</a>
                <a href="foods.php" class="btn btn-success">Manage Food Items</a>
                <a href="orders.php" class="btn btn-warning">View Orders</a>
                <a href="customers.php" class="btn btn-info">View Customers</a>
            </div>
        </div>
    </div>
</body>
</html>