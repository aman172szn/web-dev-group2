<?php
    session_start();
    // Connect to the database
    require_once '../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.html");
        exit();
    }

    // Get the book_id from the URL
    if (isset($_GET['book_id'])) {
        $book_id = mysqli_real_escape_string($conn, $_GET['book_id']);
        
        // Query to fetch the details of the book
        $sql = "SELECT * FROM books WHERE id = '$book_id'";
        $result = mysqli_query($conn, $sql);
        
        // Check if the book is found
        if (mysqli_num_rows($result) > 0) {
            $book = mysqli_fetch_assoc($result);
            
            // Check if book is already sold
            if ($book['status'] === 'sold') {
                $error_message = "Sorry, this book has already been sold.";
            }
        } else {
            echo "Book not found!";
            exit;
        }
    } else {
        echo "Invalid book ID!";
        exit;
    }

    // Handle purchase
    $purchase_message = '';
    $error_message = '';
    if(isset($_POST['purchase'])) {
        // Check if book is already sold
        $check_sql = "SELECT status FROM books WHERE id = '$book_id'";
        $check_result = mysqli_query($conn, $check_sql);
        $book_status = mysqli_fetch_assoc($check_result)['status'];

        if ($book_status === 'sold') {
            $error_message = "Sorry, this book has already been sold.";
        } else {
            // Start transaction
            mysqli_begin_transaction($conn);
            try {
                // Insert order
                $buyer_id = $_SESSION['user_id'];
                $order_sql = "INSERT INTO orders (book_id, buyer_id) VALUES ('$book_id', '$buyer_id')";
                mysqli_query($conn, $order_sql);

                // Update book status
                $update_sql = "UPDATE books SET status = 'sold' WHERE id = '$book_id'";
                mysqli_query($conn, $update_sql);

                // Commit transaction
                mysqli_commit($conn);
                $purchase_message = "Thank you for your purchase! Your order will be processed and our team will reach you within 1 working day.";
                
                // Refresh book data
                $result = mysqli_query($conn, "SELECT * FROM books WHERE id = '$book_id'");
                $book = mysqli_fetch_assoc($result);
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error_message = "Error processing your purchase. Please try again.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link rel="stylesheet" href="BookDetails.css">
    <!-- icon -->
    <link rel="icon" type="image/svg+xml" href="../media/ICON.svg" />
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Galdeano&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&family=Inconsolata:wdth,wght@82,200..900&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="landingPage.css">
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .error-message {
            background-color: #f44336;
            color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .sold-badge {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="navleft">
                <a href="landingPage.php" style="text-decoration: none;">
                    <h1>ez<span>Books</span></h1>
                </a>
            </div>
            <div class="navmid">
                <!-- Add your buttons here if needed -->
            </div>
            <div class="navright">
                <div class="navSearch">
                    <form action="landingPage.php" method="GET" style="height:100%; width:100%;">
                        <input type="text" id="bookname" name="bookname" placeholder="Search a book">
                    </form>
                </div>
                <div class="navbarSocial">
                    <i class="fa-brands fa-facebook fa-2xl"></i>
                    <i class="fa-brands fa-instagram fa-2xl"></i>
                </div>
                <div class="navMidButton3">
                    <a href="landingPage.php?logout=true">
                        <button type="button">Logout</button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if(!empty($purchase_message)): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($purchase_message); ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($error_message)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <!-- Book Details -->
        <div class="BookDetails">
            <div class="detailsBox">
                <div class="bookTitle">
                    <?php echo htmlspecialchars($book['title']); ?>
                    <?php if($book['status'] === 'sold'): ?>
                        <span class="sold-badge">SOLD</span>
                    <?php endif; ?>
                </div>
                <div class="bookRemainingDetails">
                    <div class="DetailsUpperBody">
                        <div class="bookDetailsimage">
                            <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="no image">
                        </div>
                        <div class="bookDescription">
                            <p><?php echo htmlspecialchars($book['description']); ?></p>
                        </div>
                    </div>
                    <div class="DetailsLowerBody">
                        <div><h3>EDITION: <?php echo htmlspecialchars($book['edition']); ?></h3></div>
                        <div><h3>CONDITION: <?php echo htmlspecialchars($book['book_condition']); ?></h3></div>
                    </div>
                    <div class="priceDetails">
                        <h4>PRICE: ₹<?php echo htmlspecialchars($book['price']); ?></h4>
                        <?php if($book['status'] !== 'sold'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="purchase" value="1">
                                <button type="submit">Buy Now</button>
                            </form>
                        <?php else: ?>
                            <button disabled style="background-color: #ccc;">Sold Out</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

