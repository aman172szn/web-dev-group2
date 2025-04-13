<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit();
}

// book approve
if (isset($_POST['approve_book'])) {
    $book_id = $_POST['book_id'];
    
    $stmt = $conn->prepare("UPDATE books SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    if($stmt->execute()) {
        $_SESSION['success'] = "Book approved successfully!";
    } else {
        $_SESSION['error'] = "Failed to approve book.";
    }
    header('Location: admin_managebook.php');
    exit();
}

// rej book
if (isset($_POST['reject_book'])) {
    $book_id = $_POST['book_id'];
    
    $stmt = $conn->prepare("UPDATE books SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    if($stmt->execute()) {
        $_SESSION['success'] = "Book rejected successfully!";
    } else {
        $_SESSION['error'] = "Failed to reject book.";
    }
    header('Location: admin_managebook.php');
    exit();
}

// rest book
$stmt = $conn->prepare("
    SELECT b.*, u.username as seller_name 
    FROM books b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'pending'
");
$stmt->execute();
$result = $stmt->get_result();
$pendingBooks = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Books - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Doto:wght@100..900&family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Galdeano&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&family=Inconsolata:wdth,wght@82,200..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="managebook.css" />
</head>
<body>
    <div class="navbar">
        <h2><span class="ez">ez</span><span class="books">Books</span></h2>
        <div class="nav-links">
            <a href="admin_dashboard.php">Home</a>
            <a href="admin_managebook.php" class="active">Manage Books</a>
            <a href="admin_manageuser.php">Manage Users</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="main">
        <h2>Pending Book Submissions</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="book-container">
            <?php if(count($pendingBooks) > 0): ?>
                <?php foreach($pendingBooks as $book): ?>
                    <div class="book-card">
                        <img src="../<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Cover" onerror="this.src='../assets/images/default-book.jpg'"/>
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                        <p><strong>Edition:</strong> <?php echo htmlspecialchars($book['edition']); ?></p>
                        <p><strong>Condition:</strong> <?php echo htmlspecialchars($book['book_condition']); ?></p>
                        <p><strong>MRP:</strong> ₹<?php echo number_format($book['MRP'], 2); ?></p>
                        <p><strong>Seller:</strong> <?php echo htmlspecialchars($book['seller_name']); ?></p>
                        <div class="btn-group">
                            <form method="POST" action="admin_managebook.php" style="display: inline">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>" />
                                <button type="submit" name="approve_book" class="approve-btn" onclick="return confirm('Are you sure you want to approve this book?')">
                                    ✔ Approve
                                </button>
                            </form>
                            <form method="POST" action="admin_managebook.php" style="display: inline">
                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>" />
                                <button type="submit" name="reject_book" class="reject-btn" onclick="return confirm('Are you sure you want to reject this book?')">
                                    ✖ Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-books">No pending book submissions.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
