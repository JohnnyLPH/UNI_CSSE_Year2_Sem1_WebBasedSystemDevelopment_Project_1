<?php
    // Admin Dashboard: Manage Transaction for LINGsCARS
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

    function testInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $queryString = array();

    if (isset($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryString);
    }

    $manageMode = (isset($queryString['manage-mode'])) ? $queryString['manage-mode']: "";
    $passChecking = false;

    $wordToSearch = "";

    $viewTransacMsg = "";
    $allowViewTransac = false;
    
    if (!empty($manageMode)) {
        // Search Transaction
        if ($manageMode == "search-transaction") {
            $wordToSearch = (isset($queryString['word-to-search'])) ? testInput($queryString['word-to-search']): "";
        }
        // View Transaction
        else if ($manageMode == "view-transaction") {
            $transacId = (isset($queryString['transaction-id'])) ? testInput($queryString['transaction-id']): "";
            
            // Check if the transaction is allowed to be viewed.
            if (!empty($transacId) && is_numeric($transacId)) {
                $query = "SELECT id FROM transactions WHERE id=$transacId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($transac = mysqli_fetch_assoc($rs)) {
                        // Allow to view.
                        $allowViewTransac = true;
                    }
                }
            }

            if (!$allowViewTransac) {
                $viewTransacMsg = "* You are not allowed to view the selected Transaction!";
            }
        }
        // Invalid Mode
        else {
            $manageMode = "";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Dashboard: Manage Transaction | LINGsCARS</title>
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
                    <a href="/admin/index.php">Home</a>
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
                    <a href="/admin/manageTransaction.php" class="active">Manage Transaction</a>
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
                Manage Transaction
            </h2>

            <div class="manage-section">
                <?php if (isset($manageMode) && !empty($manageMode)): ?>
                    <!-- View Summary -->
                    <?php if ($manageMode == "view-summary"): ?>
                        <h3><i>Transaction Summary</i>:</h3>
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
                            }

                            // Try to fetch data from Transactions table.
                            $query = "SELECT transactions.transactionDate, transactions.creditCard FROM transactions ORDER BY transactions.transactionDate DESC;";

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

                                                if (isset($record["creditCard"]) && isset(json_decode($record["creditCard"], true)['paymentAmount'])) {
                                                    $monthYTotalAmount[$i] += json_decode($record["creditCard"], true)['paymentAmount'];
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

                                                if (isset($record["creditCard"]) && isset(json_decode($record["creditCard"], true)['paymentAmount'])) {
                                                    $weekYTotalAmount[$i] += json_decode($record["creditCard"], true)['paymentAmount'];
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

                                                if (isset($record["creditCard"]) && isset(json_decode($record["creditCard"], true)['paymentAmount'])) {
                                                    $dayYTotalAmount[$i] += json_decode($record["creditCard"], true)['paymentAmount'];
                                                }
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
                            }
                        ?>

                        <div class='chart-container'>
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
                        </div>

                        <?php
                            $lastPage = "javascript:history.go(-1)";
                            if (isset($_SERVER['HTTP_REFERER'])) {
                                $lastPage = $_SERVER['HTTP_REFERER'];
                            }
                        ?>

                        <form method='get' action='<?php
                            echo((isset($lastPage) && !empty($lastPage)) ? $lastPage: "/admin/manageVehicle.php");
                        ?>'>
                            <button>Return Previous Page</button>
                        </form>
                    <!-- View Transaction -->
                    <?php elseif ($manageMode == "view-transaction"): ?>
                        <h3>View <i>Transaction ID <?php
                            echo((isset($transacId)) ? testInput($transacId): "");
                        ?></i>:</h3>

                        <?php if (isset($viewTransacMsg) && !empty($viewTransacMsg)): ?>
                            <?php if (!$allowViewTransac): ?>
                                <span class='error-message'>
                                    <?php echo($viewTransacMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowViewTransac): ?>
                            <?php
                                $query = "SELECT transactions.id, transactions.memberId, members.email, transactions.carId, cars.carModel, brands.brandName, transactions.orderId, orders.orderStatus, orders.confirmDate, transactions.transactionDate, transactions.creditCard, transactions.receipt
                                FROM transactions
                                INNER JOIN members ON transactions.memberId = members.id
                                INNER JOIN cars ON transactions.carId = cars.id
                                INNER JOIN brands ON cars.brandId = brands.id
                                INNER JOIN orders ON transactions.orderId = orders.id
                                WHERE transactions.id=$transacId;";

                                $rs = mysqli_query($serverConnect, $query);
                            ?>

                            <?php if ($rs): ?>
                                <?php if ($record = mysqli_fetch_assoc($rs)): ?>
                                    <div class='view-content'>
                                        <table>
                                            <tr>
                                                <td>Transaction ID</td>
                                                <td>
                                                    <?php echo((isset($record["id"])) ? $record["id"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Member ID</td>
                                                <td>
                                                    <form method='get' action='/admin/manageMember.php'>
                                                        <input type='hidden' name='manage-mode' value='view-member'>
                                                        <input type='hidden' name='member-id' value='<?php
                                                            echo((isset($record["memberId"])) ? $record["memberId"]: "");
                                                        ?>'>

                                                        <button><?php
                                                            echo((isset($record["memberId"])) ? $record["memberId"]: "-");
                                                        ?></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Member Email</td>
                                                <td>
                                                    <?php echo((isset($record["email"])) ? $record["email"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Car ID</td>
                                                <td>
                                                    <form method='get' action='/admin/manageVehicle.php'>
                                                        <input type='hidden' name='manage-mode' value='view-car'>
                                                        <input type='hidden' name='car-id' value='<?php
                                                            echo((isset($record["carId"])) ? $record["carId"]: "");
                                                        ?>'>

                                                        <button><?php
                                                            echo((isset($record["carId"])) ? $record["carId"]: "-");
                                                        ?></button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Car Brand</td>
                                                <td>
                                                    <?php echo((isset($record["brandName"])) ? $record["brandName"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Car Model</td>
                                                <td>
                                                    <?php echo((isset($record["carModel"])) ? $record["carModel"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Order ID</td>
                                                <td>
                                                    <form method='get' action='/admin/manageOrder.php'>
                                                        <input type='hidden' name='manage-mode' value='view-order'>
                                                        <input type='hidden' name='order-id' value='<?php
                                                            echo((isset($record["orderId"])) ? $record["orderId"]: "");
                                                        ?>'>

                                                        <button><?php
                                                            echo((isset($record["orderId"])) ? $record["orderId"]: "-");
                                                        ?></button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Order Status</td>
                                                <td>
                                                    <?php echo((isset($record["orderStatus"])) ? $record["orderStatus"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Order Date</td>
                                                <td>
                                                    <?php echo((isset($record["confirmDate"])) ? $record["confirmDate"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Transaction Date</td>
                                                <td>
                                                    <?php echo((isset($record["transactionDate"])) ? $record["transactionDate"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Credit Card No.</td>
                                                <td>
                                                    <?php
                                                        echo(
                                                            (isset($record["creditCard"]) && isset(json_decode($record["creditCard"], true)['cardNo'])) ?
                                                            json_decode($record["creditCard"], true)['cardNo']:
                                                            "-"
                                                        );
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Amount (£)</td>
                                                <td>
                                                    <?php
                                                        echo(
                                                            (isset($record["creditCard"]) && isset(json_decode($record["creditCard"], true)['paymentAmount'])) ?
                                                            json_decode($record["creditCard"], true)["paymentAmount"]:
                                                            "-"
                                                        );
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Receipt</td>
                                                <td>
                                                    <?php echo((isset($record["receipt"])) ? $record["receipt"]: "-"); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <?php
                                        $lastPage = "javascript:history.go(-1)";
                                        if (isset($_SERVER['HTTP_REFERER'])) {
                                            $lastPage = $_SERVER['HTTP_REFERER'];
                                        }
                                    ?>

                                    <form method='get' action='<?php
                                        echo((isset($lastPage) && !empty($lastPage)) ? $lastPage: "/admin/manageVehicle.php");
                                    ?>'>
                                        <button>Return Previous Page</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (
                    (isset($manageMode) && empty($manageMode)) ||
                    $passChecking ||
                    (isset($manageMode) && $manageMode == "search-transaction") ||
                    (isset($manageMode) && $manageMode == "view-transaction" && !$allowViewTransac)
                ): ?>
                    <form id='cancel-search-form' method='get' action='/admin/manageTransaction.php'></form>

                    <form id='manage-search-form' method='get' action='/admin/manageTransaction.php' onsubmit="return searchWordValidation();">
                        <input type='hidden' name='manage-mode' value='search-transaction'>
                    </form>

                    <form id='manage-summary-form' method='get' action='/admin/manageTransaction.php'>
                        <input type='hidden' name='manage-mode' value='view-summary'>
                    </form>
                
                    <div class='button-section'>
                        <input id='word-to-search' form='manage-search-form' type='text' name='word-to-search' placeholder='Enter Transaction/Member/Car/Order ID' value='<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? testInput($wordToSearch): "");
                        ?>' minlength="1" maxlength="100" required>
                        
                        <button form='manage-search-form' class='small-button positive-button'>Search</button>

                        <button form='cancel-search-form' class='small-button negative-button'<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? "": " disabled");
                        ?>>Reset</button>

                        <button form='manage-summary-form' class='small-button'>Summary</button>
                    </div>
                    
                    <h3>Found Transactions:</h3>
                    <table class="db-table">
                        <thead>
                            <!-- 7 Columns -->
                            <tr>
                                <th>Transaction ID</th>
                                <th>Member ID</th>
                                <th>Car ID</th>
                                <th>Order ID</th>
                                <th>Transaction Date</th>
                                <th>Amount (£)</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Select from Transactions table.
                                $query = "SELECT transactions.id, transactions.memberId, transactions.carId, transactions.orderId, transactions.transactionDate, transactions.creditCard FROM transactions" .
                                (
                                    (isset($wordToSearch) && !empty($wordToSearch)) ?
                                    " WHERE transactions.id LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR transactions.memberId LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR transactions.carId LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR transactions.orderId LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%'" : ""
                                ) .
                                " ORDER BY transactions.transactionDate DESC LIMIT 25;";
                                
                                $rs = mysqli_query($serverConnect, $query);
                                $recordCount = 0;
                            ?>
                            
                            <?php if ($rs): ?>
                                <?php while ($transac = mysqli_fetch_assoc($rs)): ?>
                                    <?php $recordCount++; ?>

                                    <tr>
                                        <td class='center-text'>
                                            <?php echo((isset($transac["id"])) ? $transac["id"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($transac["memberId"])) ? $transac["memberId"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($transac["carId"])) ? $transac["carId"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($transac["orderId"])) ? $transac["orderId"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($transac["transactionDate"])) ? $transac["transactionDate"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php
                                                echo(
                                                    (isset($transac["creditCard"]) && isset(json_decode($transac["creditCard"], true)['paymentAmount'])) ?
                                                    json_decode($transac["creditCard"], true)['paymentAmount']:
                                                    "-"
                                                );
                                            ?>
                                        </td>

                                        <td>
                                            <form method='get' action='/admin/manageTransaction.php'>
                                                <input type='hidden' name='manage-mode' value='view-transaction'>
                                                <input type='hidden' name='transaction-id' value='<?php
                                                    echo((isset($transac["id"])) ? $transac["id"]: "");
                                                ?>'>

                                                <button class='positive-button'>View</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            
                            <tr>
                                <?php if (!$recordCount): ?>
                                    <td class='data-not-found' colspan='7'>
                                        * None to show
                                    </td>
                                <?php else: ?>
                                    <td colspan='7'>
                                        Total Displayed: <?php echo($recordCount); ?> [Max: 25; Order By Transaction Date]
                                    </td>
                                <?php endif; ?>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
        
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
