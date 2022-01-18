<?php

    include_once './inc/member.php';

    session_start();
    $memberId = $_SESSION['memberId'] ?? '';
    if(!$memberId) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect(RELATIVE_LOGIN_URL);
    }

    require_once './inc/dbConnection.php';
    include_once './inc/preHead.php';
?>    
    <link rel="stylesheet" href="./css/overview.css">
    <script src="./js/packery.pkgd.min.js"></script>    
    <script src="./js/draggabilly.pkgd.min.js"></script>
    <script src="./js/Chart.js/3.7.0/chart.min.js"></script>
    <script src="./js/overview.js" defer></script>
    
<?php
    include_once './inc/postHead.php';
    printNavBar(4);
?>    
    <main>
        <section class="grid">
            <div class="grid-item">
                <h3>My Cart</h3>
<?php
    /*
    Cart
    */
    $recordCount = 0;
    if (!isset($_SESSION['cart-item'])) {
        $_SESSION['cart-item'] = array();
    }

    $cartItems = $_SESSION['cart-item'];

    if(empty($cartItems)) {
        echo '<p>Empty</p>';

    } else {

        $carIds = array();

        foreach ($cartItems as $carId => $quantity) {
            array_push($carIds, $carId);
        }
        unset($carId, $quantity);
        $carIds = array_unique($carIds);
        $carsCatalogue = getMultipleCars($carIds);

        $limit = 5;
        $itemProcessed = 0;

        $htmlTable = '
                <table>
                    <thead>
                        <tr>
                            <th>Car ID</th>
                            <th>Car</th>
                            <th>Rental Fee (£/mth)</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($cartItems as $carId => $quantity) {
            if($itemProcessed < $limit) {
                $car = $carsCatalogue[$carId];
                $carHTML = '<img src="..'.$car['imagePath'].$car['carImage'].'" style="max-height:30px;">
                <p style="display:inline-block; margin:0px;"><a href="/?manage-mode=view-car&car-id='.$carId.'"><strong>'.$car['brandName'].' '.$car['carModel'].'</strong></a></p>';

                $row = array($carId, $carHTML, $carsCatalogue[$carId]['monthPrice'], $quantity);
                $htmlTable .= '<tr>';
                foreach ($row as &$cell) {
                    $htmlTable.='<td>'.$cell.'</td>';
                }
                $htmlTable .= '</tr>';
                unset($cell);
                $itemProcessed++;
            }
        }
        unset($carId, $quantity);

        $htmlTable.=
                '</tbody>
            </table>
                            <div style="text-align: center;">
                    <a class="button" href="/cart.php">View All</a>
                </div>';

        echo $htmlTable;
    }

    /*
    Orders
    */

    echo   '</div>
            <div class="grid-item">
                <h3>Proposals / Orders</h3>';

    include_once './inc/orders.php';
    
    $numOfProposalsUnderReview = 0;
    $numOfUnsubmittedProposals = 0;
    
    $orders = getOrders($memberId);
    if($orders) {
        $limit = 3;
        $orderProcessed = 0;

        $htmlTable = '
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Cars</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($row = mysqli_fetch_assoc($orders)) {
            $orderId = $row['id'];

            $type = $row['type'];       
            
            $htmlCars = getCarsHTML($row['carsId']);

            // determined final stage that has been edited
            $stage = 2;
            if($row['stages']) {
                $stages = json_decode($row['stages'], true);                
                if($stages) {
                    $maxStage = max($stages);
                    if($maxStage > 1) {
                        if($type == 1 && $maxStage > 6) {
                            $stage = 6;
                        } else if($type == 2 && $maxStage > 7) {
                            $stage = 7;
                        } else {
                            $stage = $maxStage;
                        }
                    }
                }
            }

            $orderStatus = $row['orderStatus'];
            $orderStatus = '<p style="text-align:justify; margin: 0;">'.getOrderStatus($row['orderStatus']).($row['orderStatusMessage'] ? ('<br>'.$row['orderStatusMessage']) : '').'</p>';
            $orderStatusNum = $row['orderStatus'];
            if($orderStatusNum == 1 || $orderStatusNum == 4) {
                $numOfUnsubmittedProposals++;
                $orderStatus .= '<a class="button" href="./proposal.php?id='.$orderId.'&type='.$type.'&stage='.$stage.'" target="_blank">Edit Proposal</a>';
            } else if($orderStatusNum == 5) {
                $numOfProposalsUnderReview++;
            }

            if($orderProcessed < $limit) {
                $row = array($orderId, $htmlCars, $orderStatus);
                $htmlTable.='<tr>';
                foreach ($row as &$cell) {
                    $htmlTable.='<td>'.$cell.'</td>';
                }
                unset($cell);
                $htmlTable.='</tr>';
                $orderProcessed++;
            }
        }
        unset($row);

        $htmlTable.=
                '</tbody>
            </table>
                            <div style="text-align: center;">
                    <a class="button" href="./orders.php">View All</a>
                </div>';

        if($numOfProposalsUnderReview || $numOfUnsubmittedProposals) {
            echo '<div>
                    <canvas id="orderChart" class="chart"></canvas>
                </div>';
        }

        echo $htmlTable;
    } else {
        echo '<p>No proposals have been made. Add Cars to cart and Checkout to create a new proposal.</p>';
    }
    echo '</div>
        <div class="grid-item">
            <h3>Leased Cars/Vans</h3>';

    $leasedCars = getLeasedCars($memberId);
    if($leasedCars) {
        include_once './inc/leasedCars.php';

        $htmlTable =
           '<table>
                <thead>
                    <tr>
                        <th>Lease ID</th>
                        <th>Order ID</th>
                        <th>Car</th>
                        <th>Status</th>
                        <th>Rental Fee (£/mth)</th>
                    </tr>
                </thead>
                <tbody>';
                
        $limit = 5;
        $leasedCarsProcessed = 0;

        foreach($leasedCars as $leaseId => $leasedCar) {
            if($leasedCarsProcessed < $limit) {
                $orderId = $leasedCar['orderId'];            
                
                $column = array($leaseId, $orderId, getCarHTML($leasedCar), getStatusHTML($leasedCar), $leasedCar['monthPrice']);
                $htmlTable.='<tr>';
                foreach ($column as &$cell) {
                    $htmlTable.='<td>'.$cell.'</td>';
                }
                unset($cell);
                $htmlTable.='</tr>';
                $leasedCarsProcessed++;
            }
        }
        unset($limit, $column, $leasedCarsProcessed);

        $htmlTable.=
                '</tbody>
            </table>
                <div style="text-align: center;">
                    <a class="button" href="./leasedCars.php">View All</a>
                </div>';

        echo $htmlTable;
    } else {
        echo '<p>No cars / vans have been leased.</p>';
    }
    unset($leasedCars);

    echo   '</div>
        </section>
    </main>';

    if($numOfProposalsUnderReview || $numOfUnsubmittedProposals) {
        echo '
    <script>
        const data = {
            labels: ["Unsubmitted Proposals", "Proposals Under Review"],
            datasets: [
                {
                    label: "Dataset 1",
                    data: ['.$numOfUnsubmittedProposals.', '.$numOfProposalsUnderReview.'],
                    backgroundColor: ["rgb(255, 99, 132)", "rgb(255, 205, 86)"]
                }
            ]
        };

        const config = {
            type: "doughnut",
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                legend: {
                    position: "right",
                },
                }
            },
        };
        const myChart = new Chart(
            document.getElementById("orderChart"),
            config
        );
    </script>';
    }
    
    echo HTML_FOOTER;
?>