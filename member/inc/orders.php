<?php
    function getCarsHTML($cars) {
        $htmlCars = '<p>';
        
        if($cars) {
            $cars = json_decode($cars, true);
            if($cars) {
                $carsResult = getMultipleCars(array_keys($cars));
                if($carsResult) {
                    while ($carRow = mysqli_fetch_assoc($carsResult)) {
                        $htmlCars.= $carRow['carModel'].' x'.$cars[$carRow['id']].'<br>';
                    }
                }
            }
        }
        $htmlCars .= '</p>';

        return $htmlCars;
    }
?>