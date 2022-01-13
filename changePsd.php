<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./source/favicon.ico">
        <title>Forgot Password | LINGsCARS</title>
        <link rel="stylesheet" type="text/css" href="./css/LINGsCARStemplate.css" />
        <link rel="stylesheet" type="text/css" href="./css/loginPage.css" />
        <script src="./changePsd.js" defer></script>

        <?php
            session_start();
            include_once './account/dbConnection.php';
            include_once 'assistanceTool.php';
            
            $password_change_success = false;
            if(!isset($_SESSION['CHANGE_PASSWORD_EMAIL']) || isset($_SESSION['session_otp_forgot_password'])){
                //unset $_SESSION['session_otp_forgot_password'])
                if(isset($_SESSION['session_otp_forgot_password'])){
                    unset($_SESSION['session_otp_forgot_password']);
                }
                header('Location: '.getURIDirname().'/loginPage.php'); //getURIDirname() from assistanceTool.php
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

            
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                //password
                $newPassword = $_POST['newPassword'] ?? '';
                if($newPassword === '') {
                    $passwordError = 'Enter your password';
                } else if(search('/\s/', $newPassword) >= 0) {
                    $passwordError = 'Password cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if(search('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W)(?=.*\d).{1,}$/', $newPassword) !== 0) {
                    $passwordError = 'Password must contain at least 1 uppercase character (A-Z), 1 lowercase character (a-z), 1 special character (!, @, #, $, %, ^, &, *) and 1 number (0-9)';
                } else if(strlen($newPassword) < 6) {
                    $passwordError = 'Password must have at least 6 characters';
                }

                //confirm password
                $newConfirmPassword = $_POST['newConfirmPassword'] ?? '';
                if($newConfirmPassword === '') {
                    $confirmPasswordError = 'Enter your password';
                } else if(!isset($newPassword) || $newConfirmPassword !== $newPassword) {
                    $confirmPasswordError = 'Passwords do not match. Confirm Password must be the same as Password.';
                }
                
                
                if(!isset($passwordError) || !isset($confirmPasswordError)){
                    $normalUser = new NormalUser();
                    if($normalUser->isExistInDb("normalUser", "email", $_SESSION['CHANGE_PASSWORD_EMAIL'])){
                        //get user personal information
                        if($normalUser->readUserRecordByEmail($_SESSION['CHANGE_PASSWORD_EMAIL'], $array_user)){
                            
                            //set OTP
                            if($normalUser->updateUserPasswordByEmail("normalUser",  $_SESSION['CHANGE_PASSWORD_EMAIL'], $newPassword)){
                                
                                unset($_SESSION['CHANGE_PASSWORD_EMAIL']);
                                $password_change_success = true;
                            }
                        }

                        
                    }
                }
                

            }
            
        ?>

    </head>
    <body>
        <!-- https://www.djtechblog.com/php/email-verification-in-php-using-otp/ -->
        <?php 
             include('templateHeaderFooter.php'); 
            echo header_template; 
        ?>
        <main>
            <?php 
                if(!$password_change_success){
                    echo '<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post" name="changePasswordForm" onsubmit="return(validateForm());" novalidate>
                        <legend>
                            Change Password
                        </legend>
                        <fieldset>
                            <!-- login email -->
                            <label for="newPassword">Password</label>
                            <input type="password" name="newPassword" id="newPassword">
                            <p class="warning-text hidden">Error</p>
                        </fieldset>
                        <fieldset>
                            <!-- password -->
                            <label for="newConfirmPassword">Confirm Password</label>
                            <input type="text" name="newConfirmPassword" id="newConfirmPassword" >
                            <p class="warning-text hidden">Error</p>
                        </fieldset>
                        <fieldset>
                            <!-- submit -->
                            <input type="submit" name="submit" value="Submit" class="button-flex">
                        </fieldset>
                    </form>';
                }else{
                    echo '
                        <div style="text-align: center;">
                            <img src="./source/images/registrationPage/man_girl.png" style="max-width: 200px; vertical-align: middle;">
                            <h2 style="display: inline-block;">Password Change Success</h2>
                            <img src="./source/images/registrationPage/check-mark-verified.gif" style="max-width: 100px; vertical-align: middle;">
                        </div>';
                }
                
            ?>
        </main>
        <a id="return-to-login" href="loginPage.php" >Back to Login</a>
        <a id="return-to-login" href="index.php" >Back to Main Page</a>
        <?php 
            echo footer_template; 
        ?>    
    </body>
</html>

