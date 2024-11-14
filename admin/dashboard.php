<?php
session_start();
require_once('../config/database.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$rooms_query = "SELECT * FROM vote_rooms WHERE admin_id = $admin_id";
$rooms_result = mysqli_query($conn, $rooms_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <!-- Dashboard Title -->
        <h2 class="dashboard-title">Admin Dashboard</h2>

        <!-- Create Vote Room Button at the Top -->
        
        
        <!-- Existing Vote Rooms -->
        <?php if (mysqli_num_rows($rooms_result) > 0): ?>
            <h3 class="section-title">Existing Vote Rooms</h3>
            <table class="room-table">
                <tr>
                    <th>Room Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
                    <tr>
                        <td><a href="vote_room.php?room_id=<?php echo $room['id']; ?>" class="room-link"><?php echo htmlspecialchars($room['room_name']); ?></a></td>
                        <td><?php echo htmlspecialchars($room['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($room['end_time']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-rooms-message">No voting rooms found.</p>
        <?php endif; ?>

        <div class="top-actions">
            <a href="create_room.php" class="button-primary">Create Vote Room</a>
        </div>

        <!-- Logout Button at the Bottom Right -->
        <div class="bottom-actions">
            <a href="logout.php" class="button-secondary">Logout</a>
        </div>
    </div>
</body>
</html>
