<?php
session_start();

// Redirect users if they are already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['user_type'] == 'voter') {
        header("Location: voter/dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Voting System</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function toggleSignup() {
            const userType = document.getElementById('user_type').value;
            const signupText = document.getElementById('signup_text');
            const roomFields = document.getElementById('room_fields');
            const roomNameInput = document.getElementById('room_name');
            const roomIdInput = document.getElementById('room_id');
            const loginForm = document.getElementById('login_form');

            // Adjust form behavior based on user type
            if (userType === 'admin') {
                signupText.style.display = 'block';
                roomFields.style.display = 'none';
                roomNameInput.removeAttribute('required'); // Remove required for room name
                roomIdInput.removeAttribute('required');   // Remove required for room ID
                loginForm.action = 'admin_login.php';
            } else {
                signupText.style.display = 'none';
                roomFields.style.display = 'block';
                roomNameInput.setAttribute('required', 'required'); // Add required for room name
                roomIdInput.setAttribute('required', 'required');   // Add required for room ID
                loginForm.action = 'voter_login.php';
            }
        }

        // Ensure form initializes correctly
        window.onload = toggleSignup;
    </script>
</head>
<body>
    <div class="main-container">
        <div class="left-frame">
            <h1>Welcome to VoteEase</h1>
            <p>Your seamless and secure online voting platform, designed for simplicity and reliability. Join us to make voting easier and safer.</p>
        </div>
        <div class="right-frame">
            <h2>Login</h2>
            <form id="login_form" action="admin_login.php" method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="form-group">
                    <label>Login As:</label>
                    <select name="user_type" id="user_type" required onchange="toggleSignup()">
                        <option value="admin">Admin</option>
                        <option value="voter">Voter</option>
                    </select>
                </div>
                <div id="room_fields" style="display: none;">
                    <div class="form-group">
                        <label>Room Name:</label>
                        <input type="text" name="room_name" id="room_name" placeholder="Enter the room name">
                    </div>
                    <div class="form-group">
                        <label>Room ID:</label>
                        <input type="number" name="room_id" id="room_id" placeholder="Enter the room ID">
                    </div>
                </div>
                <button type="submit">Login</button>
                <div id="signup_text" class="signup-text" style="display: none;">
                    New User? <a href="admin_signup.php">Sign Up as Admin</a>
                </div>
            </form>
            <?php if (isset($_GET['error'])): ?>
                <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
