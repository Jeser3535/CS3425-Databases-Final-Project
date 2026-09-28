<?php
session_start();

if (isset($_SESSION["customer_id"])) {
    header("Location: cust_main.php");
    exit();
}

if (isset($_SESSION["employee_id"])) {
    header("Location: emp_main.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Bookstore</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            color: white;
            max-width: 800px;
            padding: 40px;
        }
        .logo {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .tagline {
            font-size: 24px;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        .btn {
            display: block;
            padding: 20px 40px;
            background-color: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .btn-customer {
            background-color: #4CAF50;
            color: white;
        }
        .btn-customer:hover {
            background-color: #45a049;
        }
        .btn-employee {
            background-color: #2196F3;
            color: white;
        }
        .btn-employee:hover {
            background-color: #0b7dda;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 60px;
        }
        .feature {
            background-color: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .feature h3 {
            margin: 10px 0 5px 0;
        }
        .feature p {
            opacity: 0.9;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📚</div>
        <h1>Welcome to Online Bookstore</h1>
        <p class="tagline">Discover, Shop, and Read Your Next Favorite Book</p>
        
        <div class="button-group">
            <a href="cust_login.php" class="btn btn-customer">🛒 Customer Login</a>
            <a href="register.php" class="btn">✏️ Register as Customer</a>
            <a href="emp_login.php" class="btn btn-employee">👷 Employee Login</a>
            <a href="products.php" class="btn">📖 Browse Products</a>
        </div>
        
        <div class="features">
            <div class="feature">
                <div class="feature-icon">📖</div>
                <h3>Browse Collection</h3>
                <p>Explore books across various categories</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🛒</div>
                <h3>Easy Shopping</h3>
                <p>Add to cart and checkout securely</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📦</div>
                <h3>Order Tracking</h3>
                <p>View your order history and status</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔒</div>
                <h3>Secure Account</h3>
                <p>Manage your profile and preferences</p>
            </div>
        </div>
    </div>
</body>
</html>
