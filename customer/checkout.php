<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    set_message("Your cart is empty", "error");
    redirect('cart.php');
}

$user = get_user_data();
$total = get_cart_total();

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = clean_input($_POST['notes']);
    $user_id = get_user_id();
    
    // Insert order
    $insert_order = "INSERT INTO orders (user_id, total_amount, notes, status) 
                     VALUES ('$user_id', '$total', '$notes', 'Pending')";
    
    if (mysqli_query($conn, $insert_order)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($_SESSION['cart'] as $food_id => $quantity) {
            $food = get_food_by_id($food_id);
            $price = $food['price'];
            
            $insert_item = "INSERT INTO order_items (order_id, food_id, quantity, price) 
                           VALUES ('$order_id', '$food_id', '$quantity', '$price')";
            mysqli_query($conn, $insert_item);
        }
        
        // Clear cart from session
        unset($_SESSION['cart']);
        
        // JavaScript will clear localStorage on page redirect
        set_message("Order placed successfully! Order ID: #$order_id", "success");
        redirect('orders.php');
    } else {
        set_message("Failed to place order. Please try again.", "error");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Food Ordering System</title>
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
        .checkout-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .order-summary {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand"> Checkout</div>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="card">
            <h2>Order Summary</h2>
            <div class="order-summary">
                <p><strong>Total Amount:</strong> <?php echo format_price($total); ?></p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Special Instructions (Optional)</label>
                    <textarea name="notes" class="form-control" rows="4" 
                              placeholder="Any special requests? (e.g., no onions, extra spicy)"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    Place Order
                </button>
                <a href="cart.php" class="btn btn-info" style="width: 100%; margin-top: 10px;">
                    Back to Cart
                </a>
            </form>
        </div>
    </div>
</body>
</html>