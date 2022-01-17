<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../source/favicon.ico">
        <title>User Verification | LINGsCARS</title>
        <link rel="stylesheet" href="./css/member.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../css/loginPage.css" />
        
        <?php
            session_start();
            include_once './inc/member.php';
            include_once './inc/postHead.php';
            printNavBar();

            include '../account/dbConnection.php';
            require_once '../sendEmail.php';
            include '../assistanceTool.php';

            if(!checkIdleDuration()){
                header('Location: '.getURIDirname().'/loginPage.php');
                exit;
            }

            // returns the index of the first match between the regular expression $pattern and the $subject string, or -1 if no match was found
            function search($pattern, $subject) {
                preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);

                if($matches) {
                    return $matches[0][1];
                } else {
                    return -1;
                }
            }

            define('HIDDEN_WARNING_HTML', ' hidden">');
            define('NO_HIDDEN_WARNING_HTML', '">');
            define('HTML_WARNING_CLASS', ' class="warning"');

            $array_user = [];
            $verifiedEmail = "";
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                //if getOTP
                if(isset($_POST['getOTP']) && $_POST['getOTP'] === "Get Verified OTP"){
                    
                    $verifiedEmail = $_POST['memberEmail'] ?? '';
                    if($verifiedEmail === '') {
                        $emailError = 'Enter your email';
                    } else if(search('/\s/', $verifiedEmail) >= 0) {
                        $emailError = 'Email cannot contain any whitespace character (spaces, tabs, line breaks)';
                    } else if(search('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $verifiedEmail) !== 0) {
                        $emailError = 'Invalid email format. Email should have a format similar to <em>username@domain.com</em>';
                    }

                    $member = new Members();
                    if(!isset($emailError)){
                        if($member->isExistInDb("members", "email", $verifiedEmail)){
                            //get user personal information
                            if($member->readUserRecordByEmail($verifiedEmail, $array_user)){
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
                                $mail->addAddress($verifiedEmail, 'Mr. '.$array_user['firstName']); // to email and name  //set receiver's email and name   *
                                $mail->Subject = 'LINGsCARS OTP for Change of Account Password';   //set subject   *
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
                                $mail->send();
                                     //email is send 
                                echo '<script>var msg = "OTP is sent to the your email address. Please check your email inbox and enter the OTP to the field provided.\n\n";
                                msg += "Note: If you do not received the email from us, please check back you SPAM / JUNK folder\n";
                                msg += "Ensure you have provided the correct email address during registration.\n";
                                msg += "Otherwise, please contact support.";
                                alert(msg);</script>';
                                
                                
                                
                                
                            }

                            
                        }
                    }
                    

                }else if(isset($_POST['submit']) && $_POST['submit'] === "Submit"){
                    
                    if(isset($_SESSION['session_otp_forgot_password']) && isset($_POST['OTPInput'])){
                        
                        if($_SESSION['session_otp_forgot_password'] == $_POST['OTPInput']){
                            
                            unset($_SESSION['session_otp_forgot_password']);
                            header('Location: '.getURIDirname().'/memberChangePsd.php'); //getURIDirname() from assistanceTool.php
                            exit;
                        }

                    }
                    //if no click the 'Get Verified OTP' button and directly click 'Submit' button
                    $OTPError = 'Invalid OTP inputted! Please check back to your email again!';
                    
                }
                    
                


            }else{
                $array_member = [];
                $member = new Members();
                $member->readUserRecordByID($_SESSION['memberId'], $array_member);
                $memberEmail = $array_member['email'];
            }
        ?>

    </head>
    <body>
        <!-- https://www.djtechblog.com/php/email-verification-in-php-using-otp/ -->
        <section class="guidance-container">
            <h3>Guidance</h3>
            <p> 1.  Click 'Get Verified Email' button.<br/>
                2.  Copy the OTP from your email and paste the OTP to the OTP field. Remember to check your SPAM or JUNK folder as well.<br/>
                3.  If you do not receive the email, please click the 'Get Verified Email' button again<br/>
                4.  Click Submit button</p>
        </section>

        <main>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" name="forgotPasswordForm" novalidate>
                <legend>
                    Change Password?
                </legend>
                <input type="hidden" name="memberEmail" value="<?php echo $memberEmail; ?>">
                <fieldset>
                    <!-- password -->
                    <label for="OTPInput">Verified OTP</label>
                    <input type="text" name="OTPInput" id="OTPInput" >
                    <?php echo '<p class="warning-text'.(isset($OTPError) ? (NO_HIDDEN_WARNING_HTML.$OTPError) : (HIDDEN_WARNING_HTML.'Error')).'</p>'; ?>
                </fieldset>
                <fieldset>
                    <!-- submit -->
                    <input type="submit" name="submit" value="Submit" class="button-flex">
                    <input type="submit" name="getOTP" value="Get Verified OTP" class="button-flex-OTP">
                </fieldset>
            </form>
        </main>       
        <a id="return-to-main" href="./memberProfile.php" >Back to Member Profile</a>
<?php
    echo HTML_FOOTER;
?>
