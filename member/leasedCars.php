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
    <link rel="stylesheet" href="./css/table.css">
    <script src="./js/Chart.js/3.7.0/chart.min.js"></script>
    
<?php
    include_once './inc/postHead.php';
    printNavBar();

    echo '<main>';
        
    $htmlTable =
        '<div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Lease ID</th>
                        <th>Order ID</th>
                        <th>Car</th>
                        <th>Status</th>
                        <th>Rental Fee (£/mth)</th>
                        <th>Number of Months Paid</th>
                        <th>Lease Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>';

    $leasedCars = getLeasedCars($memberId);
    if($leasedCars) {
        include_once './inc/leasedCars.php';

        foreach($leasedCars as $leaseId => $leasedCar) {
            $orderId = $leasedCar['orderId'];
            $paymentMthsCompleted = $leasedCar['paymentMthsCompleted'].' / '.$leasedCar['leaseTime'];
            
            $car = getCarHTML($leasedCar);

            $status = getStatusHTML($leasedCar);
            reformatDate($leasedCar['leaseDate']);
            reformatDate($leasedCar['returnDate']);
            
            $column = array($leaseId, $orderId, $car, $status, $leasedCar['monthPrice'], $paymentMthsCompleted, $leasedCar['leaseDate'], $leasedCar['returnDate']);
            $htmlTable.='<tr>';
            foreach ($column as &$cell) {
                $htmlTable.='<td>'.$cell.'</td>';
            }
            unset($cell);
            $htmlTable.='</tr>';
        }        
    }           
    $htmlTable.=
                '</tbody>
            </table>
        </div>';

    echo
       '<h2 style="text-align: center;">All Leased Cars</h2>';

    if($leasedCars) {
        echo $htmlTable;
    } else {
        echo '<p>No cars / vans have been leased.</p>';
    }

    echo '</main>'.HTML_FOOTER;
?>