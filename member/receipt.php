<?php
    require_once './inc/dbConnection.php';

    function getHTMLReceipt() {
        global $db, $transactionId;

        $memberId = $_SESSION['memberId'];
        if(!$memberId) {
            redirect('../loginPage.php?required=true&redirect='.urlencode($_SERVER['REQUEST_URI']));
        }

        if($transactionId) {
            $result = mysqli_query($db, 'SELECT * FROM transactions WHERE id = '.$transactionId.' AND memberId = '.$memberId.' LIMIT 1');
        }

        if($transactionId && mysqli_num_rows($result) === 1) {
            $transaction = mysqli_fetch_assoc($result) or showDBError();
            $transactionDate = $transaction['transactionDate'];
            $nameOnCard = json_decode($transaction['creditCard'], true)['name'];
            $orderId = $transaction['orderId'];
            $amount = $transaction['amount'];
        } else {
            
        }

        return '
<body>
    <div style="text-align:center;">
    <h2>🚦 <i>LINGsCARS.com</i> 🚦</h2>
    <h3>Official Payment Receipt</h3>
    </div>
    <hr>
    <p><b>Transaction ID:</b> '.$transactionId.'</p>
    <p><b>Transaction Date:</b> '.$transactionDate.' (London Time Zone - GMT)</p>
    <p><b>Name on Card:</b> '.$nameOnCard.'</p>
    <p><b>Order ID:</b> '.$orderId.'</p>
    <p><b>Amount Paid:</b> '.$amount.' £</p>
    <hr>
    <div style="text-align:center;">
        <h2>Thank You</h2>
    </div>
</body>';
    }

    if(!isset($transactionId)) {
        session_start();
        $transactionId = filter_input(INPUT_GET, 'transactionId', FILTER_VALIDATE_INT);
        echo getHTMLReceipt();
    }    
?>
