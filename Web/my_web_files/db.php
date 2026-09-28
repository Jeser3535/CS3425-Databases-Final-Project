<?php
function connectDB() {
    // $configPath = __DIR__ . '/db.ini';
    // $config = parse_ini_file($configPath);
    $config = parse_ini_file('/local/my_web_files/vravotti/db.ini');
    try {
        $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}
// return number of rows matching the given user and passwd.
function authenticate($user, $passwd) {
    try {
        $dbh = connectDB();
        
  
        $hashed = hash('sha256', $passwd); 
        
        // TODO: add hashed password check here (COMPLETED BELOW)
        $sql = "SELECT count(*) FROM lab8_customer WHERE username = :username AND password = :password";
        $statement = $dbh->prepare($sql);
        
        $statement->bindParam(":username", $user);
        
        // TODO: bind the password parameter (COMPLETED BELOW)
        $statement->bindParam(":password", $hashed);
        
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;

        return $row[0]; // Returns 1 if match found, 0 if not
        
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}
function get_accounts($user) {
    try {
        $dbh = connectDB(); // Connect to the database
        
        $sql = "SELECT account_no, balance FROM lab8_accounts WHERE username = :username";
        $statement = $dbh->prepare($sql);
        
        // Bind the username parameter and execute
        $statement->bindParam(":username", $user);
        $statement->execute();
        
        // fetchAll() returns an array of all rows found
        $result = $statement->fetchAll();
        
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function transfer($from, $to, $amount, $user) {
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();

        // 1. Check if source account exists and belongs to the user
        $statement = $dbh->prepare("SELECT balance FROM lab8_accounts WHERE account_no = :from AND username = :user");
        $statement->bindParam(":from", $from);
        $statement->bindParam(":user", $user);
        $statement->execute();
        $row = $statement->fetch();

        if ($row) {
            $currentBalance = $row[0];
            if ($currentBalance < $amount) {
                $dbh->rollBack();
                return "Error: Not enough balance in $from. Current balance: $$currentBalance";
            }
        } else {
            $dbh->rollBack();
            return "Error: Account $from does not exist or does not belong to you.";
        }

        // 2. Withdraw from source
        $stmtWithdraw = $dbh->prepare("UPDATE lab8_accounts SET balance = balance - :amount WHERE account_no = :from");
        $stmtWithdraw->bindParam(":amount", $amount);
        $stmtWithdraw->bindParam(":from", $from);
        $stmtWithdraw->execute();

        if ($stmtWithdraw->rowCount() != 1) {
            $dbh->rollBack();
            return "Error: Withdrawal failed.";
        }

        //  Deposit into destination
        $stmtDeposit = $dbh->prepare("UPDATE lab8_accounts SET balance = balance + :amount WHERE account_no = :to");
        $stmtDeposit->bindParam(":amount", $amount);
        $stmtDeposit->bindParam(":to", $to);
        $stmtDeposit->execute();

        if ($stmtDeposit->rowCount() != 1) {
            $dbh->rollBack();
            return "Error: Destination account $to does not exist.";
        }

        // If we made it here, commit the changes
        $dbh->commit();
        return "Success: $$amount has been transferred from $from to $to.";

    } catch (Exception $e) {
        if ($dbh) $dbh->rollBack();
        return "Transaction Failed: " . $e->getMessage();
    }
}

// Customer Authentication
function customerAuthenticate($user, $passwd) {
    try {
        $dbh = connectDB();
        $hashed = hash('sha256', $passwd);
        $sql = "SELECT CustomerID, FirstName, LastName, Email FROM Customers WHERE Username = :username AND PasswordHash = :password";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":username", $user);
        $statement->bindParam(":password", $hashed);
        $statement->execute();
        $result = $statement->fetch();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

// Employee Authentication
function employeeAuthenticate($user, $passwd) {
    try {
        $dbh = connectDB();
        $hashed = hash('sha256', $passwd);
        $sql = "SELECT EmployeeID, Username, Email, MustUpdatePassword FROM Employees WHERE Username = :username AND PasswordHash = :password";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":username", $user);
        $statement->bindParam(":password", $hashed);
        $statement->execute();
        $result = $statement->fetch();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

// Customer Registration
function registerCustomer($username, $firstName, $lastName, $email, $password, $address) {
    try {
        $dbh = connectDB();
        $hashed = hash('sha256', $password);
        $sql = "INSERT INTO Customers (Username, FirstName, LastName, Email, PasswordHash, ShippingAddress) 
                VALUES (:username, :firstName, :lastName, :email, :password, :address)";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":username", $username);
        $statement->bindParam(":firstName", $firstName);
        $statement->bindParam(":lastName", $lastName);
        $statement->bindParam(":email", $email);
        $statement->bindParam(":password", $hashed);
        $statement->bindParam(":address", $address);
        $statement->execute();
        $customerId = $dbh->lastInsertId();
        
        // Create cart for the customer
        $sql = "INSERT INTO Carts (CustomerID) VALUES (:customerId)";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":customerId", $customerId);
        $statement->execute();
        
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

// Change Password
function changeCustomerPassword($customerId, $newPassword) {
    try {
        $dbh = connectDB();
        $hashed = hash('sha256', $newPassword);
        $sql = "UPDATE Customers SET PasswordHash = :password WHERE CustomerID = :customerId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":password", $hashed);
        $statement->bindParam(":customerId", $customerId);
        $statement->execute();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

function changeEmployeePassword($employeeId, $newPassword) {
    try {
        $dbh = connectDB();
        $hashed = hash('sha256', $newPassword);
        $sql = "UPDATE Employees SET PasswordHash = :password, MustUpdatePassword = FALSE WHERE EmployeeID = :employeeId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":password", $hashed);
        $statement->bindParam(":employeeId", $employeeId);
        $statement->execute();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

// Product Functions
function getAllCategories() {
    try {
        $dbh = connectDB();
        $sql = "SELECT Name, Description FROM Categories";
        $statement = $dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function getProductsByCategory($categoryName) {
    try {
        $dbh = connectDB();
        $sql = "SELECT ProductID, Name, Description, Price, Stock, Image FROM Products 
                WHERE CategoryName = :categoryName AND Status = 'Active'";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":categoryName", $categoryName);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function getProductById($productId) {
    try {
        $dbh = connectDB();
        $sql = "SELECT ProductID, Name, CategoryName, Description, Price, Stock, Image FROM Products WHERE ProductID = :productId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        $result = $statement->fetch();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

// Cart Functions
function getCartItems($customerId) {
    try {
        $dbh = connectDB();
        $sql = "SELECT ci.CartID, ci.ProductID, ci.Quantity, p.Name, p.Price, p.Stock 
                FROM CartItems ci 
                JOIN Carts c ON ci.CartID = c.CartID 
                JOIN Products p ON ci.ProductID = p.ProductID 
                WHERE c.CustomerID = :customerId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":customerId", $customerId);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function addToCart($customerId, $productId, $quantity) {
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();
        
        // Get cart ID
        $sql = "SELECT CartID FROM Carts WHERE CustomerID = :customerId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":customerId", $customerId);
        $statement->execute();
        $cartId = $statement->fetchColumn();
        
        // Check if product already in cart
        $sql = "SELECT Quantity FROM CartItems WHERE CartID = :cartId AND ProductID = :productId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":cartId", $cartId);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        $existing = $statement->fetch();
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['Quantity'] + $quantity;
            $sql = "UPDATE CartItems SET Quantity = :quantity WHERE CartID = :cartId AND ProductID = :productId";
            $statement = $dbh->prepare($sql);
            $statement->bindParam(":quantity", $newQuantity);
            $statement->bindParam(":cartId", $cartId);
            $statement->bindParam(":productId", $productId);
            $statement->execute();
        } else {
            // Insert new item
            $sql = "INSERT INTO CartItems (CartID, ProductID, Quantity) VALUES (:cartId, :productId, :quantity)";
            $statement = $dbh->prepare($sql);
            $statement->bindParam(":cartId", $cartId);
            $statement->bindParam(":productId", $productId);
            $statement->bindParam(":quantity", $quantity);
            $statement->execute();
        }
        
        $dbh->commit();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        if ($dbh) $dbh->rollBack();
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

function updateCartItem($customerId, $productId, $quantity) {
    try {
        $dbh = connectDB();
        $sql = "UPDATE CartItems ci JOIN Carts c ON ci.CartID = c.CartID 
                SET ci.Quantity = :quantity WHERE c.CustomerID = :customerId AND ci.ProductID = :productId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":quantity", $quantity);
        $statement->bindParam(":customerId", $customerId);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

function removeCartItem($customerId, $productId) {
    try {
        $dbh = connectDB();
        $sql = "DELETE ci FROM CartItems ci JOIN Carts c ON ci.CartID = c.CartID 
                WHERE c.CustomerID = :customerId AND ci.ProductID = :productId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":customerId", $customerId);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

// Order Functions
function getCustomerOrders($customerId) {
    try {
        $dbh = connectDB();
        $sql = "SELECT OrderID, OrderDate, Status, TotalDollars FROM Orders WHERE CustomerID = :customerId ORDER BY OrderDate DESC";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":customerId", $customerId);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function getOrderDetails($orderId) {
    try {
        $dbh = connectDB();
        $sql = "SELECT oi.ProductID, p.Name, oi.Quantity, oi.PriceAtOrder FROM OrderItems oi 
                JOIN Products p ON oi.ProductID = p.ProductID WHERE oi.OrderID = :orderId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":orderId", $orderId);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function checkoutCart($customerId) {
    try {
        $dbh = connectDB();
        $sql = "CALL checkout(:customerId, @generated_order_id, @out_of_stock_product_id)";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":customerId", $customerId);
        $statement->execute();
        
        $sql = "SELECT @generated_order_id AS order_id, @out_of_stock_product_id AS out_of_stock_id";
        $statement = $dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetch();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

// Employee Functions
function getAllProducts() {
    try {
        $dbh = connectDB();
        $sql = "SELECT ProductID, Name, CategoryName, Price, Stock, Status FROM Products ORDER BY ProductID";
        $statement = $dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function updateProductStock($productId, $newStock, $employeeId) {
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();
        
        $sql = "UPDATE Products SET Stock = :stock WHERE ProductID = :productId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":stock", $newStock);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        
        // Log the change
        $sql = "INSERT INTO ProductHistory (ProductID, Action, WhoID, Details) VALUES (:productId, 'UPDATE', :employeeId, CONCAT('Stock updated to ', :stock))";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":productId", $productId);
        $statement->bindParam(":employeeId", $employeeId);
        $statement->bindParam(":stock", $newStock);
        $statement->execute();
        
        $dbh->commit();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        if ($dbh) $dbh->rollBack();
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

function updateProductPrice($productId, $newPrice, $employeeId) {
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();
        
        $sql = "UPDATE Products SET Price = :price WHERE ProductID = :productId";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":price", $newPrice);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        
        // Log the change
        $sql = "INSERT INTO ProductHistory (ProductID, Action, WhoID, Details) VALUES (:productId, 'UPDATE', :employeeId, CONCAT('Price updated to ', :price))";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":productId", $productId);
        $statement->bindParam(":employeeId", $employeeId);
        $statement->bindParam(":price", $newPrice);
        $statement->execute();
        
        $dbh->commit();
        $dbh = null;
        return true;
    } catch (PDOException $e) {
        if ($dbh) $dbh->rollBack();
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}

function getStockHistory($productId) {
    try {
        $dbh = connectDB();
        $sql = "SELECT ph.HistoryID, ph.Timestamp, ph.Details, e.Username 
                FROM ProductHistory ph 
                LEFT JOIN Employees e ON ph.WhoID = e.EmployeeID 
                WHERE ph.ProductID = :productId AND ph.Details LIKE '%Stock%' 
                ORDER BY ph.Timestamp DESC";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function getPriceHistory($productId) {
    try {
        $dbh = connectDB();
        $sql = "SELECT ph.HistoryID, ph.Timestamp, ph.Details, e.Username,
                SUBSTRING_INDEX(SUBSTRING_INDEX(ph.Details, ' ', -1), ' ', 1) AS Price
                FROM ProductHistory ph 
                LEFT JOIN Employees e ON ph.WhoID = e.EmployeeID 
                WHERE ph.ProductID = :productId AND ph.Details LIKE '%Price%' 
                ORDER BY ph.Timestamp DESC";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":productId", $productId);
        $statement->execute();
        $result = $statement->fetchAll();
        $dbh = null;
        return $result;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function insertProduct($name, $category, $description, $price, $threshold, $stock, $image, $employeeId) {
    try {
        $dbh = connectDB();
        $dbh->beginTransaction();
        
        $sql = "INSERT INTO Products (Name, CategoryName, Description, Price, Threshold, Stock, Image, Status) 
                VALUES (:name, :category, :description, :price, :threshold, :stock, :image, 'Active')";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":name", $name);
        $statement->bindParam(":category", $category);
        $statement->bindParam(":description", $description);
        $statement->bindParam(":price", $price);
        $statement->bindParam(":threshold", $threshold);
        $statement->bindParam(":stock", $stock);
        $statement->bindParam(":image", $image);
        $statement->execute();
        
        $productId = $dbh->lastInsertId();
        
        // Log the change
        $sql = "INSERT INTO ProductHistory (ProductID, Action, WhoID, Details) VALUES (:productId, 'INSERT', :employeeId, CONCAT('New product added. Price: ', :price, ', Stock: ', :stock))";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":productId", $productId);
        $statement->bindParam(":employeeId", $employeeId);
        $statement->bindParam(":price", $price);
        $statement->bindParam(":stock", $stock);
        $statement->execute();
        
        $dbh->commit();
        $dbh = null;
        return $productId;
    } catch (PDOException $e) {
        if ($dbh) $dbh->rollBack();
        print "Error!" . $e->getMessage() . "<br/>";
        return false;
    }
}
?>