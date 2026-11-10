<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

$report_type = isset($_GET['type']) ? $_GET['type'] : 'daily';
$report_data = get_sales_report($report_type); // This function needs to be created in functions.php

$page_title = "Sales Reports";
$report_title = "Daily Sales Report";
$column_title = "Date";

switch ($report_type) {
    case 'weekly':
        $report_title = "Weekly Sales Report";
        $column_title = "Week";
        break;
    case 'monthly':
        $report_title = "Monthly Sales Report";
        $column_title = "Month";
        break;
    case 'yearly':
        $report_title = "Yearly Sales Report";
        $column_title = "Year";
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-navbar { background: #1D546D; color: white; padding: 15px 0; }
        .navbar-content { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-menu { display: flex; gap: 15px; flex-wrap: wrap; }
        .navbar-menu a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .navbar-menu a:hover { background: rgba(255,255,255,0.2); }
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .report-filters { margin-bottom: 20px; }
        .report-filters a { margin-right: 10px; }
    </style>
</head>
<body>
    <nav class="admin-navbar">
        <div class="navbar-content">
            <div class="navbar-brand" style="font-size: 1.5em; font-weight: bold;">👨‍💼 Admin Panel</div>
            <div class="navbar-menu">
                <a href="index.php">Dashboard</a>
                <a href="orders.php">Orders</a>
                <a href="categories.php">Categories</a>
                <a href="foods.php">Food Items</a>
                <a href="customers.php">Customers</a>
                <a href="../index.html">Home</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1><?php echo htmlspecialchars($report_title); ?></h1>

        <div class="card">
            <div class="report-filters">
                <strong>View Report:</strong>
                <a href="?type=daily" class="btn <?php echo $report_type == 'daily' ? 'btn-primary' : 'btn-secondary'; ?>">Daily</a>
                <a href="?type=weekly" class="btn <?php echo $report_type == 'weekly' ? 'btn-primary' : 'btn-secondary'; ?>">Weekly</a>
                <a href="?type=monthly" class="btn <?php echo $report_type == 'monthly' ? 'btn-primary' : 'btn-secondary'; ?>">Monthly</a>
                <a href="?type=yearly" class="btn <?php echo $report_type == 'yearly' ? 'btn-primary' : 'btn-secondary'; ?>">Yearly</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th><?php echo htmlspecialchars($column_title); ?></th>
                        <th>Total Orders</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['period']); ?></td>
                        <td><?php echo $row['total_orders']; ?></td>
                        <td><?php echo format_price($row['total_sales']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>