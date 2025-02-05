<?php
session_start();
require_once('../config/database.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get the admin's ID from the session
$admin_id = $_SESSION['user_id'];

$error = ""; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = mysqli_real_escape_string($conn, $_POST['room_name']);
    $num_voters = (int)$_POST['num_voters'];
    $num_candidates = (int)$_POST['num_candidates'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Validate inputs
    $current_time = date("Y-m-d H:i:s");
    if ($num_candidates < 2) {
        $error = "You must have at least 2 candidates.";
    } elseif ($num_voters <= 2) {
        $error = "The number of voters must be greater than 2.";
    } elseif ($start_time < $current_time) {
        $error = "Start time cannot be in the past.";
    } elseif ($end_time <= $start_time) {
        $error = "End time must be later than the start time.";
    } else {
        // Insert the new vote room
        $query = "INSERT INTO vote_rooms (room_name, num_voters, num_candidates, start_time, end_time, admin_id) 
                  VALUES ('$room_name', $num_voters, $num_candidates, '$start_time', '$end_time', $admin_id)";
        
        if (mysqli_query($conn, $query)) {
            $room_id = mysqli_insert_id($conn);

            // Redirect to the voter creation page
            header("Location: create_voters.php?room_id=$room_id&num_voters=$num_voters");
            exit();
        } else {
            $error = "Error creating room: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Vote Room</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>
    <div class="container">
        <h2>Create Vote Room</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Room Name:</label>
                <input type="text" name="room_name" required>
            </div>
            
            <div class="form-group">
                <label>Number of Voters:</label>
                <input type="number" name="num_voters" min="3" required>
            </div>
            
            <div class="form-group">
                <label>Number of Candidates:</label>
                <input type="number" name="num_candidates" min="2" required>
            </div>
            
            <div class="form-group">
                <label>Start Time:</label>
                <input type="datetime-local" name="start_time" required>
            </div>
            
            <div class="form-group">
                <label>End Time:</label>
                <input type="datetime-local" name="end_time" required>
            </div>
            
            <button type="submit">Create Room</button>
            <a href="dashboard.php"><button type="button">Back</button></a>
        </form>
    </div>
</body>
</html>
