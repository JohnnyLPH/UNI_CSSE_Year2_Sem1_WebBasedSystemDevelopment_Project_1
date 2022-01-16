<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../source/favicon.ico">
        <title>Member Profile | LINGsCARS</title>
        <link rel="stylesheet" href="./css/member.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../css/registrationPage.css" />
        <script src="../js/memberProfile.js" defer></script>
        <?php
            session_start();
            include_once './inc/member.php';
            include_once './inc/postHead.php';
            printNavBar();
            
            include_once '../account/dbConnection.php';
            include_once '../assistanceTool.php';

            if(!checkIdleDuration()){
                redirect('../loginPage.php');
                die();
            }

            //update current page as previous page
            updatePreviousPageRedirected();

            echo '<script type="text/javascript">
                function resetForm() {
                    if(window.confirm("Remove the changes made?")) {
                        return true;
                    } else {
                        return false;                
                    }
                }
            </script>';

            define('HIDDEN_WARNING_HTML', ' hidden">');
            define('NO_HIDDEN_WARNING_HTML', '">');
            define('HTML_WARNING_CLASS', ' class="warning"');

             // returns the index of the first match between the regular expression $pattern and the $subject string, or -1 if no match was found
            function search($pattern, $subject) {
                preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);

                if($matches) {
                    return $matches[0][1];
                } else {
                    return -1;
                }
            }
            
            $stateList = array('UK-01' =>'England', 'UK-02' => 'Nothern Ireland', 'UK-03' => 'Scotland', 'UK-04' => 'Wales');

            /*
            ?? is PHP 7 Null Coalescing Operator
            https://www.php.net/manual/en/language.operators.comparison.php#language.operators.comparison.coalesce
            */

            // perform input validation
            if($_SERVER["REQUEST_METHOD"] === "POST"){
                $firstName = $_POST['firstName'] ?? '';
                if($firstName === '') {
                    $firstNameError = 'Enter your first name';
                } else if(search('/\s/', $firstName) >= 0) {
                    $firstNameError = 'First name cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if(search('/[0-9]/', $firstName) >= 0) {
                    $firstNameError = 'First name cannot contain number(s)';
                } else if(search('/[A-Z]/', $firstName) != 0) {
                    $firstNameError = 'First name must begin with an uppercase character (A-Z)';
                } else if(search('/[A-Z]/', substr($firstName, 1)) >= 0) {
                    $firstNameError = 'All characters after the first character must be lowercase characters';
                } else if(strlen($firstName) < 2) {
                    $firstNameError = 'First name must have at least 2 characters';
                }

                $lastName = $_POST['lastName'] ?? '';
                if($lastName === '') {
                    $lastNameError = 'Enter your last name';
                } else if(search('/\s/', $lastName) >= 0) {
                    $lastNameError = 'Last name cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if(search('/[0-9]/', $lastName) >= 0) {
                    $lastNameError = 'Last name cannot contain number(s)';
                } else if(search('/[A-Z]/', $lastName) != 0) {
                    $lastNameError = 'Last name must begin with an uppercase character (A-Z)';
                } else if(search('/[A-Z]/', substr($lastName, 1)) >= 0) {
                    $lastNameError = 'All characters after the first character must be lowercase characters';
                } else if(strlen($lastName) < 2) {
                    $lastNameError = 'Last name must have at least 2 characters';
                }

                $email = $_POST['memberEmailDefault'] ?? '';
                
                if($email === '') {
                    $emailError = 'Enter your email';
                } else if(search('/\s/', $email) >= 0) {
                    $emailError = 'Email cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if(search('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $email) !== 0) {
                    $emailError = 'Invalid email format. Email should have a format similar to <em>username@domain.com</em>';
                }

                $phone = $_POST['phone'] ?? '';
                if($phone === '') {
                    $phoneError = 'Enter your mobile phone number';
                } else if(search('/\s/', $phone) >= 0) {
                    $phoneError = 'Phone number cannot contain any whitespace character (spaces, tabs, line breaks)';
                } else if($phone[0] !== '1') {
                    $phoneError = 'Invalid format. Malaysia mobile phone number must begin with 1';
                } else if(search('/[^0-9]/', $phone) >= 0) {
                    $phoneError = "Phone number can only contain numbers without any special character such as '-'";
                } else if(strlen($phone) < 9 || strlen($phone) > 10) {
                    $phoneError = 'Malaysia mobile phone number must have 9 - 10 digits (excluding +60)';
                }

                $gender = $_POST['gender'] ?? '';
                if($gender !== 'Male' && $gender !== 'Female') {
                    $genderError = 'Select your gender';
                }

                $state = $_POST['state'] ?? '';
                if(search('/^(UK-)(0[1-4])$/', $state) !== 0) {
                    $stateError = 'Select your state';
                }

                $terms = $_POST['terms'] ?? '';
                if($terms !== 'on') {
                    $termsError = 'The Terms and Conditions must be accepted';
                }

                $currentDate = date('Y-m-d H:i:s');
                $dob = $_POST['dob'] ?? '';
                $dobTime = ($dob != '')?date($dob): '';
                if($dobTime !== ''){
                    if((strtotime($currentDate) - strtotime($dobTime)) <= 0){
                        $termsError = 'Select correct date of birth';
                    }
                }else{
                    $termsError = 'Select correct date of birth';
                }
                
                if(!(isset($firstNameError) || isset($lastNameError) || isset($emailError) || isset($phoneError) || isset($genderError) || isset($stateError) || isset($dobError))){
                    $_SESSION['memberFirstName'] = $firstName;
                    //if no error, means we can save then in database
                    //write in database through MYSQL
                    $member = new Members();
                    //In mysql -> gender VARCHAR(1), SO WE NEED TO CONVERT IT INTO 
                    //F == FEMALE OR M == MALE
                    
                    $genderSymbol = ($gender == 'Female')?2:1;
                    if($member->updateExistedRecord($firstName, $lastName, $email, $phone, $genderSymbol, $state, $dob)){
                       
                    }
                    redirect('memberProfile.php');
                    die();
                }
                

            }else{
                $member = new Members();
                if($member->readUserRecordByID($_SESSION["memberId"], $array_user)){
                    $firstName = $array_user['firstName'];
                    $lastName = $array_user['lastName'];
                    $phone = $array_user['phone'];
                    $gender = ($array_user['gender'] === '1')?  'Male':'Female';
                    $state = $array_user['state'];
                    $dob = $array_user['dob'];
                    $_SESSION['memberEmailSelected'] = $array_user['email'];
                }
            }
        
        
        ?>
    </head>
    <body>  
        <main>
            <?php echo '
                <noscript>
                    <div class="warning-banner">
                        <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
                        <h1>JavaScript Disabled</h1>
                        <h2>Please enable JavaScript and reload page to have features such as Clear Form Confirmation and clickable Terms and Conditions.</h2>
                    </div>
                </noscript>
                <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post" name="registrationForm" onsubmit="return(validateForm());" novalidate>            
                    <div style="text-align: center;">
                        <img src="../source/images/registrationPage/form-icon-png-15.jpg" style="max-width: 100px; vertical-align: middle;">
                        <h1 style="display: inline-block;">Member Profile</h1>
                    </div>
        
                    <p style="text-align: center;">The profile information is editable.</p>            
                    <fieldset>
                        <label for="firstName">First Name: </label>
                        <div class="input">
                            <span class="form-icon badge"></span>
                            <div>
                                <input type="text" name="firstName" id="firstName"'.(isset($firstNameError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($firstName).'">
                                <p class="warning-text'.(isset($firstNameError) ? (NO_HIDDEN_WARNING_HTML.$firstNameError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <label for="lastName">Last Name: </label>
                        <div class="input">
                            <span class="form-icon badge"></span>
                            <div>
                                <input type="text" name="lastName" id="lastName"'.(isset($lastNameError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($lastName).'">
                                <p class="warning-text'.(isset($lastNameError) ? (NO_HIDDEN_WARNING_HTML.$lastNameError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <label for="email">Email: </label>
                        <div class="input">
                            <span class="form-icon email"></span>
                            <div>
                                <input type="email" name="email" disabled placeholder="username@domain.com" id="email"'.(isset($emailError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($_SESSION['memberEmailSelected']).'">
                                <p class="warning-text'.(isset($emailError) ? (NO_HIDDEN_WARNING_HTML.$emailError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <input type="hidden" name="memberEmailDefault" value="'.htmlspecialchars($_SESSION['memberEmailSelected']).'"/>
                    <fieldset>
                        <label for="phone">Phone Number: </label>
                        <div class="input">
                            <span class="form-icon smartphone"></span>
                            <div>
                                <div style="display: flex; align-items: center;">
                                    <span style="white-space: pre;">+60 </span>
                                    <input type="tel" name="phone" placeholder="123456789" id="phone"'.(isset($phoneError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($phone).'">
                                </div>
                                <p class="warning-text'.(isset($phoneError) ? (NO_HIDDEN_WARNING_HTML.$phoneError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <label for="gender">Gender: </label>
                        <div class="input" style="text-align: center;">
                            <span class="form-icon wc"></span>
                            <div>
                                <input type="radio" name="gender" value="Male" id="male"'.(($gender === 'Male') ? ' checked' : '').'>
                                <label for="male" style="font-weight: normal;">Male</label>
                                <input type="radio" name="gender" value="Female" id="female"'.(($gender === 'Female') ? ' checked' : '').'>
                                <label for="female" style="font-weight: normal;">Female</label>                
                                <p class="warning-text'.(isset($genderError) ? (NO_HIDDEN_WARNING_HTML.$genderError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <label for="state">State: </label>
                        <div class="input">
                            <span class="form-icon flag"></span>
                            <div>
                                <select name="state" id="state"'.(isset($stateError) ? HTML_WARNING_CLASS : '').'>
                                    <option value="">-- Select state --</option>    
                                    <optgroup label="United Kingdom">
                                        <option value="UK-01"'.(($state === 'UK-01') ? ' selected' : '').'>England</option>
                                        <option value="UK-02"'.(($state === 'UK-02') ? ' selected' : '').'>Nothern Ireland</option>
                                        <option value="UK-03"'.(($state === 'UK-03') ? ' selected' : '').'>Scotland</option>
                                        <option value="UK-04"'.(($state === 'UK-04') ? ' selected' : '').'>Wales</option> 
                                    </optgroup>                
                                </select>
                                <p class="warning-text'.(isset($stateError) ? (NO_HIDDEN_WARNING_HTML.$stateError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <label for="dob">Date of Birth: </label>
                        <div class="input">
                            <span class="form-icon calender"></span>
                            <div>
                                <input type="date" id="dob" name="dob" value="'.$dob.'">
                                <p class="warning-text'.(isset($dobError) ? (NO_HIDDEN_WARNING_HTML.$dobError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <input type="submit" value="Save Change" class="button-flex">
                        <a href="./memberProfile.php" onclick="return(resetForm());" id="reset" class="button-flex">Remove Change</a>
                    </fieldset>
                </form>
                <h3> Do you want to change password? <a href="memberVerifyEmailForPassword.php">Change Password</a></h3>
        </main>';

                echo HTML_FOOTER;