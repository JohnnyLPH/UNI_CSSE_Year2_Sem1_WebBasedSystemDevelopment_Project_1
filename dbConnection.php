<!-- For DB Connection -->
<?php
    $conn = "";
    
    try {
        $serverName = "localhost:3306";
        $dbName = "LINGsCARS";
        $username = "root";
        $password = "";
    
        $conn = new PDO(
            "mysql:host=$serverName; dbname=$dbName",
            $username, $password
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $err) {
        echo "-Error: Failed DB Connection. " . $err->getMessage();
    }
?>
