<?php
    // Admin Dashboard: Home for LINGsCARS
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/admin/adminAuthenticate.php");
    if (!checkAdminLogin()) {
        header("Location: /admin/adminLogout.php");
        exit;
    }
    // Check if admin is deleted.
    else {
        $foundAdmin = false;
        $query = "SELECT id, adminName FROM admins WHERE id=" . $_SESSION["adminId"] . ";";

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
        <script type="text/javascript" src="/js/adminFormValidation.js" defer></script>
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
                <h3><i>Full Report for LINGsCARS</i>:</h3>
                
                <?php
                    // Store total no. of transactions and total amount from current month to (current - 11) month.
                    $monthXLabels = array();
                    $monthYTotalTransac = array();
                    $monthYTotalAmount = array();

                    // Store total no. of transactions and total amount from current week to (current - 11) week.
                    $weekXLabels = array();
                    $weekYTotalTransac = array();
                    $weekYTotalAmount = array();

                    // Store total no. of transactions and total amount from current day to (current - 11) day.
                    $dayXLabels = array();
                    $dayYTotalTransac = array();
                    $dayYTotalAmount = array();

                    // Store total transaction and amount in a year (12 months).
                    $yearTransacTotal = 0;
                    $yearAmountTotal = 0;

                    // Store total no. of registration from current month to (current - 11) month.
                    // $monthXLabels = array();
                    $monthYTotalRegister = array();

                    // Store total registration in a year (12 months).
                    $yearRegisterTotal = 0;

                    // Prepare for storing data.
                    $currentDate = strtotime(date("Y-m-d H:i:s"));

                    for ($i = 0; $i < 12; $i++) {
                        // Month stored in format (e.g., Jan 2022).
                        $monthXLabels[$i] = date('M Y', strtotime((-11 + $i) . " month", strtotime(date("Y-m-15"))));
                        // Week stored in format (e.g. 2022-01-12).
                        $weekXLabels[$i] = date('Y-m-d', strtotime((-11 + $i) . " week", $currentDate));
                        // Day stored in format (e.g. 2022-01-12).
                        $dayXLabels[$i] = date('Y-m-d', strtotime((-11 + $i) . " day", $currentDate));
                        
                        $monthYTotalTransac[$i] = $monthYTotalAmount[$i] = 0;
                        $weekYTotalTransac[$i] = $weekYTotalAmount[$i] = 0;
                        $dayYTotalTransac[$i] = $dayYTotalAmount[$i] = 0;

                        $monthYTotalRegister[$i] = 0;
                    }

                    // Try to fetch data from Transactions table.
                    $query = "SELECT transactions.transactionDate, transactions.amount FROM transactions ORDER BY transactions.transactionDate DESC;";

                    $rs = mysqli_query($serverConnect, $query);
                    $totalRecord = 0;

                    if ($rs) {
                        $totalRecord = mysqli_num_rows($rs);
                        while ($record = mysqli_fetch_assoc($rs)) {
                            if (isset($record['transactionDate'])) {
                                // Check month.
                                for ($i = 0; $i < 12; $i++) {
                                    // Same month.
                                    if (
                                        $monthXLabels[$i] == date('M Y', strtotime($record['transactionDate']))
                                    ) {
                                        $monthYTotalTransac[$i]++;

                                        if (isset($record["amount"])) {
                                            $monthYTotalAmount[$i] += $record["amount"];
                                        }
                                        break;
                                    }
                                }

                                // Check week.
                                for ($i = 0; $i < 12; $i++) {
                                    // Less than last day of the week (based on current day).
                                    if (
                                        strtotime($weekXLabels[$i]) >= strtotime(date('Y-m-d', strtotime($record['transactionDate']))) &&
                                        strtotime("-6 day", strtotime($weekXLabels[$i])) <= strtotime(date('Y-m-d', strtotime($record['transactionDate'])))
                                    ) {
                                        $weekYTotalTransac[$i]++;

                                        if (isset($record["amount"])) {
                                            $weekYTotalAmount[$i] += $record["amount"];
                                        }
                                        break;
                                    }
                                }

                                // Check day.
                                for ($i = 0; $i < 12; $i++) {
                                    if (
                                        $dayXLabels[$i] == date('Y-m-d', strtotime($record['transactionDate']))
                                    ) {
                                        $dayYTotalTransac[$i]++;

                                        if (isset($record["amount"])) {
                                            $dayYTotalAmount[$i] += $record["amount"];
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    // Try to fetch data from Members table.
                    $query = "SELECT members.registerDate FROM members ORDER BY registerDate";

                    $rs = mysqli_query($serverConnect, $query);
                    $totalRecord = 0;

                    if ($rs) {
                        $totalRecord = mysqli_num_rows($rs);
                        while ($record = mysqli_fetch_assoc($rs)) {
                            if (isset($record['registerDate'])) {
                                // Check month.
                                for ($i = 0; $i < 12; $i++) {
                                    // Same month.
                                    if (
                                        $monthXLabels[$i] == date('M Y', strtotime($record['registerDate']))
                                    ) {
                                        $monthYTotalRegister[$i]++;
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    // Change label for month & week & day.
                    for ($i = 0; $i < 12; $i++) {
                        $monthXLabels[$i] = date('M', strtotime((-11 + $i) . " month", strtotime(date("Y-m-15"))));
                        $weekXLabels[$i] = date('d', strtotime((-11 + $i) . " week -6 day", $currentDate)) . "-" . date('d', strtotime((-11 + $i) . " week", $currentDate));
                        $dayXLabels[$i] = date('M d', strtotime((-11 + $i) . " day", $currentDate));

                        // Add to total transaction and amount.
                        $yearTransacTotal += $monthYTotalTransac[$i];
                        $yearAmountTotal += $monthYTotalAmount[$i];

                        // Add to total registration.
                        $yearRegisterTotal += $monthYTotalRegister[$i];
                    }
                ?>

                <div class='chart-container'>
                    <div class='data-value'>
                        <div class='data-group'>
                            <span class='overall-data'><?php
                                echo($yearTransacTotal . "/year; " . round($yearTransacTotal / 12, 1) . '/month');
                            ?></span>
                            <span class='data-title'>Total Transaction (last 12 Months)</span>
                        </div>

                        <div class='data-group'>
                            <span class='overall-data'><?php
                                echo("£" . $yearAmountTotal . "/year; £" . round($yearAmountTotal / 12, 2) . '/month');
                            ?></span>
                            <span class='data-title'>Transaction Amount (last 12 Months)</span>
                        </div>
                    </div>
                    
                    <div class='data-value'>
                        <div class='data-group'>
                            <span class='overall-data'><?php
                                echo($yearRegisterTotal . "/year; " . round($yearRegisterTotal / 12, 1) . '/month');
                            ?></span>
                            <span class='data-title'>New Member (last 12 Months)</span>
                        </div>
                    </div>
                    
                    <div class='chart'>
                        <canvas id="monthTransacChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($monthXLabels)) ?>;
                            var yValues = <?php echo(json_encode($monthYTotalTransac)) ?>;

                            new Chart(
                                "monthTransacChart", {
                                    type: "line",
                                    data: {
                                        labels: xValues,
                                        datasets: [{
                                            label: "no. of transaction",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "Total Transaction (Monthly; till current month)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>

                    <div class='chart'>
                        <canvas id="monthAmountChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($monthXLabels)) ?>;
                            var yValues = <?php echo(json_encode($monthYTotalAmount)) ?>;

                            new Chart(
                                "monthAmountChart", {
                                    type: "line",
                                    data: {
                                        labels: xValues,
                                        datasets: [{
                                            label: "transaction amount (£)",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "Transaction Amount (Monthly; till current month)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>
                
                    <div class='chart'>
                        <canvas id="weekTransacChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($weekXLabels)) ?>;
                            var yValues = <?php echo(json_encode($weekYTotalTransac)) ?>;

                            new Chart(
                                "weekTransacChart", {
                                    type: "line",
                                    data: {
                                        labels: xValues,
                                        datasets: [{
                                            label: "no. of transaction",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "Total Transaction (Weekly; 7 days from current day)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>

                    <div class='chart'>
                        <canvas id="weekAmountChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($weekXLabels)) ?>;
                            var yValues = <?php echo(json_encode($weekYTotalAmount)) ?>;

                            new Chart(
                                "weekAmountChart", {
                                    type: "line",
                                    data: {
                                        labels: xValues,
                                        datasets: [{
                                            label: "transaction amount (£)",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "Transaction Amount (Weekly; 7 days from current day)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>
                
                    <div class='chart'>
                        <canvas id="dayTransacChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($dayXLabels)) ?>;
                            var yValues = <?php echo(json_encode($dayYTotalTransac)) ?>;

                            new Chart(
                                "dayTransacChart", {
                                    type: "line",
                                    data: {
                                        labels: xValues,
                                        datasets: [{
                                            label: "no. of transaction",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "Total Transaction (Daily; from current day)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>

                    <div class='chart'>
                        <canvas id="dayAmountChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($dayXLabels)) ?>;
                            var yValues = <?php echo(json_encode($dayYTotalAmount)) ?>;

                            new Chart(
                                "dayAmountChart", {
                                    type: "line",
                                    data: {
                                        label: "transaction amount (£)",
                                        labels: xValues,
                                        datasets: [{
                                            label: "transaction amount (£)",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "Transaction Amount (Daily; from current day)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>

                    <div class='chart'>
                        <canvas id="monthRegisterChart"></canvas>

                        <script>
                            var xValues = <?php echo(json_encode($monthXLabels)) ?>;
                            var yValues = <?php echo(json_encode($monthYTotalRegister)) ?>;

                            new Chart(
                                "monthRegisterChart", {
                                    type: "line",
                                    data: {
                                        labels: xValues,
                                        datasets: [{
                                            label: "no. of registration",
                                            fill: true,
                                            pointRadius: 1,
                                            borderColor: "rgba(255,0,0,0.75)",
                                            data: yValues
                                        }]
                                    },
                                    options: {
                                        title: {
                                            display: true,
                                            text: "New Member (Monthly; till current month)"
                                        }
                                    }
                                }
                            );
                        </script>
                    </div>
                
                    <?php
                        // Try to fetch data from Admins table.
                        $query = "SELECT lastLogin FROM admins ORDER BY lastLogin DESC;";

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
                                            else if ($currentDate - strtotime($record['lastLogin']) < 86400 * 30) {
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
