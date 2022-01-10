 <!-- Admin Login Page for LINGsCARS -->
<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/admin/adminAuthenticate.php");
    // Already logged in, redirect to admin dashboard.
    if (checkAdminLogin()) {
        header("Location: /admin/index.php");
        exit;
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
            $adminId = 0;
            // Make sure Admins table (id, adminName, adminPassword, lastLogin) is already created.
            $query = "SELECT id, adminName, adminPassword FROM Admins WHERE adminName='$adminName';";

            $rs = mysqli_query($serverConnect, $query);
            if ($rs) {
                if ($user = mysqli_fetch_assoc($rs)) {
                    if ($user["adminName"] == $adminName && $user["adminPassword"] == $adminPassword) {
                        $found = true;
                        $adminId = $user["id"];
                    }
                }
            }

            if ($found) {
                // Date stored as YYYY-MM-DD HH:MM:SS. Refer https://www.w3schools.com/php/func_date_date.asp
                $currentDate = date("Y-m-d H:i:s");
                // Record new login date.
                mysqli_query(
                    $serverConnect, "UPDATE Admins SET lastLogin='$currentDate' WHERE adminName='$adminName';"
                );

                $_SESSION["adminId"] = $adminId;
                $_SESSION["adminName"] = $adminName;
                $_SESSION["adminLastActive"] = strtotime($currentDate);

                $adminName = $adminPassword = $adminLoginErr = "";
                // Redirect to admin dashboard after login.
                header("Location: /admin/index.php");
                exit;
            }
            else {
                $adminName = "";
                $adminLoginErr = "* Failed Login! Make sure all inputs are correct!";
            }
        }
    }
    // Close as it's not used anymore.
    mysqli_close($serverConnect);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Login | LINGsCARS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/admin.css">
        <link rel="shortcut icon" href="/favicon.ico">
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
            <span class="error-message">
                <?php
                    echo $adminLoginErr;
                ?>
            </span>
            <form id="admin-login-form" method="post" action="/admin/adminLogin.php">
                <div>
                    <label for="admin-name">
                        Admin Name:
                    </label><br>
                    <input id="admin-name" type="text" name="admin-name" placeholder="Admin Name" value="<?php
                        echo $adminName;
                    ?>">
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
        
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
