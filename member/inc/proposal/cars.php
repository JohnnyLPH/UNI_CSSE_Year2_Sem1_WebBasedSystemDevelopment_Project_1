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

    function printCarsTable($cars, $carsResult) {
        if($cars && $carsResult) {
            echo 
            '<br>
            <div style="overflow-x:auto;">
                <table id="car-confirmation-table">
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Rental Fee (£/mth)</th>
                            <th>Lease Time (Month)</th>
                            <th>Initial Pay (* £/mth)</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach($carsResult as &$carRow) {
                echo '<tr><td><img src="..'.$carRow['imagePath'].$carRow['carImage'].'">
                <p><strong>'.$carRow['brandName'].' '.$carRow['carModel'].'</strong><br>
                <strong>Car ID: </strong>'.$carRow['id'].'</p></td>
                <td>'.$carRow['monthPrice'].'</td>
                <td>'.$carRow['leaseTime'].'</td>
                <td>'.$carRow['initialPay'].'</td>
                <td>'.$cars[$carRow['id']].'</td>
                </tr>';
            }
            unset($carRow);
            echo
                    '</tbody>
                </table>
            </div>';
        }
    }

    /*
    Allowed Methods:
    GET from existing order
    POST from existing order (confirm == true)
    POST from CART
    POST to create new order (confirm == true)
    */   
    
    $confirm = $_POST['confirm'] ?? '';

    if($post) {        
        $applicationType = filter_input(INPUT_POST, 'applicationType', FILTER_VALIDATE_INT);
        $cars = $_POST['cart'] ?? '';
    } else {
        // retrieve from database
        $result = getOrderCol('type, carsId');

        if(!$result) {
            showProposalNotFoundError();
            die();
        }
        
        $applicationType = intval($result['type']); 
        $cars = $result['carsId'];
    }

    if($cars) {
        $cars = json_decode($cars, true);

        $carsResult = getMultipleCars(array_keys($cars));            
    }
    
    if(($confirm && $post) || $stageStatus === -1) {
        // perform input validation if new data is received or existing stage data is incomplete
        
        if($applicationType !== NULL && $applicationType !== false && $applicationType >= 1 && $applicationType <= 2) {
            $type = $applicationType;
        } else if($post) {
            $inputError['applicationType'] = 'Select your application type';
        }

        if($post) {
            if($memberId && empty($inputError)) {
                // all inputs valid, save to database
                if(!$orderId) {
                    $orderId = newProposal($type, $cars);
                    foreach(array_keys($cars) as &$value) {
                        unset($_SESSION['cart-item'][$value]);
                    }
                    unset($value);
                } else if(orderExists()) {
                    updateOrderCol('type', $type);                    
                    setCurrentStageStatus(1);                 
                } else {            
                    showProposalNotFoundError();
                    die();
                }
                redirect(getFormActionURL($requestedStage + 1));
            }
        }
    }

    if($memberId) {
        printHeader();
        printNavBar();
        if($carsResult) {
            printFormHeader1();
        } else {
            showError('500 Error: No Cars in Proposal / Cart', 'Please try again or contact support.');
        }
    } else {
        // session expired
        showSessionExpiredError();
    }
    printCarInfoBanner();
    printCarsTable($cars, $carsResult);
    printFormHeader2();                
    printAppTypeInfoBanner();
    if((($confirm && $post) || $stageStatus === -1) && !empty($inputError)) {
        // 1 or more invalid inputs, show warning
        if($orderId && orderExists()) {
            setCurrentStageStatus(-1);
        }
        echo HTML_WARNING_BANNER;
    }

    echo   '<input type="hidden" name="cart" value="'.htmlspecialchars(json_encode($cars)).'">
            <input type="hidden" name="confirm" value="true">
            <fieldset>
                <label for="applicationType">Type: </label>
                <div class="input" style="text-align: center;">
                    <span class="form-icon">help_outline</span>
                    <div>
                        <input type="radio" name="applicationType" value="1" id="personal"'.(($applicationType === 1) ? ' checked' : '').'>
                        <label for="personal" style="font-weight: normal;">Personal</label>
                        <input type="radio" name="applicationType" value="2" id="business"'.(($applicationType === 2) ? ' checked' : '').'>
                        <label for="business" style="font-weight: normal;">Business</label>        
                        <p class="warning-text'.(isset($inputError['applicationType']) ? (HTML_SHOW_WARNING.$inputError['applicationType']) : (HTML_HIDE_WARNING.'Error')).'</p>
                    </div>
                </div>
            </fieldset>';
        
    echo printFormFooter();
?>