<?php
include('../functions/db_connect.php');

if (isset($_GET['getrooms'])) {
    $roombuilding = $_GET['getrooms'];

    $result = executeNonQuery($connect, "SELECT * FROM rooms where building_id='$roombuilding' order by room_floor");

    $resultArr = array();

    while ($rooms = fetchAssoc($connect, $result)) {
        array_push($resultArr, $rooms);
    }

    echo json_encode($resultArr);
}
