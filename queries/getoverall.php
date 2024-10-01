<?php
include('../functions/db_connect.php');

if (isset($_GET['getall'])) {
    [$sy, $sem] = explode("_", $_GET['getall']);

    $query = executeNonQuery($connect, "SELECT scheduled_classes.*, subjects.subject_code, rooms.room_number from scheduled_classes join subjects join rooms where scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.schoolyear = '$sy' and scheduled_classes.semester = '$sem' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.conflict_status != 'conflicted' order by rooms.room_number");

    $resultArr = array();

    while ($row = fetchAssoc($connect, $query)) {
        $schedid = $row['schedule_id'];
        $merged = [];
        $query2 = executeNonQuery($connect, "SELECT course from merged_classes where schedule_id = '$schedid'");
        while ($row2 = fetchAssoc($connect, $query2)) {
            array_push($merged, $row2['course']);
        }
        $row['merged'] = $merged;
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}
