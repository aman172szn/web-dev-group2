<?php
// Connect to database
require_once 'config/database.php';

// Initialize error array
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $phno = trim($_POST["phno"]);
    $password = trim($_POST["password"]);

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long";
    }

    if (empty($phno)) {
        $errors[] = "Phone number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $phno)) {
        $errors[] = "Please enter a valid 10-digit phone number";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check for duplicate username
        $checkStmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
    
        if ($checkResult->num_rows > 0) {
            $errors[] = "Username already exists";
        } else {
            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (username, phone_no, password, is_admin) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sss", $username, $phno, $password);

            if ($stmt->execute()) {        
                // redirect to login page with success message
                header("Location: login.php?success=Registration successful! Please login.");
                exit();
            } else {
                $errors[] = "Registration failed: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ezBooks</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Galdeano&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #121212;
            color: white;
            font-family: 'Galdeano', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: #1e1e1e;
            padding: 2rem;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #d93f3f;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #999;
            transform: translateY(0);
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .form-group input:focus + label {
            color: #d93f3f;
            transform: translateY(-5px);
        }

        input {
            width: 100%;
            padding: 10px;
            background: #2a2a2a;
            border: 2px solid transparent;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #d93f3f;
            box-shadow: 0 0 0 2px rgba(217, 63, 63, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 2px solid #d93f3f;
            color: #d93f3f;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        button:hover {
            background: #d93f3f;
            color: white;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .error-list {
            background: rgba(217, 63, 63, 0.1);
            border: 1px solid #d93f3f;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #d93f3f;
            font-size: 0.9rem;
        }

        .error-list li {
            margin-left: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
        }

        .login-link a {
            color: #d93f3f;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #ff6b6b;
            text-decoration: underline;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error-list">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       class="<?php echo isset($errors['username']) ? 'error' : ''; ?>">
            </div>

            <div class="form-group">
                <label for="phno">Phone Number</label>
                <input type="tel" id="phno" name="phno" required pattern="[0-9]{10}"
                       value="<?php echo isset($_POST['phno']) ? htmlspecialchars($_POST['phno']) : ''; ?>"
                       class="<?php echo isset($errors['phno']) ? 'error' : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                       class="<?php echo isset($errors['password']) ? 'error' : ''; ?>">
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        // Add animation to form groups when focused
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>
