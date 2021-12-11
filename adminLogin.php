<!DOCTYPE html>
 <!-- Admin Login Page for LINGsCARS -->
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

        <?php
            include_once('dbConnection.php');

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
                    $stmt = $conn->prepare("SELECT * FROM Admins");
                    $stmt->execute();
                    $users = $stmt->fetchAll();
                    $found = false;

                    foreach($users as $user) {
                        if ($user["adminName"] == $adminName && $user["password"] == $adminPassword) {
                            $found = true;
                            break;
                        }
                    }

                    if ($found) {
                        $adminName = $adminPassword = $adminLoginErr = "";
                        header("Location: index.html");
                    }
                    else {
                        $adminName = "";
                        $adminLoginErr = "* Failed Login! Make sure all inputs are correct!";
                    }
                }
            }
        ?>
       
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
