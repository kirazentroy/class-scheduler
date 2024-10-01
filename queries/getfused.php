<?php
include('../functions/db_connect.php');

if (isset($_GET['checkgetfused'])) {

    $id = $_GET['checkgetfused'];

    $query = executeNonQuery($connect, "SELECT * FROM merged_classes where schedule_id = '$id'");

    $data = [];

    while ($row = fetchAssoc($connect, $query)) {
        array_push($data, $row['course']);
    }

    echo json_encode($data);
}

if (isset($_GET['checkgetfusedshs'])) {


    $data = [];

    echo json_encode($data);
}

// if (isset($_GET['mergedfrom'])) {
//     $id = $_GET['mergedfrom'];

//     $queryVar = executeNonQuery($connect, "SELECT scheduled_classes.course as course FROM scheduled_classes join merged_classes where scheduled_classes.schedule_id = merged_classes.schedule_id and merged_classes.merged_id = '$id'");
//     $query = fetchAssoc($connect, $queryVar);

//     "Merged from: " . $query['course'];
// }
