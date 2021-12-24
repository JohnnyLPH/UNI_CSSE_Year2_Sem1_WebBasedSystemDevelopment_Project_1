<!-- Admin Dashboard: Manage Admin for LINGsCARS -->
<?php
    require_once("./dbConnection.php");
    require_once("./adminAuthenticate.php");
    if (!checkAdminLogin()) {
        header("Location: ./adminLogout.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Dashboard: : Manage Admin | LINGsCARS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="stylesheet" href="./css/admin.css">
        <link rel="shortcut icon" href="./favicon.ico">
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
                    <a href="./adminDashboard.php">Home</a>
                </li>
                <li>
                    <a href="./manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="./manageProduct.php">Manage Product</a>
                </li>
                <li>
                    <a href="./manageTransaction.php">Manage Transaction</a>
                </li>
                <li>
                    <a href="./manageAdmin.php" class="active">Manage Admin</a>
                </li>
                <li>
                    <a href="./adminLogout.php">Log Out</a>
                </li>
            </ul>
        </nav>

        <main>
            <h2>
                Manage Admin
            </h2>

            <div class="manage-section">
                <form>
                    <input type="hidden" id="manageMode" name="manageMode" value="add">
                    <button class="add-button">
                        Add Admin
                    </button>
                </form>

                <table class="db-table">
                    <thead>
                        <!-- 4 Columns -->
                        <tr>
                            <th>Admin ID</th>
                            <th>Name</th>
                            <th>Password</th>
                            <th>Last Login</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = "SELECT * FROM Admins ORDER BY lastLogin DESC;";
                            $rs = mysqli_query($serverConnect, $query);
                            $recordCount = 0;
                            if ($rs) {
                                while ($user = mysqli_fetch_assoc($rs)) {
                                    $recordCount++;
                                    print("
                                        <tr>
                                            <td>" . $user["id"] . "</td>
                                            <td>" . $user["adminName"] . "</td>
                                            <td>" . $user["adminPassword"] . "</td>
                                            <td>" . $user["lastLogin"] . "</td>
                                            <td>
                                                <form>
                                                <input type='hidden' id='manageMode' name='manageMode' value='edit'>
                                                <button class='edit-button'>Edit</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form>
                                                <input type='hidden' id='manageMode' name='manageMode' value='delete'>
                                                <button class='delete-button'>Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    ");
                                }

                                if (!$recordCount) {
                                    print("
                                        <tr>
                                            <td class='data-not-found' colspan='6'>* None to show</td>
                                        </tr>
                                    ");
                                }
                                else {
                                    print("
                                        <tr>
                                            <td colspan='6'>Total Displayed: " . $recordCount . "</td>
                                        </tr>
                                    ");
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
        
        <hr>
        <footer>
            <p>
                By G03-ABC
            </p>
        </footer>
    </body>
</html>
