<!-- Admin Dashboard: Home for LINGsCARS -->
<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/admin/adminAuthenticate.php");
    if (!checkAdminLogin()) {
        header("Location: /admin/adminLogout.php");
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
            header("Location: /admin/adminLogout.php");
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
        <link rel="stylesheet" href="/css/admin.css">
        <link rel="shortcut icon" href="/favicon.ico">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
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
                    <a href="/admin/index.php" class="active">Home</a>
                </li>
                <li>
                    <a href="/admin/manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="/admin/manageVehicle.php">Manage Vehicle</a>
                </li>
                <li>
                    <a href="/admin/manageOrder.php">Manage Order</a>
                </li>
                <li>
                    <a href="/admin/manageTransaction.php">Manage Transaction</a>
                </li>
                <li>
                    <a href="/admin/manageAdmin.php">Manage Admin</a>
                </li>
                <li>
                    <a href="/admin/adminLogout.php">Log Out</a>
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

            <div class="dashboard-content">
                <div class='chart-container'>
                    <?php
                        // Try to fetch data from Admins table.
                        $query = "SELECT lastLogin FROM Admins ORDER BY lastLogin DESC;";

                        $rs = mysqli_query($serverConnect, $query);
                        $currentDate = strtotime(date("Y-m-d H:i:s"));
                        $addToYValues = array(0, 0, 0, 0);
                    ?>

                    <?php if ($rs): ?>
                        <div class='chart'>
                            <canvas id="adminChart"></canvas>

                            <script>
                                var xValues = ["Within Day", "Within Week (7d)", "Within Month (30d)", "Over Month (30d)"];

                                var yValues = [];

                                <?php while ($record = mysqli_fetch_assoc($rs)): ?>
                                    <?php
                                        if (isset($record['lastLogin']) && !empty($record['lastLogin'])) {
                                            if ($currentDate - strtotime($record['lastLogin']) < 86400) {
                                                $addToYValues[0]++;
                                                $addToYValues[1]++;
                                                $addToYValues[2]++;
                                            }
                                            else if ($currentDate - strtotime($record['lastLogin']) < 86400 * 7) {
                                                $addToYValues[1]++;
                                                $addToYValues[2]++;
                                            }
                                            else if ($currentDate - strtotime($record['lastLogin']) < 86400 * 7) {
                                                $addToYValues[2]++;
                                            }
                                            else {
                                                $addToYValues[3]++;
                                            }
                                        }
                                        else {
                                            $addToYValues[3]++;
                                        }
                                    ?>
                                <?php endwhile; ?>

                                <?php for ($y = 0; $y < 4; $y++): ?>
                                    yValues[<?php echo $y; ?>] = <?php echo $addToYValues[$y]; ?>;
                                <?php endfor; ?>


                                var barColors = [
                                "#b91d47",
                                "#00aba9",
                                "#2b5797",
                                "#e8c3b9",
                                ];

                                new Chart(
                                    "adminChart", {
                                        type: "doughnut",
                                        data: {
                                            labels: xValues,
                                            datasets: [{
                                                backgroundColor: barColors,
                                                data: yValues
                                            }]
                                        },
                                        options: {
                                            title: {
                                                display: true,
                                                text: "Admin Last Login"
                                            }
                                        }
                                    }
                                );
                            </script>
                        </div>
                    <?php endif; ?>

                    <?php
                        // Try to fetch data from Admins table.
                        $query = "SELECT lastLogin FROM Admins ORDER BY lastLogin DESC;";

                        $rs = mysqli_query($serverConnect, $query);
                        $currentDate = strtotime(date("Y-m-d H:i:s"));
                        $addToYValues = array(0, 0, 0, 0);
                    ?>
                </div>
            </div>
            
        </main>
        
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
