<?php
    
    require_once './inc/formValidation.php';

    function printFormHeader2() {
        global $orderId;
        printHTMLFormHeader(basename($_SERVER['SCRIPT_NAME']).'?id='.$orderId);       
    }

    $orderId = filter_input(INPUT_GET, 'orderId', FILTER_VALIDATE_INT);

    if($post) {
        $name = $_POST['name'] ?? '';
        $number = $_POST['number'] ?? '';
        $expiry = $_POST['expiry'] ?? '';
        $cvv = filter_input(INPUT_POST, 'cvv', FILTER_VALIDATE_INT);
    } else {
        $name = '';
        $number = '';    
        $expiry = '';
        $cvv = '';

        printHeader();
        printNavBar();
        printFormHeader1();
        printFormHeader2();
    }
    
    if($post) {
        // perform input validation if new data is received or existing stage data is incomplete       

        validateName($name, 'Name on card', 'name', $creditCard, false);
        
        if($number === '') {
            $inputError['number'] = 'Enter your credit card number.';
        } else if (search('/[^\d]/', $number) >= 0) {
            $inputError['number'] = 'Invalid number. Credit card number can only contain number digits without any space nor any other character.';
        } else if (strlen($number) < 8) {
            $inputError['number'] = 'Credit card number must have at least 8 digits.';
        } else {
            $creditCard['number'] = $number;
        }
        
        if($expiry === '') {
            $inputError['expiry'] = 'Enter your credit card expiry date.';
        } else if (search('/^[0-9][0-9]\/[2-3][0-9]$/', $expiry) < 0) {
            $inputError['expiry'] = 'Invalid expiry date. Credit card expiry date must follow MM/YY format, where MM and YY are month and year of expiry.';
        } else {
            $creditCard['expiry'] = $expiry;
        }

        if($cvv === NULL || $cvv === false) {
            $inputError['cvv'] = 'Enter your credit card security code or CVV.';
        } else if ($cvv < 100 || $cvv > 9999) {
            $inputError['cvv'] = 'Invalid security code. Credit card security code must have 3-4 digits.';
        } else {
            $creditCard['cvv'] = $cvv;
        }

        if($post) {
            $carId = '5';
            $amount = 10;
            if(!$memberId) {
                // session expired
                showSessionExpiredError();
                printFormHeader2();
            } else if(empty($inputError)) {
                // all inputs valid, save to database
                if(orderExists()) {
                    $transactionId = newTrans($carId, $creditCard, $amount);
                    if(!$transactionId) {
                        printHeader();
                        printNavBar();
                        showError('Error 500: ', 'Transaction Error. Please try again or contact support.');
                        die();
                    }

                    require_once './receipt.php';

                    require_once './phpMailer/Exception.php';
                    require_once './phpMailer/PHPMailer.php';
                    require_once './phpMailer/SMTP.php';
                    $email = getMemberEmail();
                    
                    if($email) {
                        // send receipt to email
                        $mail = new PHPMailer\PHPMailer\PHPMailer();
                        $mail->isSMTP(); 
                        $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages        **
                        $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
                        $mail->Port = 587; // TLS only
                        $mail->SMTPSecure = 'tls'; // ssl is deprecated
                        $mail->SMTPAuth = true;
                        $mail->Username = 'gorilajaker456@gmail.com'; // email
                        $mail->Password = 'piicqkofqhuyzrad'; // password
                        $mail->setFrom('noreply@LINGsCARS.com', 'LINGsCARS.com'); // From email and name  //set sender name   *
                        $mail->addAddress($email, 'Mr. '.$_SESSION['memberFirstName']); // to email and name  //set receiver's email and name   *
                        $mail->Subject =     'LingsCar\'s OTP for forgotten account password';   //set subject   *
                        $mail->msgHTML(getHTMLReceipt()); //*$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,*
                        $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
                        $mail->SMTPOptions = array(
                                'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );
                        $mail->send();
                    }
                    redirect('receipt.php?transactionId='.$transactionId);
                } else {            
                    showProposalNotFoundError();
                    die();
                }
            } else {
                // 1 or more invalid inputs, show warning
                printHeader();
                printNavBar();
                printFormHeader1();
                printFormHeader2();
                echo HTML_WARNING_BANNER;
            }
        }
    }

    echo 
           '<fieldset>
                <label for="name">Name on Credit Card: </label>
                <div class="input">
                    <span class="form-icon">badge</span>
                    <div>
                        <input type="text" name="name" id="name"'.(isset($inputError['name']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($name).'">
                        <p class="warning-text'.(isset($inputError['name']) ? (HTML_NO_HIDDEN_WARNING.$inputError['name']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="number">Credit Card Number: </label>
                <div class="input">
                <span class="form-icon">numbers</span>
                    <div>
                        <input type="number" min="1000000000" placeholder="Carefully enter your correct credit card number." name="number" id="number"'.(isset($inputError['number']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($number).'">
                        <p class="warning-text'.(isset($inputError['number']) ? (HTML_NO_HIDDEN_WARNING.$inputError['number']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="expiry">Credit Card Expiry Date (MM/YY): </label>
                <div class="input">
                <span class="form-icon">event</span>
                    <div>
                        <input type="text" maxlength="5" placeholder="MM/YY" name="expiry" id="expiry"'.(isset($inputError['expiry']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($expiry).'">
                        <p class="warning-text'.(isset($inputError['expiry']) ? (HTML_NO_HIDDEN_WARNING.$inputError['expiry']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>          
            <fieldset>
                <label for="cvv">Credit Card Security Code (CVV): </label>
                <div class="input">
                <span class="form-icon">pin</span>
                    <div>
                        <input type="number" min="10" max="9999" name="cvv" id="cvv"'.(isset($inputError['cvv']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($cvv).'">
                        <p class="warning-text'.(isset($inputError['cvv']) ? (HTML_NO_HIDDEN_WARNING.$inputError['cvv']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>';
        
    echo   '<fieldset>
                <input type="submit" value="$ CONFIRM PAYMENT" class="button-flex">
            </fieldset>
            <fieldset>
                <a href="./" id="reset" class="button-flex"><span class="material-icons-outlined" style="vertical-align: middle;">cancel</span> CANCEL</a>
            </fieldset>
        </form>
        <div>Attribution: Some icons made by <a href="https://www.flaticon.com/authors/monkik" target="_blank" rel="noopener noreferrer" title="monkik">monkik</a>, <a href="https://www.flaticon.com/authors/srip" target="_blank" rel="noopener noreferrer" title="srip">srip</a>, <a href="https://www.freepik.com" target="_blank" rel="noopener noreferrer" title="Freepik">Freepik</a> and <a href="https://www.flaticon.com/authors/smashicons" target="_blank" rel="noopener noreferrer" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" target="_blank" rel="noopener noreferrer" title="Flaticon">www.flaticon.com</a></div>
    </main>';
?>