<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$room_id = $_GET['room_id'];

// Fetch the voters for this room
$voter_query = "SELECT * FROM voters WHERE room_id = $room_id AND is_candidate = 0";
$voter_result = mysqli_query($conn, $voter_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Voters</title>
    <link rel="stylesheet" href="../css/style_manage_voter.css">
</head>
<body>
    <div class="container">
        <h2 class="center">Existing Voters</h2>

        <!-- Voters Table -->
        <table class="table">
            <tr>
                <th>Username</th>
            </tr>
            <?php while ($voter = mysqli_fetch_assoc($voter_result)): ?>
                <tr>
                    <td>
                        <a href="edit_voter.php?voter_id=<?php echo $voter['id']; ?>" class="link"><?php echo htmlspecialchars($voter['username']); ?></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Add New Voter Button -->
        <div class="actions">
            <a href="add_new_voter.php?room_id=<?php echo $room_id; ?>">
                <button class="btn">Add New Voter</button>
            </a>
        </div>

        <!-- Back to Vote Room Button -->
        <div class="actions">
            <a href="vote_room.php?room_id=<?php echo $room_id; ?>"><button class="btn-voter">Back to Vote Room</button></a>
        </div>
    </div>
</body>
</html>
