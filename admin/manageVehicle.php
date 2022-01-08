<!-- Admin Dashboard: Manage Vehicle for LINGsCARS -->
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

    $editCarMsg = "";
    $allowEditCar = false;

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
                    $initialPay = (isset($_POST['initial-pay'])) ? testInput($_POST['initial-pay']): "";
                    $carDesc = (isset($_POST['car-desc'])) ? testInput($_POST['car-desc']): "";
                    
                    // Refer common car brands only: https://www.carlogos.org/car-brands-a-z/
                    // Check if car brand is provided. All letters, start word with 1 uppercase, 1 space / dash between.
                    if (
                        empty($carBrand) ||
                        strlen($carBrand) < 3 || strlen($carBrand) > 100 ||
                        !preg_match("/^[A-Z]{1}[A-Za-z]*([-\s]{1}[A-Z]{1}[A-Za-z]*)*$/",$carBrand)
                    ) {
                        $addCarMsg = "* Enter Car Brand<br>(Min: 3 Char; Alphabets Only; 1 Space/Dash Between;<br>Start Word with 1 Upper)!";
                    }
                    // Check if car model is provided. Letters or digits, 1 space / dash between.
                    else if (
                        empty($carModel) ||
                        strlen($carModel) < 2 || strlen($carModel) > 100 ||
                        !preg_match("/^[A-Za-z0-9]{1}[A-Za-z0-9]*([-\s]{1}[A-Za-z0-9]{1}[A-Za-z0-9]*)*$/",$carModel)
                    ) {
                        $addCarMsg = "* Enter Car Model<br>(Min: 2 Char; Alphabets & Digits; 1 Space/Dash Between)!";
                    }
                    // Check if month price is provided.
                    else if (empty($monthPrice) || is_nan($monthPrice) || $monthPrice < 100 || $monthPrice > 1000) {
                        $addCarMsg = "* Enter Price Per Month (Min: 100; Max: 1000)!";
                    }
                    // Check if lease time is provided.
                    else if (empty($leaseTime) || is_nan($leaseTime) || $leaseTime < 6 || $leaseTime > 60) {
                        $addCarMsg = "* Enter Lease Time (Min: 6; Max: 60; Months)!";
                    }
                    // Check if initial pay is provided.
                    else if (empty($initialPay) || is_nan($initialPay) || $initialPay < 3 || $initialPay > 10) {
                        $addCarMsg = "* Enter Initial Pay (Min: 3; Max: 10; * Price)!";
                    }
                    // Check if car description is provided.
                    else if (empty($carDesc) || strlen($carDesc) < 5 || strlen($carDesc) > 512) {
                        $addCarMsg = "* Enter Car Description (Min: 5 Char)!";
                    }
                    // Check if car model (same brand) is already added before.
                    else {
                        $query = "SELECT Brands.brandName, Cars.carModel FROM Cars INNER JOIN Brands ON Cars.brandId = Brands.id WHERE carModel='$carModel';";
                        $rs = mysqli_query($serverConnect, $query);

                        $passChecking = true;
                        if ($rs) {
                            if ($car = mysqli_fetch_assoc($rs)) {
                                if ($car["brandName"] == $carBrand && $car["carModel"] == $carModel) {
                                    $addCarMsg = "* Car Model is already added before!";
                                    $passChecking = false;
                                }
                            }
                        }
                    }

                    $currentDate = date("Y-m-d H:i:s");
                    $carImageName = $targetImagePath = "";

                    // Continue checking.
                    if ($passChecking) {
                        $passChecking = false;

                        // Only allow PNG or JPG.
                        $allowImageType = array('png', 'jpeg', 'jpg');
                        $carImageName = (isset($_FILES['car-image']['name'])) ? testInput($_FILES['car-image']['name']): "";
                        $carImageName = str_replace(" ", "_", $carImageName);

                        // Get image type.
                        $imageFileType = (!empty($carImageName)) ? pathinfo($carImageName, PATHINFO_EXTENSION): "";
                        
                        // Target path to store image.
                        $targetImagePath = "/img/car/";
                        $targetImagePath .= str_replace(" ", "_", strtolower($carBrand)) . "_";

                        $targetImagePath .= str_replace(" ", "_", strtolower($carModel)) . "_";
                        // $targetImagePath .= str_replace(" ", "_", strtolower($carModel));

                        $targetImagePath .= strtotime($currentDate) . "/";
                        // $targetImagePath .= "/";

                        // Check if car image is provided (actual max is 2 MiB).
                        if (
                            !isset($_FILES['car-image']) ||
                            $_FILES['car-image']['size'] < 1 || $_FILES['car-image']['size'] > 2097152 ||
                            !in_array($imageFileType, $allowImageType)
                        ) {
                            $addCarMsg = "* Upload a Car Image (Max: 2 MB; Only PNG or JPG)!";
                        }
                        // Try to create folder and upload image.
                        else {
                            // Try to create folder if not exist, remember to add root path.
                            if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath)) {
                                mkdir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath, 0777, true);
                            }

                            // Try to store the image, check if file already exists first.
                            if (
                                file_exists($_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $carImageName) ||
                                !move_uploaded_file($_FILES["car-image"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $carImageName)
                            ) {
                                $addCarMsg = "* ERROR: Failed to upload Car Image! Possibly Image already exists!";
                            }
                            else {
                                $passChecking = true;
                            }
                        }
                    }

                    // Try to insert new record.
                    if ($passChecking) {
                        $passChecking = false;

                        $brandId = "";

                        // Check if car brand already exists or not.
                        $query = "SELECT Brands.id, Brands.brandName FROM Brands WHERE Brands.brandName = '$carBrand';";
                        $rs = mysqli_query($serverConnect, $query);

                        if ($rs) {
                            if ($car = mysqli_fetch_assoc($rs)) {
                                if ($car["brandName"] == $carBrand) {
                                    // Car brand is added before.
                                    $brandId = $car['id'];
                                    $passChecking = true;
                                }
                            }
                        }

                        // Car brand is not added before.
                        if (!$passChecking || empty($brandId)) {
                            $passChecking = false;

                            $query = "INSERT INTO Brands(brandName) VALUES ('$carBrand');";

                            $rs = mysqli_query($serverConnect, $query);
                            
                            if ($rs) {
                                // Get the newly added brand id.
                                $query = "SELECT Brands.id FROM Brands WHERE Brands.brandName = '$carBrand';";
                                $rs = mysqli_query($serverConnect, $query);
        
                                if ($rs) {
                                    if ($car = mysqli_fetch_assoc($rs)) {
                                        $brandId = $car['id'];
                                        $passChecking = true;
                                    }
                                }

                                if (!$passChecking || empty($brandId)) {
                                    $addCarMsg = "* ERROR: Failed to get Brand ID!";
                                    $passChecking = false;
                                }
                            }
                            else {
                                $addCarMsg = "* ERROR: Failed to add new Car Brand!";
                            }
                        }

                        // Try to insert new record.
                        if ($passChecking && !empty($brandId)) {
                            $query = "INSERT INTO Cars(brandId, carModel, monthPrice, leaseTime, initialPay, carDesc, carImage, imagePath, dateAdded)
                            VALUES
                            ('$brandId', '$carModel', '$monthPrice', '$leaseTime', '$initialPay', '$carDesc', '$carImageName', '$targetImagePath', '$currentDate')
                            ;";

                            $rs = mysqli_query($serverConnect, $query);
                            
                            if ($rs) {
                                $addCarMsg = "* New Car has been successfully added!";
                            }
                            else {
                                $passChecking = false;
                                $addCarMsg = "* ERROR: Failed to add new Car!";
                            }
                        }

                        if (!$passChecking) {
                            // Better to manually delete the folder as this is too dangerous!
                            // Delete the image only.
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $carImageName)) {
                                unlink($_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $carImageName);
                            }
                        }
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
            // Edit Car
            else if ($_POST["manage-mode"] == "edit-car") {
                $manageMode = $_POST["manage-mode"];

                $carId = (isset($_POST['car-id'])) ? testInput($_POST['car-id']): "";
                $carBrand = (isset($_POST['car-brand'])) ? testInput($_POST['car-brand']): "";
                $carModel = (isset($_POST['car-model'])) ? testInput($_POST['car-model']): "";
                $monthPrice = (isset($_POST['month-price'])) ? testInput($_POST['month-price']): "";
                $leaseTime = (isset($_POST['lease-time'])) ? testInput($_POST['lease-time']): "";
                $initialPay = (isset($_POST['initial-pay'])) ? testInput($_POST['initial-pay']): "";
                $carDesc = (isset($_POST['car-desc'])) ? testInput($_POST['car-desc']): "";
                
                // Check if the car is allowed to be edited.
                if (!empty($carId) && !empty($carBrand) && !empty($carModel)) {
                    $query = "SELECT Brands.brandName, Cars.carModel FROM Cars INNER JOIN Brands ON Cars.brandId = Brands.id WHERE Cars.id=$carId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($car = mysqli_fetch_assoc($rs)) {
                            if ($car['brandName'] == $carBrand && $car['carModel'] == $carModel) {
                                // Allow to edit.
                                $allowEditCar = true;
                            }
                        }
                    }
                }

                if (!$allowEditCar) {
                    $editCarMsg = "* You are not allowed to edit the selected Car!";
                }
                else if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    // Check if month price is provided.
                    if (empty($monthPrice) || is_nan($monthPrice) || $monthPrice < 100 || $monthPrice > 1000) {
                        $editCarMsg = "* Enter Price Per Month (Min: 100; Max: 1000)!";
                    }
                    // Check if lease time is provided.
                    else if (empty($leaseTime) || is_nan($leaseTime) || $leaseTime < 6 || $leaseTime > 60) {
                        $editCarMsg = "* Enter Lease Time (Min: 6; Max: 60; Months)!";
                    }
                    // Check if initial pay is provided.
                    else if (empty($initialPay) || is_nan($initialPay) || $initialPay < 3 || $initialPay > 10) {
                        $editCarMsg = "* Enter Initial Pay (Min: 3; Max: 10; * Price)!";
                    }
                    // Check if car description is provided.
                    else if (empty($carDesc) || strlen($carDesc) < 5 || strlen($carDesc) > 512) {
                        $editCarMsg = "* Enter Car Description (Min: 5 Char)!";
                    }
                    else {
                        $passChecking = true;
                    }
                    
                    $newCarImage = false;
                    $currentDate = date("Y-m-d H:i:s");
                    $carImageName = $oldCarImage = $targetImagePath = "";

                    // Continue checking.
                    if ($passChecking) {
                        $passChecking = false;

                        // Only allow PNG or JPG.
                        $allowImageType = array('png', 'jpeg', 'jpg');
                        $carImageName = (isset($_FILES['car-image']['name'])) ? testInput($_FILES['car-image']['name']): "";
                        $carImageName = str_replace(" ", "_", $carImageName);

                        // Get image type.
                        $imageFileType = (!empty($carImageName)) ? pathinfo($carImageName, PATHINFO_EXTENSION): "";

                        // No new image is provided.
                        if (!isset($_FILES['car-image']) || empty($carImageName)) {
                            $passChecking = true;
                        }
                        // Check if new image is valid (actual max is 2 MiB).
                        else if (
                            $_FILES['car-image']['size'] < 1 || $_FILES['car-image']['size'] > 2097152 ||
                            !in_array($imageFileType, $allowImageType)
                        ) {
                            $editCarMsg = "* Upload a Car Image (Max: 2 MB; Only PNG or JPG)!";
                        }
                        // Check if the image already exists (file with same name detected).
                        else {
                            $query = "SELECT Cars.carImage, Cars.imagePath FROM Cars WHERE Cars.id=$carId;";
                            $rs = mysqli_query($serverConnect, $query);

                            if ($rs) {
                                if ($car = mysqli_fetch_assoc($rs)) {
                                    $oldCarImage = ((isset($car['carImage'])) ? $car['carImage']: "");
                                    $targetImagePath = ((isset($car['imagePath'])) ? $car['imagePath']: "");
                                }
                            }

                            // Folder not found.
                            if (empty($targetImagePath) || !is_dir($_SERVER['DOCUMENT_ROOT'] . $targetImagePath)) {
                                $editCarMsg = "* ERROR: Image Folder not found!";
                            }
                            // Image already exists.
                            else if (file_exists($_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $carImageName)) {
                                $editCarMsg = "* Car Image already exists, try image with different name!";
                            }
                            else {
                                $passChecking = true;
                                $newCarImage = true;
                            }
                        }
                    }

                    // Try to update record (upload new image first if provided).
                    if ($passChecking) {
                        $query = "UPDATE Cars SET Cars.monthPrice='$monthPrice', Cars.leaseTime='$leaseTime', Cars.initialPay='$initialPay', Cars.carDesc='$carDesc', Cars.dateEdited='$currentDate' WHERE Cars.id=$carId;";

                        // Check if new image is provided.
                        if ($newCarImage) {
                            $passChecking = false;
                            
                            // Delete old image.
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $oldCarImage)) {
                                unlink($_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $oldCarImage);
                            }

                            // Upload new image.
                            if (!move_uploaded_file($_FILES["car-image"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $targetImagePath . $carImageName)) {
                                $editCarMsg = "* ERROR: Failed to upload new Car Image!";
                            }
                            else {
                                $query = "UPDATE Cars SET Cars.monthPrice='$monthPrice', Cars.leaseTime='$leaseTime', Cars.initialPay='$initialPay', Cars.carDesc='$carDesc', Cars.dateEdited='$currentDate', Cars.carImage='$carImageName' WHERE Cars.id=$carId;";

                                $passChecking = true;
                            }
                        }

                        if ($passChecking) {
                            $rs = mysqli_query($serverConnect, $query);
                            
                            if ($rs) {
                                $editCarMsg = "* Changes have been saved successfully!";
                            }
                            else {
                                $passChecking = false;
                                $editCarMsg = "* ERROR: Failed to save changes!";
                            }
                        }
                    }
                }
            }
            // Delete Car
            else if ($_POST["manage-mode"] == "delete-car") {
                $manageMode = $_POST["manage-mode"];

                $carId = (isset($_POST['car-id'])) ? testInput($_POST['car-id']): "";
                $currentAdminPass = (isset($_POST['current-admin-password'])) ? testInput($_POST['current-admin-password']): "";

                // Check if the car is allowed to be deleted.
                if (!empty($carId)) {
                    $query = "SELECT id FROM Cars WHERE id=$carId;";
                    $rs = mysqli_query($serverConnect, $query);

                    if ($rs) {
                        if ($car = mysqli_fetch_assoc($rs)) {
                            // Allow to delete.
                            $allowDeleteCar = true;
                        }
                    }
                }

                if (!$allowDeleteCar) {
                    $deleteCarMsg = "* You are not allowed to delete the selected Car!";
                }
                else if (isset($_POST["check-form"]) && $_POST["check-form"] == "yes") {
                    $passChecking = true;

                    // Check if password of logged in admin is provided.
                    if (
                        empty($currentAdminPass) ||
                        strlen($currentAdminPass) < 6 || strlen($currentAdminPass) > 256 ||
                        !preg_match("/^(?=(?:.*[A-Z]))(?=(?:.*[a-z]))(?=.*?[^A-Za-z0-9])(?=(?:.*[\t\n]){0})(?=(?:.*\d){3,})(.{6,})$/", $currentAdminPass)
                    ) {
                        $deleteCarMsg = "* Enter Your Password to Confirm Delete!";
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
                            $deleteCarMsg = "* Invalid Password Entered!";
                        }
                    }
                    
                    if ($passChecking) {
                        // Get the image folder path to delete the image in it.
                        $imagePathDel = "";
                        $imageToDel = "";
                        $query = "SELECT imagePath, carImage FROM Cars WHERE id=$carId;";
                        $rs = mysqli_query($serverConnect, $query);
    
                        if ($rs) {
                            if ($car = mysqli_fetch_assoc($rs)) {
                                $imagePathDel = (isset($car['imagePath'])) ? testInput($car['imagePath']): "";
                                $imageToDel = (isset($car['carImage'])) ? testInput($car['carImage']): "";
                            }
                        }

                        $query = "DELETE FROM Cars WHERE id=$carId;";
                        $rs = mysqli_query($serverConnect, $query);

                        if (!($rs)) {
                            $passChecking = false;
                            $deleteCarMsg = "* ERROR: Failed to delete Car ID $carId! Recheck if it is used in other tables!";
                        }
                        
                        if ($passChecking) {
                            $deleteCarMsg = "* Car ID $carId has been deleted successfully!";

                            // Check if folder exists.
                            if (is_dir($_SERVER['DOCUMENT_ROOT'] . $imagePathDel)) {
                                // Delete the image.
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePathDel . $imageToDel)) {
                                    unlink($_SERVER['DOCUMENT_ROOT'] . $imagePathDel . $imageToDel);
                                }
                            }
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
                            <form id='manage-add-form' method='post' action='/admin/manageVehicle.php' enctype='multipart/form-data'>
                                <input type='hidden' name='manage-mode' value='add-car'>
                                <input type='hidden' name='check-form' value='yes'>

                                <div>
                                    <label for='car-brand'>
                                        Brand (No Edit if Saved):
                                    </label><br>

                                    <input id='car-brand' type='text' name='car-brand' placeholder='Car Brand (Min: 3 Char)' value='<?php
                                        echo((isset($_POST['car-brand'])) ? testInput($_POST['car-brand']): '');
                                    ?>' list='all-brand' minlength="3" maxlength="100" required>
                                    
                                    <?php
                                        $query = "SELECT brandName FROM Brands;";
                                        $rs = mysqli_query($serverConnect, $query);
                                    ?>

                                    <datalist id="all-brand">
                                        <?php if ($rs): ?>
                                            <?php while ($brand = mysqli_fetch_assoc($rs)): ?>
                                                <?php if (isset($brand['brandName']) && !empty($brand['brandName'])): ?>
                                                    <option value='<?php
                                                        echo(testInput($brand['brandName']));
                                                    ?>'>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </datalist>
                                </div>

                                <div>
                                    <label for='car-model'>
                                        Model (No Edit if Saved):
                                    </label><br>

                                    <input id='car-model' type='text' name='car-model' placeholder='Car Model (Min: 2 Char)' value='<?php
                                        echo((isset($_POST['car-model'])) ? testInput($_POST['car-model']): '');
                                    ?>' minlength="2" maxlength="100" required>
                                </div>

                                <div>
                                    <label for='month-price'>
                                        Price (£/mth):
                                    </label><br>

                                    <input id='month-price' type='number' min="100" max="1000" name='month-price' placeholder='(Min: 100; Max: 1000) Per Month' value='<?php
                                        echo((isset($_POST['month-price'])) ? testInput($_POST['month-price']): '');
                                    ?>' required>
                                </div>

                                <div>
                                    <label for='lease-time'>
                                        Lease Time (Month):
                                    </label><br>

                                    <input id='lease-time' type='number' min="6" max="60" name='lease-time' placeholder='(Min: 6; Max: 60) Months' value='<?php
                                        echo((isset($_POST['lease-time'])) ? testInput($_POST['lease-time']): '');
                                    ?>' required>
                                </div>

                                <div>
                                    <label for='initial-pay'>
                                        Initial Pay (* £/mth):
                                    </label><br>

                                    <input id='initial-pay' type='number' min="3" max="10" name='initial-pay' placeholder='(Min: 3; Max: 10) * Price' value='<?php
                                        echo((isset($_POST['initial-pay'])) ? testInput($_POST['initial-pay']): '');
                                    ?>' required>
                                </div>

                                <div>
                                    <label for='car-desc'>
                                        Description:
                                    </label><br>

                                    <textarea id='car-desc' type='text' name='car-desc' placeholder='Car Description (Min: 5 Char)' rows='5' minlength="5" maxlength='512' required><?php
                                        echo((isset($_POST['car-desc'])) ? testInput($_POST['car-desc']): '');
                                    ?></textarea>
                                </div>

                                <div>
                                    <label for='car-image'>
                                        Car Image (Max: 2 MB; PNG/JPG):
                                    </label><br>

                                    <input id='car-image' type="file" name='car-image' accept="image/png, image/jpg, image/jpeg" required>
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
                                $query = "SELECT Cars.id, Cars.carModel, Cars.monthPrice, Cars.leaseTime, Cars.initialPay, Cars.carDesc, Cars.carImage, Cars.imagePath, Cars.dateAdded, Cars.dateEdited, Brands.brandName FROM Cars INNER JOIN Brands ON Cars.brandId = Brands.id WHERE Cars.id=$carId;";
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
                                                    <?php echo((isset($car["brandName"])) ? $car["brandName"]: ""); ?>
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
                                                <td class='multiline-text'><?php
                                                    echo((isset($car["carDesc"])) ? testInput($car["carDesc"]): ""); 
                                                ?></td>
                                            </tr>
                                            
                                            <tr>
                                                <td>Car Image</td>
                                                <td>
                                                    <img src='<?php
                                                        echo((isset($car["imagePath"]) && !empty($car["imagePath"]) && isset($car["carImage"]) && !empty($car["carImage"])) ? $car["imagePath"] . $car["carImage"]: "");
                                                    ?>' alt='<?php
                                                        echo((isset($car["carModel"])) ? $car["carModel"] . "_image": "_none");
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

                                    <form method='post' action='/admin/manageVehicle.php'>
                                        <input type='hidden' name='manage-mode' value='edit-car'>
                                        <input type='hidden' name='car-id' value='<?php
                                            echo((isset($car["id"])) ? $car["id"]: "");
                                        ?>'>
                                        <input type='hidden' name='car-brand' value='<?php
                                            echo((isset($car["brandName"])) ? $car["brandName"]: "");
                                        ?>'>
                                        <input type='hidden' name='car-model' value='<?php
                                            echo((isset($car["carModel"])) ? $car["carModel"]: "");
                                        ?>'>
                                        <input type='hidden' name='month-price' value='<?php
                                            echo((isset($car["monthPrice"])) ? $car["monthPrice"]: "");
                                        ?>'>
                                        <input type='hidden' name='lease-time' value='<?php
                                            echo((isset($car["leaseTime"])) ? $car["leaseTime"]: "");
                                        ?>'>
                                        <input type='hidden' name='initial-pay' value='<?php
                                            echo((isset($car["initialPay"])) ? $car["initialPay"]: "");
                                        ?>'>
                                        <input type='hidden' name='car-desc' value='<?php
                                            echo((isset($car["carDesc"])) ? $car["carDesc"]: "");
                                        ?>'>

                                        <button class='positive-button'>Edit Car</button>
                                    </form>

                                    <?php
                                        $lastPage = "javascript:history.go(-1)";
                                        if (isset($_SERVER['HTTP_REFERER'])) {
                                            $lastPage = $_SERVER['HTTP_REFERER'];
                                        }
                                    ?>

                                    <form method='post' action='<?php
                                        echo((isset($lastPage) && !empty($lastPage)) ? $lastPage: "/admin/manageVehicle.php");
                                    ?>'>
                                        <button>Return Previous Page</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <!-- Edit Car -->
                    <?php elseif ($manageMode == "edit-car"): ?>
                        <h3>Edit <i>Car ID <?php
                            echo((isset($_POST['car-id'])) ? testInput($_POST['car-id']): "");
                        ?></i>:</h3>

                        <?php if (isset($editCarMsg) && !empty($editCarMsg)): ?>
                            <?php if (!$allowEditCar || !$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($editCarMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($editCarMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowEditCar && !$passChecking): ?>
                            <form id='manage-edit-form' method='post' action='/admin/manageVehicle.php' enctype='multipart/form-data'>
                                <input type='hidden' name='manage-mode' value='edit-car'>
                                <input type='hidden' name='car-id' value='<?php
                                    echo((isset($_POST["car-id"])) ? $_POST["car-id"]: "");
                                ?>'>
                                <input type='hidden' name='car-brand' value='<?php
                                    echo((isset($_POST["car-brand"])) ? $_POST["car-brand"]: "");
                                ?>'>
                                <input type='hidden' name='car-model' value='<?php
                                    echo((isset($_POST["car-model"])) ? $_POST["car-model"]: "");
                                ?>'>
                                <input type='hidden' name='check-form' value='yes'>

                                <div>
                                    <label for='car-name'>
                                        Brand & Model:
                                    </label><br>

                                    <input id='car-name' type='text' value='<?php
                                        echo((isset($_POST['car-brand'])) ? testInput($_POST['car-brand']): '');
                                        echo((isset($_POST['car-model'])) ? " " . testInput($_POST['car-model']): '');
                                    ?>' disabled>
                                </div>

                                <div>
                                    <label for='month-price'>
                                        Price (£/mth):
                                    </label><br>

                                    <input id='month-price' type='number' min="100" max="1000" name='month-price' placeholder='(Min: 100; Max: 1000) Per Month' value='<?php
                                        echo((isset($_POST['month-price'])) ? testInput($_POST['month-price']): '');
                                    ?>' required>
                                </div>

                                <div>
                                    <label for='lease-time'>
                                        Lease Time (Month):
                                    </label><br>

                                    <input id='lease-time' type='number' min="6" max="60" name='lease-time' placeholder='(Min: 6; Max: 60) Months' value='<?php
                                        echo((isset($_POST['lease-time'])) ? testInput($_POST['lease-time']): '');
                                    ?>' required>
                                </div>

                                <div>
                                    <label for='initial-pay'>
                                        Initial Pay (* £/mth):
                                    </label><br>

                                    <input id='initial-pay' type='number' min="3" max="10" name='initial-pay' placeholder='(Min: 3; Max: 10) * Price' value='<?php
                                        echo((isset($_POST['initial-pay'])) ? testInput($_POST['initial-pay']): '');
                                    ?>' required>
                                </div>

                                <div>
                                    <label for='car-desc'>
                                        Description:
                                    </label><br>

                                    <textarea id='car-desc' type='text' name='car-desc' placeholder='Car Description (Min: 5 Char)' rows='5' minlength="5" maxlength='512' required><?php
                                        echo((isset($_POST['car-desc'])) ? testInput($_POST['car-desc']): '');
                                    ?></textarea>
                                </div>

                                <div>
                                    <label for='car-image'>
                                        Car Image (Replace Old Image):
                                    </label><br>

                                    <input id='car-image' type="file" name='car-image' accept="image/png, image/jpg, image/jpeg">
                                </div>
                            </form>

                            <form id='cancel-edit-form' method='post' action='/admin/manageVehicle.php'></form>

                            <div class='button-section'>
                                <button form='manage-edit-form' class='positive-button' type='submit'>
                                    Confirm Edit
                                </button>
                                
                                <button form='cancel-edit-form' class='negative-button'>
                                    Cancel
                                </button>
                            </div>
                        <?php endif; ?>
                    <!-- Delete Car -->
                    <?php elseif ($manageMode == "delete-car"): ?>
                        <h3>Delete <i>Car ID <?php
                            echo((isset($_POST['car-id'])) ? testInput($_POST['car-id']): "");
                        ?></i>:</h3>

                        <?php if (isset($deleteCarMsg) && !empty($deleteCarMsg)): ?>
                            <?php if (!$allowDeleteCar || !$passChecking): ?>
                                <span class='error-message'>
                                    <?php echo($deleteCarMsg); ?>
                                </span>
                            <?php else: ?>
                                <span class='success-message'>
                                    <?php echo($deleteCarMsg); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($allowDeleteCar && !$passChecking): ?>
                            <form id='manage-delete-form' method='post' action='/admin/manageVehicle.php'>
                                <input type='hidden' name='manage-mode' value='delete-car'>
                                <input type='hidden' name='check-form' value='yes'>
                                <input type='hidden' name='car-id' value='<?php
                                    echo((isset($_POST['car-id'])) ? testInput($_POST['car-id']): "");
                                ?>'>

                                <div>
                                    <label for='current-admin-password'>
                                        Your Password:
                                    </label><br>

                                    <input id='current-admin-password' type='password' name='current-admin-password' placeholder='Required to confirm delete' minlength="6" maxlength="256" required>
                                </div>
                            </form>

                            <form id='cancel-delete-form' method='post' action='/admin/manageVehicle.php'></form>
                            
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
                    (isset($manageMode) && $manageMode == "search-car") ||
                    (isset($manageMode) && $manageMode == "view-car" && !$allowViewCar) ||
                    (isset($manageMode) && $manageMode == "edit-car" && !$allowEditCar) ||
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
                        ?>' minlength="1" maxlength="100" required>
                        
                        <button form='manage-search-form' class='small-button positive-button'>Search</button>

                        <button form='cancel-search-form' class='small-button negative-button'<?php
                            echo((isset($wordToSearch) && !empty($wordToSearch)) ? "": " disabled");
                        ?>>Reset</button>

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
                                // Select from Cars and Brands tables.
                                $query = "SELECT Cars.id, Cars.carModel, Cars.monthPrice, Cars.leaseTime, Cars.initialPay, Brands.brandName FROM Cars INNER JOIN Brands ON Cars.brandId = Brands.id" .
                                (
                                    (isset($wordToSearch) && !empty($wordToSearch)) ?
                                    " WHERE Cars.id LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR Brands.brandName LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%' OR Cars.carModel LIKE '%" .
                                    testInput($wordToSearch) .
                                    "%'" : ""
                                ) .
                                " ORDER BY Cars.dateEdited DESC;";
                                
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
                                            <?php echo((isset($car["brandName"])) ? $car["brandName"]: ""); ?>
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
