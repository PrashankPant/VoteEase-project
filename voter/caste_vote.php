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

// Check if the voter has already voted
$voter_query = "SELECT has_voted FROM voters WHERE id = $voter_id";
$voter_result = mysqli_query($conn, $voter_query);
$voter = mysqli_fetch_assoc($voter_result);

if ($voter['has_voted']) {
    header("Location: dashboard.php");
    exit();
}

// Process the vote submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_id = $_POST['candidate_id'];
    
    // Record the vote
    $vote_query = "INSERT INTO votes (room_id, voter_id, candidate_id) VALUES ($room_id, $voter_id, $candidate_id)";
    mysqli_query($conn, $vote_query);
    
    // Update the voter's status to indicate they have voted
    $update_query = "UPDATE voters SET has_voted = 1 WHERE id = $voter_id";
    mysqli_query($conn, $update_query);
    
    // Redirect back to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Fetch the candidates for the current voting room
$candidates_query = "SELECT * FROM voters WHERE room_id = $room_id AND is_candidate = 1";
$candidates_result = mysqli_query($conn, $candidates_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cast Vote</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Cast Your Vote</h2>
        
        <form method="POST">
            <div class="form-group">
                <label>Select Candidate:</label>
                <?php while ($candidate = mysqli_fetch_assoc($candidates_result)): ?>
                    <div class="candidate-option">
                        <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
                        <?php echo htmlspecialchars($candidate['username']); ?>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <button type="submit">Submit Vote</button>
            <a href="dashboard.php"><button type="button">Back</button></a>
        </form>
    </div>
</body>
</html>
