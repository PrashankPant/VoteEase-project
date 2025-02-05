<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$room_id = $_GET['room_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password before storing (even though you requested basic methods, hashing is important for security)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new voter into the database
    $insert_query = "INSERT INTO voters (username, password, room_id, is_candidate) VALUES ('$username', '$hashed_password', $room_id, 0)";
    if (mysqli_query($conn, $insert_query)) {
        header("Location: manage_voters.php?room_id=" . $room_id);
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Voter</title>
    <link rel="stylesheet" href="../css/style_add_voter.css">
</head>
<body>
    <div class="container">
        <h2 class="center">Add New Voter</h2>

        <!-- Add Voter Form -->
        <form action="add_new_voter.php?room_id=<?php echo $room_id; ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Add Voter</button>
            </div>
        </form>

        <!-- Back to Manage Voters Button -->
        <div class="actions">
            <a href="manage_voters.php?room_id=<?php echo $room_id; ?>">
                <button class="btn-secondary">Back to Voters</button>
            </a>
        </div>
    </div>
</body>
</html>
