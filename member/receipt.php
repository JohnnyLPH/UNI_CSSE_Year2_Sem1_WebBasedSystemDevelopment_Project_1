<?php
    include_once './inc/member.php';
    require_once './inc/dbConnection.php';
    require_once './inc/payment.php';

    function getHTMLReceipt($htmlPaymentTable = false, $browserView = false) {
        global $db, $transactionId;

        $memberId = $_SESSION['memberId'] ?? '';
        $adminId = $_SESSION['adminId'] ?? '';
        if(!$memberId && !$adminId) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            redirect(RELATIVE_LOGIN_URL);
        }

        if($browserView) {
            include_once './inc/preHead.php';
            echo '<link rel="stylesheet" href="./css/receipt.css">';
            include_once './inc/postHead.php';
            printNavBar();
        }

        if($transactionId) {
            $result = mysqli_query($db, 'SELECT * FROM transactions WHERE id = '.$transactionId.($adminId ? '' : ' AND memberId = '.$memberId).' LIMIT 1');
        }

        if($transactionId && mysqli_num_rows($result) === 1) {
            $transaction = mysqli_fetch_assoc($result) or showDBError();
            $transactionDate = $transaction['transactionDate'];
            if($transactionDate) {
                $transactionDate = date_format(date_create($transactionDate), 'j F Y g:i:s A');
            }
            $nameOnCard = json_decode($transaction['creditCard'], true)['name'];
            $orderId = $transaction['orderId'];
            $amount = $transaction['amount'];
            if(!$htmlPaymentTable) {
                $leasedCars = json_decode($transaction['leasedCars'], true);
                $htmlPaymentTable = getHTMLPaymentTableAndLeasedCars($leasedCars, LEASE_ID);
            }
        } else {
            if($browserView) {
                if(!$transactionId) {
                    showError('405 Error: Missing / Invalid Transaction ID in URL', 'Please try again or contact support.');
                } else {
                    showError('404 Error: Transaction Not Found', ($adminId ? 'Transaction ID '.$transactionId.' is not found in the system.' : 'Your account does not have transaction ID '.$transactionId.'.'));
                }
                die();
            } else {
                return false;
            }
        }

        $html = '
<body>';

        if($browserView) {
            $html.='<main><p class="hidden" style="text-align:center;">Receipt was sent to your email after transaction. Remember to check your <i>SPAM / JUNK</i> folder.</p>';
        }

        $html.='
    <div style="text-align:center;">
    <h2>🚦 <i>LINGsCARS.com</i> 🚦</h2>
    <h3>Official Payment Receipt</h3>
    <p><b>Email:</b> <a href="mailto:sales@LINGsCARS.com">sales@LINGsCARS.com</a></p>
    <p><b>Tel:</b> 0191 460 9444</p>
    <p><b>Company Reg No:</b> 6178634</p>
    <p><b>VAT No:</b> 866 0241 30</p>
    </div>
    <hr>
    <p><b>Transaction ID:</b> '.$transactionId.'</p>
    <p><b>Transaction Date:</b> '.$transactionDate.' (London Time Zone - GMT)</p>
    <p><b>Name on Card:</b> '.$nameOnCard.'</p>
    <p><b>Order ID:</b> '.$orderId.'</p>
    '.$htmlPaymentTable.'
    <p><b>Amount Paid:</b> '.$amount.' £</p>
    <hr>
    <div style="text-align:center;">
        <h2>Thank You</h2>
    </div>';
    if($browserView) {
        $html.='<div style="text-align:center;"><a class="hidden button" onclick="window.print();">Print</a></div>
        </main>'.HTML_FOOTER;
    } else {
        $html.='</body>';
    }

        return $html;
    }

    if(!isset($transactionId)) {
        session_start();
        $transactionId = filter_input(INPUT_GET, 'transactionId', FILTER_VALIDATE_INT);
        echo getHTMLReceipt(false, true);
    }    
?>
