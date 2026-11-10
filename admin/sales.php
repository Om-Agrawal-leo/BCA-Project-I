<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Get sales data
$total_sales = get_total_sales();
$today_sales = get_today_sales();

// Get monthly sales
$query = "SELECT 
            DATE_FORMAT(order_time, '%Y-%m') as month,
            SUM(total_amount) as total,
            COUNT(*) as order_count
          FROM orders 
          WHERE status != 'Cancelled'
          GROUP BY DATE_FORMAT(order_time, '%Y-%m')
          ORDER BY month DESC
          LIMIT 12";
$result = mysqli_query($conn, $query);

$monthly_sales = array();
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_sales[] = $row;
    }
}

// Get top selling items
$top_items_query = "SELECT 
                      f.name as food_name,
                      SUM(oi.quantity) as total_quantity,
                      SUM(oi.quantity * oi.price) as total_revenue
                    FROM order_items oi
                    LEFT JOIN foods f ON oi.food_id = f.id
                    LEFT JOIN orders o ON oi.order_id = o.id
                    WHERE o.status != 'Cancelled'
                    GROUP BY oi.food_id
                    ORDER BY total_quantity DESC
                    LIMIT 10";
$top_items_result = mysqli_query($conn, $top_items_query);

$top_items = array();
if ($top_items_result && mysqli_num_rows($top_items_result) > 0) {
    while ($row = mysqli_fetch_assoc($top_items_result)) {
        $top_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Admin</title>
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
        .sales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .sales-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .sales-value {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="navbar-content">
            <div class="navbar-brand" style="font-size: 1.5em; font-weight: bold;">👨‍💼 Admin Panel</div>
            <div class="navbar-menu">
                <a href="index.php">Dashboard</a>
                <a href="categories.php">Categories</a>
                <a href="foods.php">Food Items</a>
                <a href="orders.php">Orders</a>
                <a href="customers.php">Customers</a>
                <a href="sales.php">Sales Report</a>
                <a href="../index.html">Home</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Sales Report</h1>

        <!-- Summary Cards -->
        <div class="sales-grid">
            <div class="sales-card">
                <h3>Total Sales</h3>
                <div class="sales-value"><?php echo format_price($total_sales); ?></div>
                <p>All time revenue</p>
            </div>
            
            <div class="sales-card">
                <h3>Today's Sales</h3>
                <div class="sales-value"><?php echo format_price($today_sales); ?></div>
                <p><?php echo date('l, F j, Y'); ?></p>
            </div>
            
            <div class="sales-card">
                <h3>Total Orders</h3>
                <div class="sales-value">
                    <?php
                    $count_query = "SELECT COUNT(*) as total FROM orders WHERE status != 'Cancelled'";
                    $count_result = mysqli_query($conn, $count_query);
                    $count = mysqli_fetch_assoc($count_result);
                    echo $count['total'];
                    ?>
                </div>
                <p>Completed orders</p>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="card">
            <h2>Monthly Sales Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Orders</th>
                        <th>Total Revenue</th>
                        <th>Average Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($monthly_sales)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No sales data available</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($monthly_sales as $sale): ?>
                        <tr>
                            <td><?php echo date('F Y', strtotime($sale['month'] . '-01')); ?></td>
                            <td><?php echo $sale['order_count']; ?></td>
                            <td><?php echo format_price($sale['total']); ?></td>
                            <td><?php echo format_price($sale['total'] / $sale['order_count']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Selling Items -->
        <div class="card mt-20">
            <h2>Top Selling Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Food Item</th>
                        <th>Quantity Sold</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($top_items)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No sales data available</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $rank = 1;
                        foreach ($top_items as $item): 
                        ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                            <td><?php echo $item['total_quantity']; ?></td>
                            <td><?php echo format_price($item['total_revenue']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Export Options -->
        <div class="card mt-20">
            <h2>Export Reports</h2>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-primary" onclick="window.print()">Print Report</button>
                <button class="btn btn-success" onclick="exportSalesData()">Export to CSV</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        function exportSalesData() {
            // This would export the sales data to CSV
            alert('Export functionality - This would download a CSV file with sales data');
        }
    </script>
</body>
</html>