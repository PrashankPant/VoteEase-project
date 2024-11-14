<?php
session_start();
require_once('../config/database.php');

// Redirect if not logged in as a voter
if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];
$room_id = $_SESSION['room_id'];

// Fetch the voter's information
$voter_query = "SELECT * FROM voters WHERE id = $voter_id";
$voter_result = mysqli_query($conn, $voter_query);
$voter = mysqli_fetch_assoc($voter_result);

// Fetch the voting room details
$room_query = "SELECT * FROM vote_rooms WHERE id = $room_id";
$room_result = mysqli_query($conn, $room_query);
$room = mysqli_fetch_assoc($room_result);

// Set the correct time zone (e.g., Asia/Kathmandu for Nepal)
date_default_timezone_set('Asia/Kathmandu');
$current_time = date('Y-m-d H:i:s');

// Check if voting is still open
$voting_open = ($current_time >= $room['start_time']) && ($current_time <= $room['end_time']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($voter['username']); ?></h2>
        
        <p>Status: 
            <?php 
            if ($voter['has_voted']) {
                echo "You have already voted.";
            } elseif ($voting_open) {
                echo "Voting is open.";
            } else {
                echo "Voting is currently closed.";
            }
            ?>
        </p>

        <?php if (!$voter['has_voted'] && $voting_open): ?>
            <a href="caste_vote.php"><button>Cast Vote</button></a>
        <?php elseif ($voter['has_voted']): ?>
            <p>Thank you for voting!</p>
        <?php else: ?>
            <p>Voting is currently closed. Please check back during the voting period.</p>
        <?php endif; ?>
        
        <?php if ($room['is_published']): ?>
            <a href="view_result.php"><button>View Results</button></a>
        <?php endif; ?>
        
        <a href="logout.php"><button>Logout</button></a>
    </div>
</body>
</html>
