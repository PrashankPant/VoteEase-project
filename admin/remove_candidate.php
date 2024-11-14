<?php
session_start();
require_once('../config/database.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Check if the voter_id is provided
if (isset($_POST['voter_id'])) {
    $voter_id = $_POST['voter_id'];

    // Set iscandidate to 0, removing the candidate status but keeping the user as a voter
    $remove_candidate_query = "UPDATE voters SET is_candidate = 0 WHERE id = $voter_id";
    $remove_result = mysqli_query($conn, $remove_candidate_query);

    if ($remove_result) {
        $_SESSION['success_message'] = "Candidate status removed successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to remove candidate status.";
    }
} else {
    $_SESSION['error_message'] = "No candidate selected to remove.";
}

// Redirect back to the manage_candidate.php page
header("Location: manage_candidate.php?room_id=" . $_GET['room_id']);
exit();
?>
