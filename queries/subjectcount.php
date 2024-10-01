<?php
include('../functions/db_connect.php');

if (isset($_GET['subjectcount'])) {
    $teacher = $_GET['subjectcount'];
    $semester = $_GET['semester'];
    $schoolyear = $_GET['schoolyear'];

    $query = executeNonQuery($connect, "SELECT * FROM scheduled_classes where teacher = '$teacher' and semester = '$semester' and schoolyear = '$schoolyear' group by subject");

    echo json_encode(numRows($connect, $query));
}

if (isset($_GET['subjectcountshs'])) {
    $teacher = $_GET['subjectcountshs'];
    $semester = $_GET['semester'];
    $schoolyear = $_GET['schoolyear'];

    $query = executeNonQuery($connect, "SELECT * FROM scheduled_classes where teacher = '$teacher' and semester = '$semester' and schoolyear = '$schoolyear' group by subject");

    echo json_encode(numRows($connect, $query));
}
