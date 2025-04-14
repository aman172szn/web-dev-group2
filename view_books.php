<?php
require_once 'config/database.php';

// Fetch all books with their details
$stmt = $conn->prepare("SELECT * FROM books ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books - ezBooks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .book-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .book-card h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .price-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .mrp {
            text-decoration: line-through;
            color: #666;
        }
        .selling-price {
            color: #2e7d32;
            font-weight: bold;
            font-size: 1.1em;
        }
        .condition {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            background-color: #e3f2fd;
            color: #1565c0;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .nav-links {
            margin-bottom: 20px;
        }
        .nav-links a {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .nav-links a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="sell.php">Add New Book</a>
    </div>

    <h2>Available Books</h2>

    <div class="book-grid">
        <?php foreach($books as $book): ?>
            <div class="book-card">
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                <p><strong>Condition:</strong> <span class="condition"><?php echo htmlspecialchars($book['book_condition']); ?></span></p>
                <div class="price-info">
                    <p class="mrp">MRP: ₹<?php echo number_format($book['MRP'], 2); ?></p>
                    <p class="selling-price">Selling Price: ₹<?php echo number_format($book['price'], 2); ?></p>
                    <?php
                        $discount_percentage = round((($book['MRP'] - $book['price']) / $book['MRP']) * 100);
                        echo "<p>You save: {$discount_percentage}% off MRP</p>";
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html> 