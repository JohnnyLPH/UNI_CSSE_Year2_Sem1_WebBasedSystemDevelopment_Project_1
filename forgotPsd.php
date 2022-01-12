<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./source/favicon.ico">
        <title>Forgot Password | LINGsCARS</title>
        <link rel="stylesheet" type="text/css" href="./css/LINGsCARStemplate.css" />
        <link rel="stylesheet" type="text/css" href="./css/loginPage.css" />
        <script src="./forgotPsd.js" defer></script>

        <?php
            session_start();
            include './account/dbConnection.php';
            include 'sendEmail.php';
            include 'assistanceTool.php';

            // returns the index of the first match between the regular expression $pattern and the $subject string, or -1 if no match was found
            function search($pattern, $subject) {
                preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);

                if($matches) {
                    return $matches[0][1];
                } else {
                    return -1;
                }
            }

            $array_user = [];
            $verifiedEmail = "";
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                //if getOTP
                if(isset($_POST['getOTP']) && $_POST['getOTP'] === "Get Verified OTP"){
                    
                    $verifiedEmail = $_POST['verifiedPasswordEmail'] ?? '';
                    if($verifiedEmail === '') {
                        $emailError = 'Enter your email';
                    } else if(search('/\s/', $verifiedEmail) >= 0) {
                        $emailError = 'Email cannot contain any whitespace character (spaces, tabs, line breaks)';
                    } else if(search('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $verifiedEmail) !== 0) {
                        $emailError = 'Invalid email format. Email should have a format similar to <em>username@domain.com</em>';
                    }

                    $normalUser = new NormalUser();
                    if(!isset($emailError)){
                        if($normalUser->isExistInDb("normalUser", "email", $verifiedEmail)){
                            //get user personal information
                            if($normalUser->readUserRecordByEmail($verifiedEmail, $array_user)){
                                //set OTP
                                $otp = rand(100000, 999999); //generates random otp
                                $_SESSION['session_otp_forgot_password'] = $otp;
                                $_SESSION['CHANGE_PASSWORD_EMAIL'] = $verifiedEmail;

                                //send otp to email
                                $mail = new PHPMailer\PHPMailer\PHPMailer();
                                $mail->isSMTP(); 
                                $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages        **
                                $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
                                $mail->Port = 587; // TLS only
                                $mail->SMTPSecure = 'tls'; // ssl is deprecated
                                $mail->SMTPAuth = true;
                                $mail->Username = 'gorilajaker456@gmail.com'; // email
                                $mail->Password = 'piicqkofqhuyzrad'; // password
                                $mail->setFrom('LingsCar@siswa.unimas.my', 'LingsCar.com'); // From email and name  //set sender name   *
                                $mail->addAddress($verifiedEmail, 'Mr. '.$array_user['f_name']); // to email and name  //set receiver's email and name   *
                                $mail->Subject =     'LingsCar\'s OTP for forgotten account password';   //set subject   *
                                $mail->msgHTML("Verified OTP: ".$otp); //*$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,*
                                $mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
                                // $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
                                $mail->SMTPOptions = array(
                                                    'ssl' => array(
                                                        'verify_peer' => false,
                                                        'verify_peer_name' => false,
                                                        'allow_self_signed' => true
                                                    )
                                                );
                                $mail->send(); //send email
                                
                            }

                            
                        }
                    }
                    

                }else if(isset($_POST['submit']) && $_POST['submit'] === "Submit"){
                    echo '<br/>done 1';
                    if(isset($_SESSION['session_otp_forgot_password']) && isset($_POST['OTPInput'])){
                        echo '<br/>done 2';
                        echo '<br/>'.$_SESSION['session_otp_forgot_password'];
                        echo '<br/>'.$_POST['OTPInput'];
                        if($_SESSION['session_otp_forgot_password'] == $_POST['OTPInput']){
                            echo '<br/>done 3';
                            unset($_SESSION['session_otp_forgot_password']);
                            header('Location: '.getURIDirname().'/changePsd.php'); //getURIDirname() from assistanceTool.php
                            exit;
                        }
                    }
                }
                    
                


            }
        ?>

    </head>
    <body>
        <!-- https://www.djtechblog.com/php/email-verification-in-php-using-otp/ -->
        <?php /* include('templateHeaderFooter.php'); 
            echo header_template; */
        ?>
        <main>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" name="forgotPasswordForm" onsubmit="return(validateForm());" novalidate>
                <legend>
                    Fogotten Your Password?
                </legend>
                <fieldset>
                    <!-- login email -->
                    <label for="verifiedPasswordEmail">User Email</label>
                    <input type="email" name="verifiedPasswordEmail" id="verifiedPasswordEmail" value='<?php echo $verifiedEmail ?>'>
                    <p class="warning-text hidden">Error</p>
                </fieldset>
                <fieldset>
                    <!-- password -->
                    <label for="OTPInput">Verified OTP</label>
                    <input type="text" name="OTPInput" id="OTPInput" >
                    <p class="warning-text hidden">Error</p>
                </fieldset>
                <fieldset>
                    <!-- submit -->
                    <input type="submit" name="submit" value="Submit" class="button-flex">
                    <input type="submit" name="getOTP" value="Get Verified OTP" class="button-flex-OTP">
                </fieldset>
            </form>
        </main>
    </body>
</html>

