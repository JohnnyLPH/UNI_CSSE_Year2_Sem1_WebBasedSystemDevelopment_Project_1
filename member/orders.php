<?php

    include_once './inc/member.php';

    session_start();
    $memberId = $_SESSION['memberId'] ?? '';
    if(!$memberId) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect(RELATIVE_LOGIN_URL);
    }
    
    require_once './inc/dbConnection.php';
    include_once './inc/orders.php';
    
    function getOrderStatus($type) {
        $status = array(
            'Ineligible.',
            'Changes required.',
            'Incomplete Payment.',
            'Proposal Cancelled.',
            'Draft Proposal pending submission. Please complete and submit your proposal.',
            'Proposal under review.',
            'Proposal approved. Awaiting for your confirmation. Click <b>Pay</b> to confirm.',
            'Order Confirmed.');
        
        if($type >= 0 && isset($status[$type])) {
            return $status[$type];
        } else {
            return '-';
        }
    }

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
                        <th>Order ID</th>
                        <th>Type</th>
                        <th>Cars</th>
                        <th>Status</th>
                        <th>Proposal Date</th>
                        <th>Review Date</th>
                        <th>Confirm Date</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>';                

    $numOfProposalsUnderReview = 0;
    $numOfUnsubmittedProposals = 0;

    $orders = getAllOrders($memberId);
    if($orders) {
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
            } else if($orderStatusNum == 2 || $orderStatusNum == 6 || $orderStatusNum == 7) {
                $payment = '<a class="button"  href="./payment.php?orderId='.$orderId.'" target="_blank">Pay</a>';
            }    

            if($type == 1) {
                $type = 'Personal';
            } else if($type == 2) {
                $type = 'Business';
            }

            reformatDate($row['proposalDate']);
            reformatDate($row['reviewDate']);
            reformatDate($row['confirmDate']);
            
            $row = array($orderId, $type, $htmlCars, $orderStatus, $row['proposalDate'], $row['reviewDate'], $row['confirmDate'], $payment ?? '-');
            $htmlTable.='<tr>';
            foreach ($row as &$cell) {
                $htmlTable.='<td>'.$cell.'</td>';
            }
            unset($cell, $payment);
            $htmlTable.='</tr>';
        }        
    }           
    $htmlTable.=
                '</tbody>
            </table>
        </div>';

    echo
        '<h2 style="text-align: center;">Pending Proposals</h2>';

    if($numOfProposalsUnderReview || $numOfUnsubmittedProposals) {
        echo
       '<div>
            <canvas id="chart"></canvas>
        </div>';
    } else {
        echo '<p>There are no pending proposals.</p>';
    }

    echo
       '<h2 style="text-align: center;">All Proposals</h2>';
    if($orders) {
        echo $htmlTable;
    } else {
        echo '<p>No proposals have been made. Add Cars to cart and Checkout to create a new proposal.</p>';
    }

    if($numOfProposalsUnderReview || $numOfUnsubmittedProposals) {    
        echo
        '<script>
            const DATA_COUNT = 5;
            const NUMBER_CFG = {count: DATA_COUNT, min: 0, max: 100};

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
                document.getElementById("chart"),
                config
            );
        </script>';
    }

    echo '</main>'.HTML_FOOTER;
?>