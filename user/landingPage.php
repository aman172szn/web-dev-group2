<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ezBooks</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="navleft">
                <h1>ez<span>Books</span></h1>
            </div>
            <div class="navmid">
                <div class="navMidButton1">
                <button type="button" id="buyingButton" onclick="toBuyingPage()">Buy</button>
                </div>
                <div class="navMidButton2">
                <button type="button" id="sellingButton" onclick="toSellingPage()">Sell</button>
                </div>
            </div>
            <div class="navright">
                <div class="navSearch">
                    <form action=""  method="GET" style="height:100%; width:100%;">
                        <input type="text" id="bookname" name="bookname" placeholder="Search a book">
                    </form>
                </div>
                <div class="navbarSocial">
                    <i class="fa-brands fa-facebook fa-2xl"></i>
                    <i class="fa-brands fa-instagram fa-2xl"></i>
                </div>
                <div class="navMidButton3">
                    <a href="?logout=true">
                        <button type="button" >Logout</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="LandingPage">
            <div class="buyingPage" >
                <div class="row">
                    <?php
                        //for logout
                        session_start();
                        if (isset($_GET['logout'])) {
                            session_unset();
                            session_destroy();
                            header("Location: ../login.html"); 
                            exit();
                        }
                        //connect db
                        require_once '../config/database.php';

                        //search for a book
                        if (isset($_GET['bookname']) && !empty(trim($_GET['bookname']))) {
                            $bookname = mysqli_real_escape_string($conn, $_GET['bookname']);

                            $query = "SELECT * FROM books WHERE title LIKE '$bookname%' AND status='approved'";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $book_id = $row['id']; // Assuming 'id' is the unique identifier for each book
                                    echo '
                                            <div class="col">
                                            <a style="text-decoration: none;" href="BookDetails.php?book_id=' . $book_id . '">
                                                    <div class="bookDetails" style="color:black">
                                                        <div class="bookeDetailsimage">
                                                        <img src="' . $row["image_path"] . '" alt="no image">
                                                        </div>
                                                        <div class="bookDescription">
                                                        <h1>' . htmlspecialchars($row["title"]) . '</h1>
                                                            <div class="bookPrice">
                                                            <h1>₹' . htmlspecialchars($row["price"]) . '</h1>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        ';
                                }
                                echo "</div>";
                            } else {
                                echo "<p>No books found with that title.</p>";
                            }
                        }

                        else{
                            // Query to fetch all books
                            $sql = "SELECT * FROM books where status='approved'";
                            $result = $conn->query($sql);

                            // Loop through each book and display each
                            if ($result->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $book_id = $row['id']; // Assuming 'id' is the unique identifier for each book
                                    echo '
                                        <div class="col">
                                            <a style="text-decoration: none;" href="BookDetails.php?book_id=' . $book_id . '">
                                                <div class="bookDetails" style="color:black">
                                                    <div class="bookeDetailsimage">
                                                        <img src="' . $row["image_path"] . '" alt="no image">
                                                    </div>
                                                    <div class="bookDescription">
                                                        <h1>' . htmlspecialchars($row["title"]) . '</h1>
                                                        <div class="bookPrice">
                                                            <h1>₹' . htmlspecialchars($row["price"]) . '</h1>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    ';
                                }
                            } else {
                                echo "<p>No books found.</p>";
                            }
                        }
                        
                        
                    ?>
                </div>
            </div>
            <div class="sellingPage" style="display: none;">
                    <div class="sellingForm">
                        <h1>Sell a book</h1>
                        <form action="sellBook.php" method="POST" enctype="multipart/form-data">
                            <div class="formFields1">
                                <label for="title">TITLE</label><br> 
                                <input type="text" id="title" name="title" placeholder="Enter book title" required>
                            </div>
                            <div class="formFields1">
                                <label for="author">AUTHOR</label><br> 
                                <input type="text" id="author" name="author" placeholder="Enter book's author name" required>
                            </div>
                            <div class="formFields1">
                                <label for="edition">EDITION</label><br> 
                                <input type="text" id="edition" name="edition" placeholder="Edition of the book" required>
                         </div>
                            <div class="formFields2">
                                <label>BOOK CONDITION  :</label>
                                <select id="bookcondition" name="book_condition" required>
                                    <option>Brand New</option>
                                    <option>Like New</option>
                                    <option>Good</option>
                                    <option>Fair</option>
                                </select>
                            </div>
                            <div class="formFields1">
                                <label for="price">MRP</label><br> 
                                <input type="text" id="price" name="MRP" placeholder="Enter M.R.P of book" required>
                            </div>
                            <div class="formFields3">
                                <label>UPLOAD COVER IMAGE :</label>
                                <input type="file" id="img" name="img" accept="image/*" required>
                            </div>
                            <div class="formFields4">
                                <label>DESCRIPTION</label>
                                <textarea placeholder="Description of the book" id="description" name="description"></textarea>
                            </div>
                            <div class="formFields1">
                                <button type="submit">Submit</button>
                            </div>
                        </form>
                        <?php
                            if (isset($_GET['status']) && $_GET['status'] === 'success') {
                                echo '<p style="color: green; margin-top: 10px;text-align: center">Book submitted successfully and is pending approval from admin.</p>';
                            }
                        ?>
                    </div>
            </div>

        </div>
    </div>  
</body>
<script>
    if (window.location.href.includes("status=success")) {
        const url = new URL(window.location.href);
        url.searchParams.delete("status");
        window.history.replaceState({}, document.title, url.toString());
    }
    const buyingPage = document.querySelector(".buyingPage");
    const sellingPage = document.querySelector(".sellingPage");
    const buyingPageButton = document.getElementById("buyingButton");
    const sellingPageButton = document.getElementById("sellingButton");

    function toSellingPage(){
        buyingPage.style.display ="none";
        sellingPage.style.display ="flex";
        buyingPageButton.style.boxShadow="inset -3px -3px 19px rgba(0, 0, 0, 0.5)";
        sellingPageButton.style.boxShadow="none";
    }
    function toBuyingPage(){
        buyingPage.style.display ="block";
        sellingPage.style.display ="none";
        buyingPageButton.style.boxShadow="none";
        sellingPageButton.style.boxShadow="inset 5px -9px 19px rgba(0, 0, 0, 0.5)";
    }
</script>
</html>