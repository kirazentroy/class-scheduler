<?php
include('../functions/db_connect.php');

if (isset($_GET['getupdateinfo'])) {
    $id = $_GET['getupdateinfo'];
    $result = executeNonQuery($connect, "SELECT * FROM members where member_id = '$id'");
    $resultArr = array();

    $row = fetchAssoc($connect, $result);
    array_push($resultArr, $row);

    echo json_encode($resultArr);
}
