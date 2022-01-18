<?php
    function printJobInfoBanner() {
        printInfoBanner('work-history.png', 'Finance provider requires your current job information',
        'Preferably you have worked in your current company for at least 3 years.');
    }

    function printCompanyInfoBanner() {
        printInfoBanner('office-building.png', 'Finance providers require your company information',
        'They need to ensure your company has a good credit score.');
    }

    if($post) {       
        if($type === 1 || $requestedStage === 5) {
            // personal (stage 4) or business (stage 5) only

            $companyName = $_POST['name'] ?? '';
            $add1 = $_POST['add1'] ?? '';
            $add2 = $_POST['add2'] ?? '';
            $city = $_POST['city'] ?? '';
            $postcode = $_POST['postcode'] ?? '';
            $email = $_POST['email'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $companyDescription = $_POST['description'] ?? '';            
        }

        if($requestedStage === 4) {
            // personal (stage 4) or business (stage 4) only
            
            $jobTitle = $_POST['title'] ?? '';
            $salary = filter_input(INPUT_POST, 'salary', FILTER_VALIDATE_INT);
            $incomeDescription = $_POST['incomeDescription'] ?? '';
            $workedYrs = filter_input(INPUT_POST, 'workedYrs', FILTER_VALIDATE_INT);
            $workedMths = filter_input(INPUT_POST, 'workedMths', FILTER_VALIDATE_INT);
        } else {
            // business (stage 5) only

            $companyType = filter_input(INPUT_POST, 'companyType', FILTER_VALIDATE_INT);
            $regNum = $_POST['regNum'] ?? '';
            $estYr = filter_input(INPUT_POST, 'estYr', FILTER_VALIDATE_INT);
        }

    } else {
        // retrieve from database
        $jobCompany = getOrderCol('job, company');
        
        if(!$jobCompany) {
            showProposalNotFoundError();
            die();
        }                    

        if($type === 1 || $requestedStage === 5) {
            // personal (stage 4) or business (stage 5) only

            $company = json_decode($jobCompany['company'], true);

            $companyName = $company['name'] ?? '';
            $add1 = $company['add1'] ?? '';
            $add2 = $company['add2'] ?? '';
            $city = $company['city'] ?? '';
            $postcode = $company['postcode'] ?? '';
            $email = $company['email'] ?? '';
            $telephone = $company['telephone'] ?? '';
            $companyDescription = $company['description'] ?? '';            
        }

        if($requestedStage === 4) {
            // personal (stage 4) or business (stage 4) only
            
            $job = json_decode($jobCompany['job'], true);

            $jobTitle = $job['title'] ?? '';
            $salary = $job['salary'] ?? '';
            $incomeDescription = $job['incomeDescription'] ?? '';
            $workedYrs = $job['workedYrs'] ?? '';
            $workedMths = $job['workedMths'] ?? '';
        } else {
            // business (stage 5) only

            $companyType = $company['type'] ?? '';
            $regNum = $company['regNum'] ?? '';
            $estYr = $company['estYr'] ?? '';
        }

        printHeader();
        printNavBar();
        printFormHeader1();
        if($requestedStage === 4) {
            printJobInfoBanner();
        } else {
            printCompanyInfoBanner();
        }
        printFormHeader2();
    }
    
    if($post || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete       

        if($type === 1 || $requestedStage === 5) {
            // personal (stage 4) or business (stage 4) only

            validateName($companyName, 'Company name', 'name', $company);
            validateAddress($add1, 'add1', $company);
            validateAddress($add2, 'add2', $company);
            validateName($city, 'Company\'s town/city name', 'city', $company);
            validatePostcode($postcode, 'postcode', $company);
            validateEmail($email, 'email', $company);
            validatePhone($telephone, 'telephone', $company);        
            validateDescription($companyDescription, 'description', $company);
        }

        if($requestedStage === 4) {
            // personal (stage 4) or business (stage 4) only

            validateName($jobTitle, 'Job title', 'title', $job);

            if($salary === NULL || $salary === false) {
                if($post) {
                    $inputError['salary'] = 'Enter your monthly salary';
                }
            } else if ($salary < 10) {
                $inputError['salary'] = 'Invalid salary. Your monthly salary should be at least 10 £.';
            }
            $job['salary'] = $salary;

            validateDescription($incomeDescription, 'incomeDescription', $job);
            
            if($workedYrs === NULL || $workedYrs === false) {
                
            } else if ($workedYrs < 0 || $workedYrs > 100) {
                $inputError['workedYrs'] = 'Invalid number of years worked. Number of years worked must be in 0 - 100 years.';
            }
            $job['workedYrs'] = $workedYrs;

            if($workedMths === NULL || $workedMths === false) {
                
            } else if ($workedMths < 0 || $workedMths > 12) {
                $inputError['workedMths'] = 'Invalid number of months worked. Number of months worked must be in 0 - 12 months.';
            }
            $job['workedMths'] = $workedMths;

        } else {
            // business (stage 5) only
            
            if($companyType === NULL || $companyType === false || $companyType === '') {
                if($post) {
                    $inputError['companyType'] = 'Select your company type';
                }
            } else if ($companyType < 1 || $companyType > 6) {
                $inputError['companyType'] = 'Invalid company type. Please select a company type from the dropdown menu.';
            }
            $company['type'] = $companyType;

            if($regNum === '') {
                if($post) {
                    $inputError['regNum'] = 'Enter your company\'s registration number';
                }
            } else if(search('/[^\w\d]/', $regNum) >= 0) {
                $inputError['regNum'] = 'Invalid character. Registration number can only contain A-Z, a-z, and 0-9 without any space \' \' character.';
            } else if(strlen($regNum) !== 8) {
                $inputError['regNum'] = 'Invalid length. UK company registration number must have exactly 8 number of characters.';
            }
            $regNum = strtoupper($regNum);
            $company['regNum'] = $regNum;

            if($estYr === NULL || $estYr === false || $estYr === '') {
                if($post) {
                    $inputError['estYr'] = 'Enter your company\'s establishment year';
                }
            } else if ($estYr < 1900) {
                $inputError['estYr'] = 'Invalid year. Year must be at least year 1900.';
            } else if ($estYr > intval(date('Y'))) {
                $inputError['estYr'] = 'Invalid year. Year cannot be later than year '.date('Y').'.';
            }
            $company['estYr'] = $estYr;
        }

        if($post) {
            if(!$memberId) {
                // session expired
                showSessionExpiredError();
                if($requestedStage === 4) {
                    printJobInfoBanner();
                } else {
                    printCompanyInfoBanner();
                }
                printFormHeader2();
            } else if(empty($inputError) || $goPrevious) {
                // all inputs valid, save to database
                if(orderExists()) {
                    if(!empty($job)) {
                        updateOrderCol('job', $job); 
                    } 
                    if(!empty($company)) {
                        updateOrderCol('company', $company);
                    }
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
                if($requestedStage === 4) {
                    printJobInfoBanner();
                } else {
                    printCompanyInfoBanner();
                }
                printFormHeader2();
                echo HTML_WARNING_BANNER;
            }
        }
    }
    
    if($type === 1 || $requestedStage === 5) {
        // personal (stage 4) or business (stage 5) only

        echo   
           '<fieldset>
                <label for="name">Full Company Name: </label>
                <div class="input">
                    <span class="form-icon">business</span>
                    <div>
                        <input type="text" name="name" id="name"'.(isset($inputError['name']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($companyName).'">
                        <p class="warning-text'.(isset($inputError['name']) ? (HTML_SHOW_WARNING.$inputError['name']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="add1">Company Address Line 1: </label>
                <div class="input">
                    <span class="form-icon">place</span>
                    <div>
                        <input type="text" name="add1" id="add1"'.(isset($inputError['add1']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($add1).'">
                        <p class="warning-text'.(isset($inputError['add1']) ? (HTML_SHOW_WARNING.$inputError['add1']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="add2">Company Address Line 2: </label>
                <div class="input">
                    <span class="form-icon">place</span>
                    <div>
                        <input type="text" name="add2" id="add2"'.(isset($inputError['add2']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($add2).'">
                        <p class="warning-text'.(isset($inputError['add2']) ? (HTML_SHOW_WARNING.$inputError['add2']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="city">Company Town / City: </label>
                <div class="input">
                    <span class="form-icon">location_city</span>
                    <div>
                        <input type="text" name="city" id="city"'.(isset($inputError['city']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($city).'">
                        <p class="warning-text'.(isset($inputError['city']) ? (HTML_SHOW_WARNING.$inputError['city']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="postcode">Company Postal Code: </label>
                <div class="input">
                    <span class="form-icon">markunread_mailbox</span>
                    <div>
                        <input type="text" name="postcode" id="postcode"'.(isset($inputError['postcode']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($postcode).'">
                        <p class="warning-text'.(isset($inputError['postcode']) ? (HTML_SHOW_WARNING.$inputError['postcode']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="email">Company Email: </label>
                <div class="input">
                    <span class="form-icon">email</span>
                    <div>
                        <input type="email" name="email" placeholder="username@domain.com" id="email"'.(isset($inputError['email']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($email).'">
                        <p class="warning-text'.(isset($inputError['email']) ? (HTML_SHOW_WARNING.$inputError['email']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="telephone">Company Telephone Number: </label>
                <div class="input">
                    <span class="form-icon">call</span>
                    <div>
                        <div style="display: flex; align-items: center;">
                            <span style="white-space: pre;">+44 </span>
                            <input type="tel" name="telephone" placeholder="7123456789" id="telephone"'.(isset($inputError['telephone']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($telephone).'">
                        </div>
                        <p class="warning-text'.(isset($inputError['telephone']) ? (HTML_SHOW_WARNING.$inputError['telephone']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="description">Company Profile: </label>
                <div class="input">
                    <span class="form-icon">location_city</span>
                    <div>
                        <input type="text" placeholder="What is the company about?" name="description" id="description"'.(isset($inputError['description']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($companyDescription).'">
                        <p class="warning-text'.(isset($inputError['description']) ? (HTML_SHOW_WARNING.$inputError['description']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>';
    }
    
    if($requestedStage === 5) {
        echo
           '<fieldset>
                <label for="companyType">Company Type: </label>
                <div class="input">
                    <span class="form-icon">business</span>
                    <div>
                        <select name="companyType" id="companyType"'.(isset($inputError['companyType']) ? HTML_WARNING_CLASS : '').'>
                            <option value="">-- Select type --</option>
                            <option value="1"'.(($companyType == 1) ? ' selected' : '').'>Sole Proprietorship</option>
                            <option value="2"'.(($companyType == 2) ? ' selected' : '').'>Partnership</option>
                            <option value="3"'.(($companyType == 3) ? ' selected' : '').'>Private Limited</option>
                            <option value="4"'.(($companyType == 4) ? ' selected' : '').'>Public Limited</option>
                            <option value="5"'.(($companyType == 5) ? ' selected' : '').'>Government Agency</option>
                            <option value="6"'.(($companyType == 6) ? ' selected' : '').'>Other</option>
                        </select>
                        <p class="warning-text'.(isset($inputError['companyType']) ? (HTML_SHOW_WARNING.$inputError['companyType']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="regNum">Company Registration Number: </label>
                <div class="input">
                    <span class="form-icon">numbers</span>
                    <div>
                        <input type="text" name="regNum" id="regNum"'.(isset($inputError['regNum']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($regNum).'">
                        <p class="warning-text'.(isset($inputError['regNum']) ? (HTML_SHOW_WARNING.$inputError['regNum']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="estYr">Company Year of Establishment: </label>
                <div class="input">
                    <span class="form-icon">schedule</span>
                    <div>
                        <input type="number" min="1900" max="'.date('Y').'" maxlength="4" placeholder="Which year was your company founded?" name="estYr" id="estYr"'.(isset($inputError['estYr']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($estYr).'">
                        <p class="warning-text'.(isset($inputError['estYr']) ? (HTML_SHOW_WARNING.$inputError['estYr']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>';            
    } else if($requestedStage === 4) {
        echo            
           '<fieldset>
                <label for="title">Your Job Title: </label>
                <div class="input">
                    <span class="form-icon">work</span>
                    <div>
                        <input type="text" name="title" id="title"'.(isset($inputError['title']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($jobTitle).'">
                        <p class="warning-text'.(isset($inputError['title']) ? (HTML_SHOW_WARNING.$inputError['title']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
           <fieldset>
                <label for="salary">Your Gross Salary (Monthly): </label>
                <div class="input">
                    <span class="form-icon">local_atm</span>
                    <div>
                        <input type="number" name="salary" id="salary"'.(isset($inputError['salary']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($salary).'">
                        <p class="warning-text'.(isset($inputError['salary']) ? (HTML_SHOW_WARNING.$inputError['salary']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="incomeDescription">Your Salary Bonus/Allowance Information: </label>
                <div class="input">
                    <span class="form-icon">local_atm</span>
                    <div>
                        <input type="text" placeholder="What / How are the bonuses / allowances offered?" name="incomeDescription" id="incomeDescription"'.(isset($inputError['incomeDescription']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($incomeDescription).'">
                        <p class="warning-text'.(isset($inputError['incomeDescription']) ? (HTML_SHOW_WARNING.$inputError['incomeDescription']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label>Your Working Duration: </label>
                <div class="input">
                    <span class="form-icon">schedule</span>
                    <div>
                        <div class="input-flex-double">
                            <div>
                                <input type="number" min="0" max="999" name="workedYrs" id="workedYrs"'.(isset($inputError['workedYrs']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($workedYrs).'">
                                <label for="workedYrs">year(s)</label>
                            </div>
                            <div>
                                <select name="workedMths" id="workedMths">
                                    <option value=""></option>
                                    <option value="0"'.(($workedMths == 0) ? ' selected' : '').'>0</option>
                                    <option value="1"'.(($workedMths == 1) ? ' selected' : '').'>1</option>
                                    <option value="2"'.(($workedMths == 2) ? ' selected' : '').'>2</option>
                                    <option value="3"'.(($workedMths == 3) ? ' selected' : '').'>3</option>
                                    <option value="4"'.(($workedMths == 4) ? ' selected' : '').'>4</option>
                                    <option value="5"'.(($workedMths == 5) ? ' selected' : '').'>5</option>
                                    <option value="6"'.(($workedMths == 6) ? ' selected' : '').'>6</option>
                                    <option value="7"'.(($workedMths == 7) ? ' selected' : '').'>7</option>
                                    <option value="8"'.(($workedMths == 8) ? ' selected' : '').'>8</option>
                                    <option value="9"'.(($workedMths == 9) ? ' selected' : '').'>9</option>
                                    <option value="10"'.(($workedMths == 10) ? ' selected' : '').'>10</option>
                                    <option value="11"'.(($workedMths == 11) ? ' selected' : '').'>11</option>
                                    <option value="12"'.(($workedMths == 12) ? ' selected' : '').'>12</option>
                                </select>
                                <label for="workedMths">month(s)</label>
                            </div>                        
                        </div>
                        <p class="warning-text';

    if(isset($inputError['workedMths'])) {
        echo HTML_SHOW_WARNING.$inputError['workedMths'];
    } else if(isset($inputError['workedYrs'])) {
        echo HTML_SHOW_WARNING.$inputError['workedYrs'];
    } else {
        echo HTML_HIDE_WARNING.'Error';
    }

    echo               '</p>
                    </div>
                </div>
            </fieldset>';
    }
    echo printFormFooter();
?>