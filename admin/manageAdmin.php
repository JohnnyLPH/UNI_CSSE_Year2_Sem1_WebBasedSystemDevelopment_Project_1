<?php
    // Admin Dashboard: Manage Admin for LINGsCARS
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

    $addAdminMsg = "";

    $editAdminMsg = "";
    $allowEditAdmin = false;

    $deleteAdminMsg = "";
    $allowDeleteAdmin = false;
    
    if (!empty($manageMode)) {
        // Add Admin
        if ($manageMode == "add-admin") {
            // Check form.
            if (
                $_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($queryString["check-form"]) && $queryString["check-form"] == "yes"
            ) {
                $adminName = (isset($_POST['admin-name'])) ? testInput($_POST['admin-name']): "";
                $adminPass = (isset($_POST['admin-password'])) ? testInput($_POST['admin-password']): "";
                $adminPass2 = (isset($_POST['admin-password2'])) ? testInput($_POST['admin-password2']): "";

                // Check if admin name is provided.
                if (
                    empty($adminName) ||
                    strlen($adminName) < 3 || strlen($adminName) > 128 ||
                    !preg_match("/^[A-Za-z0-9]{1}[A-Za-z0-9]+(\s[A-Za-z0-9]{1}[A-Za-z0-9]+)*$/",$adminName)
                ) {
                    $addAdminMsg = "* Enter Admin Name<br>(3 - 128 Char; Alphabets & Digits;<br>Min 2 Char per Word; Allow 1 Space Between)!";
                }
                // Check if admin name is already used.
                else {
                    $query = "SELECT id, adminName FROM admins WHERE adminName='$adminName';";
                    $rs = mysqli_query($serverConnect, $query);

                    $passChecking = true;
                    if ($rs) {
                        if ($user = mysqli_fetch_assoc($rs)) {
                            if ($user["adminName"] == $adminName) {
                                $addAdminMsg = "* Admin Name is already used!";
                                $passChecking = false;
                            }
                        }
                    }
                }
                
                // Continue checking.
                if ($passChecking) {
                    $passChecking = false;
                    // Check if password is provided and has at least 6 char.
                    // Min 1 special character, 1 uppercase, 1 lowercase, 3 digit.
                    if (
                        empty($adminPass) ||
                        strlen($adminPass) < 6 || strlen($adminPass) > 256 ||
                        !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $adminPass)
                    ) {
                        $addAdminMsg = "* Enter a Password<br>(1 Special Char, 1 Upper, 1 Lower, 3 Digits;<br>6 - 256 Char; Space Ignored at Start & End)!";
                    }
                    // Check if password is reentered.
                    else if (empty($adminPass2)) {
                        $addAdminMsg = "* Reenter the Password!";
                    }
                    // Check if reentered password is the same.
                    else if ($adminPass != $adminPass2) {
                        $addAdminMsg = "* Password entered must be the same!";
                    }
                    // Password is okay.
                    else {
                        $passChecking = true;
                    }
                }

                // Try to insert new record.
                if ($passChecking) {
                    $hash = password_hash($adminPass, PASSWORD_DEFAULT);
                    $query = "INSERT INTO admins(adminName, adminPassword)
                    VALUES
                    ('$adminName', '$hash')
                    ;";

                    $rs = mysqli_query($serverConnect, $query);
                    
                    if ($rs) {
                        $addAdminMsg = "* New Admin has been successfully added!";
                    }
                    else {
                        $passChecking = false;
                        $addAdminMsg = "* ERROR: Failed to add new Admin!";
                    }
                }
            }
        }
        // Edit Admin
        else if ($manageMode == "edit-admin") {
            $adminId = (isset($queryString['admin-id'])) ? testInput($queryString['admin-id']): "";
            $adminName = "";
            $newAdminName = (isset($_POST['new-admin-name'])) ? testInput($_POST['new-admin-name']): "";
            $newAdminPass = (isset($_POST['new-admin-password'])) ? testInput($_POST['new-admin-password']): "";
            $newAdminPass2 = (isset($_POST['new-admin-password2'])) ? testInput($_POST['new-admin-password2']): "";
            $oldAdminPass = (isset($_POST['old-admin-password'])) ? testInput($_POST['old-admin-password']): "";

            // Check admin id first to see if the admin can be edited.
            if (!empty($adminId) && is_numeric($adminId)) {
                $allowEditAdmin = true;
            }

            if ($allowEditAdmin) {
                $allowEditAdmin = false;

                $query = "SELECT id, adminName FROM admins WHERE id=$adminId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($user = mysqli_fetch_assoc($rs)) {
                        // Allow to edit.
                        $allowEditAdmin = true;
                        $adminName = (isset($user['adminName'])) ? testInput($user['adminName']): "";
                    }
                }

                if ($adminId == "1" && $_SESSION["adminId"] != 1) {
                    $allowEditAdmin = false;
                }
            }

            if (!$allowEditAdmin) {
                $editAdminMsg = "* You are not allowed to edit the selected Admin!";
            }
            // Check form.
            else if (
                $_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($queryString["check-form"]) && $queryString["check-form"] == "yes"
            ) {
                // Check if new admin name is provided.
                if (
                    empty($newAdminName) ||
                    strlen($newAdminName) < 3 || strlen($newAdminName) > 128 ||
                    !preg_match("/^[A-Za-z0-9]{1}[A-Za-z0-9]+(\s[A-Za-z0-9]{1}[A-Za-z0-9]+)*$/",$newAdminName)
                ) {
                    $editAdminMsg = "* Enter Admin Name<br>(3 - 128 Char; Alphabets & Digits;<br>Min 2 Char per Word; Allow 1 Space Between)!";
                }
                // Check if there is no change to admin name.
                else if ($newAdminName == $adminName) {
                    $passChecking = true;
                }
                // Check if new admin name is already used.
                else {
                    $query = "SELECT id,adminName FROM admins WHERE adminName='$newAdminName';";
                    $rs = mysqli_query($serverConnect, $query);

                    $passChecking = true;

                    if ($rs) {
                        if ($user = mysqli_fetch_assoc($rs)) {
                            if ($user["adminName"] == $newAdminName) {
                                $editAdminMsg = "* Admin Name has already been used!";
                                $passChecking = false;
                            }
                        }
                    }
                }

                // If logged in admin's id is 1, can edit other admins without their password.
                // If edit own details, must enter password.
                if ($passChecking && ($_SESSION["adminId"] != 1 || $adminId == "1")) {
                    $passChecking = false;

                    // Check if original password is provided.
                    if (
                        empty($oldAdminPass) ||
                        strlen($oldAdminPass) < 6 || strlen($oldAdminPass) > 256 ||
                        !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $oldAdminPass)
                    ) {
                        $editAdminMsg = "* Enter Old Password to Save Changes!";
                    }
                    // Check if original password is correct.
                    else {
                        $query = "SELECT adminPassword FROM admins WHERE id=$adminId;";
                        $rs = mysqli_query($serverConnect, $query);

                        if ($rs) {
                            if ($user = mysqli_fetch_assoc($rs)) {
                                if (password_verify($oldAdminPass, $user["adminPassword"])) {
                                    $passChecking = true;
                                }
                            }
                        }
                        
                        if (!$passChecking) {
                            $editAdminMsg = "* Invalid Old Password!";
                        }
                    }
                }

                // Check if new password is provided.
                if ($passChecking) {
                    // Both empty, no change to password.
                    if (empty($newAdminPass) && empty($newAdminPass2)) {
                        // Admin name has changed (update admin name only).
                        if ($newAdminName != $adminName) {
                            $query = "UPDATE admins SET adminName='$newAdminName' WHERE id=$adminId;";

                            $rs = mysqli_query($serverConnect, $query);

                            if (!($rs)) {
                                $passChecking = false;
                                $editAdminMsg = "* ERROR: Failed to save changes!";
                            }
                            // Change admin name stored in session.
                            else if ($_SESSION['adminId'] == "$adminId") {
                                $_SESSION['adminName'] = $newAdminName;
                            }
                        }
                    }
                    else {
                        $passChecking = false;
                        // Check if new password is provided and has at least 6 char.
                        if (
                            empty($newAdminPass) ||
                            strlen($newAdminPass) < 6 || strlen($newAdminPass) > 256 ||
                            !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $newAdminPass)
                        ) {
                            $editAdminMsg = "* Invalid New Password<br>(1 Special Char, 1 Upper, 1 Lower, 3 Digits;<br>6 - 256 Char; Space Ignored at Start & End)!";
                        }
                        // Check if new password is reentered.
                        else if (empty($newAdminPass2)) {
                            $editAdminMsg = "* Reenter the New Password!";
                        }
                        // Check if reentered password is the same.
                        else if ($newAdminPass != $newAdminPass2) {
                            $editAdminMsg = "* New Password entered must be the same!";
                        }
                        // Password is okay.
                        else {
                            $passChecking = true;
                            
                            $hash = password_hash($newAdminPass, PASSWORD_DEFAULT);
                            $query = "UPDATE admins SET adminPassword='$hash' WHERE id=$adminId;";

                            // Admin name has changed.
                            if ($newAdminName != $adminName) {
                                $query = "UPDATE admins SET adminPassword='$hash', adminName='$newAdminName' WHERE id=$adminId;";
                            }

                            $rs = mysqli_query($serverConnect, $query);

                            if (!($rs)) {
                                $passChecking = false;
                                $editAdminMsg = "* ERROR: Failed to save changes!";
                            }
                            // Change admin name stored in session.
                            else if ($newAdminName != $adminName && $_SESSION['adminId'] == "$adminId") {
                                $_SESSION['adminName'] = $newAdminName;
                            }
                        }
                    }

                    if ($passChecking) {
                        $editAdminMsg = "* Changes have been saved successfully!";
                    }
                }
            }
        }
        // Delete Admin
        else if ($manageMode == "delete-admin") {
            $adminId = (isset($queryString['admin-id'])) ? testInput($queryString['admin-id']): "";
            $oldAdminPass = (isset($_POST['old-admin-password'])) ? testInput($_POST['old-admin-password']): "";
            $currentAdminPass = (isset($_POST['current-admin-password'])) ? testInput($_POST['current-admin-password']): "";

            // Check if the admin is allowed to be deleted.
            if (!empty($adminId) && is_numeric($adminId) && $adminId != "1") {
                $query = "SELECT id FROM admins WHERE id=$adminId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($user = mysqli_fetch_assoc($rs)) {
                        // Allow to delete.
                        $allowDeleteAdmin = true;
                    }
                }
            }

            if (!$allowDeleteAdmin) {
                $deleteAdminMsg = "* You are not allowed to delete the selected Admin!";
            }
            else if (
                $_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($queryString["check-form"]) && $queryString["check-form"] == "yes"
            ) {
                $passChecking = true;

                // Check if password of logged in admin is provided.
                if (
                    empty($currentAdminPass) ||
                    strlen($currentAdminPass) < 6 || strlen($currentAdminPass) > 256 ||
                    !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $currentAdminPass)
                ) {
                    $deleteAdminMsg = "* Enter Your Password to Confirm Delete!";
                    $passChecking = false;
                }

                // If logged in admin's id is not 1, cannot delete other admins without their password.
                if ($passChecking && $_SESSION["adminId"] != 1) {
                    $passChecking = false;

                    if (
                        empty($oldAdminPass) ||
                        strlen($oldAdminPass) < 6 || strlen($oldAdminPass) > 256 ||
                        !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $oldAdminPass)
                    ) {
                        $deleteAdminMsg = "* Enter Admin ID $adminId Password to Confirm Delete!";
                    }
                    // Check password of admin to be deleted.
                    else {
                        $query = "SELECT adminPassword FROM admins WHERE id=$adminId;";
                        $rs = mysqli_query($serverConnect, $query);

                        if ($rs) {
                            if ($user = mysqli_fetch_assoc($rs)) {
                                if (password_verify($oldAdminPass, $user["adminPassword"])) {
                                    $passChecking = true;
                                }
                            }
                        }

                        if (!$passChecking) {
                            $deleteAdminMsg = "* Invalid Password Entered!";
                        }
                    }
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
                        $deleteAdminMsg = "* Invalid Password Entered!";
                    }
                }
                
                if ($passChecking) {
                    $query = "DELETE FROM admins WHERE id=$adminId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if (!($rs)) {
                        $passChecking = false;
                        $deleteAdminMsg = "* ERROR: Failed to delete Admin ID $adminId!";
                    }
                    // If logged in admin is deleted.
                    else if ($_SESSION['adminId'] == "$adminId") {
                        header("Location: /admin/adminLogout.php");
                        exit;
                    }
                    
                    if ($passChecking) {
                        $deleteAdminMsg = "* Admin ID $adminId has been deleted successfully!";
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
        <title>Admin Dashboard: : Manage Admin | LINGsCARS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/admin.css">
        <link rel="shortcut icon" href="/favicon.ico">
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <script type="text/javascript" src="/admin/adminFormValidation.js" defer></script>
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
                    <a href="/admin/manageTransaction.php">Manage Transaction</a>
                </li>
                <li>
                    <a href="/admin/manageAdmin.php" class="active">Manage Admin</a>
                </li>
                <li>
                    <a href="/admin/adminLogout.php">Log Out</a>
                </li>
            </ul>
        </nav>

        <main>
            <h2>
                Manage Admin
            </h2>

            <div class="manage-section">
                <?php if (isset($manageMode) && !empty($manageMode)): ?>
                    <!-- Add Admin -->
                    <?php if ($manageMode == "add-admin"): ?>
                        <h3>Add New Admin:</h3>

                        <?php if (isset($addAdminMsg) && !empty($addAdminMsg)): ?>
                            <?php if (!$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($addAdminMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($addAdminMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!$passChecking): ?>
                            <?php
                                $newQueryString = array();
                                $newQueryString['manage-mode'] = 'add-admin';
                                $newQueryString['check-form'] = 'yes';
                            ?>

                            <form id='manage-add-form' method='post' action='/admin/manageAdmin.php?<?php
                                echo(http_build_query($newQueryString));
                            ?>' onsubmit="return addAdminValidation();">
                                <div>
                                    <label for='admin-name'>
                                        Admin Name:
                                    </label><br>

                                    <input id='admin-name' type='text' name='admin-name' placeholder='Admin Name (Min: 3 Char)' value='<?php
                                        echo((isset($adminName)) ? testInput($adminName): '');
                                    ?>' minlength="3" maxlength="128" required>
                                </div>

                                <div>
                                    <label for='admin-password'>
                                        Password:
                                    </label><br>

                                    <input id='admin-password' type='password' name='admin-password' placeholder='Password (Min: 6 Char)' minlength="6" maxlength="256" required>
                                </div>

                                <div>
                                    <label for='admin-password2'>
                                        Reconfirm Password:
                                    </label><br>

                                    <input id='admin-password2' type='password' name='admin-password2' placeholder='Reenter Password' minlength="6" maxlength="256" required>
                                </div>
                            </form>

                            <form id='cancel-add-form' method='get' action='/admin/manageAdmin.php'></form>
                            
                            <div class='button-section'>
                                <button form='manage-add-form' class='positive-button' type='submit'>
                                    Add Admin
                                </button>
                                
                                <button form='cancel-add-form' class='negative-button'>
                                    Cancel
                                </button>
                            </div>
                        <?php endif; ?>
                    <!-- Edit Admin -->
                    <?php elseif ($manageMode == "edit-admin"): ?>
                        <h3>Edit <i>Admin ID <?php
                            echo((isset($queryString['admin-id'])) ? testInput($queryString['admin-id']): "");
                        ?></i>:</h3>

                        <?php if (isset($editAdminMsg) && !empty($editAdminMsg)): ?>
                            <?php if (!$allowEditAdmin || !$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($editAdminMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($editAdminMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($allowEditAdmin && !$passChecking): ?>
                            <?php
                                $newQueryString = array();
                                $newQueryString['manage-mode'] = 'edit-admin';
                                $newQueryString['check-form'] = 'yes';
                                $newQueryString['admin-id'] = (isset($adminId)) ? $adminId: "";
                            ?>

                            <form id='manage-edit-form' method='post' action='/admin/manageAdmin.php?<?php
                                echo(http_build_query($newQueryString));
                            ?>' onsubmit="return editAdminValidation();">
                                <div>
                                    <label for='new-admin-name'>
                                        Admin Name:
                                    </label><br>

                                    <input id='new-admin-name' type='text' name='new-admin-name' placeholder='Admin Name (Min: 3 Char)' value='<?php
                                        if (isset($newAdminName) && !empty($newAdminName)) {
                                            echo(testInput($newAdminName));
                                        }
                                        else if (isset($adminName) && !empty($adminName)) {
                                            echo(testInput($adminName));
                                        }
                                    ?>' minlength="3" maxlength="128" required>
                                </div>
        
                                <div>
                                    <label for='new-admin-password'>
                                        New Password:
                                    </label><br>

                                    <input id='new-admin-password' type='password' name='new-admin-password' placeholder='Leave Empty = No Change' minlength="6" maxlength="256">
                                </div>
        
                                <div>
                                    <label for='new-admin-password2'>
                                        Reconfirm New Password:
                                    </label><br>

                                    <input id='new-admin-password2' type='password' name='new-admin-password2' placeholder='Reenter or Leave Empty' minlength="6" maxlength="256">
                                </div>

                                <!-- If logged in admin's id is 1, can edit other admins without their password. -->
                                <!-- If edit own details, must enter password. -->
                                <?php if (
                                    $_SESSION["adminId"] != 1 ||
                                    (isset($adminId) && testInput($adminId) == "1")
                                ): ?>
                                    <div>
                                        <label for='old-admin-password'>
                                            Old Password:
                                        </label><br>

                                        <input id='old-admin-password' type='password' name='old-admin-password' placeholder='Required to save changes' minlength="6" maxlength="256" required>
                                    </div>
                                <?php endif; ?>
                            </form>

                            <form id='cancel-edit-form' method='get' action='/admin/manageAdmin.php'></form>

                            <div class='button-section'>
                                <button form='manage-edit-form' class='positive-button' type='submit'>
                                    Confirm Edit
                                </button>
                                
                                <button form='cancel-edit-form' class='negative-button'>
                                    Cancel
                                </button>
                            </div>
                        <?php endif; ?>
                    <!-- Delete Admin -->
                    <?php elseif ($manageMode == "delete-admin"): ?>
                        <h3>Delete <i>Admin ID <?php
                            echo((isset($adminId)) ? testInput($adminId): "");
                        ?></i>:</h3>

                        <?php if (isset($deleteAdminMsg) && !empty($deleteAdminMsg)): ?>
                            <?php if (!$allowDeleteAdmin || !$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($deleteAdminMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($deleteAdminMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowDeleteAdmin && !$passChecking): ?>
                            <?php
                                $newQueryString = array();
                                $newQueryString['manage-mode'] = 'delete-admin';
                                $newQueryString['check-form'] = 'yes';
                                $newQueryString['admin-id'] = (isset($adminId)) ? $adminId: "";
                            ?>

                            <form id='manage-delete-form' method='post' action='/admin/manageAdmin.php?<?php
                                echo(http_build_query($newQueryString));
                            ?>' onsubmit="return adminDeleteValidation();">
                                <!-- If logged in admin's id is 1, can delete other admins without their password. -->
                                <?php if ($_SESSION["adminId"] != 1): ?>
                                    <div>
                                        <label for='old-admin-password'>
                                            <i>Admin ID <?php
                                                echo((isset($adminId)) ? testInput($adminId): "");
                                            ?></i> Password:
                                        </label><br>

                                        <input id='old-admin-password' type='password' name='old-admin-password' placeholder='Required to confirm delete' minlength="6" maxlength="256" required>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <label for='current-admin-password'>
                                        Your Password:
                                    </label><br>

                                    <input id='current-admin-password' type='password' name='current-admin-password' placeholder='Required to confirm delete' minlength="6" maxlength="256" required>
                                </div>
                            </form>

                            <form id='cancel-delete-form' method='get' action='/admin/manageAdmin.php'></form>
                            
                            <div class='button-section'>
                                <button form='manage-delete-form' class='positive-button' type='submit'>
                                    Confirm Delete
                                </button>
                                
                                <button form='cancel-delete-form' class='negative-button'>
                                    Cancel
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (
                    (isset($manageMode) && empty($manageMode)) ||
                    $passChecking ||
                    (isset($manageMode) && $manageMode == "edit-admin" && !$allowEditAdmin) ||
                    (isset($manageMode) && $manageMode == "delete-admin" && !$allowDeleteAdmin)
                ): ?>
                    <div class='chart-container'>
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

                    <form method='get' action='/admin/manageAdmin.php'>
                        <input type='hidden' name='manage-mode' value='add-admin'>
                        <button>
                            Add Admin
                        </button>
                    </form>

                    <?php
                        $mainAdmin = false;
                        if ($_SESSION['adminId'] == 1) {
                            $mainAdmin = true;
                        }
                    ?>
                    <h3>Available Admins:</h3>
                    <table class="db-table">
                        <thead>
                            <!-- 5 Columns -->
                            <tr>
                                <th>Admin ID</th>
                                <th>Name</th>
                                <?php
                                    // 6 Columns if currently logged in admin is main admin (Admin Id = 1).
                                    // if (isset($mainAdmin) && $mainAdmin) {
                                    //     echo("<th>Password</th>");
                                    // }
                                ?>
                                <th>Last Login</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $query = "SELECT id, adminName, lastLogin FROM admins ORDER BY lastLogin DESC LIMIT 25;";
                                
                                // if (isset($mainAdmin) && $mainAdmin) {
                                //     $query = "SELECT * FROM admins ORDER BY lastLogin DESC LIMIT 25;";
                                // }

                                $rs = mysqli_query($serverConnect, $query);
                                $recordCount = 0;
                            ?>
                            
                            <?php if ($rs): ?>
                                <?php while ($user = mysqli_fetch_assoc($rs)): ?>
                                    <?php $recordCount++; ?>

                                    <tr>
                                        <td class='center-text'>
                                            <?php echo((isset($user["id"])) ? $user["id"]: "-"); ?>
                                        </td>

                                        <td>
                                            <?php echo((isset($user["adminName"])) ? $user["adminName"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($user["lastLogin"])) ? $user["lastLogin"]: "-"); ?>
                                        </td>

                                        <td>
                                            <form method='get' action='/admin/manageAdmin.php'>
                                                <input type='hidden' name='manage-mode' value='edit-admin'>
                                                <input type='hidden' name='admin-id' value='<?php
                                                    echo((isset($user["id"])) ? $user["id"]: "");
                                                ?>'>

                                                <button class='positive-button'>Edit</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method='get' action='/admin/manageAdmin.php'>
                                                <input type='hidden' name='manage-mode' value='delete-admin'>
                                                <input type='hidden' name='admin-id' value='<?php
                                                    echo((isset($user["id"])) ? $user["id"]: "");
                                                ?>'>

                                                <button class='negative-button'>Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            
                            <tr>
                                <?php if (!$recordCount): ?>
                                    <td class='data-not-found' colspan='5'>
                                        * None to show
                                    </td>
                                <?php else: ?>
                                    <td colspan='5'>
                                        Total Displayed: <?php echo($recordCount); ?> [Max: 25; Order By Login Date]
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
