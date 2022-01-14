<?php
    require_once './inc/dbConnection.php';   

    $transactionId = filter_input(INPUT_GET, 'transactionId', FILTER_VALIDATE_INT);
    
    if($transactionId) {
        $result = mysqli_query($db, 'SELECT * FROM transactions WHERE id = '.$transactionId.' LIMIT 1');
    }

    if($transactionId && mysqli_num_rows($result) === 1) {
        $transaction = mysqli_fetch_assoc($result) or showDBError();
        $transactionDate = $transaction['transactionDate'];
        $orderId = $transaction['orderId'];
        $amount = $transaction['amount'];
    } else {
        
    }

    echo '
    <body>
<h2>LINGsCARS.com</h2>
<h3>Official Payment Receipt</h3>
<br>
<p><b>Transaction ID:</b> '.$transactionId.'</p>
<p><b>Transaction Date:</b> '.$transactionDate.'</p>
<p><b>Order ID:</b> '.$orderId.'</p>
<p><b>Amount Paid:</b> '.$amount.'£</p>
</body>
    ';
?>
