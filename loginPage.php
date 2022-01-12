<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>User Login | LINGsCARS</title>
        <link rel="stylesheet" type="text/css" href="./css/LINGsCARStemplate.css" />
        <link rel="stylesheet" type="text/css" href="./css/loginPage.css" />
        <link rel="shortcut icon" href="./source/favicon.ico">
        <script src="./loginPage.js" defer></script>

        <?php
            //https://stackoverflow.com/questions/35040566/php-session-why-is-session-start-required-multiple-times
            session_start();
            include_once './account/dbConnection.php';
            /* include('templateHeaderFooter.php'); */
            date_default_timezone_set('Europe/London');
            //get server address
            //echo '<script>alert();</script>';
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                //echo '<script>alert("kakaha");</script>';
                if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
                    $uri = 'https://';
                } else {
                    $uri = 'http://';
                }
                
                $uri .= $_SERVER['HTTP_HOST'];
                $dirname = dirname($_SERVER['PHP_SELF']);
                if(strlen($dirname) === 1) {
                    $dirname = '';
                }

                $loginEmail = $_POST['userEmail']?? '';
                $loginPassword = $_POST['userPassword']?? ''; 

                //set AuthenticateIdentity object for login purpose    
                $userLogin = new NormalUser();
                
                echo '<script>alert("'.$uri.'");</script>';
                echo '<script>alert("'.$dirname.'");</script>';
                if($userLogin->login($loginEmail, $loginPassword)){
                    //if login success
                    $previous_page = (isset($_SESSION['redirect_url']))?$_SESSION['redirect_url']: $uri.$dirname.'/index.html';
                    unset($_SESSION['redirect_url']);
                    header('Location: '.$previous_page); 
                    echo '<script>alert("login success");</script>'; 
                    exit;
                }else{
                    //if login failure, back to login page
                    echo '<script>alert("login failure");</script>';
                    header('Location: '.$uri.$dirname.'/loginPage.php');  
                    exit;
                }
            }
            

            
            
        ?>
    </head>
    <body>
        <?php /* echo header_template; */ ?>
        <main>
            <noscript>
                <div class="warning-banner">
                    <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
                    <h1>JavaScript Disabled</h1>
                    <h2>Please enable JavaScript and reload page to have features such as Clear Form Confirmation and clickable Terms and Conditions.</h2>
                </div>
            </noscript>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" name="loginForm" onsubmit="return(validateForm());" novalidate>
                <legend>
                    Login Account
                </legend>
                <fieldset>
                    <!-- login email -->
                    <label for="userEmail">User Email</label>
                    <input type="email" name="userEmail" id="userEmail">
                    <p class="warning-text hidden">Error</p>
                </fieldset>
                <fieldset>
                    <!-- password -->
                    <label for="userPassword">Password</label>
                    <span><a href="forgotPsd.php">Forgot password?</a></span>
                    <input type="password" name="userPassword" id="userPassword" >
                    <p class="warning-text hidden">Error</p>
                </fieldset>
                <fieldset>
                    <!-- submit -->
                    <input type="submit" value="Log in" class="button-flex">
                </fieldset>
            </form>
            <h3> Don’t have an account? <a href="registrationPage.html">Sign up</a></h3>
        </main>

        <?php /* echo footer_template; */?>
        
    </body>
</html>