<?php
    // Member Dashboard: Home for LINGsCARS
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

    $wordToSearch = "";

    $viewCarMsg = "";
    $allowViewCar = false;

    $addCartMsg = "";
    $allowAddCart = false;
    
    if (!empty($manageMode)) {
        // Search Car
        if ($manageMode == "search-car") {
            $wordToSearch = (isset($queryString['word-to-search'])) ? testInput($queryString['word-to-search']): "";
        }
        // View Car
        else if ($manageMode == "view-car") {
            $carId = (isset($queryString['car-id'])) ? testInput($queryString['car-id']): "";
            
            // Check if the Car is allowed to be viewed.
            if (!empty($carId) && is_numeric($carId)) {
                $query = "SELECT id FROM cars WHERE id=$carId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($record = mysqli_fetch_assoc($rs)) {
                        // Allow to view.
                        $allowViewCar = true;
                    }
                }
            }

            if (!$allowViewCar) {
                $viewCarMsg = "* You are not allowed to view the selected Car!";
            }
        }
        // Add to Cart
        else if ($manageMode == "add-to-cart") {
            $carId = (isset($queryString['car-id'])) ? testInput($queryString['car-id']): "";
            
            // Check if the Car is allowed to be added.
            if (!empty($carId) && is_numeric($carId)) {
                $query = "SELECT id FROM cars WHERE id=$carId;";
                $rs = mysqli_query($serverConnect, $query);

                if ($rs) {
                    if ($record = mysqli_fetch_assoc($rs)) {
                        // Allow to add to cart.
                        $allowAddCart = true;
                    }
                }
            }

            if (!$allowAddCart) {
                $addCartMsg = "* You are not allowed to add the selected Car!";
            }
            else if (
                $_SERVER["REQUEST_METHOD"] == "POST" &&
                isset($_POST['allow-add-cart']) && $_POST['allow-add-cart'] == 'yes'
            ) {
                if (!isset($_SESSION['cart-item'])) {
                    $_SESSION['cart-item'] = array();
                }

                // Store into session, if car quantity is greater than 0 then add one, else set to 1.
                if (isset($_SESSION['cart-item'][$carId]) && $_SESSION['cart-item'][$carId] > 0) {
                    // Max = 10.
                    if ($_SESSION['cart-item'][$carId] > 9) {
                        $_SESSION['cart-item'][$carId] = 10;
                        $addCartMsg = "* Car ID " . $carId . " has reached Maximum Quantity (10)!";
                        $allowAddCart = false;
                    }
                    else {
                        $_SESSION['cart-item'][$carId]++;
                    }
                }
                else {
                    $_SESSION['cart-item'][$carId] = 1;
                }
                
                if ($allowAddCart) {
                    $addCartMsg = "* Car is added to your cart! Car ID " . $carId . ": Current Quantity = " . $_SESSION['cart-item'][$carId] . "!";
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
                    <a href="./index.php" class="active">&#127984; <b>Home</b> &#127984;</a>
                </li>

                <li class="dropdown_list">
                    Profile
                    <div class="dropdown_menu">
                        <a href="#">Manage Profile</a>
                        <a href="#">Log Out</a>
                    </div>
                </li>

                <li>
                    <a href="/about.html">About Ling</a>
                </li>

                <li>
                    <a href="/cart.php">Cart</a>
                </li>

                <li>
                    <a href="#">Order History</a>
                </li>

                <li>
                    <a href="/loginPage.php" style="margin-top:3px;">Sign In</a>
                </li>
            </ul>
        </nav>
        
        <main>
            <div class="video-container">
                <video autoplay loop muted>
                    <source src="./video/customMainPromo/mainPromo.mp4" type="video/mp4">
                </video>
                <div id="business-info">
                    <h3 style="text-decoration: underline;">Business Info</h3>
                    <p>15 Riverside Studios
                        Newcastle Business Park
                        Newcastle upon Tyne, NE4 7YL</p>   
                    <p>TEL <span class="fa fa-phone"></span> 0191 460 9444</p>
                    <p>FAX <span class="fa fa-fax"></span> 0870 486 1130</p>
                    <p>EMAIL <span class="fa fa-envelope"></span> <a href="mailto:sales@lingscars.com">sales@LINGsCARS.com</a></p>     
                    <p>Consumer Credit Licence: 663330</p>
                    <p>Data Protection No: Z1098490</p>
                </div>
            </div>

            <h1 style="text-align: center;">&#128293;&#128293; <span style="display: inline-block;">MAIN DEALS</span>&#10069;&#10069;&#10069; &#128293;&#128293;</h1>

            <?php if ($manageMode == "view-car"): ?>
                <h3 style="text-align: center;">View <i>Car ID <?php
                    echo((isset($carId)) ? $carId: "");
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
                        $query = "SELECT cars.id, cars.carModel, cars.monthPrice, cars.leaseTime, cars.initialPay, cars.carDesc, cars.carImage, cars.imagePath, cars.dateAdded, cars.dateEdited, brands.brandName FROM cars INNER JOIN brands ON cars.brandId = brands.id WHERE cars.id=$carId;";
                        $rs = mysqli_query($serverConnect, $query);
                    ?>

                    <?php if ($rs): ?>
                        <?php if ($car = mysqli_fetch_assoc($rs)): ?>
                            <div class='view-content'>
                                <table>
                                    <tr>
                                        <td>Car ID</td>
                                        <td>
                                            <?php echo((isset($car["id"])) ? $car["id"]: "-"); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Brand</td>
                                        <td>
                                            <?php echo((isset($car["brandName"])) ? $car["brandName"]: "-"); ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Model</td>
                                        <td>
                                            <?php echo((isset($car["carModel"])) ? $car["carModel"]: "-"); ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Price (£/mth)</td>
                                        <td>
                                            <?php echo((isset($car["monthPrice"])) ? $car["monthPrice"]: "-"); ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Lease Time (Month)</td>
                                        <td>
                                            <?php echo((isset($car["leaseTime"])) ? $car["leaseTime"]: "-"); ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Initial Pay (* £/mth)</td>
                                        <td>
                                            <?php echo((isset($car["initialPay"])) ? $car["initialPay"]: "-"); ?>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Description</td>
                                        <td class='multiline-text'><?php
                                            echo((isset($car["carDesc"])) ? testInput($car["carDesc"]): "-"); 
                                        ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Car Image</td>
                                        <td>
                                            <img src="<?php
                                                echo((isset($car["imagePath"]) && !empty($car["imagePath"]) && isset($car["carImage"]) && !empty($car["carImage"])) ? $car["imagePath"] . $car["carImage"]: "");
                                            ?>" alt='<?php
                                                echo((isset($car["carModel"])) ? $car["carModel"] . "_image": "_none");
                                            ?>'>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>Added On</td>
                                        <td>
                                            <?php echo((isset($car["dateAdded"])) ? $car["dateAdded"]: "-"); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Last Edit</td>
                                        <td>
                                            <?php echo((isset($car["dateEdited"])) ? $car["dateEdited"]: "-"); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <?php
                                $newQueryString = array();
                                $newQueryString['manage-mode'] = 'add-to-cart';
                                $newQueryString['car-id'] = (isset($car["id"])) ? $car["id"]: '';
                            ?>

                            <form method='post' action='/index.php?<?php
                                echo(http_build_query($newQueryString));
                            ?>'>
                                <input type='hidden' name='allow-add-cart' value='yes'>
                                <button>Add to Cart</button>
                            </form>

                            <form method='get' action='/index.php'>
                                <button>Return to Home Page</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($manageMode != "view-car" || !$allowViewCar): ?>
                <?php if (!empty($addCartMsg)): ?>
                    <h2 class='<?php
                        echo(($allowAddCart) ? "success-message": "error-message")
                    ?>'><?php
                        echo($addCartMsg);
                    ?></h2>
                <?php endif; ?>

                <form id='cancel-search-form' method='get' action='/index.php'></form>

                <form id='manage-search-form' method='get' action='/index.php'>
                    <input type='hidden' name='manage-mode' value='search-car'>
                </form>

                <form id='view-cart-form' method='get' action='/cart.php'></form>
            
                <div class='button-section'>
                    <input id='word-to-search' form='manage-search-form' type='text' name='word-to-search' placeholder='Enter Car Brand or Model' value='<?php
                        echo((isset($wordToSearch) && !empty($wordToSearch)) ? testInput($wordToSearch): "");
                    ?>' minlength="1" maxlength="100" required>
                    
                    <button form='manage-search-form' class='small-button positive-button'>Search</button>

                    <button form='cancel-search-form' class='small-button negative-button'<?php
                        echo((isset($wordToSearch) && !empty($wordToSearch)) ? "": " disabled");
                    ?>>Reset</button>

                    <button form='view-cart-form' class='small-button'>View Cart</button>
                </div>

                <section class="flex-container" style="text-align: center;">
                    <?php
                        $query = "SELECT cars.id, cars.carModel, cars.monthPrice, cars.leaseTime, cars.initialPay, cars.carDesc, cars.carImage, cars.imagePath, cars.dateAdded, cars.dateEdited, brands.brandName FROM cars INNER JOIN brands ON cars.brandId = brands.id" .
                        (
                            (isset($wordToSearch) && !empty($wordToSearch)) ?
                            " WHERE brands.brandName LIKE '%" .
                            testInput($wordToSearch) .
                            "%' OR cars.carModel LIKE '%" .
                            testInput($wordToSearch) .
                            "%'" : ""
                        ) .
                        " ORDER BY cars.dateEdited DESC;";
                        $rs = mysqli_query($serverConnect, $query);
                    ?>

                    <?php if ($rs): ?>
                        <?php $totalCarFound = mysqli_num_rows($rs); ?>

                        <?php while ($car = mysqli_fetch_assoc($rs)): ?>
                            <div class="flex-item car-details car-colors-purple">
                                <p class="car-details-headline">
                                    <b>&#128226;GREAT DEAL&#10071;</b>
                                </p>
                                <a class="car-details-link" href="/index.php?manage-mode=view-car&car-id=<?php
                                        echo((isset($car["id"])) ? $car["id"]: '');
                                    ?>">
                                    <img class="car-details-image" src="<?php
                                        echo((isset($car["imagePath"]) && !empty($car["imagePath"]) && isset($car["carImage"]) && !empty($car["carImage"])) ? $car["imagePath"] . $car["carImage"]: "");
                                    ?>" alt='<?php
                                        echo((isset($car["carModel"])) ? $car["carModel"] . "_image": "_none");
                                    ?>'>

                                    <h2><?php
                                        echo((isset($car["brandName"])) ? "" . $car["brandName"]: '');
                                        echo((isset($car["carModel"])) ? ": " . $car["carModel"]: '');
                                    ?></h2>

                                    <span class="car-details-trim multiline-text"><?php
                                        echo((isset($car["carDesc"])) ? testInput($car["carDesc"]): "-"); 
                                    ?></span>
                                </a>
                                <div class="car-details-content">
                                    <div class="car-details-price">
                                        <p>&#128176; <b>£<?php
                                            echo((isset($car["monthPrice"])) ? $car["monthPrice"]: "-");
                                        ?>/mth</b> (VAT) &#128176;</p>
                                    </div>

                                    <div class="car-details-ling car-details-ling-8"></div>

                                    <div class="car-details-term payments-3-47"></div>

                                    <p class="car-details-term-description">
                                        <b><?php
                                            if (isset($car['leaseTime']) && is_numeric($car['leaseTime'])) {
                                                if ((int)($car['leaseTime'] / 12) > 0) {
                                                    echo((int)($car['leaseTime'] / 12) . ' year ');
                                                }
                                                if ($car['leaseTime'] % 12 > 0) {
                                                    echo(($car['leaseTime'] % 12) . ' month');
                                                }
                                            }
                                        ?></b> cheap<br> car leasing
                                    </p>

                                    <div class="car-features">
                                        <span class="car-details-petrol">&#9989;<b>Powerful engine</b></span><br>
                                        <span class="car-details-manual">&#9989;<b>Good condition</b></span><br>
                                        <span class="car-details-metallic">&#9989;<b>Attractive Design</b></span>
                                    </div>
                                    <div class="car-details-mileage">
                                        <p>(Other mileages available)</p>
                                    </div>
                                </div>
                                <?php
                                    $newQueryString = array();
                                    $newQueryString['manage-mode'] = 'add-to-cart';
                                    $newQueryString['car-id'] = (isset($car["id"])) ? $car["id"]: '';
                                ?>

                                <form method='post' action='/index.php?<?php
                                    echo(http_build_query($newQueryString));
                                ?>'>
                                    <input type='hidden' name='allow-add-cart' value='yes'>
                                    <button>Add to Cart</button>
                                </form>
                            </div>
                        <?php endwhile; ?>

                        <?php if ($totalCarFound < 1): ?>
                            <div class="flex-item car-details car-colors-purple">
                                <p style='color: red;' class="car-details-headline">
                                    <b>&#10071; NO CAR FOUND &#10071;</b>
                                </p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

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
