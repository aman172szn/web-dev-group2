<?php
require_once '../config/database.php';

// Test orders table
echo "<h3>Orders Table:</h3>";
$result = mysqli_query($conn, "SELECT * FROM orders");
if (!$result) {
    echo "Error checking orders table: " . mysqli_error($conn);
} else {
    echo "Number of orders: " . mysqli_num_rows($result) . "<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "<br>";
    }
}

// Test books table
echo "<h3>Books Table:</h3>";
$result = mysqli_query($conn, "SELECT * FROM books WHERE status='sold'");
if (!$result) {
    echo "Error checking books table: " . mysqli_error($conn);
} else {
    echo "Number of sold books: " . mysqli_num_rows($result) . "<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "<br>";
    }
}

// Test users table
echo "<h3>Users Table:</h3>";
$result = mysqli_query($conn, "SELECT id, username, phone_no FROM users");
if (!$result) {
    echo "Error checking users table: " . mysqli_error($conn);
} else {
    echo "Number of users: " . mysqli_num_rows($result) . "<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "<br>";
    }
}
?> 