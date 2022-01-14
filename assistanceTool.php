<?php
    //session_start();
    date_default_timezone_set('Europe/London');

    //get server address
    //would not use $_SERVER["HTTP_REFERER"], 
    //cuz https://stackoverflow.com/questions/6880659/in-what-cases-will-http-referer-be-empty
    //set current webpage to session
    function updatePreviousPageRedirected(){
        if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
            $uri = 'https://';
        } else {
            $uri = 'http://';
        }
    
        $uri .= $_SERVER['HTTP_HOST'];

        
        unset($_SESSION['redirect_url']);
        $_SESSION['redirect_url'] = $uri.$_SERVER['PHP_SELF'];
        

    }

    function getPreviousPageURI(){
        if(isset($_SESSION['redirect_url'])){
            return $_SESSION['redirect_url'];
            
        }else{
            //set current page to session
            return getURIDirname().'/index.html';
        }
    }

    function backToPreviousPage(){
        header('Location: '.getPreviousPageURI());
        exit;
    }

    function getURIDirname(){
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

        return $uri.$dirname;
    }
    
    function updateActiveTime(){
        $_SESSION["loggedInTime"] = date('Y-m-d H:i:s');
    }

    function checkIdleDuration(){
        //return true == the account is log in and still be active within 30 minutes
        //return false == the account is not log in or 
        //                        the account is log in but it not active greater than 30 minutes 
        if(isset($_SESSION["loggedIn"])){
            if($_SESSION["loggedIn"] = true){
                
                //pre-set logout datetime, if user directly close the window,
                //php perform operation to myql would not be handled
                $temp_login_time = $_SESSION["loggedInTime"];
                $minutes_to_add = 5;

                $time = new DateTime();
                $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

                $temp_logout_time = $time->format('Y-m-d H:i:s');

                $temp_duration = $time->format('U') - strtotime( $_SESSION["loggedInTime"]);
                if($temp_duration > 1800){
                    //1800 second == 30 minutes
                    //if the idle duration is greater than 30 minutes, user are required to login again
                    if(isset($_SESSION['memberId']) && isset($_SESSION['loggedInTime'])){
                        include './account/dbConnection.php';
                        $member = new Member();
                        $member->updateLogoutDT();
                        
                    }
                    if(isset($_SESSION["loggedIn"])){
                        unset($_SESSION["loggedIn"]);
                    }
                    if(isset($_SESSION["memberId"])){
                        unset($_SESSION['memberId']);
                    }
                    if(isset($_SESSION["memberFirstName"])){
                        unset($_SESSION["memberFirstName"]);
                    }
                    if(isset($_SESSION['loggedInTime'])){
                        unset($_SESSION['loggedInTime']);
                    }
                    return false;
                }else{
                    
                    updateActiveTime();
                    //if the idle duration is within 30 minutes, return true
                    //means that no need to prompt user to login again
                    return true;
                }
            }
        }
       
        return false;
    }

?>  