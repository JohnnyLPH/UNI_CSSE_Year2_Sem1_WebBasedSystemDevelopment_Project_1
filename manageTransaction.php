<!-- Admin Dashboard: Manage Transaction for LINGsCARS -->
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
        <title>Admin Dashboard: Manage Transaction | LINGsCARS</title>
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

        <nav class="fixed_nav_bar">
            <ul>
                <li>
                    <a href="./adminDashboard.php">Home</a>
                </li>
                <li>
                    <a href="./manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="./manageProduct.php">Manage Product</a>
                </li>
                <li>
                    <a href="./manageTransaction.php" class="active">Manage Transaction</a>
                </li>
                <li>
                    <a href="./manageAdmin.php">Manage Admin</a>
                </li>
                <li>
                    <a href="./adminLogout.php">Log Out</a>
                </li>
            </ul>
        </nav>

        <main>
            <h2>
                Manage Transaction
            </h2>

            
        </main>
        
        <hr>
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
