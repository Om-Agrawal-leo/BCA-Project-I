<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$query = "SELECT * FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$customers = array();
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customers - Admin</title>
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
                <a href="../index.html">Home</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Customer List</h1>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No customers found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td><?php echo format_date($customer['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>