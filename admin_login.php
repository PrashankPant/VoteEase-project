<?php
session_start();
require_once('config/database.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username = '$username' AND user_type = 'admin'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = 'admin';
            header("Location: admin/dashboard.php");
            exit();
        }
    }
    
    header("Location: index.php?error=Invalid admin credentials");
    exit();
}
?>