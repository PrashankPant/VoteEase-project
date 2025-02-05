<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['voter_id'])) {
    $voter_id = $_GET['voter_id'];

    // Fetch the candidate details
    $candidate_query = "SELECT * FROM voters WHERE id = $voter_id";
    $candidate_result = mysqli_query($conn, $candidate_query);
    $candidate = mysqli_fetch_assoc($candidate_result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_candidate'])) {
        // Delete the candidate
        $delete_query = "DELETE FROM voters WHERE id = $voter_id";
        if (mysqli_query($conn, $delete_query)) {
            header("Location: manage_candidate.php?room_id=" . $candidate['room_id']);
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        // Update candidate details
        $username = $_POST['username'];
        $is_candidate = isset($_POST['is_candidate']) ? 1 : 0;

        $update_query = "UPDATE voters SET username = '$username', is_candidate = $is_candidate WHERE id = $voter_id";
        if (mysqli_query($conn, $update_query)) {
            header("Location: manage_candidate.php?room_id=" . $candidate['room_id']);
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
    <title>Edit Candidate</title>
    <link rel="stylesheet" href="../css/style_edit_candidate.css">
</head>
<body>
    <div class="container">
        <h2 class="center">Edit Candidate: <?php echo htmlspecialchars($candidate['username']); ?></h2>

        <!-- Edit Candidate Form -->
        <form action="edit_candidate.php?voter_id=<?php echo $voter_id; ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($candidate['username']); ?>" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label for="is_candidate">Is Candidate?</label>
                <input type="checkbox" id="is_candidate" name="is_candidate" <?php echo $candidate['is_candidate'] == 1 ? 'checked' : ''; ?>>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Save Changes</button>
            </div>
        </form>

        <!-- Delete Candidate Button -->
        <form action="edit_candidate.php?voter_id=<?php echo $voter_id; ?>" method="post" style="display:inline;">
            <input type="hidden" name="delete_candidate" value="1">
            <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to remove this candidate?')">Remove Candidate</button>
        </form>

        <!-- Back to Vote Room Button -->
        <div class="actions">
            <a href="manage_candidate.php?room_id=<?php echo $candidate['room_id']; ?>">
                <button class="btn-secondary">Back to Candidates</button>
            </a>
        </div>
    </div>
</body>
</html>
