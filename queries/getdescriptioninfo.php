<?php

include('../functions/db_connect.php');

if (isset($_GET['description'])) {

    $id = $_GET['description'];

    $result = executeNonQuery($connect, "SELECT description FROM subjects where subject_id = '$id'");
    if (numRows($connect, $result) === 0) {
        echo json_encode("No subjects found");
    } else {
        $result = fetchAssoc($connect, $result);
        $result = $result['description'];
        echo json_encode($result);
    }
}
