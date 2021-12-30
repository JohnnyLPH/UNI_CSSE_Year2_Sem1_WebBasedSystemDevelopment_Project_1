<!-- Admin Dashboard: Manage Admin for LINGsCARS -->
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

    $manageMode = "";
    $passChecking = false;

    $addAdminMsg = "";

    $editAdminMsg = "";
    $allowEditAdmin = false;

    $deleteAdminMsg = "";
    $allowDeleteAdmin = false;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["manage-mode"])) {
            // Add Admin
            if ($_POST["manage-mode"] == "add-admin") {
                $manageMode = $_POST["manage-mode"];

                // Check form.
                if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    $adminName = (isset($_POST['admin-name'])) ? testInput($_POST['admin-name']): "";
                    $adminPass = (isset($_POST['admin-password'])) ? testInput($_POST['admin-password']): "";
                    $adminPass2 = (isset($_POST['admin-password2'])) ? testInput($_POST['admin-password2']): "";

                    // Check if admin name is provided.
                    if (empty($adminName) || strlen($adminName) < 3) {
                        $addAdminMsg = "* Enter Admin Name (Min: 3 Char)!";
                    }
                    // Check if admin name is already used.
                    else {
                        $query = "SELECT id, adminName FROM Admins WHERE adminName='$adminName';";
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
                            strlen($adminPass) < 6 ||
                            !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $adminPass)
                        ) {
                            $addAdminMsg = "* Enter a Password (Min: 1 special character, 1 uppercase, 1 lowercase, 3 digits)!";
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
                        $query = "INSERT INTO Admins(adminName, adminPassword)
                        VALUES
                        ('$adminName', '$adminPass')
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
            else if ($_POST["manage-mode"] == "edit-admin") {
                $manageMode = $_POST["manage-mode"];

                $adminId = (isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "";
                $adminName = (isset($_POST['admin-name'])) ? testInput($_POST['admin-name']): "";
                $newAdminName = (isset($_POST['new-admin-name'])) ? testInput($_POST['new-admin-name']): "";
                $newAdminPass = (isset($_POST['new-admin-password'])) ? testInput($_POST['new-admin-password']): "";
                $newAdminPass2 = (isset($_POST['new-admin-password2'])) ? testInput($_POST['new-admin-password2']): "";
                $oldAdminPass = (isset($_POST['old-admin-password'])) ? testInput($_POST['old-admin-password']): "";

                
                // Check admin id first to see if the admin can be edited.
                if (!empty($adminId)) {
                    $allowEditAdmin = true;
                }

                if ($allowEditAdmin) {
                    $allowEditAdmin = false;

                    $query = "SELECT id FROM Admins WHERE id=$adminId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($user = mysqli_fetch_assoc($rs)) {
                            // Allow to edit.
                            $allowEditAdmin = true;
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
                else if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    $passChecking = true;

                    // If logged in admin's id is 1, can edit other admins without their password.
                    // If edit own details, must enter password.
                    if ($_SESSION["adminId"] != 1 || $adminId == "1") {
                        $passChecking = false;

                        // Check if original password is provided.
                        if (empty($oldAdminPass)) {
                            $editAdminMsg = "* Enter Old Password to Save Changes!";
                        }
                        // Check if original password is correct.
                        else {
                            $query = "SELECT adminPassword FROM Admins WHERE id=$adminId;";
                            $rs = mysqli_query($serverConnect, $query);

                            if ($rs) {
                                if ($user = mysqli_fetch_assoc($rs)) {
                                    if ($user["adminPassword"] == $oldAdminPass) {
                                        $passChecking = true;
                                    }
                                }
                            }
                            
                            if (!$passChecking) {
                                $editAdminMsg = "* Invalid Old Password!";
                            }
                        }
                    }

                    // Check if admin name is changed.
                    if ($passChecking) {
                        $passChecking = false;

                        // Check if new admin name is provided.
                        if (empty($newAdminName) || strlen($newAdminName) < 3) {
                            $editAdminMsg = "* Enter Admin Name (Min: 3 Char)!";
                        }
                        // Check is there is no change to admin name.
                        else if ($newAdminName == $adminName) {
                            $passChecking = true;
                        }
                        // Check if new admin name is already used.
                        else {
                            $query = "SELECT id,adminName FROM Admins WHERE adminName='$newAdminName';";
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
                    }

                    // Check if new password is provided.
                    if ($passChecking) {
                        // Both empty, no change to password.
                        if (empty($newAdminPass) && empty($newAdminPass2)) {
                            // Admin name has changed (update admin name only).
                            if ($newAdminName != $adminName) {
                                $query = "UPDATE Admins SET adminName='$newAdminName' WHERE id=$adminId;";

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
                                strlen($newAdminPass) < 6 ||
                                !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $newAdminPass)
                            ) {
                                $editAdminMsg = "* Invalid New Password (Min: 1 special character, 1 uppercase, 1 lowercase, 3 digits)!";
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
                                $query = "UPDATE Admins SET adminPassword='$newAdminPass' WHERE id=$adminId;";

                                // Admin name has changed.
                                if ($newAdminName != $adminName) {
                                    $query = "UPDATE Admins SET adminPassword='$newAdminPass', adminName='$newAdminName' WHERE id=$adminId;";
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
            else if ($_POST["manage-mode"] == "delete-admin") {
                $manageMode = $_POST["manage-mode"];

                $adminId = (isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "";
                $oldAdminPass = (isset($_POST['old-admin-password'])) ? testInput($_POST['old-admin-password']): "";
                $currentAdminPass = (isset($_POST['current-admin-password'])) ? testInput($_POST['current-admin-password']): "";

                // Check if the admin is allowed to be deleted.
                if (!empty($adminId) && $adminId != "1") {
                    $query = "SELECT id FROM Admins WHERE id=$adminId;";
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
                else if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    $passChecking = true;

                    // Check if password of logged in admin is provided.
                    if (empty($currentAdminPass)) {
                        $deleteAdminMsg = "* Enter Your Password to Confirm Delete!";
                        $passChecking = false;
                    }

                    // If logged in admin's id is 1, can delete other admins without their password.
                    if ($passChecking && $_SESSION["adminId"] != 1) {
                        $passChecking = false;

                        if (empty($oldAdminPass)) {
                            $deleteAdminMsg = "* Enter Admin ID $adminId Password to Confirm Delete!";
                        }
                        // Check password of admin to be deleted.
                        else {
                            $query = "SELECT adminPassword FROM Admins WHERE id=$adminId;";
                            $rs = mysqli_query($serverConnect, $query);

                            if ($rs) {
                                if ($user = mysqli_fetch_assoc($rs)) {
                                    if ($user["adminPassword"] == $oldAdminPass) {
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

                        $query = "SELECT adminPassword FROM Admins WHERE id=" . $_SESSION["adminId"] . ";";
                        $rs = mysqli_query($serverConnect, $query);

                        if ($rs) {
                            if ($user = mysqli_fetch_assoc($rs)) {
                                if ($user["adminPassword"] == $currentAdminPass) {
                                    $passChecking = true;
                                }
                            }
                        }

                        if (!$passChecking) {
                            $deleteAdminMsg = "* Invalid Password Entered!";
                        }
                    }
                    
                    if ($passChecking) {
                        $query = "DELETE FROM Admins WHERE id=$adminId;";
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
                            $deleteAdminMsg = "* Admin ID $adminId have been deleted successfully!";
                        }
                    }
                }
            }
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
                    <a href="/admin/adminDashboard.php">Home</a>
                </li>
                <li>
                    <a href="/admin/manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="/admin/manageProduct.php">Manage Product</a>
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
                            <form id='manage-add-form' method='post' action='/admin/manageAdmin.php'>
                                <input type='hidden' name='manage-mode' value='add-admin'>
                                <input type='hidden' name='check-form' value='yes'>

                                <div>
                                    <label for='admin-name'>
                                        Admin Name:
                                    </label><br>

                                    <input id='admin-name' type='text' name='admin-name' placeholder='Admin Name (Min: 3 Char)' value='<?php
                                        echo((isset($_POST['admin-name'])) ? testInput($_POST['admin-name']): '');
                                    ?>'>
                                </div>

                                <div>
                                    <label for='admin-password'>
                                        Password:
                                    </label><br>

                                    <input id='admin-password' type='password' name='admin-password' placeholder='Password (Min: 6 Char)'>
                                </div>

                                <div>
                                    <label for='admin-password2'>
                                        Reconfirm Password:
                                    </label><br>

                                    <input id='admin-password2' type='password' name='admin-password2' placeholder='Reenter Password'>
                                </div>
                            </form>

                            <form id='cancel-add-form' method='post' action='/admin/manageAdmin.php'></form>
                            
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
                            echo((isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "");
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
                            <form id='manage-edit-form' method='post' action='/admin/manageAdmin.php'>
                                <input type='hidden' name='manage-mode' value='edit-admin'>
                                <input type='hidden' name='check-form' value='yes'>
                                <input type='hidden' name='admin-id' value='<?php
                                    echo((isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "");
                                ?>'>
                                <input type='hidden' name='admin-name' value='<?php
                                    echo((isset($_POST['admin-name'])) ? testInput($_POST['admin-name']): "");
                                ?>'>

                                <div>
                                    <label for='new-admin-name'>
                                        Admin Name:
                                    </label><br>

                                    <input id='new-admin-name' type='text' name='new-admin-name' placeholder='Admin Name (Min: 3 Char)' value='<?php
                                        if (isset($_POST['new-admin-name']) && !empty($_POST['new-admin-name'])) {
                                            echo(testInput($_POST['new-admin-name']));
                                        }
                                        else if (isset($_POST['admin-name']) && !empty($_POST['admin-name'])) {
                                            echo(testInput($_POST['admin-name']));
                                        }
                                    ?>'>
                                </div>
        
                                <div>
                                    <label for='new-admin-password'>
                                        New Password:
                                    </label><br>

                                    <input id='new-admin-password' type='password' name='new-admin-password' placeholder='Leave Empty = No Change'>
                                </div>
        
                                <div>
                                    <label for='new-admin-password2'>
                                        Reconfirm New Password:
                                    </label><br>

                                    <input id='new-admin-password2' type='password' name='new-admin-password2' placeholder='Reenter or Leave Empty'>
                                </div>

                                <!-- If logged in admin's id is 1, can edit other admins without their password. -->
                                <!-- If edit own details, must enter password. -->
                                <?php if (
                                    $_SESSION["adminId"] != 1 ||
                                    (isset($_POST['admin-id']) && testInput($_POST['admin-id']) == "1")
                                ): ?>
                                    <div>
                                        <label for='old-admin-password'>
                                            Old Password:
                                        </label><br>

                                        <input id='old-admin-password' type='password' name='old-admin-password' placeholder='Required to save changes'>
                                    </div>
                                <?php endif; ?>
                            </form>

                            <form id='cancel-edit-form' method='post' action='/admin/manageAdmin.php'></form>

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
                            echo((isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "");
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
                            <form id='manage-delete-form' method='post' action='/admin/manageAdmin.php'>
                                <input type='hidden' name='manage-mode' value='delete-admin'>
                                <input type='hidden' name='check-form' value='yes'>
                                <input type='hidden' name='admin-id' value='<?php
                                    echo((isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "");
                                ?>'>

                                <!-- If logged in admin's id is 1, can delete other admins without their password. -->
                                <?php if ($_SESSION["adminId"] != 1): ?>
                                    <div>
                                        <label for='old-admin-password'>
                                            <i>Admin ID <?php
                                                echo((isset($_POST['admin-id'])) ? testInput($_POST['admin-id']): "");
                                            ?></i> Password:
                                        </label><br>

                                        <input id='old-admin-password' type='password' name='old-admin-password' placeholder='Required to confirm delete'>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <label for='current-admin-password'>
                                        Your Password:
                                    </label><br>

                                    <input id='current-admin-password' type='password' name='current-admin-password' placeholder='Required to confirm delete'>
                                </div>
                            </form>

                            <form id='cancel-delete-form' method='post' action='/admin/manageAdmin.php'></form>
                            
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
                    <form method='post' action='/admin/manageAdmin.php'>
                        <input type='hidden' name='manage-mode' value='add-admin'>
                        <button class='add-button'>
                            Add Admin
                        </button>
                    </form>
                <?php endif; ?>

                <?php
                    $mainAdmin = false;
                    if ($_SESSION['adminId'] == 1) {
                        $mainAdmin = true;
                    }
                ?>
                <table class="db-table">
                    <thead>
                        <!-- 5 Columns -->
                        <tr>
                            <th>Admin ID</th>
                            <th>Name</th>
                            <?php
                                // 6 Columns if currently logged in admin is main admin (Admin Id = 1).
                                if (isset($mainAdmin) && $mainAdmin) {
                                    echo("<th>Password</th>");
                                }
                            ?>
                            <th>Last Login</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT id, adminName, lastLogin FROM Admins ORDER BY lastLogin DESC;";
                            
                            if (isset($mainAdmin) && $mainAdmin) {
                                $query = "SELECT * FROM Admins ORDER BY lastLogin DESC;";
                            }

                            $rs = mysqli_query($serverConnect, $query);
                            $recordCount = 0;
                        ?>
                        
                        <?php if ($rs): ?>
                            <?php while ($user = mysqli_fetch_assoc($rs)): ?>
                                <?php $recordCount++; ?>

                                <tr>
                                    <td>
                                        <?php echo((isset($user["id"])) ? $user["id"]: ""); ?>
                                    </td>

                                    <td>
                                        <?php echo((isset($user["adminName"])) ? $user["adminName"]: ""); ?>
                                    </td>
                                
                                    <?php if (isset($mainAdmin) && $mainAdmin): ?>
                                        <?php if (isset($user["id"]) && $user["id"] == $_SESSION['adminId']): ?>
                                            <td>
                                                <i>*Hidden*</i>
                                            </td>
                                        <?php else: ?>
                                            <td>
                                                <?php echo((isset($user["adminPassword"])) ? $user["adminPassword"]: ""); ?>
                                            </td>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <td>
                                        <?php echo((isset($user["lastLogin"])) ? $user["lastLogin"]: ""); ?>
                                    </td>

                                    <td>
                                        <form method='post' action='/admin/manageAdmin.php'>
                                            <input type='hidden' name='manage-mode' value='edit-admin'>
                                            <input type='hidden' name='admin-id' value='<?php
                                                echo((isset($user["id"])) ? $user["id"]: "");
                                            ?>'>
                                            <input type='hidden' name='admin-name' value='<?php
                                                echo((isset($user["adminName"])) ? $user["adminName"]: "");
                                            ?>'>

                                            <button class='positive-button'>Edit</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method='post' action='/admin/manageAdmin.php'>
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
                                <?php if (isset($mainAdmin) && $mainAdmin): ?>
                                    <td class='data-not-found' colspan='6'>
                                        * None to show
                                    </td>
                                <?php else: ?>
                                    <td class='data-not-found' colspan='5'>
                                        * None to show
                                    </td>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if (isset($mainAdmin) && $mainAdmin): ?>
                                    <td colspan='6'>
                                        Total Displayed: <?php echo($recordCount); ?>
                                    </td>
                                <?php else: ?>
                                    <td colspan='5'>
                                        Total Displayed: <?php echo($recordCount); ?>
                                    </td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
        
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
