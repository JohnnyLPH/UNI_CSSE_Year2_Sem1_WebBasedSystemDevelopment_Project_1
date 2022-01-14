<?php

    date_default_timezone_set('Europe/London');

    $db = mysqli_connect('localhost', 'id18274200_wbsd', 'G03abc-abc03G', 'id18274200_lingscars');

    function showDBError() {
        global $db;
        echo "<p>Unable to execute the query.</p>"
            . "<p>Error code " . mysqli_errno($db)
            . ": " . mysqli_error($db) . "</p>";
        die();
    }

    $stageStatus = 0;

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

    function newTrans($carId, $creditCard, $amount) {
        // prepared statement to prevent SQL injection

        global $db, $orderId, $memberId;
        $updateSTMT = mysqli_prepare($db, 'INSERT INTO transactions (memberId, orderId, carId, creditCard, amount) VALUES (?, ?, ?, ?, ?)') or showDBError();
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