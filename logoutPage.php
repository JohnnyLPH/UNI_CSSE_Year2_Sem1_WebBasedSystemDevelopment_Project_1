<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>User Login | LINGsCARS</title>
        <link rel="stylesheet" type="text/css" href="./css/LINGsCARStemplate.css" />
        <link rel="stylesheet" type="text/css" href="./css/loginPage.css" />
        <link rel="shortcut icon" href="./source/favicon.ico">
        

        <?php
            //https://stackoverflow.com/questions/35040566/php-session-why-is-session-start-required-multiple-times
            session_start();
            include_once './account/dbConnection.php';
            include_once './assistanceTool.php';
            //get server address
            $hadLogin = false;


            if(checkIfLogin()){
                $member = new Members();
                $member->updateCurrentLogoutDT();
                $hadLogin = true;
                if(isset($_SESSION["loggedIn"])){
                    unset($_SESSION["loggedIn"]);
                }
                if(isset($_SESSION['memberId'])){
                    unset($_SESSION['memberId']);
                }
                if(isset($_SESSION['loggedInTime'])){
                    unset($_SESSION['loggedInTime']);
                }
                if(isset($_SESSION["memberFirstName"])){
                    unset($_SESSION["memberFirstName"]);
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
                if($hadLogin){
                    echo '
                    <div style="text-align: center;">
                        <img src="./source/images/registrationPage/man_girl.png" style="max-width: 200px; vertical-align: middle;">
                        <h2 style="display: inline-block;">Logged Out Successfully<p>Have a nice day!</p></h2>
                        
                        <img src="./source/images/registrationPage/check-mark-verified.gif" style="max-width: 100px; vertical-align: middle;">
                    </div>';
                }else{
                    echo '
                    <div style="text-align: center;height: 250px;">
                        <h2 style="display: block;">You have not log in yet?<p><a href="loginPage.php">Log in</a></p></h2>
                        
                        <img src="./source/images/logout/smile.jpg" style="max-width: 100px; vertical-align: middle;">
                    </div>';
                }
                
            ?>
        </main>

        
        <a id="return-to-main" href="index.php" >Back to Main Page</a>
            
        
        <?php  
            echo footer_template; 
        ?>
    </body>
</html>