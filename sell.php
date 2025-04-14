<?php
session_start();
require_once 'config/database.php';

if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $condition = $_POST['condition'];
    $mrp = $_POST['mrp'];
    $description = $_POST['description'];
    
    // Using prepared statement to insert
    $stmt = $conn->prepare("INSERT INTO books (user_id, title, author, book_condition, description, MRP) VALUES (?, ?, ?, ?, ?, ?)");
    $user_id = 2; // Using the test user ID for now
    $stmt->bind_param("issssd", $user_id, $title, $author, $condition, $description, $mrp);
    
    if($stmt->execute()) {
        // Get the calculated price from the database
        $book_id = $conn->insert_id;
        $result = $conn->query("SELECT price FROM books WHERE id = $book_id");
        $price = $result->fetch_assoc()['price'];
        $_SESSION['success'] = "Book added successfully! Calculated price: ₹" . number_format($price, 2);
    } else {
        $_SESSION['error'] = "Error adding book: " . $conn->error;
    }
    
    header("Location: sell.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Book - ezBooks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
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
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        #pricePreview {
            margin-top: 5px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h2>Sell Your Book</h2>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div class="error">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="title">Book Title*</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="author">Author*</label>
            <input type="text" id="author" name="author" required>
        </div>

        <div class="form-group">
            <label for="condition">Condition*</label>
            <select id="condition" name="condition" required>
                <option value="Brand New">Brand New (30% off MRP)</option>
                <option value="Like New">Like New (30% off MRP)</option>
                <option value="Good">Good (35% off MRP)</option>
                <option value="Fair">Fair (40% off MRP)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="mrp">MRP (₹)*</label>
            <input type="number" id="mrp" name="mrp" step="0.01" required>
            <div id="pricePreview"></div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <button type="submit" name="submit">Add Book</button>
    </form>

    <script>
        // Live price calculation preview
        function updatePricePreview() {
            const mrp = parseFloat(document.getElementById('mrp').value) || 0;
            const condition = document.getElementById('condition').value;
            let discount = 0;
            
            switch(condition) {
                case 'Brand New':
                case 'Like New':
                    discount = 0.30;
                    break;
                case 'Good':
                    discount = 0.35;
                    break;
                case 'Fair':
                    discount = 0.40;
                    break;
            }
            
            const estimatedPrice = mrp - (mrp * discount);
            document.getElementById('pricePreview').textContent = 
                `Estimated selling price: ₹${estimatedPrice.toFixed(2)}`;
        }

        document.getElementById('mrp').addEventListener('input', updatePricePreview);
        document.getElementById('condition').addEventListener('change', updatePricePreview);
    </script>
</body>
</html> 