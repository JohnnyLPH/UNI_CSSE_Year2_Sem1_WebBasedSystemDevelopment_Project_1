<!-- Authenticate Admin Login -->
<?php
    session_start();
    date_default_timezone_set("Asia/Kuala_Lumpur");

    // Return true if admin has logged in.
    function checkAdminLogin() {
        $expireMin = 10;

        // Confirm logged in.
        if (
            !isset($_SESSION["adminLoggedIn"]) || empty($_SESSION["adminLoggedIn"]) || 
            $_SESSION["adminLoggedIn"] != "true" || !isset($_SESSION["adminName"]) || empty($_SESSION["adminName"])
        ) {
            return false;
        }
        // Check last active time.
        else if (
            !isset($_SESSION["lastActive"]) || 
            strtotime(date("Y-m-d H:i:s")) - $_SESSION["lastActive"] > ($expireMin * 60)
        ) {
            return false;
        }
        // Reset last active time.
        $_SESSION["lastActive"] = strtotime(date("Y-m-d H:i:s"));
        return true;
    }
?>
