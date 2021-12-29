<!-- Admin Dashboard: Home for LINGsCARS -->
<?php
    require_once("./dbConnection.php");
    require_once("./adminAuthenticate.php");
    if (!checkAdminLogin()) {
        header("Location: ./adminLogout.php");
        exit;
    }
    // Check if admin is deleted.
    else {
        $foundAdmin = false;
        $query = "SELECT id, adminName FROM Admins WHERE id=" . $_SESSION["adminId"] . ";";

        $rs = mysqli_query($serverConnect, $query);
        if ($rs) {
            if ($user = mysqli_fetch_assoc($rs)) {
                if ($user["id"] == $_SESSION["adminId"]) {
                    $foundAdmin = true;
                    $_SESSION["adminName"] = $user["adminName"];
                }
            }
        }

        if (!$foundAdmin) {
            header("Location: ./adminLogout.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Dashboard: Home | LINGsCARS</title>
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
                    <a href="./adminDashboard.php" class="active">Home</a>
                </li>
                <li>
                    <a href="./manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="./manageProduct.php">Manage Product</a>
                </li>
                <li>
                    <a href="./manageTransaction.php">Manage Transaction</a>
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
                Logged in: <i>
                    <?php
                        if (isset($_SESSION["adminName"])) {
                            echo $_SESSION["adminName"];
                        }
                        if (isset($_SESSION["adminLastActive"])) {
                            echo "</i>; Active: <i>" . date("Y-m-d H:i", $_SESSION["adminLastActive"]);
                        }
                    ?>
                </i>
            </h2>

            
        </main>
        
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
