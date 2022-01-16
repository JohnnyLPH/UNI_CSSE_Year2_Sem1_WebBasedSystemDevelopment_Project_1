<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>User Login | LINGsCARS</title>
        <link rel="stylesheet" type="text/css" href="./css/LINGsCARStemplate.css" />
        <link rel="stylesheet" type="text/css" href="./css/loginPage.css" />
        <link rel="shortcut icon" href="./source/favicon.ico">
        <script src="./js/loginPage.js" defer></script>

        <?php
            //https://stackoverflow.com/questions/35040566/php-session-why-is-session-start-required-multiple-times
            session_start();
            include_once './account/dbConnection.php';
            date_default_timezone_set('Europe/London');
            //get server address

            define('HIDDEN_WARNING_HTML', ' hidden">');
            define('NO_HIDDEN_WARNING_HTML', '">');
            define('HTML_WARNING_CLASS', ' class="warning"');

            $loginEmail = '';
            $loginPassword = '';

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
                if($loginEmail === '') {
                    $emailError = 'Enter your email';
                } else if(search('/\s/', $loginEmail) >= 0) {
                    $emailError = 'Email cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if(search('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $loginEmail) !== 0) {
                    $emailError = 'Invalid email format. Email should have a format similar to <em>username@domain.com</em>';
                }
                $loginPassword = $_POST['userPassword']?? ''; 
                if($loginPassword === '') {
                    $passwordError = 'Enter your password';
                } else if(search('/\s/', $loginPassword) >= 0) {
                    $passwordError = 'Password cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if(search('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W)(?=.*\d).{1,}$/', $loginPassword) !== 0) {
                    $passwordError = 'Password must contain at least 1 uppercase character (A-Z), 1 lowercase character (a-z), 1 special character (!, @, #, $, %, ^, &, *) and 1 number (0-9)';
                } else if(strlen($loginPassword) != 6) {
                    $passwordError = 'Password must have exact 6 characters';
                }

                
                
                if(!isset($emailError) || !isset($passwordError)){
                    //set AuthenticateIdentity object for login purpose    
                    $userLogin = new Members();
                    if($userLogin->login($loginEmail, $loginPassword)){
                        //if login success
                        $previous_page = $uri.((isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '/'));
                        unset($_SESSION['redirect_url']);
                        header('Location: '.$previous_page); 
                        echo '<script>alert("login success");</script>'; 
                        exit;
                    }else{
                        //if login failure, back to login page
                       
                        header('Location: '.$uri.$dirname.'/loginPage.php');  
                        exit;
                    }
                }
                
            }
            

            
            
        ?>
    </head>
    <body>
        <?php 
            include('templateHeaderFooter.php'); 
            echo header_template; 
        ?>
        <main>
            <?php 
                echo'
                <noscript>
                    <div class="warning-banner">
                        <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
                        <h1>JavaScript Disabled</h1>
                        <h2>Please enable JavaScript and reload page to have features such as input validation.</h2>
                    </div>
                </noscript>
                <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post" name="loginForm" onsubmit="return(validateForm());" novalidate>
                    <legend>
                        Login Account
                    </legend>
                    <fieldset>
                        <!-- login email -->
                        <label for="userEmail">User Email</label>
                        <input type="email" name="userEmail" id="userEmail"'.(isset($emailError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($loginEmail).'">
                        <p class="warning-text'.(isset($emailError) ? (NO_HIDDEN_WARNING_HTML.$emailError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                    </fieldset>
                    <fieldset>
                        <!-- password -->
                        <label for="userPassword">Password</label>
                        <span><a href="forgotPsd.php">Forgot password?</a></span>
                        <input type="password" name="userPassword" id="userPassword"'.(isset($passwordError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($loginPassword).'">
                        <p class="warning-text'.(isset($passwordError) ? (NO_HIDDEN_WARNING_HTML.$passwordError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                    </fieldset>
                    <fieldset>
                        <!-- submit -->
                        <input type="submit" value="Log in" class="button-flex">
                    </fieldset>
                </form>
                <h3> Don’t have an account? <a href="registrationPage.html">Sign up</a></h3>
            </main>

            
            <a id="return-to-main" href="index.php" >Back to Main Page</a>
            ';
        ?>
            
        
        <?php 
           
            echo footer_template; 
        ?>
    </body>
</html>