<?php
    session_start();
    
    function getOrderStatus($type) {
        $status = array('Ineligible.',
        'Changes required.',
        'Incomplete Payment.',
        'Proposal cancelled.',
        'Draft Proposal pending submission. Please complete and submit your proposal.',
        'Proposal approved. Awaiting for your confirmation.',
        'Order Confirmed.');
        
        if($type >= 0 && isset($status[$type])) {
            return $status[$type];
        }
    }

    include_once './inc/preHead.php';
?>    
    <link rel="stylesheet" href="./css/table.css">
    <script src="./js/Chart.js/3.7.0/chart.min.js"></script>
    
<?php include_once './inc/postHead.php';
    printNavBar(4);
?>
    <main>
        <h2 style="text-align: center;">Pending Proposals</h2>    
        <div>
            <canvas id="myChart"></canvas>
        </div>
        <h2 style="text-align: center;">All Proposals</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Cars</th>
                    <th>Status</th>
                    <th>Proposal Date</th>
                    <th>Review Date</th>
                    <th>Confirm Date</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td><td>Test</td><td><p style="margin: 0;">Draft Proposal pending submission. Please complete and submit your order.</p><a class="button">Complete Order</a></td><td>-</td><td>-</td><td>-</td><td>-</td>
                </tr>
            </tbody>
        </table>
        <script>
            const DATA_COUNT = 5;
            const NUMBER_CFG = {count: DATA_COUNT, min: 0, max: 100};

            const data = {
                labels: ['Unsubmitted Proposals', 'Proposals Under Review'],
                datasets: [
                    {
                        label: 'Dataset 1',
                        data: [70, 30],
                        backgroundColor: ['rgb(255, 99, 132)', 'rgb(255, 205, 86)']
                    }
                ]
            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                    legend: {
                        position: 'right',
                    },
                    }
                },
            };
            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        </script>
    </main>