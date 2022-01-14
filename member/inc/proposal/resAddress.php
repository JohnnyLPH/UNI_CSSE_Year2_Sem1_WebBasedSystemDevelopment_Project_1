<?php
    function printAddInfoBanner() {
        printInfoBanner('house.png', 'Many funders need to know your 3-5 years address history',
    'Your current address should be the <strong>same</strong> as the address on your driving license.</p>
    <p>Otherwise, you may be required to update your driving license before obtaining the vehicle(s).');
    }

    if($post) {
        $add1 = $_POST['add1'] ?? '';
        $add2 = $_POST['add2'] ?? '';
        $city = $_POST['city'] ?? '';
        $postcode = $_POST['postcode'] ?? '';
        $status = $_POST['status'] ?? '';
        $livedYrs = filter_input(INPUT_POST, 'livedYrs', FILTER_VALIDATE_INT);
        $livedMths = filter_input(INPUT_POST, 'livedMths', FILTER_VALIDATE_INT);

    } else {
        // retrieve from database
        $residentialAddress = getOrderCol('residentialAddress');
                
        if(!$residentialAddress) {
            showProposalNotFoundError();
            die();
        }

        $residentialAddress = json_decode($residentialAddress['residentialAddress'], true);
        
        $add1 = $residentialAddress['add1'] ?? '';
        $add2 = $residentialAddress['add2'] ?? '';
        $city = $residentialAddress['city'] ?? '';
        $postcode = $residentialAddress['postcode'] ?? '';
        $status = $residentialAddress['status'] ?? '';
        $livedYrs = $residentialAddress['livedYrs'] ?? '';
        $livedMths = $residentialAddress['livedMths'] ?? '';

        printHeader();
        printNavBar();
        printFormHeader1();        
        printAddInfoBanner();
        printFormHeader2();
    }

    if($post || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete       

        validateAddress($add1, 'add1', $residentialAddress);
        validateAddress($add2, 'add2', $residentialAddress);
        validateName($city, 'Town/city name', 'city', $residentialAddress);

        if($postcode === '') {
            if($post) {
                $inputError['postcode'] = 'Enter your house postcode';
            }
        } else if(search('/[^\w]/', $postcode) >= 0) {
            $inputError['postcode'] = 'Invalid character. Postcode can only contain letters from A-Z and a-z without any space \' \' character.';
        } else if(strlen($postcode) < 5 || strlen($postcode) > 7) {
            $inputError['postcode'] = 'Invalid length. UK postcode must have 5 - 7 number of characters.';
        }
        $postcode = strtoupper($postcode);
        $residentialAddress['postcode'] = $postcode;

        $residentialAddress['status'] = intval($status);

        if($livedYrs === NULL || $livedYrs === false) {
                
        } else if ($livedYrs < 0 || $livedYrs > 100) {
            $inputError['livedYrs'] = 'Invalid number of years lived. Number of years lived must be in 0 - 100 years.';
        }
        $residentialAddress['livedYrs'] = $livedYrs;

        if($livedMths === NULL || $livedMths === false) {
            
        } else if ($livedMths < 0 || $livedMths > 12) {
            $inputError['livedMths'] = 'Invalid number of months lived. Number of months lived must be in 0 - 12 months.';
        }
        $residentialAddress['livedMths'] = $livedMths;
        

        if($post) {
            if(!$memberId) {
                // session expired                
                showSessionExpiredError();                
                printAddInfoBanner();
                printFormHeader2();
            } else if(empty($inputError) || $goPrevious) {
                // all inputs valid, save to database
                if(orderExists()) {
                    updateOrderCol('residentialAddress', $residentialAddress);
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
                printAddInfoBanner();
                printFormHeader2();
                echo HTML_WARNING_BANNER;
            }
        }
    }

    echo
           '<fieldset>
                <label for="add1">Address Line 1: </label>
                <div class="input">
                    <span class="form-icon">place</span>
                    <div>
                        <input type="text" name="add1" id="add1"'.(isset($inputError['add1']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($add1).'">
                        <p class="warning-text'.(isset($inputError['add1']) ? (HTML_NO_HIDDEN_WARNING.$inputError['add1']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="add2">Address Line 2: </label>
                <div class="input">
                    <span class="form-icon">place</span>
                    <div>
                        <input type="text" name="add2" id="add2"'.(isset($inputError['add2']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($add2).'">
                        <p class="warning-text'.(isset($inputError['add2']) ? (HTML_NO_HIDDEN_WARNING.$inputError['add2']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="city">Town / City: </label>
                <div class="input">
                    <span class="form-icon">location_city</span>
                    <div>
                        <input type="text" name="city" id="city"'.(isset($inputError['city']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($city).'">
                        <p class="warning-text'.(isset($inputError['city']) ? (HTML_NO_HIDDEN_WARNING.$inputError['city']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="postcode">Postal Code: </label>
                <div class="input">
                    <span class="form-icon">markunread_mailbox</span>
                    <div>
                        <input type="text" name="postcode" id="postcode"'.(isset($inputError['postcode']) ? HTML_WARNING_CLASS : '').' value="'.htmlspecialchars($postcode).'">
                        <p class="warning-text'.(isset($inputError['postcode']) ? (HTML_NO_HIDDEN_WARNING.$inputError['postcode']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label for="status">Residential Status: </label>
                <div class="input">
                    <span class="form-icon">people</span>
                    <div>
                        <select name="status" id="status">
                            <option value="">-- Select status --</option>
                            <option value="1">Property Owner</option>
                            <option value="2">Property Tenant</option>
                            <option value="3">Property Occupant (Live with Parents)</option>
                            <option value="4">Property Occupant (Live with Friends/Partner)</option>
                        </select>
                        <p class="warning-text hidden">Error</p>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <label>Living Duration: </label>
                <div class="input">
                    <span class="form-icon">schedule</span>
                    <div>
                        <div class="input-flex-double">
                            <div>
                                <input type="number" min="0" max="100" name="livedYrs" id="livedYrs">
                                <label for="livedYrs">year(s)</label>
                            </div>
                            <div>
                                <select name="livedMths" id="livedMths">
                                    <option value=""></option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <label for="livedMths">month(s)</label>
                            </div>                        
                        </div>
                        <p class="warning-text hidden">Error</p>
                    </div>
                </div>
            </fieldset>';
    
    echo printFormFooter();
?>
