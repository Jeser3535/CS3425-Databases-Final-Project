<?php
require "db.php";
session_start();

if (!isset($_SESSION["customer_id"])) {
    header("Location: cust_login.php");
    exit();
}

$customerId = $_SESSION["customer_id"];
$orders = getCustomerOrders($customerId);
$selectedOrderId = isset($_GET["order_id"]) ? $_GET["order_id"] : null;
$orderDetails = $selectedOrderId ? getOrderDetails($selectedOrderId) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
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
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .order-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .order-header {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-info {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .order-number {
            font-size: 18px;
            font-weight: bold;
        }
        .order-date {
            color: rgba(255,255,255,0.9);
        }
        .order-status {
            background-color: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .order-total {
            font-size: 20px;
            font-weight: bold;
        }
        .order-details-btn {
            background-color: white;
            color: #4CAF50;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .order-details-btn:hover {
            background-color: #f0f0f0;
        }
        .order-items {
            padding: 20px;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #4CAF50;
        }
        .order-items td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .order-items tr:last-child td {
            border-bottom: none;
        }
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-orders h2 {
            color: #333;
        }
        .empty-orders p {
            font-size: 18px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 My Orders</h1>
        <div class="nav-links">
            <a href="cust_main.php">Dashboard</a>
            <a href="products.php">Browse Products</a>
            <a href="cart.php">Cart</a>
        </div>
    </div>
    
    <div class="container">
        <h1>Order History</h1>
        
        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <h2>No orders yet</h2>
                <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
                <a href="products.php" class="btn">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <div class="order-number">
                                    Order #<?php echo $order["OrderID"]; ?>
                                </div>
                                <div class="order-date">
                                    <?php echo date("F j, Y, g:i a", strtotime($order["OrderDate"])); ?>
                                </div>
                                <div class="order-status">
                                    <?php echo htmlspecialchars($order["Status"]); ?>
                                </div>
                            </div>
                            <div class="order-info">
                                <div class="order-total">
                                    $<?php echo number_format($order["TotalDollars"], 2); ?>
                                </div>
                                <a href="?order_id=<?php echo $order["OrderID"]; ?>" 
                                   class="order-details-btn">
                                    <?php echo $selectedOrderId == $order["OrderID"] ? 'Hide Details' : 'View Details'; ?>
                                </a>
                            </div>
                        </div>
                        
                        <?php if ($selectedOrderId == $order["OrderID"]): ?>
                            <div class="order-items">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderDetails as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item["Name"]); ?></td>
                                                <td>$<?php echo number_format($item["PriceAtOrder"], 2); ?></td>
                                                <td><?php echo $item["Quantity"]; ?></td>
                                                <td>$<?php echo number_format($item["PriceAtOrder"] * $item["Quantity"], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
