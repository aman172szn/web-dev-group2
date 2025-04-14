<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit();
}

// Handle order status updates
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_sql = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    mysqli_query($conn, $update_sql);
}

// Fetch orders with book and buyer details
$sql = "SELECT 
    o.id as order_id,
    o.book_id,
    o.buyer_id,
    o.purchase_date,
    o.status as order_status,
    b.title as book_title,
    b.price as book_price,
    u.username as buyer_name,
    u.phone_no as buyer_phone
FROM orders o 
LEFT JOIN books b ON o.book_id = b.id 
LEFT JOIN users u ON o.buyer_id = u.id 
ORDER BY o.id ASC";

$result = mysqli_query($conn, $sql);
$orders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Galdeano&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&family=Inconsolata:wdth,wght@82,200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="home.css">
    <style>
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            color: #333;
        }
        .orders-table th, .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .orders-table th {
            background-color: #f0f0f0;
            font-weight: 600;
            color: #222;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        .orders-table tr:hover {
            background-color: #f8f9fa;
        }
        .status-pending { color: #f39c12; font-weight: 600; }
        .status-processed { color: #3498db; font-weight: 600; }
        .status-completed { color: #2ecc71; font-weight: 600; }
        .status-cancelled { color: #e74c3c; font-weight: 600; }
        .status-select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: white;
            font-size: 14px;
            cursor: pointer;
            color: #333;
        }
        .status-select:hover {
            border-color: #999;
            background-color: #f5f5f5;
        }
        .main {
            padding: 20px;
            margin: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
        }
        .order-id {
            font-weight: 600;
            color: #444;
        }
        .price {
            font-weight: 600;
            color: #2c3e50;
        }
        .orders-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><span class="ez">ez</span><span class="books">Books</span></h2>
        <div class="nav-links">
            <a href="admin_dashboard.php">Home</a>
            <a href="admin_managebook.php">Manage Books</a>
            <a href="admin_manageuser.php">Manage Users</a>
            <a href="orders.php" class="active">Orders</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main">
        <h1>Order Management</h1>
        
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Book Title</th>
                    <th>Price</th>
                    <th>Buyer</th>
                    <th>Contact</th>
                    <th>Purchase Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No orders found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td class="order-id">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['book_title'] ?? 'N/A'); ?></td>
                        <td class="price">₹<?php echo htmlspecialchars($order['book_price'] ?? '0.00'); ?></td>
                        <td><?php echo htmlspecialchars($order['buyer_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($order['buyer_phone'] ?? 'N/A'); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['purchase_date'])); ?></td>
                        <td class="status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($order['order_status'])); ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processed" <?php echo $order['order_status'] === 'processed' ? 'selected' : ''; ?>>Processed</option>
                                    <option value="completed" <?php echo $order['order_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 