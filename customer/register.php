<?php
// Include configuration
require_once '../includes/config.php';

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = clean_input($_POST['password']);
    $confirm_password = clean_input($_POST['confirm_password']);
    
    // Validation
    $errors = array();
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Email already registered";
        }
    }
    
    // If no errors, insert user
    if (empty($errors)) {
        // Store password as plain text (as per requirements - not recommended in production)
        $insert_query = "INSERT INTO users (name, email, phone, password) 
                        VALUES ('$name', '$email', '$phone', '$password')";
        
        if (mysqli_query($conn, $insert_query)) {
            set_message("Registration successful! Please login.", "success");
            redirect('login.php');
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - Food Ordering System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h2 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 0.9em;
        }

        .error-list {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        .error-list ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-list li {
            color: #721c24;
            font-size: 14px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .back-home {
            text-align: center;
            margin-top: 15px;
        }

        .back-home a {
            color: #999;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2>🍔 Create Account</h2>
            <p>Register as a customer</p>
        </div>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="error-list">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small style="color: #999;">Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Register
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>

        <div class="back-home">
            <a href="../index.html">← Back to Home</a>
        </div>
    </div>

    <script>
        // Real-time validation and error display
        const form = document.getElementById('registerForm');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        // Create error display
        const errorList = document.createElement('div');
        errorList.className = 'error-list';
        errorList.style.display = 'none';
        form.parentNode.insertBefore(errorList, form);

        function showErrors(errors) {
            if (errors.length > 0) {
                errorList.innerHTML = '<ul>' + errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                errorList.style.display = 'block';
            } else {
                errorList.innerHTML = '';
                errorList.style.display = 'none';
            }
        }

        function validate() {
            const errors = [];
            if (nameInput.value.trim() === '') errors.push('Name is required');
            if (emailInput.value.trim() === '') {
                errors.push('Email is required');
            } else {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value.trim())) errors.push('Invalid email format');
            }
            if (phoneInput.value.trim() === '') {
                errors.push('Phone number is required');
            } else if (phoneInput.value.trim().length < 10) {
                errors.push('Please enter a valid phone number');
            }
            if (passwordInput.value.length < 6) errors.push('Password must be at least 6 characters');
            if (passwordInput.value !== confirmPasswordInput.value) errors.push('Passwords do not match');
            showErrors(errors);
            return errors.length === 0;
        }

        // Real-time validation
        [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput].forEach(input => {
            input.addEventListener('input', validate);
        });

        // Prevent submit if errors
        form.addEventListener('submit', function(e) {
            if (!validate()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>