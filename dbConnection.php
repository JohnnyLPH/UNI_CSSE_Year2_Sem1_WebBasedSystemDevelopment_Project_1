<?php
    // For DB Connection
    $dbHost = "localhost:3306";

    $dbUsername = "wbsd";
    $dbPassword = "G03abc-abc03G";

    // Make sure the DB is already created.
    $dbName = "LINGsCARS";

    // Use mysqli_close($serverConnect) to terminate connection after use.
    $serverConnect = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if (!$serverConnect) {
        echo "-Error Connecting to DB Server!<br>";
        // trigger_error("-Error Connecting to DB Server!", E_USER_ERROR);
        exit;
    }

    $dbSelect = mysqli_select_db($serverConnect, $dbName);
    if (!$dbSelect) {
        echo "Error Selecting DB!<br>";
        // trigger_error("-Error Selecting DB!", E_USER_ERROR);
        exit;
    }
?>
