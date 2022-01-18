<?php

    function getCarHTML($leasedCar) {
        return '<img src="..'.$leasedCar['imagePath'].$leasedCar['carImage'].'" style="max-height:30px;">
        <p style="display:inline-block; margin:0px;"><a href="/?manage-mode=view-car&car-id='.$leasedCar['carId'].'"><strong>'.$leasedCar['brandName'].' '.$leasedCar['carModel'].'</strong></a><br>
        <strong>Car ID: </strong>'.$leasedCar['carId'].'</p>';
    }

    function getStatus($status) {
        $statusList = array(
            'Cancelled or Returned.',
            'To be Delivered.',
            'Leased.',
            'Under Maintenance.');

        if($status >= 0 && isset($statusList[$status])) {
            return $statusList[$status];
        } else {
            return '-';
        }
    }

    function getStatusHTML($leasedCar) {
        return '<p style="text-align:justify; margin: 0;">'.getStatus($leasedCar['status']).($leasedCar['statusMessage'] ? ('<br>'.$leasedCar['statusMessage']) : '').'</p>';
    }
?>