<?php
    
    // redirect user to original registration page if POST is not received
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
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
        
        header('Location: '.$uri.$dirname.'/registrationPage.html');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./source/favicon.ico">
    <title>User Account Registration | LINGsCARS</title>
    <link rel="stylesheet" type="text/css" href="./css/registrationPage.css" />
    <link rel="stylesheet" type="text/css" href="./css/LINGsCARStemplate.css" />
    <?php
        session_start();
        include_once './account/dbConnection.php';
                
        define('SCRIPT_HTML', '
            <script type="text/javascript">
                function resetForm() {
                    if(window.confirm("Clear the Registration Form?")) {
                        return true;
                    } else {
                        return false;                
                    }
                }

                function showTerms() {
                    document.registrationForm.style.display = "none";
                    document.getElementById("terms-statement").style.display = "block";
                }
        
                function hideTerms() {
                    document.registrationForm.style.removeProperty("display");
                    document.getElementById("terms-statement").style.removeProperty("display");
                }
            </script>
            <script src="./js/registrationPage.js" defer></script>
            ');
            
            include('templateHeaderFooter.php'); 
            define('HEAD_HTML', '        
            </head>
            <body>'.header_template.'
            <main>
            ');
        
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

        $email = $_POST['email'] ?? '';
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
        
        $password = $_POST['password'] ?? '';
        if($password === '') {
            $passwordError = 'Enter your password';
        } else if(search('/\s/', $password) >= 0) {
            $passwordError = 'Password cannot contain any whitespace character (spaces, tabs, line breaks)';
        } else if(search('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W)(?=.*\d).{1,}$/', $password) !== 0) {
            $passwordError = 'Password must contain at least 1 uppercase character (A-Z), 1 lowercase character (a-z), 1 special character (!, @, #, $, %, ^, &, *) and 1 number (0-9)';
        } else if(strlen($password) < 6) {
            $passwordError = 'Password must have at least 6 characters';
        }

        $confirmPassword = $_POST['confirmPassword'] ?? '';
        if($confirmPassword === '') {
            $confirmPasswordError = 'Enter your password';
        } else if(!isset($password) || $confirmPassword !== $password) {
            $confirmPasswordError = 'Passwords do not match. Confirm Password must be the same as Password.';
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
        

        if(isset($firstNameError) || isset($lastNameError) || isset($emailError) || isset($phoneError) || isset($passwordError) || isset($confirmPasswordError) || isset($genderError) || isset($stateError) || isset($termsError) || isset($dobError)) {
            // at least one input value is invalid, prompt user to correct input

            define('HIDDEN_WARNING_HTML', ' hidden">');
            define('NO_HIDDEN_WARNING_HTML', '">');
            define('HTML_WARNING_CLASS', ' class="warning"');

            echo SCRIPT_HTML;
            echo HEAD_HTML;
            echo '
                <noscript>
                    <div class="warning-banner">
                        <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
                        <h1>JavaScript Disabled</h1>
                        <h2>Please enable JavaScript and reload page to have features such as Clear Form Confirmation and clickable Terms and Conditions.</h2>
                    </div>
                </noscript>
                <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post" name="registrationForm" onsubmit="return(validateForm());" novalidate>            
                    <div style="text-align: center;">
                        <img src="./images/form-icon-png-15.jpg" style="max-width: 100px; vertical-align: middle;">
                        <h1 style="display: inline-block;">User Account Registration</h1>
                    </div>
                    <div class="warning-banner">
                        <svg width="40" height="40" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
                        <h1>Oops</h1>
                        <h2>Some form field(s) require your attention.</h2>
                    </div>
                    <p style="text-align: center;">Please fill in all details.</p>            
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
                                <input type="email" name="email" placeholder="username@domain.com" id="email"'.(isset($emailError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($email).'">
                                <p class="warning-text'.(isset($emailError) ? (NO_HIDDEN_WARNING_HTML.$emailError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
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
                        <label for="password">Password: </label>
                        <div class="input">
                            <span class="form-icon vpn_key"></span>
                            <div>
                                <input type="password" name="password" id="password"'.(isset($passwordError) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($password).'">
                                <p class="warning-text'.(isset($passwordError) ? (NO_HIDDEN_WARNING_HTML.$passwordError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <label for="confirmPassword">Confirm Password: </label>
                        <div class="input">
                            <span class="form-icon vpn_key"></span>
                            <div>
                                <input type="password" name="confirmPassword" id="confirmPassword"'.(isset($confirmPasswordError) ? HTML_WARNING_CLASS : '').'>
                                <p class="warning-text'.(isset($confirmPasswordError) ? (NO_HIDDEN_WARNING_HTML.$confirmPasswordError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
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
                    <fieldset style="display: block; text-align: center;">
                        <input type="checkbox" name="terms" id="terms"'.(($terms === 'on') ? ' checked' : '').'>
                        <label for="terms">I accept the <a href="#" onclick="showTerms()">Terms and Conditions</a></label>
                        <p class="warning-text'.(isset($termsError) ? (NO_HIDDEN_WARNING_HTML.$termsError) : (HIDDEN_WARNING_HTML.'Error')).'</p>
                    </fieldset>        
                    <fieldset>
                        <input type="submit" value="REGISTER" class="button-flex">
                        <a href="./registration.html" onclick="return(resetForm());" id="reset" class="button-flex">CLEAR</a>
                    </fieldset>
                </form>
                <article id="terms-statement">
                    <div style="text-align: right;">
                        <img src="./images/outline_cancel_black_18dp.png" onclick="hideTerms()" style="cursor: pointer;">
                    </div>
                    <div style="text-align: center;">
                        <h1>Terms and Conditions</h1>
                        <h3>Last Updated: 2021</h3>
                    </div>
                    <div style="text-align: justify;">
                        <p>1. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions.</p>
                        <p>2. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions.</p>
                        <p>3. This is Terms and Conditions. This is Terms and Conditions. This is Terms and Conditions.</p>
                    </div>
                </article>
            ';
        } else {
            // all inputs are correct, 
            
            //write in database through MYSQL
            $userRegister = new Members();
            //In mysql -> gender VARCHAR(1), SO WE NEED TO CONVERT IT INTO 
            //F == FEMALE OR M == MALE
            $genderSymbol = ($gender == 'Female')?2:1;
            if($userRegister->insertNewRecord($firstName, $lastName, $email, $phone, $password, $genderSymbol, $state, $dob)){
                //if insert the user register record success
                //print welcome and registration complete message

                echo HEAD_HTML;
                echo '
                <div style="text-align: center;">
                    <img src="./source/images/registrationPage/man_girl.png" style="max-width: 200px; vertical-align: middle;">
                    <div style="display: inline-block;">
                        <h1>Welcome Aboard,</h1>
                        <h2>'.htmlspecialchars($firstName).' '.htmlspecialchars($lastName).'</h2>
                    </div>
                    <hr>
                    <h2 style="display: inline-block;">Registration Complete</h2>
                    <img src="./source/images/registrationPage/check-mark-verified.gif" style="max-width: 100px; vertical-align: middle;">
                </div>

                <fieldset id="member-details">
                    <legend>Member Details</legend>            
                    <div>
                        <h3>First Name:</h3><p>'.htmlspecialchars($firstName).'</p>
                    </div>
                    <div>
                        <h3>Last Name:</h3><p>'.htmlspecialchars($lastName).'</p>
                    </div>
                    <div>
                        <h3>Email:</h3><p>'.htmlspecialchars($email).'</p>
                    </div>
                    <div>
                        <h3>Phone Number:</h3><p>+60 '.substr(htmlspecialchars($phone), 0, 2).'-'.substr(htmlspecialchars($phone), 2, ((strlen($phone) > 9) ? 4 : 3)).' '.substr(htmlspecialchars($phone), ((strlen($phone) > 9) ? 6 : 5)).'</p>
                    </div>
                    <div>
                        <h3>Gender:</h3><p>'.htmlspecialchars($gender).'</p>
                    </div>
                    <div>
                        <h3>State:</h3><p>'.$stateList[$state].'</p>
                    </div>
                </fieldset>

                <br>
                <hr>
                <div style="text-align: center;height:fit-content;">
                    <h2 style="display: inline-block;">Enjoy LINGsCARS Benefits</h2>
                    <img src="./source/images/registrationPage/Membership_Badge1.png" style="max-width: 100px; vertical-align: middle;">
                    <h2>Thank You</h2>    
                </div>
                ';
            }else{
                echo HEAD_HTML;
                echo '<p style="display: block;height: 14.67rem;text-align: center;">Sorry, this email have been registered. 
                <br/>Please use another valid email to register. 
                <br/>Otherwise you can go back to login page there, then clicking \'forgot password\'!<p>';
            }
            
        }

    ?>
    
    </main>
        <div class="go-back-container">
            <a id="return-to-login" href="index.php" >Back to Main Page</a>
            <a id="return-to-login" href="loginPage.php" >Back to Login</a>
        </div>
        <?php 
            echo footer_template; 
        ?>
  </body>
  </html>