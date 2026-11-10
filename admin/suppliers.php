<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('login.php');
}

// Handle supplier addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = clean_input($_POST['name']);
    $contact_person = clean_input($_POST['contact_person']);
    $phone = clean_input($_POST['phone']);
    $email = clean_input($_POST['email']);
    $address = clean_input($_POST['address']);
    
    $query = "INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES ('$name', '$contact_person', '$phone', '$email', '$address')";
    if (mysqli_query($conn, $query)) {
        set_message("Supplier added successfully", "success");
    } else {
        set_message("Error adding supplier: " . mysqli_error($conn), "error");
    }
    redirect('suppliers.php');
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $check_query = "SELECT COUNT(*) as count FROM raw_materials WHERE supplier_id = '$id'";
    $check_result = mysqli_query($conn, $check_query);
    $in_use = mysqli_fetch_assoc($check_result)['count'] > 0;

    if ($in_use) {
        set_message("Cannot delete supplier as it is linked to raw materials.", "error");
    } else {
        $query = "DELETE FROM suppliers WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            set_message("Supplier deleted successfully", "success");
        } else {
            set_message("Error deleting supplier", "error");
        }
    }
    redirect('suppliers.php');
}

$suppliers = get_all_suppliers(); // Assumes function exists in functions.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Suppliers - Admin</title>
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
        <h1>Manage Suppliers</h1>
        <?php display_message(); ?>

        <div class="card">
            <h2>Add New Supplier</h2>
            <form method="POST" action=""><input type="hidden" name="action" value="add">
                <div class="form-group"><label>Supplier Name *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label>Contact Person</label><input type="text" name="contact_person" class="form-control"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
                <div class="form-group"><label>Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary">Add Supplier</button>
            </form>
        </div>

        <div class="card mt-20">
            <h2>All Suppliers</h2>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?php echo $supplier['id']; ?></td><td><?php echo htmlspecialchars($supplier['name']); ?></td><td><?php echo htmlspecialchars($supplier['contact_person']); ?></td><td><?php echo htmlspecialchars($supplier['phone']); ?></td><td><?php echo htmlspecialchars($supplier['email']); ?></td>
                        <td><a href="?delete=<?php echo $supplier['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure? This cannot be undone.')">Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>