<?php
    function printBankInfoBanner() {
        printInfoBanner('bank.png', 'Our funders need to know your current bank details',
    'Preferably your current bank account has been opened for at least 3 years.');
    }

    if($post) {
        $bankName = $_POST['name'] ?? '';
        $add1 = $_POST['add1'] ?? '';
        $add2 = $_POST['add2'] ?? '';
        $city = $_POST['city'] ?? '';
        $postcode = $_POST['postcode'] ?? '';
        $sortCode = ($_POST['sortCode'][0] ?? '').($_POST['sortCode'][1] ?? '').($_POST['sortCode'][2] ?? '');
        $accountName = $_POST['accountName'] ?? '';
        $accountNum = $_POST['accountNum'] ?? '';
        $accountYr = filter_input(INPUT_POST, 'accountYr', FILTER_VALIDATE_INT);

    } else {
        // retrieve from database
        $bank = getOrderCol('bank');
                
        if(!$bank) {
            showProposalNotFoundError();
            die();
        }

        $bank = json_decode($bank['bank'], true);

        $bankName = $bank['name'] ?? '';
        $add1 = $bank['add1'] ?? '';
        $add2 = $bank['add2'] ?? '';
        $city = $bank['city'] ?? '';
        $postcode = $bank['postcode'] ?? '';
        $sortCode = $bank['sortCode'] ?? '';
        $accountName = $bank['accountName'] ?? '';
        $accountNum = $bank['accountNum'] ?? '';
        $accountYr = $bank['accountYr'] ?? '';
        
        printHeader();
        printNavBar();
        printFormHeader1();        
        printBankInfoBanner();
        printFormHeader2();
    }

    if($post || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete       
                
        validateName($bankName, 'Bank name', 'name', $bank, false);
        validateAddress($add1, 'add1', $bank);
        validateAddress($add2, 'add2', $bank);
        validateName($city, 'Bank\'s town/city name', 'city', $bank);
        validatePostcode($postcode, 'postcode', $bank);
        
        if($sortCode === '') {
            if($post) {
                $inputError['sortCode'] = 'Enter your '.($type === 2 ? 'company\'s ' : '').'bank sort code';
            }
        } else if (search('/[^\d]/', $sortCode) >= 0) {
            $inputError['sortCode'] = 'Invalid number. Bank sort code can only contain number digits without any space nor any other character.';
        } else if (strlen($sortCode) !== 6) {
            $inputError['sortCode'] = 'UK bank sort code must have exactly 6 digits.';
        }
        $bank['sortCode'] = $sortCode;

        validateName($accountName, ($type === 2 ? 'Company\'s ' : '').'bank account name', 'accountName', $bank, false);

        if($accountNum === '') {
            if($post) {
                $inputError['accountNum'] = 'Enter your '.($type === 2 ? 'company\'s ' : '').'bank account number';
            }
        } else if (search('/[^\d]/', $accountNum) >= 0) {
            $inputError['accountNum'] = 'Invalid number. Bank account number can only contain number digits without any space nor any other character.';
        } else if (strlen($accountNum) < 7 || strlen($accountNum) > 8) {
            $inputError['accountNum'] = 'UK bank account number must have 7-8 digits.';
        }
        $bank['accountNum'] = $accountNum;

        if($accountYr === NULL || $accountYr === false) {
            if($post) {
                $inputError['accountYr'] = 'Enter the year your '.($type === 2 ? 'company\'s ' : '').'bank account was opened';
            }
        } else if ($accountYr < 1900) {
            $inputError['accountYr'] = 'Invalid year. Year must be at least year 1900.';
        } else if ($accountYr > intval(date('Y'))) {
            $inputError['accountYr'] = 'Invalid year. Year cannot be later than year '.date('Y').'.';
        }
        $bank['accountYr'] = $accountYr;

        if($post) {
            if(!$memberId) {
                // session expired
                showSessionExpiredError();                
                printBankInfoBanner();
                printFormHeader2();
            } else if(empty($inputError) || $goPrevious) {
                // all inputs valid, save to database
                if(orderExists()) {
                    updateOrderCol('bank', $bank);
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
                printBankInfoBanner();
                printFormHeader2();
                echo HTML_WARNING_BANNER;
            }
        }
    }

    echo
           '<fieldset>
                <label for="name">Bank Name: </label>
                <div class="input">
                    <span class="form-icon">business</span>
                    <div>
                        <input type="text" name="name" id="name"'.(isset($inputError['name']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($bankName).'">
                        <p class="warning-text'.(isset($inputError['name']) ? (HTML_SHOW_WARNING.$inputError['name']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="add1">Bank Address Line 1: </label>
                <div class="input">
                    <span class="form-icon">place</span>
                    <div>
                        <input type="text" name="add1" id="add1"'.(isset($inputError['add1']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($add1).'">
                        <p class="warning-text'.(isset($inputError['add1']) ? (HTML_SHOW_WARNING.$inputError['add1']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="add2">Bank Address Line 2: </label>
                <div class="input">
                    <span class="form-icon">place</span>
                    <div>
                        <input type="text" name="add2" id="add2"'.(isset($inputError['add2']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($add2).'">
                        <p class="warning-text'.(isset($inputError['add2']) ? (HTML_SHOW_WARNING.$inputError['add2']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="city">Bank Town / City: </label>
                <div class="input">
                    <span class="form-icon">location_city</span>
                    <div>
                        <input type="text" name="city" id="city"'.(isset($inputError['city']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($city).'">
                        <p class="warning-text'.(isset($inputError['city']) ? (HTML_SHOW_WARNING.$inputError['city']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="postcode">Bank Postal Code: </label>
                <div class="input">
                    <span class="form-icon">markunread_mailbox</span>
                    <div>
                        <input type="text" name="postcode" id="postcode"'.(isset($inputError['postcode']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($postcode).'">
                        <p class="warning-text'.(isset($inputError['postcode']) ? (HTML_SHOW_WARNING.$inputError['postcode']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>         
            <fieldset>
                <label for="sortCode">Bank Sort Code: </label>
                <div class="input">
                    <span class="form-icon">pin</span>
                    <div>
                        <div class="input-flex-triple" id="sortCode">
                            <input type="text" maxlength="2" inputmode="numeric" name="sortCode[0]" id="sortCode[0]"'.(isset($inputError['sortCode']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars(substr($sortCode, 0,2)).'">
                            <label>–</label>
                            <input type="text" maxlength="2" inputmode="numeric" name="sortCode[1]" id="sortCode[1]"'.(isset($inputError['sortCode']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars(substr($sortCode, 2,2)).'">
                            <label>–</label>
                            <input type="text" maxlength="2" inputmode="numeric" name="sortCode[2]" id="sortCode[2]"'.(isset($inputError['sortCode']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars(substr($sortCode, 4,2)).'">
                        </div>
                        <p class="warning-text'.(isset($inputError['sortCode']) ? (HTML_SHOW_WARNING.$inputError['sortCode']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="accountName">Your Full Bank Account Name: </label>
                <div class="input">
                    <span class="form-icon">badge</span>
                    <div>
                        <input type="text" placeholder="What is the full legal name of your bank account?" name="accountName" id="accountName"'.(isset($inputError['accountName']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($accountName).'">
                        <p class="warning-text'.(isset($inputError['accountName']) ? (HTML_SHOW_WARNING.$inputError['accountName']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="accountNum">Your Bank Account Number: </label>
                <div class="input">
                <span class="form-icon">numbers</span>
                    <div>
                        <input type="number" min="1000000" placeholder="Carefully enter your correct bank account number." name="accountNum" id="accountNum"'.(isset($inputError['accountNum']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($accountNum).'">
                        <p class="warning-text'.(isset($inputError['accountNum']) ? (HTML_SHOW_WARNING.$inputError['accountNum']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="accountYr">Your Bank Account Opening Year: </label>
                <div class="input">
                    <span class="form-icon">schedule</span>
                    <div>
                        <input type="number" min="1900" max="'.date('Y').'" maxlength="4" placeholder="Which year was your bank account opened?" name="accountYr" id="accountYr"'.(isset($inputError['accountYr']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($accountYr).'">
                        <p class="warning-text'.(isset($inputError['accountYr']) ? (HTML_SHOW_WARNING.$inputError['accountYr']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>';

    echo printFormFooter();
?>
