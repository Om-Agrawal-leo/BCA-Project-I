<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    
    $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, description) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $name, $description);
    
    if (mysqli_stmt_execute($stmt)) {
        set_message("Category added successfully", "success");
    } else {
        set_message("Error adding category: " . mysqli_error($conn), "error");
    }
    redirect('categories.php');
}

// Handle category deletion
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        set_message("Category deleted successfully", "success");
    } else {
        set_message("Error deleting category: " . mysqli_error($conn), "error");
    }
    redirect('categories.php');
}

$categories = get_categories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
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
        <h1>Manage Categories</h1>

        <?php display_message(); ?>

        <!-- Add Category Form -->
        <div class="card">
            <h2>Add New Category</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="card mt-20">
            <h2>All Categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No categories found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                            <td><?php echo format_date($category['created_at']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $category['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete this category?')">Delete</a>
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