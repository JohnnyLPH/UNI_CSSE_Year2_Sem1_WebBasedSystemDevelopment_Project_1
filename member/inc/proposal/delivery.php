
<?php

    function printDeliveryInfoBanner() {
        printInfoBanner('car-time.png', 'When do you need the car(s)?',
    'It takes time to review and approve proposals. Some cars have a longer waiting period depending on availability, while some are only available upon order.</p>
    <p>Your expected delivery time allows us to plan ahead and make the appropriate decisions.</p>');
    }

    if($post) {
        $deliveryMth = filter_input(INPUT_POST, 'delivery-mth', FILTER_VALIDATE_INT);
    } else {
        // retrieve from database
        $preferredDelivery = getOrderCol('preferredDelivery');
                
        if(!$preferredDelivery) {
            showProposalNotFoundError();
            die();
        }

        $preferredDelivery = $preferredDelivery['preferredDelivery'];

        printHeader();
        printNavBar();
        printFormHeader1();        
        printDeliveryInfoBanner();
        printFormHeader2();
    }

    if($post || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete       

        if($deliveryMth === NULL || $deliveryMth === false) {
            if($post) {
                $inputError['deliveryMth'] = 'Select your preferred delivery month';
            }
        } else {
            if($deliveryMth >= date('n')) {
                $preferredDelivery = date('Y-').(strlen($deliveryMth) === 1 ? '0' : '').$deliveryMth.'-01';       
            } else {
                $preferredDelivery = (date('Y') + 1).(strlen($deliveryMth) === 1 ? '-0' : '-').$deliveryMth.'-01';
            }
        }


        if($post) {
            if(!$memberId) {
                // session expired
                showSessionExpiredError();                
                printDeliveryInfoBanner();
                printFormHeader2();
            } else if(empty($inputError) || $goPrevious) {
                // all inputs valid, save to database
                if(orderExists()) {
                    updateOrderCol('preferredDelivery', $preferredDelivery);                    
                    if(empty($inputError)) {
                        setCurrentStageStatus(1);
                    } else {
                        setCurrentStageStatus(-1);
                    }
                    if($goPrevious) {
                        redirect(getFormActionURL($requestedStage - 1));
                    } else {                        
                        printHeader();
                        printNavBar();
                        echo
   '<main>
        <div style="text-align: center;">
            <img src="./img/check-mark-verified.gif" style="max-width: 100px; vertical-align: middle;">              
            <h1 style="display: inline-block;">Proposal Submitted</h1>
            <hr>
            <h2>Thank You</h2>
            <p>It will be reviewed shortly.</p>    
            <a class="button" href="./orders.php">View All Proposals / Orders</a>        
        </div>
        <a>
    </main>
</body>';
                        die();
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
                printDeliveryInfoBanner();
                printFormHeader2();
                echo HTML_WARNING_BANNER;
            }
        }
    }
    
    echo   '<fieldset>
                <label for="delivery-mth">Preferred Delivery Month: </label>
                <div class="input">
                    <span class="form-icon">event</span>
                    <select name="delivery-mth">
                        <option value="">-- Select Month --</option>';

    $currentMonth = date_create();

    for ($i = 0; $i < 12; $i++) { 
        echo '<option value="'.date_format($currentMonth, 'n').'">'.date_format($currentMonth, 'F Y').'</option>';
        
        // advance to next month
        date_modify($currentMonth, 'first day of next month');
    }

    echo               '</select>
                    <p class="warning-text hidden">Error</p>
                </div>
            </fieldset>';

    echo printFormFooter();
?>