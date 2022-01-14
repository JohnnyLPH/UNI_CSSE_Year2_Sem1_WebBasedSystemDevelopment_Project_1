<?php
    if (session_id() == "") {
        session_start();
    }
    session_destroy();
    // Redirect back to admin login page.
    header("Location: /admin/adminLogin.php");
    exit;
?>
