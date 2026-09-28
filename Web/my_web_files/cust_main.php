<?php
require "db.php";
session_start();

if (!isset($_SESSION["customer_id"])) {
    header("Location: cust_login.php");
    exit();
}

$customerId = $_SESSION["customer_id"];
$firstName = $_SESSION["first_name"];
$lastName = $_SESSION["last_name"];

$error = "";
$success = "";

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: cust_login.php");
    exit();
}

if (isset($_POST["change_password"])) {
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];
    
    if ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match";
    } elseif (strlen($newPassword) < 6) {
        $error = "New password must be at least 6 characters";
    } else {
        $result = customerAuthenticate($_SESSION["username"], $currentPassword);
        if ($result) {
            if (changeCustomerPassword($customerId, $newPassword)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Failed to change password";
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            text-align: right;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .action-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .action-card h3 {
            margin-top: 0;
            color: #333;
        }
        .action-card p {
            color: #666;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #2196F3;
        }
        .btn-secondary:hover {
            background-color: #0b7dda;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        .password-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Online Bookstore</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($firstName) . " " . htmlspecialchars($lastName); ?>!</span>
            <form action="cust_login.php" method="post" style="display: inline; margin-left: 20px;">
                <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <h2>Welcome to Our Online Bookstore</h2>
            <p>Browse our collection, add items to your cart, and manage your orders.</p>
        </div>
        
        <div class="actions-grid">
            <div class="action-card">
                <h3>📖 Browse Products</h3>
                <p>Explore our book collection by category</p>
                <a href="products.php" class="btn">Browse Now</a>
            </div>
            
            <div class="action-card">
                <h3>🛒 Shopping Cart</h3>
                <p>View and manage your cart items</p>
                <a href="cart.php" class="btn">View Cart</a>
            </div>
            
            <div class="action-card">
                <h3>📦 My Orders</h3>
                <p>View your order history and details</p>
                <a href="orders.php" class="btn">View Orders</a>
            </div>
        </div>
        
        <div class="password-section">
            <h3>Change Password</h3>
            
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            
            <form method="post" action="cust_main.php">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password (min 6 characters):</label>
                    <input type="password" id="new_password" name="new_password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" name="change_password" class="btn-submit">Change Password</button>
            </form>
        </div>
    </div>
</body>
</html>
