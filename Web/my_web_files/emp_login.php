<?php
require "db.php";
session_start();

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: emp_login.php");
    exit();
}

$error = "";
$success = "";
$forcePasswordChange = false;

if (isset($_POST["login"])) {
    $result = employeeAuthenticate($_POST["username"], $_POST["password"]);
    if ($result) {
        $_SESSION["employee_id"] = $result["EmployeeID"];
        $_SESSION["username"] = $result["Username"];
        $_SESSION["email"] = $result["Email"];
        $_SESSION["user_type"] = "employee";
        
        if ($result["MustUpdatePassword"]) {
            $forcePasswordChange = true;
            $_SESSION["force_password_change"] = true;
        } else {
            header("Location: emp_main.php");
            exit();
        }
    } else {
        $error = "Incorrect username or password";
    }
}

if (isset($_POST["change_password"])) {
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];
    $employeeId = $_SESSION["employee_id"];
    
    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        if (changeEmployeePassword($employeeId, $newPassword)) {
            $success = "Password changed successfully! Please login again.";
            unset($_SESSION["force_password_change"]);
            session_destroy();
        } else {
            $error = "Failed to change password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 100px auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
            font-size: 48px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #4CAF50;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: #f44336;
            margin-bottom: 15px;
            text-align: center;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            color: #4CAF50;
            margin-bottom: 15px;
            text-align: center;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .customer-link {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .customer-link a {
            color: #2196F3;
            text-decoration: none;
            font-weight: bold;
        }
        .customer-link a:hover {
            text-decoration: underline;
        }
        .password-change-notice {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <?php if ($forcePasswordChange): ?>
        <div class="login-container">
            <div class="logo">🔐</div>
            <h2>Change Your Password</h2>
            
            <div class="password-change-notice">
                <strong>⚠️ First Login Required</strong><br>
                You must change your password before continuing.
            </div>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo htmlspecialchars($success); ?></div>
                <div class="back-link">
                    <a href="emp_login.php">Return to Login</a>
                </div>
            <?php else: ?>
                <form method="post" action="emp_login.php">
                    <div class="form-group">
                        <label for="new_password">New Password (min 6 characters):</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                    
                    <button type="submit" name="change_password">Change Password</button>
                </form>
                
                <div class="customer-link">
                    <a href="cust_login.php">Customer Login</a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="login-container">
            <div class="logo">👷</div>
            <h2>Employee Login</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="post" action="emp_login.php">
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
            
            <div class="customer-link">
                <a href="cust_login.php">Customer Login</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
