<?php

    include_once './inc/member.php';

    session_start();
    $memberId = $_SESSION['memberId'] ?? '';
    if(!$memberId) {
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
                <p>Empty</p>
            </div>
            <div class="grid-item">
                <h3>Proposals / Orders</h3>
                <div>
                    <canvas id="orderChart" class="chart"></canvas>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Cars</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td><td>Test</td><td><p style="margin: 0;">Draft Proposal pending submission. Please complete and submit your order.</p></td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: center;">
                    <a class="button" href="./orders.php">View All</a>
                </div>
            </div>
        </section>
    </main>
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
            document.getElementById('orderChart'),
            config
        );
    </script>
<?php
    echo HTML_FOOTER;
?>