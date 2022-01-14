<?php

    if($post) {
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $gender = filter_input(INPUT_POST, 'gender', FILTER_VALIDATE_INT);
        $dobYr = filter_input(INPUT_POST, 'dob-yr', FILTER_VALIDATE_INT);
        $dobMth = filter_input(INPUT_POST, 'dob-mth', FILTER_VALIDATE_INT);
        $dobDay = filter_input(INPUT_POST, 'dob-day', FILTER_VALIDATE_INT);
    } else {
        // retrieve from database
        $personal = getOrderCol('personal');
        
        if(!$personal) {
            showProposalNotFoundError();
            die();
        }

        $personal = json_decode($personal['personal'], true);        

        $firstName = $personal['firstName'] ?? '';
        $lastName = $personal['lastName'] ?? '';
        $email = $personal['email'] ?? '';
        $phone = $personal['phone'] ?? '';
        $gender = $personal['gender'] ?? '';        
        $dobDay = substr($personal['dob'] ?? '', 8);        
        $dobMth = substr($personal['dob'] ?? '', 5, 2);
        $dobYr = substr($personal['dob'] ?? '', 0, 4);

        printHeader();
        printNavBar();
        printFormHeader1();
        printFormHeader2();
    }
    
    if($post || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete       

        validateName($firstName, 'First name', 'firstName', $personal, false);
        validateName($lastName, 'Last name', 'lastName', $personal, false);
        validateEmail($email, 'email', $personal);
        validatePhone($phone, 'phone', $personal);
        
        if($gender !== NULL && $gender !== false && $gender >= 0 && $gender <= 2) {        
            $personal['gender'] = $gender;
        } else if($post) {
            $inputError['gender'] = 'Select your gender';
        }

        // date of birth
        if($dobYr === NULL || $dobYr === false) {
            if($post) {
                $inputError['dobYr'] = 'Select your year of birth';
            }
        } else if ($dobYr < 1900) {
            $inputError['dobYr'] = 'Invalid year. Select your year of birth that is older than year 1900.';
        } else if ($dobYr > intval(date('Y')) - 18) {
            $inputError['dobYr'] = 'Invalid year. Applicants must be at least 18 years old.';
        }
        
        if($dobMth === NULL || $dobMth === false) {
            if($post) {
                $inputError['dobMth'] = 'Select your month of birth';
            }
        } else if ($dobMth < 1 || $dobMth > 12) {
            $inputError['dobMth'] = 'Invalid month. Select your month of birth from Jan to Dec.';
        }

        if($dobDay === NULL || $dobDay === false) {
            if($post) {
                $inputError['dobDay'] = 'Select your day of birth';
            }
        } else if ($dobDay < 1) {
            $inputError['dobDay'] = 'Invalid day. Your day of birth must be day 1 or later.';
        } else if (!isset($inputError['dobMth'])) {            
            $lastDay = 31;
            if($dobMth === 4 || $dobMth === 6 || $dobMth === 9 || $dobMth === 11) {
                $lastDay = 30;
            } else if($dobMth === 2) {
                $lastDay = 28;
                
                if(!isset($inputError['dobYr']) && date('L', strtotime($dobYr.'-01-01')) === '1') {
                    // year selected is a leap year
                    $lastDay = 29;
                }
            }
            if($dobDay > $lastDay) {
                $inputError['dobDay'] = 'Invalid day. The last day of this month is day '.$lastDay.'.';
            }
        }

        if($post && !isset($inputError['dobDay']) && !isset($inputError['dobMth']) && !isset($inputError['dobYr'])) {
            // valid date of birth
            $personal['dob'] = $dobYr.'-';
            if($dobMth < 10) {
                $personal['dob'].= '0';
            }
            $personal['dob'].= $dobMth.'-';
            if($dobDay < 10) {
                $personal['dob'].= '0';
            }
            $personal['dob'].= $dobDay;
        }

        if($post) {
            if(!$memberId) {
                // session expired
                showSessionExpiredError();
                printFormHeader2();
            } else if(empty($inputError) || $goPrevious) {
                // all inputs valid, save to database
                if(orderExists()) {
                    updateOrderCol('personal', $personal);                   
                    if(empty($inputError)) {
                        setCurrentStageStatus(1);
                    } else {
                        setCurrentStageStatus(-1);
                    }
                    if($goPrevious) {
                        redirect(getFormActionURL($requestedStage - 1));
                    } else {
                        redirect(getFormActionURL($requestedStage + 1));
                    }
                } else {            
                    showProposalNotFoundError();
                    die();
                }
            } else {
                // 1 or more invalid inputs, show warning
                setCurrentStageStatus(-1);
                printHeader();
                printNavBar();
                printFormHeader1();
                printFormHeader2();
                echo HTML_WARNING_BANNER;
            }
        }
    }

    echo 
           '<fieldset>
                <label for="firstName">First Name: </label>
                <div class="input">
                    <span class="form-icon">badge</span>
                    <div>
                        <input type="text" name="firstName" id="firstName"'.(isset($inputError['firstName']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($firstName).'">
                        <p class="warning-text'.(isset($inputError['firstName']) ? (HTML_NO_HIDDEN_WARNING.$inputError['firstName']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="lastName">Last Name: </label>
                <div class="input">
                    <span class="form-icon">badge</span>
                    <div>
                        <input type="text" name="lastName" id="lastName"'.(isset($inputError['lastName']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($lastName).'">
                        <p class="warning-text'.(isset($inputError['lastName']) ? (HTML_NO_HIDDEN_WARNING.$inputError['lastName']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="email">Email: </label>
                <div class="input">
                    <span class="form-icon">email</span>
                    <div>
                        <input type="email" name="email" placeholder="username@domain.com" id="email"'.(isset($inputError['email']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($email).'">
                        <p class="warning-text'.(isset($inputError['email']) ? (HTML_NO_HIDDEN_WARNING.$inputError['email']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="phone">Phone Number: </label>
                <div class="input">
                    <span class="form-icon">smartphone</span>
                    <div>
                        <div style="display: flex; align-items: center;">
                            <span style="white-space: pre;">+44 </span>
                            <input type="tel" name="phone" placeholder="7123456789" id="phone"'.(isset($inputError['phone']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($phone).'">
                        </div>
                        <p class="warning-text'.(isset($inputError['phone']) ? (HTML_NO_HIDDEN_WARNING.$inputError['phone']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="gender">Gender: </label>
                <div class="input" style="text-align: center;">
                    <span class="form-icon">wc</span>
                    <div>
                        <input type="radio" name="gender" value="1" id="male"'.(($gender === 1) ? ' checked' : '').'>
                        <label for="male" style="font-weight: normal;">Male</label>
                        <input type="radio" name="gender" value="2" id="female"'.(($gender === 2) ? ' checked' : '').'>
                        <label for="female" style="font-weight: normal;">Female</label>
                        <input type="radio" name="gender" value="0" id="hidden"'.(($gender === 0) ? ' checked' : '').'>
                        <label for="hidden" style="font-weight: normal;">Prefer Not to Say</label>               
                        <p class="warning-text'.(isset($inputError['gender']) ? (HTML_NO_HIDDEN_WARNING.$inputError['gender']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="dob">Date of Birth: </label>
                <div class="input">
                    <span class="form-icon">cake</span>
                    <div>
                        <div class="input-flex-triple" id="dob">
                            <select name="dob-day"'.(isset($inputError['dobDay']) ? HTML_WARNING_CLASS : '').'>';
                                printSelectNumOptions(1, 31, 'Day', $dobDay);
        echo               '</select>
                            <select name="dob-mth"'.(isset($inputError['dobMth']) ? HTML_WARNING_CLASS : '').'>
                                <option value="">Month</option>
                                <option value="1"'.(($dobMth == 1) ? ' selected' : '').'>Jan</option>
                                <option value="2"'.(($dobMth == 2) ? ' selected' : '').'>Feb</option>
                                <option value="3"'.(($dobMth == 3) ? ' selected' : '').'>Mar</option>
                                <option value="4"'.(($dobMth == 4) ? ' selected' : '').'>Apr</option>
                                <option value="5"'.(($dobMth == 5) ? ' selected' : '').'>May</option>
                                <option value="6"'.(($dobMth == 6) ? ' selected' : '').'>Jun</option>
                                <option value="7"'.(($dobMth == 7) ? ' selected' : '').'>Jul</option>
                                <option value="8"'.(($dobMth == 8) ? ' selected' : '').'>Aug</option>
                                <option value="9"'.(($dobMth == 9) ? ' selected' : '').'>Sep</option>
                                <option value="10"'.(($dobMth == 10) ? ' selected' : '').'>Oct</option>
                                <option value="11"'.(($dobMth == 11) ? ' selected' : '').'>Nov</option>
                                <option value="12"'.(($dobMth == 12) ? ' selected' : '').'>Dec</option>
                            </select>
                            <select name="dob-yr"'.(isset($inputError['dobYr']) ? HTML_WARNING_CLASS : '').'>';
                                printSelectNumOptions(1900, intval(date('Y')), 'Year', $dobYr, true);
        echo               '</select>
                        </div>
                        <p class="warning-text';
        if(isset($inputError['dobYr']) && isset($inputError['dobMth']) && isset($inputError['dobDay'])) {
            echo HTML_NO_HIDDEN_WARNING.'Select your date of birth';
        } else if(isset($inputError['dobYr'])) {
            echo HTML_NO_HIDDEN_WARNING.$inputError['dobYr'];
        } else if(isset($inputError['dobMth'])) {
            echo HTML_NO_HIDDEN_WARNING.$inputError['dobMth'];
        } else if(isset($inputError['dobDay'])) {
            echo HTML_NO_HIDDEN_WARNING.$inputError['dobDay'];
        } else {
            echo HTML_HIDDEN_WARNING.'Error';
        }
        echo           '</p>
                    </div>
                </div>
            </fieldset>';
        
        echo printFormFooter();
?>