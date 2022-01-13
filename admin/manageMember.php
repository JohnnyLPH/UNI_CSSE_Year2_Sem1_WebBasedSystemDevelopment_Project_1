<?php
    // Admin Dashboard: Manage Member for LINGsCARS
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

    $viewMemberMsg = "";
    $allowViewMember = false;

    $deleteMemberMsg = "";
    $allowDeleteMember = false;
    
    if (!empty($manageMode)) {
        // Search Member
        if ($manageMode == "search-member") {
            $wordToSearch = (isset($queryString['word-to-search'])) ? testInput($queryString['word-to-search']): "";
        }
        // View Member
        else if ($manageMode == "view-member") {
            $memberId = (isset($queryString['member-id'])) ? testInput($queryString['member-id']): "";
            
            // Check if the member is allowed to be viewed.
            if (!empty($memberId)) {
                $query = "SELECT id FROM members WHERE id=$memberId;";
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
        else if ($manageMode == "delete-member") {
            $memberId = (isset($queryString['member-id'])) ? testInput($queryString['member-id']): "";
            $currentAdminPass = (isset($_POST['current-admin-password'])) ? testInput($_POST['current-admin-password']): "";

            // Check if the member is allowed to be deleted.
            if (!empty($memberId)) {
                $query = "SELECT id FROM members WHERE id=$memberId;";
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
                    $deleteMemberMsg = "* Enter Your Password to Confirm Delete!";
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
                        $deleteMemberMsg = "* Invalid Password Entered!";
                    }
                }
                
                if ($passChecking) {
                    $query = "DELETE FROM members WHERE id=$memberId;";
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
        // Invalid Mode
        else {
            $manageMode = "";
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
                    <a href="/admin/manageMember.php" class="active">Manage Member</a>
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
                Manage Member
            </h2>

            <div class="manage-section">
                <?php if (isset($manageMode) && !empty($manageMode)): ?>
                    <!-- View Member -->
                    <?php if ($manageMode == "view-member"): ?>
                        <h3>View <i>Member ID <?php
                            echo((isset($memberId)) ? $memberId: "");
                        ?></i>:</h3>

                        <?php if (isset($viewMemberMsg) && !empty($viewMemberMsg)): ?>
                            <?php if (!$allowViewMember): ?>
                                <span class='error-message'>
                                    <?php echo($viewMemberMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowViewMember): ?>
                            <?php
                                // Select everything from Members table except password to display, select from MemberLog table too.
                                $query = "SELECT members.id, members.firstName, members.lastName, members.email, members.countryCode, members.phone, members.gender, members.state, members.registerDate, memberlog.loginDate FROM members LEFT JOIN memberlog ON members.id = memberlog.memberId WHERE members.id=$memberId;";
                                $rs = mysqli_query($serverConnect, $query);
                            ?>

                            <?php if ($rs): ?>
                                <?php if ($user = mysqli_fetch_assoc($rs)): ?>
                                    <div class='view-content'>
                                        <table>
                                            <tr>
                                                <td>Member ID</td>
                                                <td>
                                                    <?php echo((isset($user["id"])) ? $user["id"]: "-"); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>First Name</td>
                                                <td>
                                                    <?php echo((isset($user["firstName"])) ? $user["firstName"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Last Name</td>
                                                <td>
                                                    <?php echo((isset($user["lastName"])) ? $user["lastName"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Email</td>
                                                <td>
                                                    <?php echo((isset($user["email"])) ? $user["email"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Country Code</td>
                                                <td>
                                                    <?php echo((isset($user["countryCode"])) ? $user["countryCode"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Phone No.</td>
                                                <td>
                                                    <?php echo((isset($user["phone"])) ? $user["phone"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Gender</td>
                                                <td>
                                                    <?php
                                                        $allGender = array(
                                                            'Prefer Not to Say',
                                                            'Male',
                                                            'Female'
                                                        );

                                                        echo((isset($user["gender"]) && isset($allGender[$user["gender"]])) ? $allGender[$user["gender"]]: "-");
                                                    ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>State</td>
                                                <td>
                                                    <?php echo((isset($user["state"])) ? $user["state"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Register On</td>
                                                <td>
                                                    <?php echo((isset($user["registerDate"])) ? $user["registerDate"]: "-"); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Last Login</td>
                                                <td>
                                                    <?php echo((isset($user["loginDate"])) ? $user["loginDate"]: "-"); ?>
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
                                        echo((isset($lastPage) && !empty($lastPage)) ? $lastPage: "/admin/manageMember.php");
                                    ?>'>
                                        <button>Return Previous Page</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <!-- Delete Member -->
                    <?php elseif ($manageMode == "delete-member"): ?>
                        <h3>Delete <i>Member ID <?php
                            echo((isset($memberId)) ? testInput($memberId): "");
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
                            <?php
                                $newQueryString = array();
                                $newQueryString['manage-mode'] = 'delete-member';
                                $newQueryString['check-form'] = 'yes';
                                $newQueryString['member-id'] = (isset($memberId)) ? $memberId: "";
                            ?>

                            <form id='manage-delete-form' method='post' action='/admin/manageMember.php?<?php
                                echo(http_build_query($newQueryString));
                            ?>' onsubmit="return adminDeleteValidation();">
                                <div>
                                    <label for='current-admin-password'>
                                        Your Password:
                                    </label><br>

                                    <input id='current-admin-password' type='password' name='current-admin-password' placeholder='Required to confirm delete' minlength="6" maxlength="256" required>
                                </div>
                            </form>

                            <form id='cancel-delete-form' method='get' action='/admin/manageMember.php'></form>
                            
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
                    <?php
                        // Store total no. of registration from current month to (current - 11) month.
                        $monthXLabels = array();
                        $monthYTotalRegister = array();

                        // Prepare for storing data.
                        $currentDate = strtotime(date("Y-m-d H:i:s"));

                        for ($i = 0; $i < 12; $i++) {
                            // Month stored in format (e.g., Jan 2022).
                            $monthXLabels[$i] = date('M Y', strtotime((-11 + $i) . " month", strtotime(date("Y-m-15"))));
                            $monthYTotalRegister[$i] = 0;
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

                        // Change label for month.
                        for ($i = 0; $i < 12; $i++) {
                            $monthXLabels[$i] = date('M', strtotime((-11 + $i) . " month", strtotime(date("Y-m-15"))));
                        }
                    ?>

                    <div class='chart-container'>
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
                    </div>

                    <form id='cancel-search-form' method='get' action='/admin/manageMember.php'></form>

                    <form id='manage-search-form' method='get' action='/admin/manageMember.php' onsubmit="return searchWordValidation();">
                        <input type='hidden' name='manage-mode' value='search-member'>
                    </form>

                    <div class='button-section'>
                        <input id='word-to-search' form='manage-search-form' type='text' name='word-to-search' placeholder='Enter Member ID or Email' value='<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? testInput($wordToSearch): "");
                        ?>' minlength="1" maxlength="100" required>
                        
                        <button form='manage-search-form' class='small-button positive-button'>Search</button>

                        <button form='cancel-search-form' class='small-button negative-button'<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? "": " disabled");
                        ?>>Reset</button>
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
                                $query = "SELECT members.id, members.email, members.phone, members.state, members.registerDate, memberlog.loginDate FROM members LEFT JOIN memberlog ON members.id = memberlog.memberId" .
                                (
                                    (isset($wordToSearch) && !empty($wordToSearch)) ?
                                    " WHERE members.id LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR members.email LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%'" : ""
                                ) .
                                " ORDER BY loginDate DESC LIMIT 25;";
                                
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
                                            <?php echo((isset($user["email"])) ? $user["email"]: "-"); ?>
                                        </td>

                                        <td>
                                            <?php echo((isset($user["phone"])) ? $user["phone"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($user["state"])) ? $user["state"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($user["registerDate"])) ? $user["registerDate"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($user["loginDate"])) ? $user["loginDate"]: "-"); ?>
                                        </td>

                                        <td>
                                            <form method='get' action='/admin/manageMember.php'>
                                                <input type='hidden' name='manage-mode' value='view-member'>
                                                <input type='hidden' name='member-id' value='<?php
                                                    echo((isset($user["id"])) ? $user["id"]: "");
                                                ?>'>

                                                <button class='positive-button'>View</button>
                                            </form>
                                        </td>
                                        
                                        <td>
                                            <form method='get' action='/admin/manageMember.php'>
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
