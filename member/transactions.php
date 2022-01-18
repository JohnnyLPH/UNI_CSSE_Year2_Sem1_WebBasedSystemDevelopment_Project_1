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
                        <th>Trans ID</th>
                        <th>Order ID</th>
                        <th>Lease ID: Mths Paid (Car)</th>
                        <th>Name on Credit Card</th>                        
                        <th>Amount Paid (£)</th>
                        <th>Transaction Date</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>';
    
    $transactions = getTransactions($memberId);
    $carIds = array();
    
    if($transactions) {
        foreach($transactions as $transaction) {
            $leasedCars = json_decode($transaction['leasedCars'], true);
            foreach($leasedCars as $leasedCar) {
                array_push($carIds, $leasedCar['carId']);
            }
            unset($leasedCar);
        }
        unset($transaction, $leasedCars);

        $carIds = array_unique($carIds); // remove duplicates

        $carsCatalogue = getMultipleCars($carIds);

        foreach($transactions as $transactionId => $transaction) {
            $leasedCars = json_decode($transaction['leasedCars'], true);
            $numOfLeasedCars = count($leasedCars);
            $leasedCarsHTML = '';
            $first = true;
            foreach($leasedCars as $leaseId => $leasedCar) {
                if(!$first) {
                    $leasedCarsHTML .= '<br>';                    
                } else {
                    $first = false;
                }
                $leasedCarsHTML .= '<strong>'.$leaseId.': </strong> '.$leasedCar['MthsPaid'].' (<a href="/?manage-mode=view-car&car-id='.$leasedCar['carId'].'">'.$carsCatalogue[$leasedCar['carId']]['brandName'].' '.$carsCatalogue[$leasedCar['carId']]['carModel'].'</a>)';
            }
            unset($leaseId, $leasedCar);

            reformatDate($transaction['transactionDate']);
        
            $column = array($transactionId, $transaction['orderId'], $leasedCarsHTML, json_decode($transaction['creditCard'], true)['name'], $transaction['amount'], $transaction['transactionDate'], '<a class="button"  href="./receipt.php?transactionId='.$transactionId.'" target="_blank">View</a>');
            $htmlTable.='<tr>';
            $numOfColumns = count($column);
            for($i = 0; $i < $numOfColumns; $i++) {
                $htmlTable .= '<td'.($i === 2 ? ' style="text-align:left;"' : '').'>'.$column[$i].'</td>';
            }
            unset($i);
            $htmlTable.='</tr>';
        }
        unset($transactionId, $transaction);
    }           
    $htmlTable.=
                '</tbody>
            </table>
        </div>';

    echo
       '<h2 style="text-align: center;">Transaction History</h2>';

    if($transactions) {
        echo $htmlTable;
    } else {
        echo '<p>Transaction history is empty.</p>';
    }

    echo '</main>'.HTML_FOOTER;
?>