<?php

    function getOrderStatus($type) {
        $status = array(
            'Ineligible.',
            'Changes required.',
            'Incomplete Payment.',
            'Proposal Cancelled.',
            'Draft Proposal pending submission. Please complete and submit your proposal.',
            'Proposal under review.',
            'Proposal approved. Awaiting for your confirmation. Click <b>Pay</b> to confirm.',
            'Order Confirmed.');
        
        if($type >= 0 && isset($status[$type])) {
            return $status[$type];
        } else {
            return '-';
        }
    }

    function getCarsHTML($cars) {
        $htmlCars = '<p>';
        
        if($cars) {
            $cars = json_decode($cars, true);
            if($cars) {
                $carsResult = getMultipleCars(array_keys($cars));
                if($carsResult) {
                    foreach($carsResult as $carId => $carRow) {
                        $htmlCars.= '<a href="/?manage-mode=view-car&car-id='.$carId.'">'.$carRow['carModel'].'</a> x'.$cars[$carRow['id']].'<br>';
                    }
                    unset($carId, $carRow);
                }
            }
        }
        $htmlCars .= '</p>';

        return $htmlCars;
    }

    function getFirstIncompleteStage($stages) {
        // returns the first stage that is not completed (or have input validation errors) by the user
        $firstIncompleteStage = 2;
        if($stages) {
            $stages = json_decode($stages, true);    
            if($stages) {
                ksort($stages); // sort by key
                foreach($stages as $stageNum => $stageStatus) {
                    $firstIncompleteStage = $stageNum;
                    if($stageStatus !== 1) {
                        break;
                    }
                }
                unset($stageNum, $stageStatus);
            }
        }
        return $firstIncompleteStage;
    }
?>