 <!-- Admin Login Page for LINGsCARS -->
<?php
    require_once("./dbConnection.php");
    require_once("./adminAuthenticate.php");
    // Already logged in, redirect to admin dashboard.
    if (checkAdminLogin()) {
        header("Location: ./adminDashboard.php");
    }

    $adminName = $adminPassword = $adminLoginErr = "";

    function testInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $adminName = testInput($_POST["admin-name"]);
        $adminPassword = testInput($_POST["admin-password"]);

        if (empty($adminName)) {
            $adminLoginErr = "* Enter Admin Name!";
        }
        else if (empty($adminPassword)) {
            $adminLoginErr = "* Enter Admin Password!";
        }
        else {
            $found = false;
            // Make sure Admins table (id, adminName, password) is already created.
            $query = "SELECT * FROM Admins WHERE adminName='$adminName'";

            $rs = mysqli_query($serverConnect, $query);
            if ($rs) {
                if ($user = mysqli_fetch_assoc($rs)) {
                    if ($user["adminName"] == $adminName && $user["password"] == $adminPassword) {
                        $found = true;
                    }
                }
            }

            if ($found) {
                $_SESSION["adminLoggedIn"] = "true";
                $_SESSION["adminName"] = $adminName;

                $adminName = $adminPassword = $adminLoginErr = "";
                // Redirect to admin dashboard after login.
                header("Location: ./adminDashboard.php");
            }
            else {
                $adminName = "";
                $adminLoginErr = "* Failed Login! Make sure all inputs are correct!";
            }
        }
    }
    // Close after use.
    mysqli_close($serverConnect);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Login | LINGsCARS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="stylesheet" href="./css/admin.css">
        <link rel="shortcut icon" href="./favicon.ico">
    </head>

    <body>
        <header>
            <p>
                LINGsCARS Admin Login Page
            </p>
        </header>

        <main>
            <h2>
                Admin Login
            </h2>
            <span class="admin-login-err">
                <?php
                    echo $adminLoginErr;
                ?>
            </span>
            <form id="admin-login-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div>
                    <label for="admin-name">
                        Admin Name:
                    </label><br>
                    <input id="admin-name" type="text" name="admin-name" placeholder="Admin Name" value="<?php echo $adminName;?>">
                </div>

                <div>
                    <label for="admin-password">
                        Password:
                    </label><br>
                    <input id="admin-password" type="password" name="admin-password" placeholder="Password">
                </div>

                <div>
                    <button type="submit">
                        Log In Admin
                    </button>
                </div>
            </form>
        </main>
        
        <hr>
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
