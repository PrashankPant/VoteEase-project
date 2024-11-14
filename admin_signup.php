<?php
session_start();
require_once('config/database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check if username already exists
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($result) > 0) {
        $error = "Username already exists";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, user_type) VALUES ('$username', '$hashed_password', 'admin')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php?success=Account created successfully");
            exit();
        } else {
            $error = "Error creating account";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="admin-signup-container">
        <h2>Admin Sign Up</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="admin-signup-form">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required placeholder="Enter username">
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required minlength="6" placeholder="Enter password">
            </div>
            
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required minlength="6" placeholder="Confirm password">
            </div>
            
            <button type="submit">Sign Up</button>
            <button type="button" class="back-to-login" onclick="window.location.href='index.php'">Back to Login</button>
        </form>
    </div>
</body>
</html>
