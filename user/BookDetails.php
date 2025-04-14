<?php
    // Connect to the database
    require_once 'C:/xampp/htdocs/web-dev-group2/config/database.php';

    // Get the book_id from the URL
    if (isset($_GET['book_id'])) {
        $book_id = $_GET['book_id'];
        
        // Query to fetch the details of the book
        $sql = "SELECT * FROM books WHERE id = '$book_id'";
        $result = mysqli_query($conn, $sql);
        
        // Check if the book is found
        if (mysqli_num_rows($result) > 0) {
            $book = mysqli_fetch_assoc($result);
        } else {
            echo "Book not found!";
            exit;
        }
    } else {
        echo "Invalid book ID!";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link rel="stylesheet" href="./BookDetails.css">
    <!-- icon -->
    <link rel="icon" type="image/svg+xml" href="./media/ICON.svg" />
    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Galdeano&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&family=Inconsolata:wdth,wght@82,200..900&display=swap" rel="stylesheet">
    <!-- css -->
    <link rel="stylesheet" href="./landingPage.css">
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="navleft">
                <h1>ez<span>Books</span></h1>
            </div>
            <div class="navmid">
                <!-- Add your buttons here if needed -->
            </div>
            <div class="navright">
                <div class="navSearch">
                    <!-- Add your search bar here -->
                </div>
                <div class="navbarSocial">
                    <i class="fa-brands fa-facebook fa-2xl"></i>
                    <i class="fa-brands fa-instagram fa-2xl"></i>
                </div>
                <div class="navMidButton3">
                    <button type="button">Logout</button>
                </div>
            </div>
        </div>

        <!-- Book Details -->
        <div class="BookDetails">
            <div class="detailsBox">
                <div class="bookTitle">
                    <?php echo htmlspecialchars($book['title']); ?>
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
                        <button>Buy Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
