<!-- Admin Dashboard for LINGsCARS -->
<?php
    require_once("./dbConnection.php");
    require_once("./adminAuthenticate.php");
    if (!checkAdminLogin()) {
        header("Location: ./adminLogin.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Dashboard | LINGsCARS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="stylesheet" href="./css/admin.css">
        <link rel="shortcut icon" href="./favicon.ico">
    </head>

    <body>
        <header>
            <p>
                LINGsCARS Admin Dashboard
            </p>
        </header>

        <main>
            <h2>
                Logged in as: <i>
                    <?php
                        if (isset($_SESSION["adminName"])) {
                            echo $_SESSION["adminName"];
                        }
                    ?>
                </i>
            </h2>

            <a href="./adminLogout.php">
                Log Out
            </a>
        </main>
        
        <hr>
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
