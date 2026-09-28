<?php
require "db.php";
session_start();

if (!isset($_SESSION["employee_id"])) {
    header("Location: emp_login.php");
    exit();
}

$employeeId = $_SESSION["employee_id"];
$username = $_SESSION["username"];

$message = "";
$messageType = "";
$products = getAllProducts();
$categories = getAllCategories();

$selectedProduct = null;
$stockHistory = [];
$priceHistory = [];

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: emp_login.php");
    exit();
}

if (isset($_POST["restock_product"])) {
    $productId = $_POST["product_id"];
    $newStock = $_POST["new_stock"];
    
    if (updateProductStock($productId, $newStock, $employeeId)) {
        $message = "Product stock updated successfully!";
        $messageType = "success";
        $products = getAllProducts();
    } else {
        $message = "Failed to update product stock";
        $messageType = "error";
    }
}

if (isset($_POST["change_price"])) {
    $productId = $_POST["product_id"];
    $newPrice = $_POST["new_price"];
    
    if (updateProductPrice($productId, $newPrice, $employeeId)) {
        $message = "Product price updated successfully!";
        $messageType = "success";
        $products = getAllProducts();
    } else {
        $message = "Failed to update product price";
        $messageType = "error";
    }
}

if (isset($_POST["view_history"])) {
    $productId = $_POST["product_id"];
    $selectedProduct = getProductById($productId);
    $stockHistory = getStockHistory($productId);
    $priceHistory = getPriceHistory($productId);
}

if (isset($_POST["insert_product"])) {
    $name = $_POST["product_name"];
    $category = $_POST["category"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $threshold = $_POST["threshold"];
    $stock = $_POST["stock"];
    $image = $_POST["image"];
    
    if (insertProduct($name, $category, $description, $price, $threshold, $stock, $image, $employeeId)) {
        $message = "Product inserted successfully!";
        $messageType = "success";
        $products = getAllProducts();
    } else {
        $message = "Failed to insert product";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #2196F3;
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
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
        }
        .action-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 10px 20px;
            background-color: white;
            border: 2px solid #2196F3;
            color: #2196F3;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s;
        }
        .tab-btn:hover {
            background-color: #2196F3;
            color: white;
        }
        .tab-btn.active {
            background-color: #2196F3;
            color: white;
        }
        .tab-content {
            display: none;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .tab-content.active {
            display: block;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        .products-table th {
            background-color: #2196F3;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .products-table tr:hover {
            background-color: #f5f5f5;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn-primary {
            background-color: #2196F3;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0b7dda;
        }
        .btn-success {
            background-color: #4CAF50;
            color: white;
        }
        .btn-success:hover {
            background-color: #45a049;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .message.success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .message.error {
            background-color: #f2dede;
            color: #a94442;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .history-table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .product-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .product-details h3 {
            margin-top: 0;
            color: #2196F3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👷 Employee Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <form action="emp_login.php" method="post" style="display: inline; margin-left: 20px;">
                <button type="submit" name="logout" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="action-tabs">
            <button class="tab-btn active" onclick="showTab('products')">Manage Products</button>
            <button class="tab-btn" onclick="showTab('restock')">Restock Product</button>
            <button class="tab-btn" onclick="showTab('price')">Change Price</button>
            <button class="tab-btn" onclick="showTab('history')">View History</button>
            <button class="tab-btn" onclick="showTab('insert')">Insert Product</button>
        </div>
        
        <!-- Products Tab -->
        <div id="products" class="tab-content active">
            <h2>All Products</h2>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product["ProductID"]; ?></td>
                            <td><?php echo htmlspecialchars($product["Name"]); ?></td>
                            <td><?php echo htmlspecialchars($product["CategoryName"]); ?></td>
                            <td>$<?php echo number_format($product["Price"], 2); ?></td>
                            <td><?php echo $product["Stock"]; ?></td>
                            <td><?php echo htmlspecialchars($product["Status"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Restock Tab -->
        <div id="restock" class="tab-content">
            <h2>Restock Product</h2>
            <form method="post" action="emp_main.php">
                <div class="form-group">
                    <label for="product_id">Select Product:</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">-- Select Product --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product["ProductID"]; ?>">
                                <?php echo htmlspecialchars($product["Name"] . " (ID: " . $product["ProductID"] . ") - Current Stock: " . $product["Stock"]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="new_stock">New Stock Quantity:</label>
                    <input type="number" id="new_stock" name="new_stock" min="0" required>
                </div>
                
                <button type="submit" name="restock_product" class="btn btn-success">Update Stock</button>
            </form>
        </div>
        
        <!-- Change Price Tab -->
        <div id="price" class="tab-content">
            <h2>Change Product Price</h2>
            <form method="post" action="emp_main.php">
                <div class="form-group">
                    <label for="product_id">Select Product:</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">-- Select Product --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product["ProductID"]; ?>">
                                <?php echo htmlspecialchars($product["Name"] . " (ID: " . $product["ProductID"] . ") - Current Price: $" . number_format($product["Price"], 2)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="new_price">New Price:</label>
                    <input type="number" id="new_price" name="new_price" step="0.01" min="0" required>
                </div>
                
                <button type="submit" name="change_price" class="btn btn-primary">Update Price</button>
            </form>
        </div>
        
        <!-- View History Tab -->
        <div id="history" class="tab-content">
            <h2>Product History</h2>
            <form method="post" action="emp_main.php">
                <div class="form-group">
                    <label for="product_id">Select Product:</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">-- Select Product --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product["ProductID"]; ?>">
                                <?php echo htmlspecialchars($product["Name"] . " (ID: " . $product["ProductID"] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" name="view_history" class="btn btn-primary">View History</button>
            </form>
            
            <?php if ($selectedProduct): ?>
                <div class="product-details">
                    <h3>Product: <?php echo htmlspecialchars($selectedProduct["Name"]); ?></h3>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($selectedProduct["CategoryName"]); ?></p>
                    <p><strong>Current Price:</strong> $<?php echo number_format($selectedProduct["Price"], 2); ?></p>
                    <p><strong>Current Stock:</strong> <?php echo $selectedProduct["Stock"]; ?></p>
                </div>
                
                <h3>Stock History</h3>
                <?php if (empty($stockHistory)): ?>
                    <p>No stock history available.</p>
                <?php else: ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Employee</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockHistory as $history): ?>
                                <tr>
                                    <td><?php echo date("Y-m-d H:i:s", strtotime($history["Timestamp"])); ?></td>
                                    <td><?php echo htmlspecialchars($history["Username"] ?? 'System'); ?></td>
                                    <td><?php echo htmlspecialchars($history["Details"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                
                <h3>Price History</h3>
                <?php if (empty($priceHistory)): ?>
                    <p>No price history available.</p>
                <?php else: ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Employee</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($priceHistory as $history): ?>
                                <tr>
                                    <td><?php echo date("Y-m-d H:i:s", strtotime($history["Timestamp"])); ?></td>
                                    <td><?php echo htmlspecialchars($history["Username"] ?? 'System'); ?></td>
                                    <td>$<?php echo number_format($history["Price"], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Insert Product Tab -->
        <div id="insert" class="tab-content">
            <h2>Insert New Product</h2>
            <form method="post" action="emp_main.php">
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category["Name"]); ?>">
                                <?php echo htmlspecialchars($category["Name"]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="threshold">Threshold:</label>
                    <input type="number" id="threshold" name="threshold" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="stock">Initial Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Image Filename:</label>
                    <input type="text" id="image" name="image" placeholder="e.g., book.jpg">
                </div>
                
                <button type="submit" name="insert_product" class="btn btn-success">Insert Product</button>
            </form>
        </div>
    </div>
    
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
