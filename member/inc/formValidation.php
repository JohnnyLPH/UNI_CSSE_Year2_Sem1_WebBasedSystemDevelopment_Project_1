<?php
    require_once './inc/dbConnection.php';

    function printHeader() {
        include_once './inc/preHead.php';
        echo
        '
    <link rel="stylesheet" href="./css/form.css">
    <script src="./js/formValidation.js" defer></script>';
        include_once './inc/postHead.php';
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

    define('PROBLEM_STAGE', -2);
    define('WARNING_STAGE', -1);
    define('INCOMPLETE_STAGE', 0);
    define('CURRENT_STAGE', 1);
    define('COMPLETED_STAGE', 2);    

    function printProgressLine($stage) {
        $stageCount = count($stage);

        echo '<article class="progress-line">
                <ul>';
        for($i = 0; $i < $stageCount; $i++) {
            echo '<li';
            switch($stage[$i][1]) {
                case CURRENT_STAGE:
                    echo ' class="current"';
                    break;
                case COMPLETED_STAGE:
                    echo ' class="completed"';
                    break;
            }
            echo '>
                    <div class="process">'.(($i !== 0) ? '<div></div>' : '').'<p>';
            switch($stage[$i][1]) {
                case COMPLETED_STAGE:
                    echo '✓';
                    break;
                default:
                    echo $i + 1;
            }       
            echo    '</p></div>
                    <a>'.$stage[$i][0].'</a>
                </li>';            
        }
        echo '</ul>
        </article>';
    }       
    
    function showError($title, $message) {
        echo
   '<main>
       <div class="warning-banner">
            <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
            <h1>'.$title.'</h1>
            <h2>'.$message.'</h2>
        </div>';
    }

    function printHTMLFormHeader($actionURL) {
        echo
        '<form action="./'.$actionURL.'" method="post" name="proposalForm" onsubmit="return(validateForm());" novalidate>
        <p style="text-align: center;">Please fill in all details accurately.</p>';        
    }

    function trimExtraSpaces($string) {
        $trimmedStr = preg_replace('/ +/', ' ', trim($string));
        return $trimmedStr;
    }

    function validateName(&$input, $title, $key, &$JSON, $allowNum = true) {
        // title is title of input field
        global $post, $inputError;

        if($input === '') {
            if($post) {
                $inputError[$key] = 'Enter your '.lcfirst($title).'';
            }
        } else if(($allowNum && search('/[^&\w\d\'\- ]/', $input) >= 0) || (!$allowNum && search('/[^&\w\'\- ]/', $input) >= 0)) {
            $inputError[$key] = 'Invalid character. '.ucfirst($title).' can only contain A-Z, a-z, '.($allowNum ? '0-9, ': '').'&, -, \' and space \' \' character.';
        } else if(strlen($input) < 3) {
            $inputError[$key] = ucfirst($title).' too short. '.ucfirst($title).' must have at least 3 characters';
        } else if(strlen($input) > 50) {
            $inputError[$key] = ucfirst($title).' too long. '.ucfirst($title).' can only have a maximum of up to 50 number of characters.';
        } else {
            $input = ucwords(strtolower(trimExtraSpaces($input)));
            $JSON[$key] = $input;
        }
    }

    define('HTML_HIDDEN_WARNING', ' hidden">');
    define('HTML_NO_HIDDEN_WARNING', '">');
    define('HTML_WARNING_CLASS', ' class="warning"');
    define('HTML_WARNING_BANNER',
    '<div class="warning-banner">
        <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
        <h1>Oops</h1>
        <h2>Some form field(s) require your attention.</h2>
    </div>');

    session_start();
    $memberId = $_SESSION['memberId'] ?? '';
    $post = $_SERVER['REQUEST_METHOD'] === 'POST';
    $orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    $inputError = array();
?>