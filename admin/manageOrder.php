<?php
    // Admin Dashboard: Manage Order for LINGsCARS
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
                $query = "SELECT id FROM orders WHERE id=$orderId;";
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

            $currentAdminPass = (isset($_POST['current-admin-password'])) ? testInput($_POST['current-admin-password']): "";
            
            // Check if the order is allowed to be edited.
            if (!empty($orderId) && is_numeric($orderId)) {
                $query = "SELECT orders.id, orders.orderStatus FROM orders WHERE orders.id=$orderId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($record = mysqli_fetch_assoc($rs)) {
                        // 8 status [0 - 7].
                        // Admin can update from 6th [5] status to 7th [6] or 1st [0].
                        // Order status 5 for those waiting for review.
                        if (isset($record['orderStatus']) && $record['orderStatus'] == 5) {
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
                $passChecking = true;

                // Check if new order status is valid, only accept 0 (Not approved) and 6 (Approved).
                if ((empty($orderStatus) && $orderStatus != 0) || !is_numeric($orderStatus) || ($orderStatus != 0 && $orderStatus != 6)) {
                    $editOrderMsg = "* Invalid Order Status ($orderStatus) to update!";
                    $passChecking = false;
                }

                // Check password of logged in admin.
                if ($passChecking) {
                    $passChecking = false;

                    $query = "SELECT adminPassword FROM admins WHERE id=" . $_SESSION["adminId"] . ";";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($user = mysqli_fetch_assoc($rs)) {
                            if (password_verify($currentAdminPass, $user["adminPassword"])) {
                                $passChecking = true;
                            }
                        }
                    }

                    if (!$passChecking) {
                        $editOrderMsg = "* Invalid Password Entered!";
                    }
                }
                
                $currentDate = date("Y-m-d H:i:s");

                if ($passChecking) {
                    $query = "UPDATE orders SET orders.orderStatus='$orderStatus', orders.reviewDate='$currentDate' WHERE orders.id=$orderId;";

                    $rs = mysqli_query($serverConnect, $query);

                    if (!($rs)) {
                        $passChecking = false;
                        $editOrderMsg = "* ERROR: Failed to save new order status!";
                    }
                    
                    if ($passChecking) {
                        $editOrderMsg = "* Order ID $orderId has been changed successfully!";
                    }
                }
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
                                $query = "SELECT orders.id, orders.memberId, orders.stages, orders.editable, orders.type, orders.fullName, orders.personal, orders.residentialAddress, orders.job, orders.company, orders.bank, orders.preferredDelivery, orders.orderStatus, orders.orderStatusMessage, orders.proposalDate, orders.reviewDate, orders.confirmDate
                                FROM orders
                                INNER JOIN members ON orders.memberId = members.id
                                WHERE orders.id=$orderId;";

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
                                                    <?php
                                                        if (isset($record["stages"])) {
                                                            $arrJson = json_decode($record["stages"], true);

                                                            foreach ($arrJson as $key=>$value) {
                                                                echo("\"". $key . "\" = ");

                                                                if ($value == 0) {
                                                                    echo("Not Started<br>");
                                                                }
                                                                else if ($value == 1) {
                                                                    echo("Completed<br>");
                                                                }
                                                                else if ($value == -1) {
                                                                    echo("Invalid Input<br>");
                                                                }
                                                                else {
                                                                    echo("-<br>");
                                                                }
                                                            }
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Editable</td>
                                                <td><?php
                                                    if (isset($record["editable"])) {
                                                        if ($record["editable"] == 1) {
                                                            echo("Yes");
                                                        }
                                                        else if ($record["editable"] == 0) {
                                                            echo("No");
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    }
                                                    else {
                                                        echo("-");
                                                    }
                                                ?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Type</td>
                                                <td><?php

                                                    // Type is 1 for personal, 2 for business.
                                                    $allType = array(
                                                        'Personal',
                                                        'Business'
                                                    );

                                                    if (
                                                        isset($record["type"]) &&
                                                        ($record["type"] == 1 || $record["type"] == 2)
                                                    ) {
                                                        echo($allType[$record["type"] - 1]);
                                                    }
                                                    else {
                                                        echo("-");
                                                    }
                                                ?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Full Name</td>
                                                <td>
                                                    <?php echo((isset($record["fullName"])) ? $record["fullName"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Personal Details</td>
                                                <td>
                                                    <?php
                                                        if (isset($record["personal"])) {
                                                            $arrJson = json_decode($record["personal"], true);

                                                            $allGender = array(
                                                                'Prefer Not to Say',
                                                                'Male',
                                                                'Female'
                                                            );

                                                            foreach ($arrJson as $key=>$value) {
                                                                if ($key == 'gender') {
                                                                    echo("\"". $key . "\" = " . $allGender[$value] . "<br>");
                                                                }
                                                                else {
                                                                    echo("\"". $key . "\" = " . $value . "<br>");
                                                                }
                                                            }
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Current Address</td>
                                                <td>
                                                    <?php
                                                        if (isset($record["residentialAddress"])) {
                                                            $arrJson = json_decode($record["residentialAddress"], true);

                                                            // Status is 1 for Property Owner, 2 for Property Tenant, 3 for Property Occupant (Live with Parents), 4 for Property Occupant (Live with Friends/Partner).

                                                            $allResidentialStatus = array(
                                                                'Property Owner',
                                                                'Property Tenant',
                                                                'Property Occupant (Live with Parents)',
                                                                'Property Occupant (Live with Friends/Partner)'
                                                            );

                                                            foreach ($arrJson as $key=>$value) {
                                                                if ($key == 'status' && $value > 0) {
                                                                    echo("\"". $key . "\" = " . $allResidentialStatus[$value - 1] . "<br>");
                                                                }
                                                                else {
                                                                    echo("\"". $key . "\" = " . $value . "<br>");
                                                                }
                                                            }
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Job Details</td>
                                                <td>
                                                    <?php
                                                        if (isset($record["job"])) {
                                                            $arrJson = json_decode($record["job"], true);

                                                            foreach ($arrJson as $key=>$value) {
                                                                echo("\"". $key . "\" = " . $value . "<br>");
                                                            }
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Company Details</td>
                                                <td>
                                                    <?php
                                                        if (isset($record["company"])) {
                                                            $arrJson = json_decode($record["company"], true);

                                                            $allCompanyType = array(
                                                                'Sole Proprietorship',
                                                                'Partnership',
                                                                'Private Limited',
                                                                'Public Limited',
                                                                'Government Agency',
                                                                'Other'
                                                            );

                                                            foreach ($arrJson as $key=>$value) {
                                                                if ($key == 'type' && $value > 0) {
                                                                    echo("\"". $key . "\" = " . $allCompanyType[$value - 1] . "<br>");
                                                                }
                                                                else {
                                                                    echo("\"". $key . "\" = " . $value . "<br>");
                                                                }
                                                            }
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Bank Details</td>
                                                <td>
                                                    <?php
                                                        if (isset($record["bank"])) {
                                                            $arrJson = json_decode($record["bank"], true);

                                                            foreach ($arrJson as $key=>$value) {
                                                                echo("\"". $key . "\" = " . $value . "<br>");
                                                            }
                                                        }
                                                        else {
                                                            echo("-");
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Preferred Delivery Date</td>
                                                <td>
                                                    <?php
                                                        echo((isset($record["preferredDelivery"])) ? date('M Y', strtotime($record["preferredDelivery"])): "-");
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Order Status</td>
                                                <td <?php
                                                    if (isset($record["orderStatus"])) {
                                                        // Not approved.
                                                        if ($record["orderStatus"] == 0) {
                                                            echo("style='color: red; font-weight: bold;'");
                                                        }
                                                        // Approved.
                                                        else if ($record["orderStatus"] > 5) {
                                                            echo("style='color: green; font-weight: bold;'");
                                                        }
                                                        // Waiting for review.
                                                        else if ($record["orderStatus"] == 5) {
                                                            echo("style='font-style: italic; font-weight: bold;'");
                                                        }
                                                    }
                                                ?>>
                                                    <?php
                                                        // 8 status [0 - 7].
                                                        // Admin can update from 6th [5] status to 7th [6] or 1st [0].
                                                        $allOrderStatus = array(
                                                            'Ineligible.',
                                                            'Changes required.',
                                                            'Incomplete Payment.',
                                                            'Proposal cancelled.',
                                                            'Draft Proposal pending submission. Please complete and submit your proposal.',
                                                            'Proposal under review.',
                                                            'Proposal approved. Awaiting for your confirmation.',
                                                            'Order Confirmed.'
                                                        );

                                                        echo((isset($record["orderStatus"]) && isset($allOrderStatus[$record["orderStatus"]])) ? $allOrderStatus[$record["orderStatus"]]: "-");
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Order Status Message</td>
                                                <td>
                                                    <?php echo((isset($record["orderStatusMessage"])) ? $record["orderStatusMessage"]: "-"); ?>
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
                                        </table>
                                    </div>
                                    
                                    <?php if (isset($record["orderStatus"]) && $record["orderStatus"] == 5): ?>
                                        <!-- Waiting for review -->
                                        <form method='get' action='/admin/manageOrder.php'>
                                            <input type='hidden' name='manage-mode' value='edit-order'>
                                            <input type='hidden' name='order-id' value='<?php
                                                echo((isset($record["id"])) ? $record["id"]: "");
                                            ?>'>

                                            <button class='positive-button'>Give Review</button>
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
                            ?>' onsubmit="return approveOrderValidation();">
                                <div>
                                    <label for='order-status'>
                                        Order Status:
                                    </label><br>

                                    <div class="radio-section">
                                        <label for="approve-order">Approve</label>
                                        <input id="approve-order" type="radio" name="order-status" value="6" checked>

                                        <label for="not-approve-order">Not Approve</label>
                                        <input id="not-approve-order" type="radio" name="order-status" value="0">
                                    </div>
                                </div>

                                <div>
                                    <label for='current-admin-password'>
                                        Your Password:
                                    </label><br>

                                    <input id='current-admin-password' type='password' name='current-admin-password' placeholder='Required to confirm new status' minlength="6" maxlength="256" required>
                                </div>
                            </form>

                            <form id='cancel-edit-form' method='get' action='/admin/manageOrder.php'>
                                <input type='hidden' name='manage-mode' value='view-order'>
                                <input type='hidden' name='order-id' value='<?php
                                    echo($orderId);
                                ?>'>
                            </form>

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

                    <form id='manage-search-form' method='get' action='/admin/manageOrder.php' onsubmit="return searchWordValidation();">
                        <input type='hidden' name='manage-mode' value='search-order'>
                    </form>
                
                    <div class='button-section'>
                        <input id='word-to-search' form='manage-search-form' type='text' name='word-to-search' placeholder='Enter Order/Member ID or Order Status' value='<?php
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
                                // 8 status [0 - 7].
                                // Admin can update from 6th [5] status to 7th [6] or 1st [0].
                                // Select from Orders table.
                                $query = "SELECT orders.id, orders.memberId, orders.orderStatus, orders.proposalDate, orders.reviewDate, orders.confirmDate FROM orders" .
                                (
                                    (isset($wordToSearch) && !empty($wordToSearch)) ?
                                    " WHERE orders.id LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR orders.memberId LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR orders.orderStatus LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%'" : ""
                                ) .
                                " ORDER BY CASE WHEN orders.orderStatus=5 THEN 1 WHEN orders.orderStatus > 5 THEN 2 ELSE 3 END LIMIT 25;";
                                
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

                                        <td class='center-text' <?php
                                            if (isset($record["orderStatus"])) {
                                                // Not approved.
                                                if ($record["orderStatus"] == 0) {
                                                    echo("style='color: red; font-weight: bold;'");
                                                }
                                                // Approved.
                                                else if ($record["orderStatus"] > 5) {
                                                    echo("style='color: green; font-weight: bold;'");
                                                }
                                                // Waiting for review.
                                                else if ($record["orderStatus"] == 5) {
                                                    echo("style='font-style: italic; font-weight: bold;'");
                                                }
                                            }
                                        ?>>
                                            <?php
                                                echo((isset($record["orderStatus"])) ? $record["orderStatus"]: "-");
                                                
                                                // Waiting for review.
                                                if (isset($record["orderStatus"]) && $record["orderStatus"] == 5) {
                                                    echo("<br>Need Review");
                                                }
                                            ?>
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
