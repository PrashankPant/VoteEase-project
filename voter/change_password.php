<?php
session_start();
require_once('../config/database.php');

if(!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voter_id = $_SESSION['voter_id'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $query = "UPDATE voters SET password = '$new_password', password_changed = 1 
              WHERE id = $voter_id";
    
    if(mysqli_query($conn, $query)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error changing password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password" required>
            </div>
            
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>