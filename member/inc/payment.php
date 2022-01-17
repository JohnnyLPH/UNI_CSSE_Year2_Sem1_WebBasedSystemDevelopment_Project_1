<?php

    define('CAR_ID', 1); // for initial payment (when a lease ID has not been generated)
    define('LEASE_ID', 2); // for recurring payment (when a lease ID has been generated)

    function getHTMLPaymentTableAndLeasedCars($carsOrLeasedCars, $type, &$leasedCars = false) {
        $htmlTable =
       '<div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Lease ID</th>
                        <th>Car</th>
                        <th>Rental Fee (£/mth)</th>
                        <th>Number of Months Charged</th>
                        <th>Subtotal (£)</th>
                    </tr>
                </thead>
                <tbody>';                

        if($type === CAR_ID) {
            $carIds = array_keys($carsOrLeasedCars);
        } else if($type === LEASE_ID) {
            $carIds = array();
            foreach($carsOrLeasedCars as $leaseId => $car) {
                array_push($carIds, $car['carId']);
            }
            unset($leaseId, $car);

            $carIds = array_unique($carIds); // remove duplicates
        }

        $carsCatalogue = getMultipleCars($carIds);
        
        $leasedCars = array();

        if($type === CAR_ID) {
            // initial payment
            foreach($carsOrLeasedCars as $carId => $quantity) {
                for($x = 0; $x < $quantity; $x++) {
                    array_push($leasedCars, array('carId' => $carId, 'MthsPaid' => $carsCatalogue[$carId]['initialPay']));
                }
                unset($x);
            }
            unset($carId, $quantity);
        } else {
            // recurring payment
            $leasedCars = $carsOrLeasedCars;
        }

        $grandTotal = 0;

        foreach($leasedCars as $leaseId => $leasedCar) {
            $carId = $leasedCar['carId'];
            $numOfMthsCharged = $leasedCar['MthsPaid'];

            $tableCol = array();
            if($type === LEASE_ID) {
                $tableCol[0] = $leaseId;
            } else {
                $tableCol[0] = 'TBD';
            }

            $car = $carsCatalogue[$carId];

            $tableCol[1] = '<img src="data:image/png;base64, '.base64_encode(file_get_contents('..'.$car['imagePath'].$car['carImage'])).'" style="max-height:30px;">
                <p style="display:inline-block; margin:0px;"><strong>'.$car['brandName'].' '.$car['carModel'].'</strong><br>
                <strong>Car ID: </strong>'.$car['id'].'</p>';
            $tableCol[2] = $car['monthPrice'];
            $tableCol[3] = $numOfMthsCharged.' Month'.($numOfMthsCharged > 1 ? 's' : '').($type === CAR_ID ? '<br>(Initial Payment)' : '');
            $tableCol[4] = $numOfMthsCharged * $car['monthPrice'];
            
            $grandTotal += $tableCol[4];

            $htmlTable .= '<tr>';
            foreach($tableCol as &$value) {
                $htmlTable .= '<td>'.$value.'</td>';
            }
            unset($value);
            $htmlTable .= '</tr>';
        }        
        unset($leaseId, $leasedCar, $carId, $numOfMthsCharged, $tableCol, $car);

        $leasedCars['total'] = $grandTotal;
        
        $htmlTable .= '<tr><td colspan="5" style="font-weight:bold;">Grand Total: '.$grandTotal.' £</td></tr>
                    </body>
                </table>
            </div>';

        if($type === CAR_ID) {
            $htmlTable .= '<p>*TBD - To be determined after payment</p>';
        }

        return $htmlTable;
    }

?>