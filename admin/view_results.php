<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$room_id = $_GET['room_id'];
$message = ""; // Initialize message variable

// Fetch candidates and their vote counts
$result_query = "SELECT v.username, COUNT(vt.id) AS total_votes
                 FROM voters v
                 LEFT JOIN votes vt ON v.id = vt.candidate_id
                 WHERE v.room_id = $room_id AND v.is_candidate = 1
                 GROUP BY v.id";
$result_result = mysqli_query($conn, $result_query);

// Calculate the winner(s)
$candidates = [];
$max_votes = 0;

while ($row = mysqli_fetch_assoc($result_result)) {
    $candidates[] = $row; // Store each candidate's data
    if ($row['total_votes'] > $max_votes) {
        $max_votes = $row['total_votes'];
    }
}

// Find all candidates with the maximum votes
$winners = array_filter($candidates, function ($candidate) use ($max_votes) {
    return $candidate['total_votes'] == $max_votes;
});

// Determine if there is a draw
$is_draw = count($winners) > 1;

// Fetch if the results are already published
$publish_query = "SELECT is_published FROM vote_rooms WHERE id = $room_id";
$publish_result = mysqli_query($conn, $publish_query);
$publish_row = mysqli_fetch_assoc($publish_result);
$is_published = $publish_row['is_published'];

// Handle publish action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publish_vote'])) {
    $publish_update_query = "UPDATE vote_rooms SET is_published = 1 WHERE id = $room_id";
    if (mysqli_query($conn, $publish_update_query)) {
        $is_published = 1;
        $message = "Vote has been published successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Result</title>
    <link rel="stylesheet" href="../css/style_view_results.css">
</head>
<body>
    <?php if (!empty($message)): ?>
        <script>
            alert("<?php echo $message; ?>");
        </script>
    <?php endif; ?>

    <div class="container">
        <h2 class="center">Result</h2>

        <!-- Result Table -->
        <table class="table">
            <tr>
                <th>Username</th>
                <th>Vote Count</th>
            </tr>
            <?php foreach ($candidates as $candidate): ?>
                <tr>
                    <td><?php echo htmlspecialchars($candidate['username']); ?></td>
                    <td><?php echo $candidate['total_votes']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Display Result -->
        <?php if ($is_draw): ?>
            <h3 class="center">Result: Draw</h3>
            <p class="center">Candidates tied with <?php echo $max_votes; ?> votes:</p>
            <ul class="center">
                <?php foreach ($winners as $winner): ?>
                    <li><?php echo htmlspecialchars($winner['username']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <h3 class="center">Winner: <?php echo htmlspecialchars($winners[0]['username']); ?></h3>
            <p class="center">Votes: <?php echo $max_votes; ?></p>
        <?php endif; ?>

        <!-- Publish Vote Button (Only show if not already published) -->
        <?php if (!$is_published): ?>
            <form method="post" action="view_results.php?room_id=<?php echo $room_id; ?>">
                <button type="submit" name="publish_vote" class="btn">Publish Vote</button>
            </form>
        <?php else: ?>
            <p class="center">Results have been published.</p>
        <?php endif; ?>

        <!-- Back to Vote Room Button -->
        <div class="actions">
            <a href="vote_room.php?room_id=<?php echo $room_id; ?>">
                <button class="btn-secondary">Back to Vote Room</button>
            </a>
        </div>
    </div>
</body>
</html>
