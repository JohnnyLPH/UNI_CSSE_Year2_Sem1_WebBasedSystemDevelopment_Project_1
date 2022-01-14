<?php

    function printCarInfoBanner() {
        printInfoBanner('car-wash.png', 'Confirm your Cars',
        'Then, fill in the proposal form.</p>
        <p>No payment is required until the order is confirmed by you after approval from LINGsCARS.');
    }

    function printAppTypeInfoBanner() {
        printInfoBanner('home-office.png', 'Personal or Business?',
        'Are you applying for yourself or applying using the name of your company for commercial usage?</p>
        <p>You can only apply for your company if you have been given the legal permission to do so.');
    }
    
    $confirm = $_POST['confirm'] ?? '';

    if($post) {        
        $applicationType = filter_input(INPUT_POST, 'applicationType', FILTER_VALIDATE_INT);
    } else {
        // retrieve from database
        $applicationType = getOrderCol('type');

        if(!$applicationType) {
            showProposalNotFoundError();
            die();
        }
        
        $applicationType = intval($applicationType['type']); 
    }
    
    if(!$confirm) {
        // either GET, or POST from cart.php
        printHeader();
        printNavBar();
        printFormHeader1();
        printCarInfoBanner();
        printFormHeader2();
        printAppTypeInfoBanner();
    }
    
    if(($confirm && $post) || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete
        
        if($applicationType !== NULL && $applicationType !== false && $applicationType >= 1 && $applicationType <= 2) {
            $type = $applicationType;
        } else if($post) {
            $inputError['applicationType'] = 'Select your application type';
        }

        if($post) {
            if(!$memberId) {
                // session expired
                showSessionExpiredError();
                printCarInfoBanner();
                printFormHeader2();
                printAppTypeInfoBanner();
            } else if(empty($inputError)) {
                // all inputs valid, save to database
                if(orderExists()) {
                    updateOrderCol('type', $type);
                    setCurrentStageStatus(1);
                    redirect(getFormActionURL($requestedStage + 1));
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
                printCarInfoBanner();
                printFormHeader2();
                printAppTypeInfoBanner();
                echo HTML_WARNING_BANNER;
            }
        }
    }
    
    echo   '<input type="hidden" name="confirm" value="true">
            <fieldset>
                <label for="applicationType">Type: </label>
                <div class="input" style="text-align: center;">
                    <span class="form-icon">help_outline</span>
                    <div>
                        <input type="radio" name="applicationType" value="1" id="personal"'.(($applicationType === 1) ? ' checked' : '').'>
                        <label for="personal" style="font-weight: normal;">Personal</label>
                        <input type="radio" name="applicationType" value="2" id="business"'.(($applicationType === 2) ? ' checked' : '').'>
                        <label for="business" style="font-weight: normal;">Business</label>        
                        <p class="warning-text'.(isset($inputError['applicationType']) ? (HTML_NO_HIDDEN_WARNING.$inputError['applicationType']) : (HTML_HIDDEN_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>';
        
        echo printFormFooter();
?>