<?php

    date_default_timezone_set('Europe/London');

    require_once('../dbConnection.php');

    $db = $serverConnect;

    function showDBError() {        
        global $db;

        echo'
        <div class="warning-banner" style="background-color: #fff0f0; color: red; text-align:center;">
            <svg width="40" height="40" style="fill:red;" viewBox="0 0 20 20"><path d="M11.31 2.85l6.56 11.93A1.5 1.5 0 0116.56 17H3.44a1.5 1.5 0 01-1.31-2.22L8.69 2.85a1.5 1.5 0 012.62 0zM10 13a.75.75 0 100 1.5.75.75 0 000-1.5zm0-6.25a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75z" fill-rule="nonzero"></path></svg>
            <h1>Database Error '.mysqli_errno($db).':</h1>
            <h2>'.mysqli_error($db).'</h2>
        </div>';
        
        die();
    }

    $stageStatus = 0;

    function getMemberEmail() {
        global $db, $memberId;
        $result = mysqli_query($db, 'SELECT email FROM members WHERE id = '.mysqli_real_escape_string($db, $memberId).' LIMIT 1') or showDBError();
        if(mysqli_num_rows($result) === 1) {
            $column = mysqli_fetch_assoc($result) or showDBError();
            
            return ($column['email'] ?? '');
        } else {
            return false;
        }
    }

    function getLeasedCars($memberId) {
        global $db;
        $result = mysqli_query($db, 'SELECT leasedCars.id, leasedCars.orderId, leasedCars.carId, cars.carImage, cars.imagePath, brands.brandName, cars.carModel, leasedCars.status, leasedCars.statusMessage, leasedCars.paymentMthsCompleted, cars.monthPrice, cars.leaseTime, leasedCars.leaseDate, leasedCars.returnDate FROM leasedCars LEFT JOIN cars ON cars.id = leasedCars.carId LEFT JOIN brands ON brands.id = cars.brandId WHERE leasedCars.memberId = '.mysqli_real_escape_string($db, $memberId).' ORDER BY leasedCars.id DESC') or showDBError();
        if(mysqli_num_rows($result) >= 1) {
            $cars = array();
            while ($carRow = mysqli_fetch_assoc($result)) {
                $cars[$carRow['id']] = $carRow;
            }
            return $cars;
        } else {
            return false;
        }
    }

    function getOrders($memberId) {
        global $db;
        $result = mysqli_query($db, 'SELECT * FROM orders WHERE memberId = '.mysqli_real_escape_string($db, $memberId).' ORDER BY id DESC') or showDBError();
        if(mysqli_num_rows($result) >= 1) {
            return ($result);
        } else {
            return false;
        }
    }

    function getTransactions($memberId) {
        global $db;
        $result = mysqli_query($db, 'SELECT id, orderId, leasedCars, creditCard, amount, transactionDate FROM transactions WHERE memberId = '.mysqli_real_escape_string($db, $memberId).' ORDER BY id DESC') or showDBError();
        if(mysqli_num_rows($result) >= 1) {
            $transactions = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $transactions[$row['id']] = $row;
            }
            return $transactions;
        } else {
            return false;
        }
    }

    function getMultipleCars($cars) {
        global $db;
        $result = mysqli_query($db, 'SELECT cars.id, brandId, brandName, carModel, monthPrice, leaseTime, initialPay, carImage, imagePath FROM cars INNER JOIN brands ON cars.brandId = brands.id WHERE cars.id IN ('.implode(',', $cars).')') or showDBError();
        if(mysqli_num_rows($result) >= 1) {
            $cars = array();
            while ($carRow = mysqli_fetch_assoc($result)) {
                $cars[$carRow['id']] = $carRow;
            }
            return $cars;
        } else {
            return false;
        }
    }

    function getOrderCol($columnName) {
        // gets data from specific column
        // also updates current proposal stage status

        global $db, $orderId, $memberId, $requestedStage, $stageStatus;
        $result = mysqli_query($db, 'SELECT '.$columnName.', orderStatus, stages FROM orders WHERE id = '.mysqli_real_escape_string($db, $orderId).' AND memberId = '.mysqli_real_escape_string($db, $memberId).' LIMIT 1') or showDBError();
        if(mysqli_num_rows($result) === 1) {
            $column = mysqli_fetch_assoc($result) or showDBError();

            if($column && $column['orderStatus'] == 3) {
                return false;
            }

            $stages = json_decode($column['stages'], true);
            if(!isset($_SESSION['stages'])) {
                $_SESSION['stages'] = array();
            }
            $_SESSION['stages'][$orderId] = $stages;
            $stageStatus = $stages[$requestedStage] ?? 0;

            return $column;
        } else {
            return false;
        }
    }

    function updateOrderCol($columnName, $value) {
        // prepared statement to prevent SQL injection

        global $db, $orderId, $memberId;
        $updateSTMT = mysqli_prepare($db, 'UPDATE orders SET '.$columnName.' = ? WHERE id = ? AND memberId = ?') or showDBError();
        if(is_array($value)) {
            $value = json_encode($value);
        }
        mysqli_stmt_bind_param($updateSTMT, 'sii', $value, $orderId, $memberId) or showDBError();
        mysqli_stmt_execute($updateSTMT) or showDBError();
    }

    function newProposal($type, $carsId) {
        // prepared statement to prevent SQL injection

        global $db, $memberId;
        $updateSTMT = mysqli_prepare($db, 'INSERT INTO orders (memberId, type, carsId) VALUES (?, ?, ?)') or showDBError();
        if(is_array($carsId)) {
            $carsId = json_encode($carsId);
        }
        mysqli_stmt_bind_param($updateSTMT, 'iis', $memberId, $type, $carsId) or showDBError();
        mysqli_stmt_execute($updateSTMT) or showDBError();

        return mysqli_insert_id($db);
    }

    function newTrans($leasedCars, $creditCard, $amount) {
        // prepared statement to prevent SQL injection

        global $db, $orderId, $memberId;
        $updateSTMT = mysqli_prepare($db, 'INSERT INTO transactions (memberId, orderId, leasedCars, transactionDate, creditCard, amount) VALUES (?, ?, ?, ?, ?, ?)') or showDBError();
        if(is_array($leasedCars)) {
            $leasedCars = json_encode($leasedCars);
        }
        if(is_array($creditCard)) {
            $creditCard = json_encode($creditCard);
        }
        mysqli_stmt_bind_param($updateSTMT, 'iisssi', $memberId, $orderId, $leasedCars, date('Y-m-d H:i:s'), $creditCard, $amount) or showDBError();
        mysqli_stmt_execute($updateSTMT) or showDBError();

        return mysqli_insert_id($db);
    }
    
    function confirmOrder($orderId, $cars) {
        global $db, $memberId;

        if($cars) {
            $numOfCars = count($cars);
            
            $stmt = 'INSERT INTO leasedCars (memberId, orderId, carId, paymentMthsCompleted) VALUES ';
            for($i = 0; $i < $numOfCars; $i++) {
                $stmt .= '('.$memberId.', '.$orderId.', '.$cars[$i]['carId'].', '.$cars[$i]['MthsPaid'].')';
                if($i < $numOfCars - 1) {
                    $stmt .= ', ';
                }
            }
            unset($i);
            
            mysqli_query($db, $stmt) or showDBError();

            $firstLeaseId = mysqli_insert_id($db);
            $leasedCars = array();
            for($i = 0; $i < $numOfCars; $i++) {
                $leasedCars[$firstLeaseId + $i] = $cars[$i]['carId'];
            }
            unset($i);
            $leasedCars = json_encode($leasedCars);

            mysqli_query($db, 'UPDATE orders SET orderStatus = 7, leasedCarsId = \''.$leasedCars.'\', confirmDate = "'.date('Y-m-d H:i:s').'" WHERE id = '.$orderId.' AND memberId = '.$memberId) or showDBError();

            $leasedCars = array();
            for($i = 0; $i < $numOfCars; $i++) {
                $leasedCars[$firstLeaseId + $i] = $cars[$i];
            }
            unset($i);
            return $leasedCars;
        } else {
            return false;
        }
    }

    function payExistingOrder($orderId, $leasedCars) {
        global $db, $memberId;
        if($leasedCars && $orderId && $memberId) {
            $stmt = 'UPDATE leasedCars SET paymentMthsCompleted = (CASE';

            foreach($leasedCars as $leaseId => $car) {
                $stmt .= ' WHEN id = '.$leaseId.' then paymentMthsCompleted + '.$car['MthsPaid'];
            }
            unset($leaseId, $car);

            $stmt .= ' END) WHERE orderId = '.$orderId.' AND memberId = '.$memberId;

            mysqli_query($db, $stmt) or showDBError();
        }
    }

    function cancelOrder($orderId) {
        global $db, $memberId;
        mysqli_query($db, 'UPDATE orders SET editable = false, orderStatus = 3, orderStatusMessage = "Cancellation made on '.date('j F Y g:i:s A').'." WHERE id = '.mysqli_real_escape_string($db, $orderId).' AND memberId = '.mysqli_real_escape_string($db, $memberId).' LIMIT 1') or showDBError();
        return mysqli_affected_rows($db);
    }

    function orderExists() {
        $result = getOrderCol('id');

        return $result;
    }