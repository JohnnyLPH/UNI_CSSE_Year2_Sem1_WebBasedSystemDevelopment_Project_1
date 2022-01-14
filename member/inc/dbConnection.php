<?php

    function redirect($page) {
        if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
            $uri = 'https://';
        } else {
            $uri = 'http://';
        }
        $uri .= $_SERVER['HTTP_HOST'];
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        if(strlen($dirname) === 1) {
            $dirname = '';
        }

        if($page[0] === '/') {
            $page = substr($page, 1);
        }
        
        header('Location: '.$uri.$dirname.'/'.$page);
        die();
    }

    date_default_timezone_set('Europe/London');

    $db = mysqli_connect('localhost', 'id18274200_wbsd', 'G03abc-abc03G', 'id18274200_lingscars');

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

    function getOrderCol($columnName) {
        // gets data from specific column
        // also updates current proposal stage status

        global $db, $orderId, $memberId, $requestedStage, $stageStatus;
        $result = mysqli_query($db, 'SELECT '.$columnName.', stages FROM orders WHERE id = '.mysqli_real_escape_string($db, $orderId).' AND memberId = '.mysqli_real_escape_string($db, $memberId).' LIMIT 1') or showDBError();
        if(mysqli_num_rows($result) === 1) {
            $column = mysqli_fetch_assoc($result) or showDBError();

            $stageStatus = json_decode($column['stages'], true);
            $stageStatus = $stageStatus[$requestedStage] ?? 0;

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

    function newTrans($carId, $creditCard, $amount) {
        // prepared statement to prevent SQL injection

        global $db, $orderId, $memberId;
        $updateSTMT = mysqli_prepare($db, 'INSERT INTO transactions (memberId, orderId, carId, creditCard, amount) VALUES (?, ?, ?, ?, ?)') or showDBError();
        if(is_array($carId)) {
            $carId = json_encode($carId);
        }
        if(is_array($creditCard)) {
            $creditCard = json_encode($creditCard);
        }
        mysqli_stmt_bind_param($updateSTMT, 'iissi', $memberId, $orderId, $carId, $creditCard, $amount) or showDBError();
        mysqli_stmt_execute($updateSTMT) or showDBError();

        return mysqli_insert_id($db);
    }

    function orderExists() {
        return getOrderCol('id');
    }