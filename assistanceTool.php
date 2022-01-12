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
    
?>