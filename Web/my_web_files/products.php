<?php
require "db.php";
session_start();

$categories = getAllCategories();
$selectedCategory = isset($_GET["category"]) ? $_GET["category"] : null;
$products = $selectedCategory ? getProductsByCategory($selectedCategory) : [];
$customerId = isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : null;

$message = "";
$messageType = "";

if (isset($_POST["add_to_cart"]) && $customerId) {
    $productId = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    
    if (addToCart($customerId, $productId, $quantity)) {
        $message = "Product added to cart!";
        $messageType = "success";
    } else {
        $message = "Failed to add product to cart";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Products</title>
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
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .nav-links a:hover {
            background-color: rgba(255,255,255,0.2);
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .categories {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .categories h2 {
            margin-top: 0;
        }
        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .category-btn:hover {
            background-color: #45a049;
        }
        .category-btn.active {
            background-color: #2196F3;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .product-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #ddd;
        }
        .product-info {
            padding: 15px;
        }
        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .product-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            min-height: 40px;
        }
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 20px;
            font-weight: bold;
            color: #4CAF50;
        }
        .product-stock {
            font-size: 14px;
            color: #666;
        }
        .product-stock.low {
            color: #f44336;
        }
        .add-to-cart-form {
            display: flex;
            gap: 10px;
        }
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-add {
            flex: 1;
            padding: 8px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-add:hover {
            background-color: #45a049;
        }
        .btn-add:disabled {
            background-color: #ccc;
            cursor: not-allowed;
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
        .login-prompt {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .login-prompt a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .login-prompt a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Online Bookstore</h1>
        <div class="nav-links">
            <?php if ($customerId): ?>
                <a href="cust_main.php">Dashboard</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
            <?php else: ?>
                <a href="cust_login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <h1>Browse Products</h1>
        
        <?php if (!$customerId): ?>
            <div class="login-prompt">
                <p>🔒 <a href="cust_login.php">Login</a> to add items to your cart and checkout</p>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="categories">
            <h2>Categories</h2>
            <div class="category-buttons">
                <?php foreach ($categories as $category): ?>
                    <a href="?category=<?php echo urlencode($category["Name"]); ?>" 
                       class="category-btn <?php echo $selectedCategory === $category["Name"] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category["Name"]); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if ($selectedCategory): ?>
            <div class="products-grid">
                <?php if (empty($products)): ?>
                    <p style="grid-column: 1 / -1; text-align: center; color: #666;">
                        No products available in this category.
                    </p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/<?php echo htmlspecialchars($product["Image"]); ?>" 
                                     alt="<?php echo htmlspecialchars($product["Name"]); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/280x200?text=Book'">
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product["Name"]); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product["Description"]); ?></p>
                                <div class="product-meta">
                                    <span class="product-price">$<?php echo number_format($product["Price"], 2); ?></span>
                                    <span class="product-stock <?php echo $product["Stock"] < 10 ? 'low' : ''; ?>">
                                        Stock: <?php echo $product["Stock"]; ?>
                                    </span>
                                </div>
                                <?php if ($customerId): ?>
                                    <form method="post" action="products.php?category=<?php echo urlencode($selectedCategory); ?>" class="add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product["ProductID"]; ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product["Stock"]; ?>" 
                                               class="quantity-input" <?php echo $product["Stock"] == 0 ? 'disabled' : ''; ?>>
                                        <button type="submit" name="add_to_cart" class="btn-add" 
                                                <?php echo $product["Stock"] == 0 ? 'disabled' : ''; ?>>
                                            <?php echo $product["Stock"] == 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666;">Please select a category to view products.</p>
        <?php endif; ?>
    </div>
</body>
</html>
