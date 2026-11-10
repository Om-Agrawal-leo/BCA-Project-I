<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Handle raw material stock addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = clean_input($_POST['name']);
    $supplier_id = !empty($_POST['supplier_id']) ? clean_input($_POST['supplier_id']) : 'NULL';
    $quantity = clean_input($_POST['quantity']);
    $unit = clean_input($_POST['unit']);
    $purchase_date = clean_input($_POST['purchase_date']);
    
    $query = "INSERT INTO raw_materials (name, supplier_id, quantity, unit, purchase_date) VALUES ('$name', $supplier_id, '$quantity', '$unit', '$purchase_date')";
    if (mysqli_query($conn, $query)) {
        set_message("Raw material added successfully", "success");
    } else {
        set_message("Error adding raw material: " . mysqli_error($conn), "error");
    }
    redirect('raw_materials.php');
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $query = "DELETE FROM raw_materials WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        set_message("Raw material record deleted successfully", "success");
    } else {
        set_message("Error deleting record", "error");
    }
    redirect('raw_materials.php');
}

$suppliers = get_all_suppliers(); // Assumes function exists in functions.php
$raw_materials = get_all_raw_materials(); // Assumes function exists in functions.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Raw Materials - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-navbar { background: #1D546D; color: white; padding: 15px 0; }
        .navbar-content { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-menu { display: flex; gap: 15px; flex-wrap: wrap; }
        .navbar-menu a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; }
        .navbar-menu a:hover { background: rgba(255,255,255,0.2); }
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
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
        <h1>Manage Raw Materials</h1>
        <?php display_message(); ?>

        <div class="card">
            <h2>Add Raw Material Stock</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="form-group"><label>Material Name *</label><input type="text" name="name" class="form-control" required placeholder="e.g., Cheese, Onion"></div>
                <div class="form-group"><label>Supplier</label><select name="supplier_id" class="form-control"><option value="">Select Supplier</option><?php foreach ($suppliers as $supplier): ?><option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['name']); ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>Quantity *</label><input type="number" name="quantity" class="form-control" step="0.01" required></div>
                <div class="form-group"><label>Unit *</label><input type="text" name="unit" class="form-control" required placeholder="e.g., kg, liters, pieces"></div>
                <div class="form-group"><label>Purchase Date *</label><input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                <button type="submit" class="btn btn-primary">Add Stock</button>
            </form>
        </div>

        <div class="card mt-20">
            <h2>Inventory List</h2>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Supplier</th><th>Quantity</th><th>Unit</th><th>Purchase Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($raw_materials)): ?>
                        <tr><td colspan="7" style="text-align: center;">No raw materials found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($raw_materials as $material): ?>
                        <tr>
                            <td><?php echo $material['id']; ?></td>
                            <td><?php echo htmlspecialchars($material['name']); ?></td>
                            <td><?php echo htmlspecialchars($material['supplier_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $material['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($material['unit']); ?></td>
                            <td><?php echo format_date($material['purchase_date']); ?></td>
                            <td><a href="?delete=<?php echo $material['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">Delete</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>