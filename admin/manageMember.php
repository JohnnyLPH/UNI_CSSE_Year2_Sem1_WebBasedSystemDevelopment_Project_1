<!-- Admin Dashboard: Manage Member for LINGsCARS -->
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

    $emailToSearch = "";

    $viewMemberMsg = "";
    $allowViewMember = false;

    $deleteMemberMsg = "";
    $allowDeleteMember = false;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["manage-mode"])) {
            // Search Member
            if ($_POST["manage-mode"] == "search-member") {
                $manageMode = $_POST["manage-mode"];
                $emailToSearch = (isset($_POST['email-to-search'])) ? testInput($_POST['email-to-search']): "";
            }
            // View Member
            else if ($_POST["manage-mode"] == "view-member") {
                $manageMode = $_POST["manage-mode"];

                $memberId = (isset($_POST['member-id'])) ? testInput($_POST['member-id']): "";
                
                // Check if the member is allowed to be viewed.
                if (!empty($memberId)) {
                    $query = "SELECT id FROM Members WHERE id=$memberId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($user = mysqli_fetch_assoc($rs)) {
                            // Allow to view.
                            $allowViewMember = true;
                        }
                    }
                }

                if (!$allowViewMember) {
                    $viewMemberMsg = "* You are not allowed to view the selected Member!";
                }
            }
            // Delete Member
            else if ($_POST["manage-mode"] == "delete-member") {
                $manageMode = $_POST["manage-mode"];

                $memberId = (isset($_POST['member-id'])) ? testInput($_POST['member-id']): "";
                $currentAdminPass = (isset($_POST['current-admin-password'])) ? testInput($_POST['current-admin-password']): "";

                // Check if the member is allowed to be deleted.
                if (!empty($memberId)) {
                    $query = "SELECT id FROM Members WHERE id=$memberId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($user = mysqli_fetch_assoc($rs)) {
                            // Allow to delete.
                            $allowDeleteMember = true;
                        }
                    }
                }

                if (!$allowDeleteMember) {
                    $deleteMemberMsg = "* You are not allowed to delete the selected Member!";
                }
                else if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    $passChecking = true;

                    // Check if password of logged in admin is provided.
                    if (empty($currentAdminPass)) {
                        $deleteMemberMsg = "* Enter Your Password to Confirm Delete!";
                        $passChecking = false;
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
                            $deleteMemberMsg = "* Invalid Password Entered!";
                        }
                    }
                    
                    if ($passChecking) {
                        $query = "DELETE FROM Members WHERE id=$memberId;";
                        $rs = mysqli_query($serverConnect, $query);

                        if (!($rs)) {
                            $passChecking = false;
                            $deleteMemberMsg = "* ERROR: Failed to delete Member ID $memberId! Recheck if it is used in other tables!";
                        }
                        
                        if ($passChecking) {
                            $deleteMemberMsg = "* Member ID $memberId has been deleted successfully!";
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
        <title>Admin Dashboard: Manage Member | LINGsCARS</title>
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
                    <a href="/admin/adminDashboard.php">Home</a>
                </li>
                <li>
                    <a href="/admin/manageMember.php" class="active">Manage Member</a>
                </li>
                <li>
                    <a href="/admin/manageProduct.php">Manage Product</a>
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
                Manage Member
            </h2>

            <div class="manage-section">
                <?php if (isset($manageMode) && !empty($manageMode)): ?>
                    <!-- View Member -->
                    <?php if ($manageMode == "view-member"): ?>
                        <h3>View <i>Member ID <?php
                            echo((isset($_POST['member-id'])) ? testInput($_POST['member-id']): "");
                        ?></i>:</h3>

                        <?php if (isset($viewMemberMsg) && !empty($viewMemberMsg)): ?>
                            <?php if (!$allowViewMember): ?>
                                <span class='error-message'>
                                    <?php echo($viewMemberMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowViewMember): ?>

                        <?php endif; ?>
                    <!-- Delete Member -->
                    <?php elseif ($manageMode == "delete-member"): ?>
                        <h3>Delete <i>Member ID <?php
                            echo((isset($_POST['member-id'])) ? testInput($_POST['member-id']): "");
                        ?></i>:</h3>

                        <?php if (isset($deleteMemberMsg) && !empty($deleteMemberMsg)): ?>
                            <?php if (!$allowDeleteMember || !$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($deleteMemberMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($deleteMemberMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowDeleteMember && !$passChecking): ?>
                            <form id='manage-delete-form' method='post' action='/admin/manageMember.php'>
                                <input type='hidden' name='manage-mode' value='delete-member'>
                                <input type='hidden' name='check-form' value='yes'>
                                <input type='hidden' name='member-id' value='<?php
                                    echo((isset($_POST['member-id'])) ? testInput($_POST['member-id']): "");
                                ?>'>

                                <div>
                                    <label for='current-admin-password'>
                                        Your Password:
                                    </label><br>

                                    <input id='current-admin-password' type='password' name='current-admin-password' placeholder='Required to confirm delete'>
                                </div>
                            </form>

                            <form id='cancel-delete-form' method='post' action='/admin/manageMember.php'></form>
                            
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
                    (isset($manageMode) && $manageMode == "search-member") ||
                    (isset($manageMode) && $manageMode == "view-member" && !$allowViewMember) ||
                    (isset($manageMode) && $manageMode == "delete-member" && !$allowDeleteMember)
                ): ?>
                    <form id='cancel-search-form' method='post' action='/admin/manageMember.php'></form>

                    <form id='manage-search-form' method='post' action='/admin/manageMember.php'>
                        <input type='hidden' name='manage-mode' value='search-member'>
                    </form>

                    <div class='button-section'>
                        <input form='manage-search-form' type='text' name='email-to-search' placeholder='Enter Member Email' value='<?php
                            echo((isset($emailToSearch) && !empty($emailToSearch)) ? testInput($emailToSearch): "");
                        ?>'>
                        
                        <button form='manage-search-form' class='small-button'>Search</button>
                        <button form='cancel-search-form' class='small-button negative-button'>Reset</button>
                    </div>
                
                    <h3>Found Members:</h3>
                    <table class="db-table">
                        <thead>
                            <!-- 8 Columns -->
                            <tr>
                                <th>Member ID</th>
                                <th>Email</th>
                                <th>Phone No.</th>
                                <th>State</th>
                                <th>Register On</th>
                                <th>Last Login</th>
                                <th>View</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $query = "SELECT Members.id, Members.email, Members.phoneNo, Members.state, Members.registerDate, MemberLog.loginDate FROM Members LEFT JOIN MemberLog ON Members.id = MemberLog.memberId" .
                                ((isset($emailToSearch) && !empty($emailToSearch)) ? " WHERE Members.email LIKE '%". testInput($emailToSearch) . "%'": "") .
                                " ORDER BY loginDate DESC;";
                                
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
                                            <?php echo((isset($user["email"])) ? $user["email"]: ""); ?>
                                        </td>

                                        <td>
                                            <?php echo((isset($user["phoneNo"])) ? $user["phoneNo"]: ""); ?>
                                        </td>

                                        <td>
                                            <?php echo((isset($user["state"])) ? $user["state"]: ""); ?>
                                        </td>

                                        <td>
                                            <?php echo((isset($user["registerDate"])) ? $user["registerDate"]: ""); ?>
                                        </td>

                                        <td>
                                            <?php echo((isset($user["loginDate"])) ? $user["loginDate"]: ""); ?>
                                        </td>

                                        <td>
                                            <form method='post' action='/admin/manageMember.php'>
                                                <input type='hidden' name='manage-mode' value='view-member'>
                                                <input type='hidden' name='member-id' value='<?php
                                                    echo((isset($user["id"])) ? $user["id"]: "");
                                                ?>'>

                                                <button class='positive-button'>View</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method='post' action='/admin/manageMember.php'>
                                                <input type='hidden' name='manage-mode' value='delete-member'>
                                                <input type='hidden' name='member-id' value='<?php
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
                                        <td class='data-not-found' colspan='8'>
                                            * None to show
                                        </td>
                                <?php else: ?>
                                        <td colspan='8'>
                                            Total Displayed: <?php echo($recordCount); ?>
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
