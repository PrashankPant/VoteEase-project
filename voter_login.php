<?php
session_start();
require_once('config/database.php');

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve input values
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = $_POST['password'];
    $room_name = trim(mysqli_real_escape_string($conn, $_POST['room_name']));
    $room_id = intval($_POST['room_id']);

    // Check the database connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Validate Room Name and Room ID
    $room_query = "SELECT * FROM vote_rooms WHERE room_name = '$room_name' AND id = $room_id";
    $room_result = mysqli_query($conn, $room_query);

    if (!$room_result || mysqli_num_rows($room_result) == 0) {
        // Invalid Room Name or Room ID
        header("Location: index.php?error=Invalid Room Name or Room ID");
        exit();
    }

    // Query the voters table for the provided username
    $query = "SELECT * FROM voters WHERE username = '$username' AND room_id = $room_id";
    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        // No matching voter record found
        header("Location: index.php?error=No voter account found for the provided Room");
        exit();
    }

    $voter = mysqli_fetch_assoc($result);

    // Check if the voting room is active
    $active_room_query = "SELECT * FROM vote_rooms WHERE id = {$voter['room_id']} AND NOW() BETWEEN start_time AND end_time";
    $active_room_result = mysqli_query($conn, $active_room_query);

    if (!$active_room_result || mysqli_num_rows($active_room_result) == 0) {
        // Room is not active
        header("Location: index.php?error=Voting room is closed or invalid");
        exit();
    }

    // Verify the entered password with the hashed password in the database
    if (password_verify($password, $voter['password'])) {
        // Store session variables for the voter
        $_SESSION['voter_id'] = $voter['id'];
        $_SESSION['room_id'] = $voter['room_id'];
        $_SESSION['user_type'] = 'voter';

        // Redirect based on whether the voter has changed their password
        if (!$voter['password_changed']) {
            header("Location: voter/change_password.php");
        } else {
            header("Location: voter/dashboard.php");
        }
        exit();
    } else {
        // Password mismatch
        header("Location: index.php?error=Incorrect password");
        exit();
    }
}
?>
