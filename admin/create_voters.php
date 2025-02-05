<?php
session_start();
require_once('../config/database.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$room_id = $_GET['room_id'];
$num_voters = $_GET['num_voters'];

// Fetch the number of candidates required for this room
$query = "SELECT num_candidates FROM vote_rooms WHERE id = $room_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$num_candidates_required = $row['num_candidates'];

$error = ""; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_candidates = 0;

    // Count the number of selected candidates
    for ($i = 1; $i <= $num_voters; $i++) {
        if (isset($_POST["is_candidate_$i"])) {
            $selected_candidates++;
        }
    }

    // Validate the number of selected candidates
    if ($selected_candidates != $num_candidates_required) {
        $error = "You must select exactly $num_candidates_required candidates.";
    } else {
        // Insert voters into the database
        for ($i = 1; $i <= $num_voters; $i++) {
            $username = mysqli_real_escape_string($conn, $_POST["username_$i"]);
            $password = mysqli_real_escape_string($conn, $_POST["password_$i"]);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashing the password
            $is_candidate = isset($_POST["is_candidate_$i"]) ? 1 : 0;

            $query = "INSERT INTO voters (username, password, is_candidate, room_id)
                      VALUES ('$username', '$hashed_password', $is_candidate, $room_id)";
            mysqli_query($conn, $query);
        }

        // Redirect to the admin dashboard
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Voters</title>
    <link rel="stylesheet" href="../css/style_create_voters.css">
</head>
<body>
    <div class="container">
        <h2>Create Voters</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST">
            <?php for ($i = 1; $i <= $num_voters; $i++): ?>
                <div class="form-group">
                    <label>Voter <?php echo $i; ?> Username:</label>
                    <input type="text" name="username_<?php echo $i; ?>" 
                           value="<?php echo isset($_POST["username_$i"]) ? htmlspecialchars($_POST["username_$i"]) : ''; ?>" 
                           required>

                    <label>Voter <?php echo $i; ?> Password:</label>
                    <input type="password" name="password_<?php echo $i; ?>" 
                           value="<?php echo isset($_POST["password_$i"]) ? htmlspecialchars($_POST["password_$i"]) : ''; ?>" 
                           required>

                    <div class="inline-checkbox">
                        <input type="checkbox" name="is_candidate_<?php echo $i; ?>" 
                               <?php echo isset($_POST["is_candidate_$i"]) ? 'checked' : ''; ?>>
                        <label>Is Candidate</label>
                    </div>
                </div>
            <?php endfor; ?>
            <button type="submit">OK</button>
        </form>
    </div>
</body>
</html>
