<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['voter_id'])) {
    $voter_id = $_GET['voter_id'];

    // Fetch the voter details
    $voter_query = "SELECT * FROM voters WHERE id = $voter_id";
    $voter_result = mysqli_query($conn, $voter_query);
    $voter = mysqli_fetch_assoc($voter_result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_voter'])) {
        // Delete the voter
        $delete_query = "DELETE FROM voters WHERE id = $voter_id";
        if (mysqli_query($conn, $delete_query)) {
            header("Location: manage_voters.php?room_id=" . $voter['room_id']);
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Update voter details
        $username = $_POST['username'];
        $is_candidate = isset($_POST['is_candidate']) ? 1 : 0;

        $update_query = "UPDATE voters SET username = '$username', is_candidate = $is_candidate WHERE id = $voter_id";
        if (mysqli_query($conn, $update_query)) {
            header("Location: manage_voters.php?room_id=" . $voter['room_id']);
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Voter</title>
    <link rel="stylesheet" href="../css/style_edit_voter.css">
</head>
<body>
    <div class="container">
        <h2 class="center">Edit Voter: <?php echo htmlspecialchars($voter['username']); ?></h2>

        <!-- Edit Voter Form -->
        <form action="edit_voter.php?voter_id=<?php echo $voter_id; ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($voter['username']); ?>" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label for="is_candidate">Is Candidate?</label>
                <input type="checkbox" id="is_candidate" name="is_candidate" <?php echo $voter['is_candidate'] == 1 ? 'checked' : ''; ?>>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Save Changes</button>
            </div>
        </form>

        <!-- Delete Voter Button -->
        <form action="edit_voter.php?voter_id=<?php echo $voter_id; ?>" method="post" style="display:inline;">
            <input type="hidden" name="delete_voter" value="1">
            <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this voter?')">Delete Voter</button>
        </form>

        <!-- Back to Voters Button -->
        <a href="manage_voters.php?room_id=<?php echo $voter['room_id']; ?>" class="btn-secondary">Back to Voters</a>
    </div>
</body>
</html>
