<?php
include('../functions/db_connect.php');

if (isset($_GET['getconflicts'])) {
    $schoolyear = $_GET['getconflicts'];
    $semester = $_GET['semester'];
    $weekday = $_GET['weekday'];
    $room = $_GET['room'];
    $teacher = $_GET['teacher'];
    $course = $_GET['course'];
    $start = $_GET['start'];
    $end = $_GET['end'];

    $datareturn = "";

    $roomsched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved'");

    $roomsched1 = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $teachersched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved'");

    $teachersched1 = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $coursesched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved'");

    $coursesched1 = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $coursemerged = executeNonQuery($connect, "SELECT * from merged_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved'");

    $coursemerged1 = executeNonQuery($connect, "SELECT * from merged_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $tofetch = [$roomsched, $teachersched, $coursesched, $coursemerged];
    $scheds = array();
    $sequenceArray = array();
    for ($i = 0; $i < count($tofetch); $i++) {
        while ($row = fetchAssoc($connect, $tofetch[$i])) {
            array_push($scheds, $row);
        }
        array_push($sequenceArray, $scheds);
        $scheds = [];
    }

    if (numRows($connect, $roomsched1) > 0 || numRows($connect, $teachersched1) > 0 || numRows($connect, $coursesched1) > 0 || numRows($connect, $coursemerged1) > 0) {
        $datareturn = "error";
    }

    if ($datareturn === 'error') {
        echo json_encode([$datareturn, numRows($connect, $roomsched1), numRows($connect, $teachersched1), numRows($connect, $coursesched1), numRows($connect, $coursemerged1)]);
    } else {
        echo json_encode($sequenceArray);
    }
}

if (isset($_GET['getconflictsshs'])) {
    $schoolyear = $_GET['getconflictsshs'];
    $semester = $_GET['semester'];
    $weekday = $_GET['weekday'];
    $room = $_GET['room'];
    $teacher = $_GET['teacher'];
    $course = $_GET['course'];
    $start = $_GET['start'];
    $end = $_GET['end'];

    $datareturn = "";

    $roomsched = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved'");

    $roomsched1 = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $teachersched = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved'");

    $teachersched1 = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $coursesched = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved'");

    $coursesched1 = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' and start_time = '$start' and end_time = '$end'");

    $tofetch = [$roomsched, $teachersched, $coursesched];
    $scheds = array();
    $sequenceArray = array();
    for ($i = 0; $i < count($tofetch); $i++) {
        while ($row = fetchAssoc($connect, $tofetch[$i])) {
            array_push($scheds, $row);
        }
        array_push($sequenceArray, $scheds);
        $scheds = [];
    }

    if (numRows($connect, $roomsched1) > 0 || numRows($connect, $teachersched1) > 0 || numRows($connect, $coursesched1) > 0) {
        $datareturn = "error";
    }

    if ($datareturn === 'error') {
        echo json_encode([$datareturn, numRows($connect, $roomsched1), numRows($connect, $teachersched1), numRows($connect, $coursesched1)]);
    } else {
        echo json_encode($sequenceArray);
    }
}

if (isset($_GET['getconflict'])) {
    $schoolyear = $_GET['getconflict'];
    $semester = $_GET['semester'];
    $weekday = $_GET['weekday'];

    $result = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and schedule_process = 'approved' order by schedule_id");

    $resultArr = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}

if (isset($_GET['getconflictsaprs'])) {
    $schoolyear = $_GET['getconflictsaprs'];
    $semester = $_GET['semester'];
    $weekday = $_GET['weekday'];
    $room = $_GET['room'];

    $result = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved' order by schedule_id");

    $resultArr = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}

if (isset($_GET['getconflictsapr'])) {
    $schoolyear = $_GET['getconflictsapr'];
    $semester = $_GET['semester'];
    $weekday = $_GET['weekday'];

    $result = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$schoolyear' and semester = '$semester' and weekday = '$weekday' and schedule_process = 'approved' order by schedule_id");

    $resultArr = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}

if (isset($_GET['timesubjects'])) {
    $schoolyear = $_GET['timesubjects'];
    $semester = $_GET['semester'];
    $course = $_GET['course'];
    $subject = $_GET['subject'];

    $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` WHERE schoolyear = '$schoolyear' and semester = '$semester' and course = '$course' and subject = '$subject' order by schedule_id");
    $resultArr = array();
    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}

if (isset($_GET['timesubjectsconfirm'])) {
    $schoolyear = $_GET['timesubjectsconfirm'];
    $semester = $_GET['semester'];
    $course = $_GET['course'];
    $subject = $_GET['subject'];

    $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` WHERE schoolyear = '$schoolyear' and semester = '$semester' and course = '$course' and subject = '$subject' and schedule_process = 'approved' order by schedule_id");
    $resultArr = array();
    while ($row = fetchAssoc($connect, $result)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}
