<!-- Admin Dashboard: Manage Product for LINGsCARS -->
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

    $wordToSearch = "";

    $addCarMsg = "";

    $viewCarMsg = "";
    $allowViewCar = false;

    $deleteCarMsg = "";
    $allowDeleteCar = false;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["manage-mode"])) {
            // Search Car
            if ($_POST["manage-mode"] == "search-car") {
                $manageMode = $_POST["manage-mode"];
                $wordToSearch = (isset($_POST['word-to-search'])) ? testInput($_POST['word-to-search']): "";
            }
            // Add Car
            else if ($_POST["manage-mode"] == "add-car") {
                $manageMode = $_POST["manage-mode"];

                // Check form.
                if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    $carBrand = (isset($_POST['car-brand'])) ? testInput($_POST['car-brand']): "";
                    $carModel = (isset($_POST['car-model'])) ? testInput($_POST['car-model']): "";
                    $monthPrice = (isset($_POST['month-price'])) ? testInput($_POST['month-price']): "";
                    $leaseTime = (isset($_POST['lease-time'])) ? testInput($_POST['lease-time']): "";
                    $initialPay = (isset($_POST['lease-time'])) ? testInput($_POST['lease-time']): "";
                    $carDesc = (isset($_POST['car-desc'])) ? testInput($_POST['car-desc']): "";

                    // Check if car brand is provided.
                    if (empty($carBrand) || strlen($carBrand) < 3) {
                        $addCarMsg = "* Enter Car Brand (Min: 3 Char)!";
                    }
                    // Check if car model is provided.
                    else if (empty($carModel) || strlen($carModel) < 2) {
                        $addCarMsg = "* Enter Car Model (Min: 2 Char)!";
                    }
                    // Check if car model (same brand) is already used.
                    else {
                        $query = "SELECT carBrand, carModel FROM Cars WHERE carModel='$carModel';";
                        $rs = mysqli_query($serverConnect, $query);

                        $passChecking = true;
                        if ($rs) {
                            if ($car = mysqli_fetch_assoc($rs)) {
                                if ($car["carBrand"] == $carBrand && $car["carModel"] == $carModel) {
                                    $addCarMsg = "* Car Model is already added before!";
                                    $passChecking = false;
                                }
                            }
                        }
                    }
                    
                    // Continue checking.
                    if ($passChecking) {
                    }

                    // Try to insert new record.
                    if ($passChecking) {
                    }
                }
            }
            // View Car
            else if ($_POST["manage-mode"] == "view-car") {
                $manageMode = $_POST["manage-mode"];

                $carId = (isset($_POST['car-id'])) ? testInput($_POST['car-id']): "";
                
                // Check if the car is allowed to be viewed.
                if (!empty($carId)) {
                    $query = "SELECT id FROM Cars WHERE id=$carId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($car = mysqli_fetch_assoc($rs)) {
                            // Allow to view.
                            $allowViewCar = true;
                        }
                    }
                }

                if (!$allowViewCar) {
                    $viewCarMsg = "* You are not allowed to view the selected Car!";
                }
            }
            // Delete Car
            else if ($_POST["manage-mode"] == "delete-car") {
                $manageMode = $_POST["manage-mode"];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin Dashboard: Manage Vehicle | LINGsCARS</title>
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
                    <a href="/admin/manageMember.php">Manage Member</a>
                </li>
                <li>
                    <a href="/admin/manageVehicle.php" class="active">Manage Vehicle</a>
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
                Manage Vehicle
            </h2>

            <div class="manage-section">
                <?php if (isset($manageMode) && !empty($manageMode)): ?>
                    <!-- Add Car -->
                    <?php if ($manageMode == "add-car"): ?>
                        <h3>Add New Car:</h3>

                        <?php if (isset($addCarMsg) && !empty($addCarMsg)): ?>
                            <?php if (!$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($addCarMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($addCarMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (!$passChecking): ?>
                            <form id='manage-add-form' method='post' action='/admin/manageVehicle.php'>
                                <input type='hidden' name='manage-mode' value='add-car'>
                                <input type='hidden' name='check-form' value='yes'>

                                <div>
                                    <label for='car-brand'>
                                        Brand:
                                    </label><br>

                                    <input id='car-brand' type='text' name='car-brand' placeholder='Car Brand (Min: 3 Char)' value='<?php
                                        echo((isset($_POST['car-brand'])) ? testInput($_POST['car-brand']): '');
                                    ?>'>
                                </div>

                                <div>
                                    <label for='car-model'>
                                        Model:
                                    </label><br>

                                    <input id='car-model' type='text' name='car-model' placeholder='Car Model (Min: 2 Char)' value='<?php
                                        echo((isset($_POST['car-model'])) ? testInput($_POST['car-model']): '');
                                    ?>'>
                                </div>

                                <div>
                                    <label for='month-price'>
                                        Price (£/mth):
                                    </label><br>

                                    <input id='month-price' type='text' name='month-price' placeholder='Price Per Month' value='<?php
                                        echo((isset($_POST['month-price'])) ? testInput($_POST['month-price']): '');
                                    ?>'>
                                </div>

                                <div>
                                    <label for='lease-time'>
                                        Lease Time (Month):
                                    </label><br>

                                    <input id='lease-time' type='text' name='lease-time' placeholder='Lease For x Month' value='<?php
                                        echo((isset($_POST['lease-time'])) ? testInput($_POST['lease-time']): '');
                                    ?>'>
                                </div>

                                <div>
                                    <label for='initial-pay'>
                                        Initial Pay (* £/mth):
                                    </label><br>

                                    <input id='initial-pay' type='text' name='initial-pay' placeholder='x Price Per Month' value='<?php
                                        echo((isset($_POST['initial-pay'])) ? testInput($_POST['initial-pay']): '');
                                    ?>'>
                                </div>

                                <div>
                                    <label for='car-desc'>
                                        Description:
                                    </label><br>

                                    <input id='car-desc' type='text' name='car-desc' placeholder='Car Description' value='<?php
                                        echo((isset($_POST['car-desc'])) ? testInput($_POST['car-desc']): '');
                                    ?>'>
                                </div>
                            </form>

                            <form id='cancel-add-form' method='post' action='/admin/manageVehicle.php'></form>
                            
                            <div class='button-section'>
                                <button form='manage-add-form' class='positive-button' type='submit'>
                                    Add Car
                                </button>
                                
                                <button form='cancel-add-form' class='negative-button'>
                                    Cancel
                                </button>
                            </div>
                        <?php endif; ?>
                    <!-- View Car -->
                    <?php elseif ($manageMode == "view-car"): ?>
                        <h3>View <i>Car ID <?php
                            echo((isset($_POST['car-id'])) ? testInput($_POST['car-id']): "");
                        ?></i>:</h3>

                        <?php if (isset($viewCarMsg) && !empty($viewCarMsg)): ?>
                            <?php if (!$allowViewCar): ?>
                                <span class='error-message'>
                                    <?php echo($viewCarMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowViewCar): ?>
                            <?php
                                // Select everything.
                                $query = "SELECT * FROM Cars WHERE id=$carId;";
                                $rs = mysqli_query($serverConnect, $query);
                            ?>

                            <?php if ($rs): ?>
                                <?php if ($car = mysqli_fetch_assoc($rs)): ?>
                                    <div class='view-content'>
                                        <table>
                                            <tr>
                                                <td>Car ID</td>
                                                <td>
                                                    <?php echo((isset($car["id"])) ? $car["id"]: ""); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Brand</td>
                                                <td>
                                                    <?php echo((isset($car["carBrand"])) ? $car["carBrand"]: ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Model</td>
                                                <td>
                                                    <?php echo((isset($car["carModel"])) ? $car["carModel"]: ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Price (£/mth)</td>
                                                <td>
                                                    <?php echo((isset($car["monthPrice"])) ? $car["monthPrice"]: ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Lease Time (Month)</td>
                                                <td>
                                                    <?php echo((isset($car["leaseTime"])) ? $car["leaseTime"]: ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Initial Pay (* £/mth)</td>
                                                <td>
                                                    <?php echo((isset($car["initialPay"])) ? $car["initialPay"]: ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Description</td>
                                                <td>
                                                    <?php echo((isset($car["carDesc"])) ? $car["carDesc"]: ""); ?>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Car Image</td>
                                                <td>
                                                    <img src='<?php
                                                        echo((isset($car["carImage"])) ? $car["carImage"]: "");
                                                    ?>'>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Added On</td>
                                                <td>
                                                    <?php echo((isset($car["dateAdded"])) ? $car["dateAdded"]: ""); ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Last Edit</td>
                                                <td>
                                                    <?php echo((isset($car["dateEdited"])) ? $car["dateEdited"]: ""); ?>
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

                                    <form id='cancel-view-form' method='post' action='<?php
                                        echo((isset($lastPage) && !empty($lastPage)) ? $lastPage: "/admin/manageVehicle.php");
                                    ?>'>
                                        <button>Return Previous Page</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <!-- Delete Car -->
                    <?php elseif ($manageMode == "delete-car"): ?>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (
                    (isset($manageMode) && empty($manageMode)) ||
                    $passChecking ||
                    (isset($manageMode) && $manageMode == "search-car") ||
                    (isset($manageMode) && $manageMode == "view-car" && !$allowViewCar) ||
                    (isset($manageMode) && $manageMode == "delete-car" && !$allowDeleteCar)
                ): ?>
                    <form id='cancel-search-form' method='post' action='/admin/manageVehicle.php'></form>

                    <form id='manage-search-form' method='post' action='/admin/manageVehicle.php'>
                        <input type='hidden' name='manage-mode' value='search-car'>
                    </form>

                    <form id='manage-add-form' method='post' action='/admin/manageVehicle.php'>
                        <input type='hidden' name='manage-mode' value='add-car'>
                    </form>
                
                    <div class='button-section'>
                        <input form='manage-search-form' type='text' name='word-to-search' placeholder='Enter Car ID or Brand or Model' value='<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? testInput($wordToSearch): "");
                        ?>'>
                        
                        <button form='manage-search-form' class='small-button positive-button'>Search</button>
                        <button form='cancel-search-form' class='small-button negative-button'>Reset</button>
                        <button form='manage-add-form' class='small-button'>Add Car</button>
                    </div>
                    
                    <h3>Found Cars:</h3>
                    <table class="db-table">
                        <thead>
                            <!-- 8 Columns -->
                            <tr>
                                <th>Car ID</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Price (£/mth)</th>
                                <th>Lease Time (Month)</th>
                                <th>Initial Pay (* £/mth)</th>
                                <th>View</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $query = "SELECT id, carBrand, carModel, monthPrice, leaseTime, initialPay FROM Cars" .
                                (
                                    (isset($wordToSearch) && !empty($wordToSearch)) ?
                                    " WHERE Cars.id LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR Cars.carBrand LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR Cars.carModel LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%'" : ""
                                ) .
                                " ORDER BY dateEdited DESC;";
                                
                                $rs = mysqli_query($serverConnect, $query);
                                $recordCount = 0;
                            ?>
                            
                            <?php if ($rs): ?>
                                <?php while ($car = mysqli_fetch_assoc($rs)): ?>
                                    <?php $recordCount++; ?>

                                    <tr>
                                        <td class='center-text'>
                                            <?php echo((isset($car["id"])) ? $car["id"]: ""); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["carBrand"])) ? $car["carBrand"]: ""); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["carModel"])) ? $car["carModel"]: ""); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["monthPrice"])) ? $car["monthPrice"]: ""); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["leaseTime"])) ? $car["leaseTime"]: ""); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["initialPay"])) ? $car["initialPay"]: ""); ?>
                                        </td>

                                        <td>
                                            <form method='post' action='/admin/manageVehicle.php'>
                                                <input type='hidden' name='manage-mode' value='view-car'>
                                                <input type='hidden' name='car-id' value='<?php
                                                    echo((isset($car["id"])) ? $car["id"]: "");
                                                ?>'>

                                                <button class='positive-button'>View</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method='post' action='/admin/manageVehicle.php'>
                                                <input type='hidden' name='manage-mode' value='delete-car'>
                                                <input type='hidden' name='car-id' value='<?php
                                                    echo((isset($car["id"])) ? $car["id"]: "");
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
