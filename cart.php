<?php
    // Member Dashboard: Shopping Cart for LINGsCARS
    if (session_id() == "") {
        session_start();
    }
    require_once($_SERVER['DOCUMENT_ROOT'] . "/dbConnection.php");

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

    $editQuantMsg = "";
    $allowEditQuant = false;

    
    if (!isset($_SESSION['cart-item'])) {
        $_SESSION['cart-item'] = array();
    }

    if (!empty($manageMode)) {
        // Edit Car Quantity
        if ($manageMode == "edit-quantity") {
            $carId = (isset($queryString['car-id'])) ? testInput($queryString['car-id']): "";
            $carQuant = (isset($_POST['car-quantity'])) ? testInput($_POST['car-quantity']): "";
            
            // Check if the Car is allowed to be edited.
            if (!empty($carId) && is_numeric($carId)) {
                $query = "SELECT id FROM cars WHERE id=$carId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($record = mysqli_fetch_assoc($rs)) {
                        // Allow to edit.
                        $allowEditQuant = true;
                    }
                }
            }

            if (!$allowEditQuant) {
                $editQuantMsg = "* You are not allowed to edit the Quantity of Car ID " . $carId . "!";
            }
            else if (
                $_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($_POST["allow-edit-quantity"]) && $_POST["allow-edit-quantity"] == "yes"
            ) {
                // Check if quantity is provided.
                if (!is_numeric($carQuant)) {
                    $editQuantMsg = "* Enter Valid Quantity (Min: 0; Max: 10)! You entered $carQuant!";
                    $allowEditQuant = false;
                }
                else {
                    // Max is 10.
                    if ($carQuant > 10) {
                        $_SESSION['cart-item'][$carId] = 10;
                        $editQuantMsg = "* Car ID " . $carId . " has reached Maximum Quantity (10)!";
                        $allowEditQuant = false;
                    }
                    else if ($carQuant < 1) {
                        unset($_SESSION['cart-item'][$carId]);
                        $editQuantMsg = "* Car ID " . $carId . " has been removed from Cart!";
                    }
                    else {
                        $_SESSION['cart-item'][$carId] = $carQuant;
                        $editQuantMsg = "* Car ID " . $carId . " has changed Quantity!";
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
        <title>Personal & Business Car Leasing | LINGsCARS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/main.css">
        <link rel="shortcut icon" href="/favicon.ico">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>

    <body>
        <header>
            <p id="header_p1">
                &#128678;<b>LINGsCARS.com</b>&#128678;
            </p>
            <p id="header_p2">
                Leader of the Pack - The UK's favorite car leasing website!&#128168;&#128168;
            </p>
            
        </header>

        <nav class="fixed_nav_bar">
            <input type="checkbox" id="car-list">
            <ul>
                <li>
                    <a href="./index.php">Home</a>
                </li>

                <li class="dropdown_list">
                    Profile
                    <div class="dropdown_menu">
                        <a href="#">Edit Profile</a>
                        <a href="#">Log Out</a>
                    </div>
                </li>

                <li>
                    <a href="/cart.php" class="active">Cart</a>
                </li>

                <li>
                    <a href="#">Order History</a>
                </li>

                <li>
                    <a href="/loginPage.php">Sign in</a>
                </li>
            </ul>
        </nav>

        <main>
            <h1 style="text-align: center;">&#128722; <span style="display: inline-block;">SHOPPING CART</span> &#128722;</h1>

            <?php if (!empty($editQuantMsg)): ?>
                <h2 class='<?php
                    echo(($allowEditQuant) ? "success-message": "error-message")
                ?>'><?php
                    echo($editQuantMsg);
                ?></h2>
            <?php endif; ?>

            <div class='manage-section'>
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
                            <th>Quantity</th>
                            <th>Make Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $recordCount = 0; ?>

                        <?php foreach ($_SESSION['cart-item'] as $key=>$value): ?>
                            <?php
                                // Select from Cars and Brands tables.
                                $query = "SELECT cars.id, cars.carModel, cars.monthPrice, cars.leaseTime, cars.initialPay, brands.brandName FROM cars INNER JOIN brands ON cars.brandId = brands.id WHERE cars.id='$key';";
                                
                                $rs = mysqli_query($serverConnect, $query);
                            ?>

                            <?php if ($rs): ?>
                                <?php if ($car = mysqli_fetch_assoc($rs)): ?>
                                    <?php $recordCount++; ?>

                                    <tr>
                                        <td class='center-text'>
                                            <?php
                                                echo((isset($car["id"])) ? $car["id"]: "-");
                                            ?>
                                        </td>

                                        <td class='center-text'>
                                            <a style='font-weight: bold; text-decoration: none;' href="/index.php?manage-mode=search-car&word-to-search=<?php
                                                echo((isset($car["brandName"])) ? $car["brandName"]: '');
                                            ?>"><?php
                                                echo((isset($car["brandName"])) ? $car["brandName"]: "-");
                                            ?></a>
                                        </td>

                                        <td class='center-text'>
                                            <a style='font-weight: bold; text-decoration: none;' href="/index.php?manage-mode=view-car&car-id=<?php
                                                echo((isset($car["id"])) ? $car["id"]: '');
                                            ?>"><?php
                                                echo((isset($car["carModel"])) ? $car["carModel"]: "-");
                                            ?></a>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["monthPrice"])) ? $car["monthPrice"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["leaseTime"])) ? $car["leaseTime"]: "-"); ?>
                                        </td>

                                        <td class='center-text'>
                                            <?php echo((isset($car["initialPay"])) ? $car["initialPay"]: "-"); ?>
                                        </td>

                                        <td>
                                            <?php
                                                $newQueryString = array();
                                                $newQueryString['manage-mode'] = 'edit-quantity';
                                                $newQueryString['car-id'] = (isset($car["id"])) ? $car["id"]: '';
                                            ?>

                                            <form method='post' action='/cart.php?<?php
                                                echo(http_build_query($newQueryString));
                                            ?>'>
                                                <input type='hidden' name='allow-edit-quantity' value='yes'>

                                                <input class='cart-quantity' type='number' min="0" max="10" name='car-quantity' placeholder='(Min: 0; Max: 10)' value='<?php
                                                    echo((isset($value)) ? $value: '');
                                                ?>' required>

                                                <button class='positive-button'>Save</button>
                                            </form>
                                        </td>
                                        
                                        <td>
                                            <!-- To proposal page -->
                                            <form method='post' action='/index.php'>
                                                <input type='hidden' name='manage-mode' value='order-car'>
                                                <input type='hidden' name='car-id' value='<?php
                                                    echo((isset($car["id"])) ? $car["id"]: "");
                                                ?>'>

                                                <button class='negative-button'>Order</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>

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
            </div>

            <form method='get' action='/index.php'>
                <button>Return to Home Page</button>
            </form>

            <div class="social-border">
                <h4 style="text-align: center; color: d#264861">&#10024; Click here to visit our Facebook and Twitter &#10024;</h4>
                <div class="social">
                    <a target="blank" href="https://www.facebook.com/lingscars/" class="fa fa-facebook"></a>
                    <a target="blank" href="https://twitter.com/LINGsCARS" class="fa fa-twitter"></a>
                </div>
            </div>
        </main>
        
        <hr>
        <footer>
            <input type="checkbox" id="leaseInfo">
            <label for="leaseInfo">View Car and Vehicle Leasing Info</label>
            <br>
            
            <p id="leaseInfoText">
            Car and Vehicle leasing is the leasing of the use of a car for a fixed period 
            of time. It is a cost-effective alternative to car or vehicle purchase. It can 
            be known as PCP or contract hire. The key difference in a car lease is that 
            after the lease expires, the customer can return the car or vehicle to the dealer 
            for no cost, or can often buy it at an agreed price. Ling owns the UK’s favourite 
            car leasing company.<br><br>
            Rationale:<br>
            Car Leasing offers big advantages to customers. For the lease buyer, lease payments 
            will usually be lower than payments on a car loan would be and qualification is usually 
            easier. Some very cheap car leasing deals are available, but these change all the time. 
            Some consumers may prefer leasing as it allows them to simply return a car and select a 
            new model when the lease expires, allowing a consumer to drive a new vehicle every few 
            years without the responsibility of selling the old car. It’s a very simple car owning 
            solution. A car leasing customer does not have to worry about the future value of the 
            car or vehicle, while a vehicle owner does have this nagging doubt.<br><br>
            For the leasing company, leasing generates income from a vehicle the car leasing company 
            still owns and will be able to sell at auction or lease again once the original lease has 
            expired. As consumers will typically use a leased vehicle for a shorter period of time 
            than one they buy outright, leasing may generate repeat customers more quickly, which 
            may fit into various aspects of a finance company’s business model.<br><br>
            Car Lease agreement:<br>
            Car leasing agreements typically stipulate an early termination fee and limit the number 
            of miles a customer can drive (for passenger cars, a common mileage is 10,000 to 15,000 
            miles per year of the car lease). If the mileage allowance is exceeded, a per-mile fee 
            is charged. Customers can negotiate a higher mileage allowance, for a higher lease payment. 
            Car lease agreements usually specify how much wear and tear on the vehicle is allowable, 
            and the customer may face a fee if the car is not in good condition at the end of the lease.
            <br><br>
            At the end of a leasing term, the customer must either return the car or vehicle to the car 
            leasing company, or purchase it. The end of lease price is usually agreed upon when the lease 
            is signed but may be affected by car condition and mileage.
            </p>

            <ul>
                <li><a href="#leaseinfo">Terms and Conditions</a></li>
                <li><a href="#leaseinfo">Privacy Policy</a></li>
                <li><a href="#leaseinfo">Problems with this website?</a></li>
            </ul>

            <p>
                Company Reg No: 6178634 || VAT No: 866 0241 30<br>
                © Copyright 2004 - 2021 LINGsCARS.com. All rights reserved.<br>
                Made in the People's Republic of China (Ling, not the website... which was handcrafted by Ling, in the UK)<br>
                ALL INCOMING CONNECTIONS TO LINGsCARS.com ARE MONITORED FOR SECURITY AND PROVENANCE. NO SCAMMERS! - Ling
            </p>
        </footer>
    </body>
</html>
