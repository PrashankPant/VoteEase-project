<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$room_id = $_GET['room_id'];

// Fetch the candidates for this room
$candidate_query = "SELECT * FROM voters WHERE room_id = $room_id AND is_candidate = 1";
$candidate_result = mysqli_query($conn, $candidate_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2 class="center">Existing Candidates</h2>

        <!-- Candidates Table -->
        <table class="table">
            <tr>
                <th>Username</th>
            </tr>
            <?php while ($candidate = mysqli_fetch_assoc($candidate_result)): ?>
                <tr>
                    <td>
                        <a href="edit_candidate.php?voter_id=<?php echo $candidate['id']; ?>" class="link"><?php echo htmlspecialchars($candidate['username']); ?></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Back to Vote Room Button -->
        <div class="actions">
            <a href="vote_room.php?room_id=<?php echo $room_id; ?>">
                <button class="btn">Back to Vote Room</button>
            </a>
        </div>
    </div>
</body>
</html>
