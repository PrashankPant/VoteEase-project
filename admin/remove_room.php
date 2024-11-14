<?php
session_start();
require_once('../config/database.php');

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get the room_id from the URL
$room_id = $_GET['room_id'];

// Confirm room exists before attempting to delete
$room_query = "SELECT room_name FROM vote_rooms WHERE id = $room_id";
$room_result = mysqli_query($conn, $room_query);
$room = mysqli_fetch_assoc($room_result);

if (!$room) {
    echo "Room not found.";
    exit();
}

// Delete the room and its associated records
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete votes associated with this room
        $delete_votes = "DELETE FROM votes WHERE room_id = $room_id";
        mysqli_query($conn, $delete_votes);

        // Delete voters (both voters and candidates) associated with this room
        $delete_voters = "DELETE FROM voters WHERE room_id = $room_id";
        mysqli_query($conn, $delete_voters);

        // Delete the room itself
        $delete_room = "DELETE FROM vote_rooms WHERE id = $room_id";
        mysqli_query($conn, $delete_room);

        // Commit transaction
        mysqli_commit($conn);

        // Redirect to dashboard with success message
        header("Location: dashboard.php?message=Room deleted successfully");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo "Error deleting room: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Remove Room - <?php echo htmlspecialchars($room['room_name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2 class="center">Remove Room: <?php echo htmlspecialchars($room['room_name']); ?></h2>
        
        <p>Are you sure you want to delete the room "<strong><?php echo htmlspecialchars($room['room_name']); ?></strong>"? This action will remove all associated voters, candidates, and votes permanently.</p>

        <form method="post" action="remove_room.php?room_id=<?php echo $room_id; ?>">
            <button type="submit" class="button-danger">Confirm Delete</button>
            <a href="dashboard.php" class="button-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
