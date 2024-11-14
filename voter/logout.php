<?php
session_start();
session_unset();
session_destroy();

// Redirect to the main login or sign-up page
header("Location: ../index.php");
exit();
?>
