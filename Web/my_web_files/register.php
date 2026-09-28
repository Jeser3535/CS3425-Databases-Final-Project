<?php
require "db.php";
session_start();

$error = "";

if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $address = $_POST["address"];
    
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        if (registerCustomer($username, $firstName, $lastName, $email, $password, $address)) {
            $result = customerAuthenticate($username, $password);
            if ($result) {
                $_SESSION["customer_id"] = $result["CustomerID"];
                $_SESSION["username"] = $username;
                $_SESSION["first_name"] = $result["FirstName"];
                $_SESSION["last_name"] = $result["LastName"];
                $_SESSION["email"] = $result["Email"];
                $_SESSION["user_type"] = "customer";
                header("Location: cust_main.php");
                exit();
            }
        } else {
            $error = "Username or email already exists";
        }
    }
}

if (isset($_POST["cancel"])) {
    header("Location: cust_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"], textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        .btn-group {
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-cancel {
            background-color: #f44336;
            color: white;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .btn-cancel:hover {
            background-color: #da190b;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        .back-link {
            margin-top: 20px;
            display: block;
        }
    </style>
</head>
<body>
    <h2>Customer Registration</h2>
    
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (!$success): ?>
        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password (min 6 characters):</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="address">Shipping Address:</label>
                <textarea id="address" name="address" required></textarea>
            </div>
            
            <div class="btn-group">
                <button type="submit" name="register" class="btn-submit">Register</button>
                <button type="submit" name="cancel" class="btn-cancel">Cancel</button>
            </div>
        </form>
    <?php endif; ?>
</body>
</html>
