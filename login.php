<?php
session_start();
require_once 'config/database.php';


if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // admin check
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password']) || $password === $user['password']) {
            // admin login success
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['is_admin'] = true;
            header("Location: admin/admin_dashboard.php");
            exit();
        }
    } else {
        // check reg user
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 0");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password']) || $password === $user['password']) {
                // login success - reg user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = false;
                header("Location: user/landingPage.php"); // bac to dashboard
                exit();
            }
        }
    }
    
    $error = "Invalid username or password";
    $_SESSION['error'] = $error;
}


include 'login.html';
?> 