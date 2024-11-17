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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    for ($i = 1; $i <= $num_voters; $i++) {
        $username = mysqli_real_escape_string($conn, $_POST["username_$i"]);
        $password = mysqli_real_escape_string($conn, $_POST["password_$i"]);
        $hashed_password = password_hash($password,PASSWORD_DEFAULT); // Hashing the password
        $is_candidate = isset($_POST["is_candidate_$i"]) ? 1 : 0;

        // Insert voter into the database
        $query = "INSERT INTO voters (username, password, is_candidate, room_id)
                  VALUES ('$username', '$hashed_password', $is_candidate, $room_id)";
        mysqli_query($conn, $query);
    }

    // Redirect to the admin dashboard
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Voters</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Create Voters</h2>
        <form method="POST">
            <?php for ($i = 1; $i <= $num_voters; $i++): ?>
                <div class="form-group">
                    <label>Voter <?php echo $i; ?> Username:</label>
                    <input type="text" name="username_<?php echo $i; ?>" required>

                    <label>Voter <?php echo $i; ?> Password:</label>
                    <input type="password" name="password_<?php echo $i; ?>" required>

                    <label>
                        <input type="checkbox" name="is_candidate_<?php echo $i; ?>">
                        Is Candidate
                    </label>
                </div>
            <?php endfor; ?>
            <button type="submit">OK</button>
        </form>
    </div>
</body>
</html>
