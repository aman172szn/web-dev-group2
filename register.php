<?php
// Connect to database
require_once 'config/database.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $phno = $_POST["phno"];
    $password = $_POST["password"];

    // Optional: validate inputs (basic check)
    // if (empty($username) || empty($phno) || empty($password)) {
    //     die("Please fill all the fields.");
    // }

    // Check for duplicate username
    $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
 
    if ($checkResult->num_rows > 0) {
        header("Location: login.html?error=Username already exists");
        exit();    }
    $checkStmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, phone_no, password, is_admin) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $username, $phno, $password);

    if ($stmt->execute()) {        
        // redirect to login page
        header("Location: login.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
