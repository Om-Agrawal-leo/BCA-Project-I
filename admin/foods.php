<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Handle food addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $category_id = clean_input($_POST['category_id']);
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $price = clean_input($_POST['price']);
    $available = isset($_POST['available']) ? 1 : 0;
    
    $stmt = mysqli_prepare($conn, "INSERT INTO foods (category_id, name, description, price, available) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issdi", $category_id, $name, $description, $price, $available);

    if (mysqli_stmt_execute($stmt)) {
        set_message("Food item added successfully", "success");
    } else {
        set_message("Error adding food item: " . mysqli_error($conn), "error");
    }
    redirect('foods.php');
}

// Handle food deletion
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM foods WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        set_message("Food item deleted successfully", "success");
    } else {
        set_message("Error deleting food item: " . mysqli_error($conn), "error");
    }
    redirect('foods.php');
}

$categories = get_categories();
$foods = get_all_foods();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food Items - Admin</title>
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
                <a href="orders.php">Orders</a>
                <a href="categories.php">Categories</a>
                <a href="foods.php">Food Items</a>
                <a href="customers.php">Customers</a>
                <a href="../index.html">Home</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Manage Food Items</h1>

        <?php display_message(); ?>

        <!-- Add Food Form -->
        <div class="card">
            <h2>Add New Food Item</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Food Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price (Rs.)*</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="available" checked> Available for order
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Food Item</button>
            </form>
        </div>

        <!-- Foods List -->
        <div class="card mt-20">
            <h2>All Food Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($foods)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No food items found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($foods as $food): ?>
                        <tr>
                            <td><?php echo $food['id']; ?></td>
                            <td><?php echo htmlspecialchars($food['name']); ?></td>
                            <td><?php echo htmlspecialchars($food['category_name']); ?></td>
                            <td><?php echo format_price($food['price']); ?></td>
                            <td>
                                <?php echo $food['available'] ? '<span style="color: green;">✓ Yes</span>' : '<span style="color: red;">✗ No</span>'; ?>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $food['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete this food item?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>