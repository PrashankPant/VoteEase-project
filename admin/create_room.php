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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = mysqli_real_escape_string($conn, $_POST['room_name']);
    $num_voters = (int)$_POST['num_voters'];
    $num_candidates = (int)$_POST['num_candidates'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Insert the new vote room with the admin_id
    $query = "INSERT INTO vote_rooms (room_name, num_voters, num_candidates, start_time, end_time, admin_id) 
              VALUES ('$room_name', $num_voters, $num_candidates, '$start_time', '$end_time', $admin_id)";
    
    if (mysqli_query($conn, $query)) {
        $room_id = mysqli_insert_id($conn);
        header("Location: manage_voters.php?room_id=$room_id");
        exit();
    } else {
        $error = "Error creating room: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Vote Room</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Create Vote Room</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Room Name:</label>
                <input type="text" name="room_name" required>
            </div>
            
            <div class="form-group">
                <label>Number of Voters:</label>
                <input type="number" name="num_voters" min="2" required>
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
