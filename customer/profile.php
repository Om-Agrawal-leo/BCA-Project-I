<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_user_data();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name']);
    $phone = clean_input($_POST['phone']);
    $user_id = get_user_id();
    
    $query = "UPDATE users SET name = '$name', phone = '$phone' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['user_name'] = $name;
        set_message("Profile updated successfully", "success");
        redirect('profile.php');
    } else {
        set_message("Error updating profile", "error");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .navbar {
            background: #F26076;
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
        .navbar-menu { display: flex; gap: 20px; }
        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }
        .profile-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand" style="font-size: 1.5em; font-weight: bold;">🍔 My Profile</div>
            <div class="navbar-menu">
                <a href="dashboard.php">Home</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <?php display_message(); ?>

        <div class="card">
            <h2>Update Profile</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email (cannot be changed)</label>
                    <input type="email" class="form-control" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>