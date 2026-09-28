<?php
require "db.php";
session_start();

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: cust_login.php");
    exit();
}

$error = "";

if (isset($_POST["login"])) {
    $result = customerAuthenticate($_POST["username"], $_POST["password"]);
    if ($result) {
        $_SESSION["customer_id"] = $result["CustomerID"];
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["first_name"] = $result["FirstName"];
        $_SESSION["last_name"] = $result["LastName"];
        $_SESSION["email"] = $result["Email"];
        $_SESSION["user_type"] = "customer";
        header("Location: cust_main.php");
        exit();
    } else {
        $error = "Incorrect username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 100px auto;
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
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .employee-link {
            text-align: center;
            margin-top: 10px;
        }
        .employee-link a {
            color: #2196F3;
            text-decoration: none;
        }
        .employee-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Customer Login</h2>
    
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="post" action="cust_login.php">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" name="login">Login</button>
    </form>
    
    <div class="register-link">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    
    <div class="employee-link">
        <p><a href="emp_login.php">Employee Login</a></p>
    </div>
</body>
</html>
