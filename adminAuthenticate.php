<!-- Authenticate Admin Login -->
<?php
    session_start();

    // Return true if admin has logged in.
    function checkAdminLogin() {
        return (
            isset($_SESSION["adminLoggedIn"]) &&
            !empty($_SESSION["adminLoggedIn"]) &&
            $_SESSION["adminLoggedIn"] == "true" &&
            isset($_SESSION["adminName"]) &&
            !empty($_SESSION["adminName"])
        );
    }
?>
