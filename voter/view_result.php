<?php
session_start();
require_once('../config/database.php');

if(!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

$room_id = $_SESSION['room_id'];

// Check if results are published
$room_query = "SELECT * FROM vote_rooms WHERE id = $room_id";
$room_result = mysqli_query($conn, $room_query);
$room = mysqli_fetch_assoc($room_result);

if(!$room['is_published']) {
    header("Location: dashboard.php");
    exit();
}

// Get voting results
$results_query = "SELECT v.username, COUNT(vo.id) as vote_count 
                 FROM voters v 
                 LEFT JOIN votes vo ON v.id = vo.candidate_id 
                 WHERE v.room_id = $room_id AND v.is_candidate = 1 
                 GROUP BY v.id 
                 ORDER BY vote_count DESC";
$results_result = mysqli_query($conn, $results_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Voting Results</h2>
        
        <table>
            <tr>
                <th>Candidate</th>
                <th>Votes</th>
            </tr>
            <?php while($result = mysqli_fetch_assoc($results_result)): ?>
                <tr>
                    <td><?php echo $result['username']; ?></td>
                    <td><?php echo $result['vote_count']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <a href="dashboard.php"><button type="button">Back to Dashboard</button></a>
    </div>
</body>
</html>