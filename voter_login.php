<?php
session_start();
require_once('config/database.php');

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve input values
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = $_POST['password'];

    // Check the database connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Query the voters table for the provided username
    $query = "SELECT * FROM voters WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    // Debugging: Check if the query executed successfully
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Check if a matching voter record was found
    if (mysqli_num_rows($result) == 1) {
        $voter = mysqli_fetch_assoc($result);

        // Check if the voting room is active
        $room_query = "SELECT * FROM vote_rooms WHERE id = {$voter['room_id']} AND NOW() BETWEEN start_time AND end_time";
        $room_result = mysqli_query($conn, $room_query);

        // Ensure the room is active and verify the password
        if (mysqli_num_rows($room_result) == 1) {
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
        } else {
            // Room is not active or doesn't exist
            header("Location: index.php?error=Voting room is closed or invalid");
            exit();
        }
    } else {
        // No matching voter record found
        header("Location: index.php?error=No voter account found with this username");
        exit();
    }
}
?>
