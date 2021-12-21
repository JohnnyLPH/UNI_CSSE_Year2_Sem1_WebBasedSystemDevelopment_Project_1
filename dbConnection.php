<!-- For DB Connection -->
<?php
    $dbHost = "localhost:3306";

    $dbUsername = "root";
    $dbPassword = "";

    // Make sure the DB is already created.
    $dbName = "LINGsCARS";

    // Use mysqli_close($serverConnect) to terminate connection after use.
    $serverConnect = mysqli_connect($dbHost, $dbUsername, $dbPassword);
    if (!$serverConnect) {
        echo "-Error Connecting to DB Server!<br>";
        trigger_error(mysqli_error(), E_USER_ERROR);
    }

    $dbSelect = mysqli_select_db($serverConnect, $dbName);
    if (!$dbSelect) {
        echo "Error Selecting DB!<br>";
        trigger_error(mysqli_error(), E_USER_ERROR); 
    }
?>
