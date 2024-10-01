<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['source'])) {

    $sy = $_GET['source'];
    $sem = $_GET['sem'];

    $admindept = $_SESSION['userdept'];

    if ($_SESSION['permission'] === '0') {
        $result = executeNonQuery($connect, "SELECT scheduled_classes.course as course FROM scheduled_classes join subjects where scheduled_classes.subject = subjects.subject_id and subjects.department = '$admindept' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.semester = '$sem' group by scheduled_classes.course order by scheduled_classes.course");
    } else {
        $result = executeNonQuery($connect, "SELECT course FROM scheduled_classes where schoolyear = '$sy' and semester = '$sem' group by course order by course");
    }
    $resultArr = [];
    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row['course']);
    }

    echo json_encode($resultArr);
}


if (isset($_GET['table'])) {
    $sy = $_GET['table'];
    $sem = $_GET['sem'];
    $course = $_GET['course'];

    $result = executeNonQuery($connect, "SELECT *, concat(members.member_salut, ' ', members.member_last, ' ', members.member_first) as teachername, subjects.subject_code as subcode, subjects.description as descript FROM scheduled_classes join members join subjects where scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.schoolyear = '$sy' and scheduled_classes.semester = '$sem' and scheduled_classes.course = '$course' and scheduled_classes.conflict_status != 'conflicted'");

    $resultArr = [];
    $mondayArr = [];
    $tuesdayArr = [];
    $wednesdayArr = [];
    $thursdayArr = [];
    $fridayArr = [];
    $saturdayArr = [];

    while ($row = fetchAssoc($connect, $result)) {
        if ($row['weekday'] === 'Monday') {
            array_push($mondayArr, $row);
        } else if ($row['weekday'] === 'Tuesday') {
            array_push($tuesdayArr, $row);
        } else if ($row['weekday'] === 'Wednesday') {
            array_push($wednesdayArr, $row);
        } else if ($row['weekday'] === 'Thursday') {
            array_push($thursdayArr, $row);
        } else if ($row['weekday'] === 'Friday') {
            array_push($fridayArr, $row);
        } else if ($row['weekday'] === 'Saturday') {
            array_push($saturdayArr, $row);
        }
    }

    array_push($resultArr, $mondayArr);
    array_push($resultArr, $tuesdayArr);
    array_push($resultArr, $wednesdayArr);
    array_push($resultArr, $thursdayArr);
    array_push($resultArr, $fridayArr);
    array_push($resultArr, $saturdayArr);

    echo json_encode($resultArr);
}


if (isset($_GET['subjects'])) {
    $course = $_GET['subjects'];
    $sem = $_GET['sem'];

    $result = executeNonQuery($connect, "SELECT * FROM subjects where course_id = '$course' and semester = '$sem' order by subject_code");
    $resultArr = [];
    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}
