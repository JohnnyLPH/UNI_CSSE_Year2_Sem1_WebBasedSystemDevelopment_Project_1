<?php
    session_start();
    session_destroy();
    // Redirect back to admin login page.
    header("Location: ./adminLogin.php");
?>
