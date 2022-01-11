<?php
    // Authenticate Admin Login
    // require_once("./dbConnection.php");
    session_start();
    date_default_timezone_set("Asia/Kuala_Lumpur");

    // Return true if admin has logged in.
    function checkAdminLogin() {
        $expireMin = 30;

        // Confirm logged in.
        if (
            !isset($_SESSION["adminId"]) || $_SESSION["adminId"] < 0 ||
            !isset($_SESSION["adminName"]) || empty($_SESSION["adminName"])
        ) {
            return false;
        }
        // Check last active time.
        else if (
            !isset($_SESSION["adminLastActive"]) ||
            strtotime(date("Y-m-d H:i:s")) - $_SESSION["adminLastActive"] > ($expireMin * 60)
        ) {
            return false;
        }

        // Reset last active time.
        $_SESSION["adminLastActive"] = strtotime(date("Y-m-d H:i:s"));
        return true;
    }
?>
