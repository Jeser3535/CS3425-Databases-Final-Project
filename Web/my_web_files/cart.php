<?php
require "db.php";
session_start();

if (!isset($_SESSION["customer_id"])) {
    header("Location: cust_login.php");
    exit();
}

$customerId = $_SESSION["customer_id"];
$cartItems = getCartItems($customerId);
$message = "";
$messageType = "";

if (isset($_POST["remove_item"])) {
    $productId = $_POST["product_id"];
    if (removeCartItem($customerId, $productId)) {
        $message = "Item removed from cart";
        $messageType = "success";
        $cartItems = getCartItems($customerId);
    } else {
        $message = "Failed to remove item";
        $messageType = "error";
    }
}

if (isset($_POST["update_quantity"])) {
    $productId = $_POST["product_id"];
    $quantity = $_POST["quantity"];
    if ($quantity > 0) {
        if (updateCartItem($customerId, $productId, $quantity)) {
            $message = "Cart updated";
            $messageType = "success";
            $cartItems = getCartItems($customerId);
        } else {
            $message = "Failed to update cart";
            $messageType = "error";
        }
    }
}

if (isset($_POST["add_more"])) {
    $productId = $_POST["product_id"];
    $additionalQuantity = $_POST["additional_quantity"];
    if ($additionalQuantity > 0) {
        if (addToCart($customerId, $productId, $additionalQuantity)) {
            $message = "Quantity updated";
            $messageType = "success";
            $cartItems = getCartItems($customerId);
        } else {
            $message = "Failed to update quantity";
            $messageType = "error";
        }
    }
}

if (isset($_POST["checkout"])) {
    $result = checkoutCart($customerId);
    if ($result && $result["order_id"]) {
        $message = "Checkout successful! Your order number is: " . $result["order_id"];
        $messageType = "success";
        $cartItems = [];
    } elseif ($result && $result["out_of_stock_id"]) {
        $message = "Checkout failed. Product ID " . $result["out_of_stock_id"] . " has insufficient stock. Please remove it from your cart.";
        $messageType = "error";
    } else {
        $message = "Checkout failed. Please try again.";
        $messageType = "error";
    }
}

$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item["Price"] * $item["Quantity"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
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
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .cart-table th {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: left;
        }
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .cart-table tr:hover {
            background-color: #f5f5f5;
        }
        .cart-table tr:last-child td {
            border-bottom: none;
        }
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .product-info img {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
        .btn-danger {
            background-color: #f44336;
            color: white;
        }
        .btn-danger:hover {
            background-color: #da190b;
        }
        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #0b7dda;
        }
        .cart-summary {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-summary h2 {
            margin-top: 0;
        }
        .total {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }
        .checkout-button {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            margin-top: 10px;
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
        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .add-more-form {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .add-more-input {
            width: 50px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛒 Shopping Cart</h1>
        <div class="nav-links">
            <a href="cust_main.php">Dashboard</a>
            <a href="products.php">Browse Products</a>
            <a href="orders.php">Orders</a>
        </div>
    </div>
    
    <div class="container">
        <h1>Your Shopping Cart</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Browse our products to add items to your cart.</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Add More</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img src="images/<?php echo htmlspecialchars($item["Image"]); ?>" 
                                         alt="<?php echo htmlspecialchars($item["Name"]); ?>"
                                         onerror="this.src='https://via.placeholder.com/60x80?text=Book'">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item["Name"]); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item["Price"], 2); ?></td>
                            <td>
                                <form method="post" action="cart.php" class="quantity-controls">
                                    <input type="hidden" name="product_id" value="<?php echo $item["ProductID"]; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item["Quantity"]; ?>" 
                                           min="1" max="<?php echo $item["Stock"]; ?>" class="quantity-input">
                                    <button type="submit" name="update_quantity" class="btn btn-secondary">Update</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="cart.php" class="add-more-form">
                                    <input type="hidden" name="product_id" value="<?php echo $item["ProductID"]; ?>">
                                    <input type="number" name="additional_quantity" value="1" 
                                           min="1" max="<?php echo $item["Stock"] - $item["Quantity"]; ?>" 
                                           class="add-more-input" 
                                           <?php echo ($item["Stock"] - $item["Quantity"]) <= 0 ? 'disabled' : ''; ?>>
                                    <button type="submit" name="add_more" class="btn btn-secondary" 
                                            <?php echo ($item["Stock"] - $item["Quantity"]) <= 0 ? 'disabled' : ''; ?>>
                                        + Add
                                    </button>
                                </form>
                                <small style="color: #666;">Stock: <?php echo $item["Stock"]; ?></small>
                            </td>
                            <td>$<?php echo number_format($item["Price"] * $item["Quantity"], 2); ?></td>
                            <td>
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $item["ProductID"]; ?>">
                                    <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-summary">
                <h2>Cart Summary</h2>
                <div class="total">Total: $<?php echo number_format($cartTotal, 2); ?></div>
                <form method="post" action="cart.php">
                    <button type="submit" name="checkout" class="btn btn-primary checkout-button">Proceed to Checkout</button>
                </form>
                <p style="margin-top: 15px; color: #666; font-size: 14px;">
                    By checking out, you agree to purchase all items in your cart.
                    Insufficient stock will prevent checkout.
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
