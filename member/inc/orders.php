<?php
    function getCarsHTML($cars) {
        $htmlCars = '<p>';
        
        if($cars) {
            $cars = json_decode($cars, true);
            if($cars) {
                $carsResult = getMultipleCars(array_keys($cars));
                if($carsResult) {
                    foreach($carsResult as &$carRow) {
                        $htmlCars.= $carRow['carModel'].' x'.$cars[$carRow['id']].'<br>';
                    }
                    unset($carRow);
                }
            }
        }
        $htmlCars .= '</p>';

        return $htmlCars;
    }
?>