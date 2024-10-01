<?php
include('../functions/db_connect.php');

if (isset($_POST['schedid'])) {

    $id = $_POST['schedid'];
    $teacher = $_POST['teacher'];
    $updatemerge = $_POST['updatemerge'];
    $room = $_POST['room'];
    $schedtype = $_POST['schedtype'];

    if ($schedtype === 'teacher') {
        executeNonQuery($connect, "UPDATE scheduled_classes SET teacher = '$teacher' where schedule_id = '$id'");
        if ($updatemerge === 'true') {
            executeNonQuery($connect, "UPDATE merged_classes SET teacher = '$teacher' where schedule_id = '$id'");
        }
    } else if ($schedtype === 'room') {
        executeNonQuery($connect, "UPDATE scheduled_classes SET room = '$room' where schedule_id = '$id'");
        if ($updatemerge === 'true') {
            executeNonQuery($connect, "UPDATE merged_classes SET room = '$room' where schedule_id = '$id'");
        }
    } else {
        executeNonQuery($connect, "UPDATE scheduled_classes SET room = '$room', teacher = '$teacher' where schedule_id = '$id'");
        if ($updatemerge === 'true') {
            executeNonQuery($connect, "UPDATE merged_classes SET room = '$room', teacher = '$teacher' where schedule_id = '$id'");
        }
    }
}

if (isset($_POST['schedid2'])) {

    $id = $_POST['schedid2'];
    $teacher = $_POST['teacher'];
    $updatemerge = $_POST['updatemerge'];
    $room = $_POST['room'];
    $schedtype = $_POST['schedtype'];

    if ($schedtype === 'teacher') {
        executeNonQuery($connect, "UPDATE scheduled_classes_shs SET teacher = '$teacher' where schedule_id = '$id'");
        // if ($updatemerge === 'true') {
        //     executeNonQuery($connect, "UPDATE merged_classes SET teacher = '$teacher' where schedule_id = '$id'");
        // }
    } else if ($schedtype === 'room') {
        executeNonQuery($connect, "UPDATE scheduled_classes_shs SET room = '$room' where schedule_id = '$id'");
        // if ($updatemerge === 'true') {
        //     executeNonQuery($connect, "UPDATE merged_classes SET room = '$room' where schedule_id = '$id'");
        // }
    } else {
        executeNonQuery($connect, "UPDATE scheduled_classes_shs SET room = '$room', teacher = '$teacher' where schedule_id = '$id'");
        // if ($updatemerge === 'true') {
        //     executeNonQuery($connect, "UPDATE merged_classes SET room = '$room', teacher = '$teacher' where schedule_id = '$id'");
        // }
    }
}
