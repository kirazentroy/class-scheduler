<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

function timeconvertion($time)
{
    if (gettype($time) === 'string') {
        $time = (int)($time);
    }
    if ($time >= 10100 && $time <= 11159) {
        $time -= 10000;
    } else if ($time >= 11200 && $time <= 11259) {
        $time -= 11200;
    } else if ($time >= 20100 && $time <= 21159) {
        $time -= 20000;
        $time += 1200;
    } else if ($time >= 21200 && $time <= 21259) {
        $time -= 20000;
        // $time += 100;
    }
    return $time;
}

function loopdatabase($data, $start, $end)
{
    for ($i = 0; $i < sizeof($data); $i++) {
        if (sizeof($data[$i]) === 0) {
            continue;
        } else {
            if (checkconflicts($i, $data, $start, $end) === 'noconflict') {
                continue;
            } else {
                return true;
            }
        }
    }
    return false;
}

function checkconflicts($counts, $data, $assignedStart, $assignedEnd)
{
    for ($i = 0; $i < sizeof($data[$counts]); $i++) {
        $starttimedb = $data[$counts][$i]['start_time'];
        $endtimedb = $data[$counts][$i]['end_time'];
        $start = timeconvertion($starttimedb);
        $end = timeconvertion($endtimedb);
        if (($assignedStart < $start && $start < $end && $end < $assignedEnd) || ($start < $assignedStart && $assignedStart < $assignedEnd && $assignedEnd < $end) || ($assignedStart === $start && $assignedEnd === $end) || ($assignedStart < $start && $start < $assignedEnd && $assignedEnd < $end) || ($start < $assignedStart && $assignedStart < $end && $end < $assignedEnd) || ($assignedStart === $start && $start < $assignedEnd && $assignedEnd < $end) || ($start === $assignedStart && $assignedStart < $end && $end < $assignedEnd) || ($start < $assignedStart && $assignedStart < $end && $end === $assignedEnd) || ($assignedStart < $start && $start < $assignedEnd && $assignedEnd === $end)) {
            return $counts;
        }
    }
    return 'noconflict';
}

if (isset($_POST['save'])) {
    $assigner = $_SESSION['id'];
    $values = $_POST['allvaluescontainer'];
    $splitted = explode(',', $values);
    $conflict = 0;

    for ($i = 0; $i < count($splitted); $i++) {
        $insert = explode('_/', $splitted[$i]);
        // echo json_encode($insert);
        $teacher = $insert[0];
        $course = $insert[1];
        $semester = $insert[2];
        $subject = $insert[3];
        $weekday = $insert[4];
        $start = $insert[5];
        $end = $insert[6];
        $sy = $insert[7];
        $room = $insert[8];
        // echo $insert[0] . '<br>';
        // echo $insert[1] . '<br>';
        // echo $insert[2] . '<br>';
        // echo $insert[3] . '<br>';
        // echo $insert[4] . '<br>';
        // echo $insert[5] . '<br>';
        // echo $insert[6] . '<br>';
        // echo $insert[7] . '<br>';
        // echo $insert[8] . '<br>';

        $roomsched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved' order by schedule_id");

        $teachersched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved' order by schedule_id");

        $coursesched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' order by schedule_id");

        $coursemerged = executeNonQuery($connect, "SELECT * from merged_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' order by merged_id");

        $tofetch = [$roomsched, $teachersched, $coursesched, $coursemerged];
        $scheds = array();
        $sequenceArray = array();
        for ($j = 0; $j < count($tofetch); $j++) {
            while ($row = fetchAssoc($connect, $tofetch[$j])) {
                array_push($scheds, $row);
            }
            array_push($sequenceArray, $scheds);
            $scheds = [];
        }

        if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === false) {
            executeNonQuery($connect, "INSERT INTO `scheduled_classes`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Regular', 'approved')");
        } else if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === true) {
            $conflict++;
            executeNonQuery($connect, "INSERT INTO `scheduled_classes`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`, `conflict_status`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Regular', 'processing', 'conflicted')");
        }
    }
    if ($_SESSION['superiority'] === 'admin') {
        $_SESSION['doneassign'] = "You successfully assign a schedule." . ($conflict === 0 ? '' : ($conflict === 1 ? $conflict . ' conflict detected. Check it on ' : $conflict . ' conflict detected. Check it on '));
    } else if ($_SESSION['superiority'] === 'faculty') {
        $_SESSION['doneassign'] = "You successfully assign a schedule. Please wait for approval of dean.";
    }
    header('location:../pages/home.php');
}

if (isset($_POST['saveshs'])) {
    $assigner = $_SESSION['id'];
    $values = $_POST['allvaluescontainer'];
    $splitted = explode(',', $values);
    $conflict = 0;

    for ($i = 0; $i < count($splitted); $i++) {
        $insert = explode('_/', $splitted[$i]);
        // echo json_encode($insert);
        $teacher = $insert[0];
        $course = $insert[1];
        $semester = $insert[2];
        $subject = $insert[3];
        $weekday = $insert[4];
        $start = $insert[5];
        $end = $insert[6];
        $sy = $insert[7];
        $room = $insert[8];
        // echo $insert[0] . '<br>';
        // echo $insert[1] . '<br>';
        // echo $insert[2] . '<br>';
        // echo $insert[3] . '<br>';
        // echo $insert[4] . '<br>';
        // echo $insert[5] . '<br>';
        // echo $insert[6] . '<br>';
        // echo $insert[7] . '<br>';
        // echo $insert[8] . '<br>';

        $roomsched = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved' order by schedule_id");

        $teachersched = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved' order by schedule_id");

        $coursesched = executeNonQuery($connect, "SELECT * from scheduled_classes_shs where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' order by schedule_id");

        // $coursemerged = executeNonQuery($connect, "SELECT * from merged_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' order by merged_id");

        $tofetch = [$roomsched, $teachersched, $coursesched];
        $scheds = array();
        $sequenceArray = array();
        for ($j = 0; $j < count($tofetch); $j++) {
            while ($row = fetchAssoc($connect, $tofetch[$j])) {
                array_push($scheds, $row);
            }
            array_push($sequenceArray, $scheds);
            $scheds = [];
        }

        if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === false) {
            executeNonQuery($connect, "INSERT INTO `scheduled_classes_shs`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Regular', 'approved')");
        } else if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === true) {
            $conflict++;
            executeNonQuery($connect, "INSERT INTO `scheduled_classes_shs`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`, `conflict_status`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Regular', 'processing', 'conflicted')");
        }
    }
    if ($_SESSION['superiority'] === 'admin') {
        $_SESSION['doneassign'] = "You successfully assign a schedule." . ($conflict === 0 ? '' : ($conflict === 1 ? $conflict . ' conflict detected. Check it on ' : $conflict . ' conflict detected. Check it on '));
    } else if ($_SESSION['superiority'] === 'faculty') {
        $_SESSION['doneassign'] = "You successfully assign a schedule. Please wait for approval of dean.";
    }
    header('location:../pages/homeoj.php');
}

if (isset($_POST['saveirreg'])) {
    $assigner = $_SESSION['id'];
    $values = $_POST['allvaluescontainer'];
    $splitted = explode(',', $values);
    for ($i = 0; $i < count($splitted); $i++) {
        $insert = explode('_/', $splitted[$i]);
        // echo json_encode($insert);
        // echo "<br>";
        $teacher = $insert[0];
        $course = $insert[1];
        $semester = $insert[2];
        $subject = $insert[3];
        $weekday = $insert[4];
        $start = $insert[5];
        $end = $insert[6];
        $sy = $insert[7];
        $room = $insert[8];

        $conflict;
        if ($_SESSION['superiority'] === 'admin') {
            $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes where weekday='$weekday' and semester='$semester'  and schoolyear='$sy' and schedule_process = 'approved'");
            $resultArr = array();

            $conflict = 0;
            $roomsched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and room = '$room' and schedule_process = 'approved' order by schedule_id");

            $teachersched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and teacher = '$teacher' and schedule_process = 'approved' order by schedule_id");

            $coursesched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' order by schedule_id");

            $coursemerged = executeNonQuery($connect, "SELECT * from merged_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' order by merged_id");

            $tofetch = [$roomsched, $teachersched, $coursesched, $coursemerged];
            $scheds = array();
            $sequenceArray = array();
            for ($j = 0; $j < count($tofetch); $j++) {
                while ($row = fetchAssoc($connect, $tofetch[$j])) {
                    array_push($scheds, $row);
                }
                array_push($sequenceArray, $scheds);
                $scheds = [];
            }

            if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === false) {
                executeNonQuery($connect, "INSERT INTO `scheduled_classes`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Custom', 'approved')");
            } else if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === true) {
                $conflict++;
                executeNonQuery($connect, "INSERT INTO `scheduled_classes`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`, `conflict_status`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Custom', 'processing', 'conflicted')");
            }
        } else {
            executeNonQuery($connect, "INSERT INTO `scheduled_classes`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Custom', 'processing')");
        }
    }
    if ($_SESSION['superiority'] === 'admin') {
        $_SESSION['doneassign'] = "You successfully assign a schedule." . ($conflict === 0 ? '' : ($conflict === 1 ? $conflict . ' conflict detected. Check it on ' : $conflict . ' conflict detected. Check it on '));
    } else {
        $_SESSION['doneassign'] = "You successfully assign a schedule. Please wait for approval of dean.";
    }
    header('location:../pages/irregularshome.php');
}


if (isset($_POST['merge'])) {
    $id = $_POST['merge'];
    $course = $_POST['course'];
    $subject = $_POST['subject'];


    $exist = executeNonQuery($connect, "SELECT * FROM scheduled_classes where schedule_id = '$id'");
    $existing = fetchAssoc($connect, $exist);
    $teacher = $existing['teacher'];
    $sy = $existing['schoolyear'];
    $semester = $existing['semester'];
    $weekday = $existing['weekday'];
    $start = $existing['start_time'];
    $end = $existing['end_time'];
    $assigner = $_SESSION['id'];
    $room = $existing['room'];


    $coursesched = executeNonQuery($connect, "SELECT * from scheduled_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved' order by schedule_id");
    $mergedcoursesched = executeNonQuery($connect, "SELECT * from merged_classes where schoolyear = '$sy' and semester = '$semester' and weekday = '$weekday' and course = '$course' and schedule_process = 'approved'");

    $tofetch = [$coursesched, $mergedcoursesched];

    $sequenceArray = [];

    $results = [];

    for ($i = 0; $i < count($tofetch); $i++) {
        while ($row = fetchAssoc($connect, $tofetch[$i])) {
            array_push($results, $row);
        }
        array_push($sequenceArray, $results);
        $results = [];
    }

    if (loopdatabase($sequenceArray, timeconvertion($start), timeconvertion($end)) === false) {
        executeNonQuery($connect, "INSERT INTO `merged_classes`(`teacher`, `course`, `semester`, `subject`, `weekday`, `start_time`, `end_time`, `schoolyear`, `assigner`, `room`, `schedule_status`, `schedule_process`, `schedule_id`) VALUES ('$teacher','$course','$semester','$subject','$weekday','$start','$end','$sy','$assigner', '$room', 'Regular', 'approved', '$id')");

        echo "success";
    } else {
        echo "error";
    }
}
