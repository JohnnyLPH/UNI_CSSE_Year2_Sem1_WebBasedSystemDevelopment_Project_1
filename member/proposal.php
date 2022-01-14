<?php
    
    require_once './inc/formValidation.php';

    // error_reporting(0);
    
    function getOrderStatus($type) {
        $status = array('Ineligible.',
        'Changes required.',
        'Incomplete Payment.',
        'Proposal cancelled.',
        'Draft Proposal pending submission. Please complete and submit your proposal.',
        'Proposal approved. Awaiting for your confirmation.',
        'Order Confirmed.');
        
        if($type >= 0 && isset($status[$type])) {
            return $status[$type];
        }
    }
    
    $requestedStage = filter_input(INPUT_GET, 'stage', FILTER_VALIDATE_INT);
    if(!$orderId && ($requestedStage > 1 || !$post)) {
        printHeader();
        printNavBar();
        showError('405 Error: Missing Proposal / Order ID in URL', 'Please try again or contact support.');
        die();
    }

    function getFormActionURL($stage) {
        global $orderId, $type;
        return basename($_SERVER['SCRIPT_NAME']).'?id='.$orderId.'&type='.$type.'&stage='.$stage;
    }
    
    $stage = array('Car Details', 'Personal Info', 'Current Address', 'Job Info', 'Bank Details', 'Submission');

    function printFormHeader1() {
        echo
        '<main>
        <div style="text-align: center;">
            <img src="./img/car-dealer.png" style="display: inline-block; max-height:80px; vertical-align:middle;"><h1 style="display:inline-block;">Car Proposal</h1>
        </div>';
     
        printProgressLine(array(array('Car Proposal', CURRENT_STAGE), array('Wait for Review', INCOMPLETE_STAGE), array('Confirmation', INCOMPLETE_STAGE), array('Delivery', INCOMPLETE_STAGE)));

        printProgressLine(array(array('Car Details', COMPLETED_STAGE), array('Personal Info', CURRENT_STAGE), array('Current Address', INCOMPLETE_STAGE), array('Job Info', INCOMPLETE_STAGE), array('Bank Details', INCOMPLETE_STAGE), array('Submission', INCOMPLETE_STAGE)));
    }
    
    function showProposalNotFoundError() {
        global $orderId;
        printHeader();
        printNavBar();
        showError('404 Error: Proposal Not Found', 'Your account does not have proposal ID '.$orderId.'.');
    }

    function showSessionExpiredError() {
        printHeader();
        printNavBar();
        showError('403 Error: Session Expired', 'Please login again.');
    }

    function printInfoBanner($imgFileName, $title, $description) {
        echo
           '<div class="banner">
                <img src="./img/'.$imgFileName.'">
                <div>
                    <h2>'.$title.'</h2>
                    <p>'.$description.'</p>
                </div>
            </div>';
    } 

    function printFormHeader2() {
        global $requestedStage;
        printHTMLFormHeader(getFormActionURL($requestedStage));       
    } 

    $type = filter_input(INPUT_GET, 'type', FILTER_VALIDATE_INT);
    if(!$type || $type < 1 || $type > 2) {
        $type = 1;
    }

    function printFormFooter() {
        global $type, $requestedStage;
        if($requestedStage > 1) {
            echo
           '<fieldset style="display: none;">
                <input type="hidden" name="goPrevious" value="false">
            </fieldset>';
        }
        echo
            '<fieldset>
                '.($requestedStage > 1 ? '<input type="submit" value="◀ PREVIOUS" class="button-flex" onclick="return(window.goPrevious());">' : '').'
                <input type="submit" value="'.((($type === 1 && $requestedStage === 6) || ($type === 2 && $requestedStage === 7)) ? 'SUBMIT ▶' : 'NEXT ▶').'" class="button-flex">
            </fieldset>
            <fieldset>
                <a onclick="return(cancel());" id="reset" class="button-flex"><span class="material-icons-outlined" style="vertical-align: middle;">cancel</span> CANCEL</a>
            </fieldset>
        </form>
        <div>Attribution: Some icons made by <a href="https://www.flaticon.com/authors/monkik" target="_blank" rel="noopener noreferrer" title="monkik">monkik</a>, <a href="https://www.flaticon.com/authors/srip" target="_blank" rel="noopener noreferrer" title="srip">srip</a>, <a href="https://www.freepik.com" target="_blank" rel="noopener noreferrer" title="Freepik">Freepik</a> and <a href="https://www.flaticon.com/authors/smashicons" target="_blank" rel="noopener noreferrer" title="Smashicons">Smashicons</a> from <a href="https://www.flaticon.com/" target="_blank" rel="noopener noreferrer" title="Flaticon">www.flaticon.com</a></div>
    </main>';
    }

    if(!$memberId && !$post) {
        redirect('../loginPage.php?required=true&redirect='.urlencode($_SERVER['REQUEST_URI']));
    } else if($requestedStage && $requestedStage >= 1) {

        function validateAddress(&$input, $key, &$JSON) {
            global $post, $inputError;

            if($input === '') {
                if($post) {
                    $inputError[$key] = 'Enter address';
                }
            } else if(search('/[^&\w\d\'\- ]/', $input) >= 0) {
                $inputError[$key] = 'Invalid character. Address can only contain A-Z, a-z, 0-9, &, -, \' and space \' \' character.';
            } else if(strlen($input) < 3) {
                $inputError[$key] = 'Address too short. Address must have at least 3 characters';
            } else if(strlen($input) > 50) {
                $inputError[$key] = 'Address too long. Address can only have a maximum of up to 50 number of characters.';
            }
            $input = ucwords(trimExtraSpaces($input));
            $JSON[$key] = $input;
        }

        function validatePostcode(&$input, $key, &$JSON) {
            global $post, $inputError;

            if($input === '') {
                if($post) {
                    $inputError[$key] = 'Enter postcode';
                }
            } else if(search('/[^\w]/', $input) >= 0) {
                $inputError[$key] = 'Invalid character. Postcode can only contain letters from A-Z and a-z without any space \' \' character.';
            } else if(strlen($input) < 5 || strlen($input) > 7) {
                $inputError[$key] = 'Invalid length. UK postcode must have 5 - 7 number of characters.';
            }
            $input = strtoupper($input);
            $JSON[$key] = $input;
        }

        function validateEmail(&$input, $key, &$JSON) {
            global $post, $inputError;

            if($input === '') {
                if($post) {
                    $inputError[$key] = 'Enter email';
                }
            } else if(search('/\s/', $input) >= 0) {
                $inputError[$key] = 'Email cannot contain any whitespace character (spaces, tabs, line breaks)';
            } else if(search('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i', $input) !== 0) {
                $inputError[$key] = 'Invalid email format. Email should have a format similar to <em>username@domain.com</em>';
            } else if(strlen($input) > 40) {
                $inputError[$key] = 'Email too long. Email can only have a maximum of up to 40 number of characters.';
            }
            $JSON[$key] = $input;
        }

        function validatePhone(&$input, $key, &$JSON) {
            global $post, $inputError;

            if($input === '') {
                if($post) {
                    $inputError[$key] = 'Enter phone number';
                }
            } else if(search('/\s/', $input) >= 0) {
                $inputError[$key] = 'Phone number cannot contain any whitespace character (spaces, tabs, line breaks)';
            } else if(search('/[^0-9]/', $input) >= 0) {
                $inputError[$key] = "Phone number can only contain numbers without any special character such as '-'";
            } else if(strlen($input) < 7 || strlen($input) > 11) {
                $inputError[$key] = 'UK phone number must have 7 - 11 digits (excluding +44)';
            }
            $JSON[$key] = $input;
        }

        function validateDescription(&$input, $key, &$JSON) {
            global $post, $inputError;

            if($input === '') {
                if($post) {
                    $inputError[$key] = 'Enter description';
                }
            } else if(search('/[^&\w\d\'\- ]/', $input) >= 0) {
                $inputError[$key] = 'Invalid character. Description can only contain A-Z, a-z, 0-9, &, -, \' and space \' \' character.';
            } else if(strlen($input) < 3) {
                $inputError[$key] = 'Description too short. Description must have at least 3 characters';
            } else if(strlen($input) > 100) {
                $inputError[$key] = 'Description too long. Description can only have a maximum of up to 100 number of characters.';
            }
            $input = trimExtraSpaces($input);
            $JSON[$key] = $input;
        }

        function printSelectNumOptions($min, $max, $placeholder, $selectedVal, $descOrder = false) {
            echo '<option value="">'.$placeholder.'</option>';
    
            $values = range($min, $max);
    
            if($descOrder) {
                $values = array_reverse($values);
            }
    
            foreach ($values as &$value) {
                echo '<option value="'.$value.'"'.(($selectedVal == $value) ? ' selected' : '').'>'.$value.'</option>';
            }
            unset($value);
        }

        function setCurrentStageStatus($statusNum) {
            global $db, $type, $requestedStage, $orderId, $memberId;
            $editable = '';
            if($statusNum === 1 && (($type === 1 && $requestedStage === 6) || ($type === 2 && $requestedStage === 7))) {
                $editable = 'editable = false, ';
            }

            mysqli_query($db, 'UPDATE orders SET '.$editable.'stages = JSON_SET(stages, "$.'.$requestedStage.'", '.$statusNum.') WHERE id = '.$orderId.' AND memberId = '.$memberId.'') or showDBError();
        }

        function getForm($name) {
            global $post, $stageStatus, $requestedStage, $type, $memberId, $orderId, $inputError;

            $goPrevious = ($_POST['goPrevious'] ?? '') === 'true';           

            require_once '../member/inc/proposal/'.$name.'.php';
        }
        
        $caseNum = $requestedStage;
        
        if($requestedStage >= 5 && $type === 1) {
            $caseNum++;
        }

        switch($caseNum) {
            case 1:
                getForm('cars');
                break;
            case 2:
                getForm('personal');
                break;
            case 3:
                getForm('resAddress');
                break;
            case 4:
                getForm('jobCompany');
                break;
            case 5:
                getForm('jobCompany');
                break;                
            case 6:
                getForm('bank');
                break;
            case 7:
                getForm('delivery');
                break;
            default:
                printHeader();
                printNavBar();
                showError('405 Error: Invalid Stage in URL', 'Please try again or contact support.');
        }
        unset($caseNum);
    }
?>        