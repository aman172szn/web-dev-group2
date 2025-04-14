<?php
require_once 'C:/xampp/htdocs/web-dev-group2/config/database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($img_ext, $allowed_ext)) {
            $unique_name = uniqid('book_', true) . '.' . $img_ext;
            $upload_path = $target_dir . $unique_name;

            if (move_uploaded_file($img_tmp_name, $upload_path)) {
                $image_path = $upload_path;
            } else {
                echo "Error uploading image.";
                exit();
            }
        } else {
            echo "Unsupported image type.";
            exit();
        }
    } else {
        echo "Image upload failed or not provided.";
        exit();
    }


    // You can get the user ID from the session if the user is logged in
    $user_id = $_SESSION['user_id'] ?? 0;

    // Default image path (you can change this to allow image uploads)

    $sql = "INSERT INTO books (user_id, title, author, edition, book_condition,description, MRP, image_path, status)
            VALUES ('$user_id', '$title', '$author', '$edition', '$book_condition','$description', '$MRP', '$image_path', 'pending')";

    if (mysqli_query($conn, $sql)) {
        // echo "Book submitted successfully and is pending approval.";
        header("location: landingPage.php?status=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
