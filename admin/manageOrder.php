<!-- Admin Dashboard: Manage Order for LINGsCARS -->
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

    $viewOrderMsg = "";
    $allowViewOrder = false;

    $editOrderMsg = "";
    $allowEditOrder = false;
    
    if (!empty($manageMode)) {
        // Search Order
        if ($manageMode == "search-order") {
            $wordToSearch = (isset($queryString['word-to-search'])) ? testInput($queryString['word-to-search']): "";
        }
        // View Order
        else if ($manageMode == "view-order") {
            $orderId = (isset($queryString['order-id'])) ? testInput($queryString['order-id']): "";
            
            // Check if the order is allowed to be viewed.
            if (!empty($orderId) && is_numeric($orderId)) {
                $query = "SELECT id FROM Orders WHERE id=$orderId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($record = mysqli_fetch_assoc($rs)) {
                        // Allow to view.
                        $allowViewOrder = true;
                    }
                }
            }

            if (!$allowViewOrder) {
                $viewOrderMsg = "* You are not allowed to view the selected Order!";
            }
        }
        // Edit Order
        else if ($manageMode == "edit-order") {
            $orderId = (isset($queryString['order-id'])) ? testInput($queryString['order-id']): "";
            $orderStatus = (isset($_POST['order-status'])) ? testInput($_POST['order-status']): "";
            $oldOlderStatus = "";
            
            // Check if the order is allowed to be edited.
            if (!empty($orderId) && is_numeric($orderId)) {
                $query = "SELECT Orders.id, Orders.orderStatus FROM Orders WHERE Orders.id=$orderId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($record = mysqli_fetch_assoc($rs)) {
                        // Order status 2 for those waiting for review.
                        if (isset($record['orderStatus']) && $record['orderStatus'] == 2) {
                            // Allow to edit.
                            $allowEditOrder = true;

                            $oldOlderStatus = (isset($record['orderStatus'])) ? testInput($record['orderStatus']): "";
                        }
                    }
                }
            }

            if (!$allowEditOrder) {
                $editOrderMsg = "* You are not allowed to edit the selected Order!";
            }
            else if (
                $_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($queryString["check-form"]) && $queryString["check-form"] == "yes"
            ) {

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
        <title>Admin Dashboard: Manage Order | LINGsCARS</title>
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
                    <a href="/admin/index.php">Home</a>
                </li>
                <li>
                    <a href="/admin/manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="/admin/manageVehicle.php">Manage Vehicle</a>
                </li>
                <li>
                    <a href="/admin/manageOrder.php" class="active">Manage Order</a>
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
                Manage Order
            </h2>

            <div class="manage-section">
                <?php if (isset($manageMode) && !empty($manageMode)): ?>
                    <!-- View Order -->
                    <?php if ($manageMode == "view-order"): ?>
                        <h3>View <i>Order ID <?php
                            echo((isset($orderId)) ? testInput($orderId): "");
                        ?></i>:</h3>

                        <?php if (isset($viewOrderMsg) && !empty($viewOrderMsg)): ?>
                            <?php if (!$allowViewOrder): ?>
                                <span class='error-message'>
                                    <?php echo($viewOrderMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowViewOrder): ?>
                            <?php
                                $query = "SELECT Orders.id, Orders.memberId, Orders.stage, Orders.editable, Orders.personal, Orders.currentAddress, Orders.job, Orders.bank, Orders.orderStatus, Orders.proposalDate, Orders.reviewDate, Orders.confirmDate, Orders.receipt
                                FROM Orders
                                INNER JOIN Members ON Orders.memberId = Members.id
                                WHERE Orders.id=$orderId;";

                                $rs = mysqli_query($serverConnect, $query);
                            ?>

                            <?php if ($rs): ?>
                                <?php if ($record = mysqli_fetch_assoc($rs)): ?>
                                    <div class='view-content'>
                                        <table>
                                            <tr>
                                                <td>Order ID</td>
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
                                                <td>Stage</td>
                                                <td>
                                                    <?php echo((isset($record["stage"])) ? $record["stage"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Editable</td>
                                                <td>
                                                    <?php echo((isset($record["editable"])) ? $record["editable"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Personal Details</td>
                                                <td>
                                                    <?php var_dump((isset($record["personal"])) ? json_decode($record["personal"], true): ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Current Address</td>
                                                <td>
                                                    <?php var_dump((isset($record["currentAddress"])) ? json_decode($record["currentAddress"], true): ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Job Details</td>
                                                <td>
                                                    <?php var_dump((isset($record["job"])) ? json_decode($record["job"], true): ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Bank Details</td>
                                                <td>
                                                    <?php var_dump((isset($record["bank"])) ? json_decode($record["bank"], true): ""); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Order Status</td>
                                                <td>
                                                    <?php echo((isset($record["orderStatus"])) ? $record["orderStatus"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Proposal Date</td>
                                                <td>
                                                    <?php echo((isset($record["proposalDate"])) ? $record["proposalDate"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Review Date</td>
                                                <td>
                                                    <?php echo((isset($record["reviewDate"])) ? $record["reviewDate"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Confirm Date</td>
                                                <td>
                                                    <?php echo((isset($record["confirmDate"])) ? $record["confirmDate"]: "-"); ?>
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
                                    
                                    <?php if (isset($record["orderStatus"]) && $record["orderStatus"] == 2): ?>
                                        <!-- Waiting for review -->
                                        <form method='get' action='/admin/manageOrder.php'>
                                            <input type='hidden' name='manage-mode' value='edit-order'>
                                            <input type='hidden' name='order-id' value='<?php
                                                echo((isset($record["id"])) ? $record["id"]: "");
                                            ?>'>

                                            <button class='positive-button'>Review Order</button>
                                        </form>
                                    <?php endif; ?>

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
                    <!-- Edit Order -->
                    <?php elseif ($manageMode == "edit-order"): ?>
                        <h3>Edit <i>Order ID <?php
                            echo((isset($orderId)) ? $orderId: "");
                        ?></i>:</h3>

                        <?php if (isset($editOrderMsg) && !empty($editOrderMsg)): ?>
                            <?php if (!$allowEditOrder || !$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($editOrderMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($editOrderMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowEditOrder && !$passChecking): ?>
                            <?php
                                $newQueryString = array();
                                $newQueryString['manage-mode'] = 'edit-order';
                                $newQueryString['check-form'] = 'yes';
                                $newQueryString['order-id'] = (isset($orderId)) ? $orderId: "";
                            ?>

                            <form id='manage-edit-form' method='post' action='/admin/manageOrder.php?<?php
                                echo(http_build_query($newQueryString));
                            ?>'>
                                <div>
                                    <label for='order-status'>
                                        Order Status:
                                    </label><br>

                                    <input id='order-status' type='number' min="1" max="4" name='order-status' placeholder='I dont know:)' value='<?php
                                        echo((!empty($orderStatus)) ? $orderStatus: $oldOlderStatus);
                                    ?>' required>
                                </div>
                            </form>

                            <form id='cancel-edit-form' method='get' action='/admin/manageOrder.php'></form>

                            <div class='button-section'>
                                <button form='manage-edit-form' class='positive-button' type='submit'>
                                    Confirm Edit
                                </button>
                                
                                <button form='cancel-edit-form' class='negative-button'>
                                    Cancel
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (
                    (isset($manageMode) && empty($manageMode)) ||
                    $passChecking ||
                    (isset($manageMode) && $manageMode == "search-order") ||
                    (isset($manageMode) && $manageMode == "view-order" && !$allowViewOrder) ||
                    (isset($manageMode) && $manageMode == "edit-order" && !$allowEditOrder)
                ): ?>
                    <form id='cancel-search-form' method='get' action='/admin/manageOrder.php'></form>

                    <form id='manage-search-form' method='get' action='/admin/manageOrder.php'>
                        <input type='hidden' name='manage-mode' value='search-order'>
                    </form>
                
                    <div class='button-section'>
                        <input form='manage-search-form' type='text' name='word-to-search' placeholder='Enter Order/Member ID or Order Status' value='<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? testInput($wordToSearch): "");
                        ?>' minlength="1" maxlength="100" required>
                        
                        <button form='manage-search-form' class='small-button positive-button'>Search</button>

                        <button form='cancel-search-form' class='small-button negative-button'<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? "": " disabled");
                        ?>>Reset</button>
                    </div>
                    
                    <h3>Found Orders:</h3>
                    <table class="db-table">
                        <thead>
                            <!-- 7 Columns -->
                            <tr>
                                <th>Order ID</th>
                                <th>Member ID</th>
                                <th>Order Status</th>
                                <th>Proposal Date</th>
                                <th>Review Date</th>
                                <th>Confirm Date</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Select from Orders table.
                                $query = "SELECT Orders.id, Orders.memberId, Orders.orderStatus, Orders.proposalDate, Orders.reviewDate, Orders.confirmDate FROM Orders" .
                                (
                                    (isset($wordToSearch) && !empty($wordToSearch)) ?
                                    " WHERE Orders.id LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR Orders.memberId LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR Orders.orderStatus LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%'" : ""
                                ) .
                                " ORDER BY CASE WHEN Orders.orderStatus=2 THEN 1 WHEN Orders.orderStatus > 2 THEN 2 ELSE 3 END LIMIT 25;";
                                
                                $rs = mysqli_query($serverConnect, $query);
                                $recordCount = 0;
                            ?>
                            
                            <?php if ($rs): ?>
                                <?php while ($record = mysqli_fetch_assoc($rs)): ?>
                                    <?php $recordCount++; ?>

                                    <tr>
                                        <td class='center-text'>
                                            <?php echo((isset($record["id"])) ? $record["id"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($record["memberId"])) ? $record["memberId"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($record["orderStatus"])) ? $record["orderStatus"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($record["proposalDate"])) ? $record["proposalDate"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($record["reviewDate"])) ? $record["reviewDate"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($record["confirmDate"])) ? $record["confirmDate"]: "-"); ?>
                                        </td>

                                        <td>
                                            <form method='get' action='/admin/manageOrder.php'>
                                                <input type='hidden' name='manage-mode' value='view-order'>
                                                <input type='hidden' name='order-id' value='<?php
                                                    echo((isset($record["id"])) ? $record["id"]: "");
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
                                        Total Displayed: <?php echo($recordCount); ?> [Max: 25; Order By Order Status]
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
