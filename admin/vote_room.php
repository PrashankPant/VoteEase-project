<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$room_id = $_GET['room_id'];

// Fetch the room details
$room_query = "SELECT room_name FROM vote_rooms WHERE id = $room_id";
$room_result = mysqli_query($conn, $room_query);
$room = mysqli_fetch_assoc($room_result);

// Fetch voters and candidates
$voter_query = "SELECT * FROM voters WHERE room_id = $room_id AND is_candidate = 0";
$candidate_query = "SELECT * FROM voters WHERE room_id = $room_id AND is_candidate = 1";

$voter_result = mysqli_query($conn, $voter_query);
$candidate_result = mysqli_query($conn, $candidate_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($room['room_name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2 class="center"><?php echo htmlspecialchars($room['room_name']); ?></h2>

        <div class="action-buttons">
            <!-- Manage Voters Button -->
            <a href="manage_voters.php?room_id=<?php echo $room_id; ?>" class="button-primary">Manage Voters</a>
            
            <!-- Manage Candidates Button -->
            <a href="manage_candidate.php?room_id=<?php echo $room_id; ?>" class="button-primary">Manage Candidates</a>
            
            <!-- View Results Button -->
            <a href="view_results.php?room_id=<?php echo $room_id; ?>" class="button-primary">View Results</a>
            
            <!-- Remove Room Button -->
            <a href="remove_room.php?room_id=<?php echo $room_id; ?>" class="button-danger" onclick="return confirm('Are you sure you want to remove this room?')">Remove Room</a>
            
            <!-- Back to Dashboard Button -->
            <a href="dashboard.php" class="button-secondary">Back to Dashboard</a>
        </div>

        
    </div>
</body>
</html>
