<?php
require_once '../config/database.php';
session_start();

// Initialize error array
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    $required_fields = ['title', 'author', 'edition', 'book_condition', 'MRP', 'description'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . " is required";
        }
    }

    // Validate MRP is a valid number
    if (!empty($_POST['MRP']) && !is_numeric($_POST['MRP'])) {
        $errors[] = "MRP must be a valid number";
    }

    if (empty($errors)) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $author = mysqli_real_escape_string($conn, $_POST['author']);
        $edition = mysqli_real_escape_string($conn, $_POST['edition']);
        $book_condition = mysqli_real_escape_string($conn, $_POST['book_condition']);
        $MRP = mysqli_real_escape_string($conn, $_POST['MRP']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        // Handle file upload
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image_path = '';
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $img_tmp_name = $_FILES['img']['tmp_name'];
            $img_name = basename($_FILES['img']['name']);
            $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

            // Validate file size (max 5MB)
            if ($_FILES['img']['size'] > 5000000) {
                $errors[] = "Image file is too large. Maximum size is 5MB.";
            }

            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($img_ext, $allowed_ext)) {
                $unique_name = uniqid('book_', true) . '.' . $img_ext;
                $upload_path = $target_dir . $unique_name;

                if (move_uploaded_file($img_tmp_name, $upload_path)) {
                    $image_path = './uploads/' . $unique_name; // Store relative path
                } else {
                    $errors[] = "Error uploading image. Please try again.";
                }
            } else {
                $errors[] = "Unsupported image type. Please use JPG, JPEG, PNG, or GIF.";
            }
        } else {
            $errors[] = "Please provide a book image.";
        }

        if (empty($errors)) {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                $errors[] = "Please log in to sell a book.";
            } else {
                $user_id = $_SESSION['user_id'];

                $sql = "INSERT INTO books (user_id, title, author, edition, book_condition, description, MRP, image_path, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssds", $user_id, $title, $author, $edition, $book_condition, $description, $MRP, $image_path);

                if ($stmt->execute()) {
                    $success = true;
                    $_SESSION['success_message'] = "Book submitted successfully and is pending approval.";
                    header("location: landingPage.php?status=success");
                    exit();
                } else {
                    $errors[] = "Error submitting book: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// If there are errors, store them in session and redirect back
if (!empty($errors)) {
    $_SESSION['sell_book_errors'] = $errors;
    $_SESSION['sell_book_form_data'] = $_POST; // Store form data for repopulation
    header("location: landingPage.php?page=sell&status=error");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Your Book - EzBooks</title>
    <style>
        .sell-book-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="file"] {
            padding: 0.5rem;
            border: 1px dashed #ddd;
            border-radius: 5px;
            width: 100%;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76,175,80,0.2);
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .error-container {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }

        .error-container ul {
            margin: 0;
            padding-left: 1.5rem;
            color: #d32f2f;
        }

        .success-message {
            background-color: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="sell-book-container">
        <h2>Sell Your Book</h2>
        
        <?php if (isset($_SESSION['sell_book_errors'])): ?>
            <div class="error-container">
                <ul>
                    <?php 
                    foreach ($_SESSION['sell_book_errors'] as $error) {
                        echo "<li>$error</li>";
                    }
                    unset($_SESSION['sell_book_errors']);
                    ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Book Title*</label>
                <input type="text" id="title" name="title" value="<?php echo $_SESSION['sell_book_form_data']['title'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="author">Author*</label>
                <input type="text" id="author" name="author" value="<?php echo $_SESSION['sell_book_form_data']['author'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="edition">Edition*</label>
                <input type="text" id="edition" name="edition" value="<?php echo $_SESSION['sell_book_form_data']['edition'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="book_condition">Book Condition*</label>
                <select id="book_condition" name="book_condition" required>
                    <option value="">Select Condition</option>
                    <option value="New" <?php echo (isset($_SESSION['sell_book_form_data']['book_condition']) && $_SESSION['sell_book_form_data']['book_condition'] == 'New') ? 'selected' : ''; ?>>New</option>
                    <option value="Like New" <?php echo (isset($_SESSION['sell_book_form_data']['book_condition']) && $_SESSION['sell_book_form_data']['book_condition'] == 'Like New') ? 'selected' : ''; ?>>Like New</option>
                    <option value="Very Good" <?php echo (isset($_SESSION['sell_book_form_data']['book_condition']) && $_SESSION['sell_book_form_data']['book_condition'] == 'Very Good') ? 'selected' : ''; ?>>Very Good</option>
                    <option value="Good" <?php echo (isset($_SESSION['sell_book_form_data']['book_condition']) && $_SESSION['sell_book_form_data']['book_condition'] == 'Good') ? 'selected' : ''; ?>>Good</option>
                    <option value="Fair" <?php echo (isset($_SESSION['sell_book_form_data']['book_condition']) && $_SESSION['sell_book_form_data']['book_condition'] == 'Fair') ? 'selected' : ''; ?>>Fair</option>
                </select>
            </div>

            <div class="form-group">
                <label for="MRP">Price (MRP)*</label>
                <input type="number" id="MRP" name="MRP" step="0.01" value="<?php echo $_SESSION['sell_book_form_data']['MRP'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description*</label>
                <textarea id="description" name="description" rows="4" required><?php echo $_SESSION['sell_book_form_data']['description'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="img">Book Image* (JPG, JPEG, PNG, or GIF, max 5MB)</label>
                <input type="file" id="img" name="img" accept="image/jpeg,image/png,image/gif" required>
            </div>

            <button type="submit" class="submit-btn">Submit Book for Sale</button>
        </form>
    </div>

    <?php
    // Clear form data after displaying
    unset($_SESSION['sell_book_form_data']);
    ?>
</body>
</html>
