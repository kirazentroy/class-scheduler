<?php

include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['getavailable'])) {
    $schedid = $_GET['getavailable'];
    $schedtype = $_GET['schedtype'];

    if ($schedtype !== 'course') {
        $queryVar = executeNonQuery($connect, "SELECT * FROM scheduled_classes where schedule_id = '$schedid'");
        $query = fetchAssoc($connect, $queryVar);
        $assignedWeekday = $query['weekday'];
        $assignedSY = $query['schoolyear'];
        $assignedSem = $query['semester'];
        $assignedStart = timeconvertion($query['start_time']);
        $assignedEnd = timeconvertion($query['end_time']);

        // teachers

        $assignedTeacher = $query['teacher'];
        $deanDept = $_SESSION['userdept'];

        if ($_SESSION['permission'] === '1') {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'active' order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        } else {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'active' and (members.member_department = '$deanDept' or members.member_department = '5') order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        }
        $self = $_SESSION['id'];
        $querySelfVar = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_id = '$self'");
        $querySelf = fetchAssoc($connect, $querySelfVar);

        $resultTeachers = [];
        if (loopdatabase($querySelf['id'], $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
            array_push($resultTeachers, $querySelf);
        }

        while ($row = fetchAssoc($connect, $queryAvailableTeachers)) {
            $queriedTeachers = (int)($row['id']);
            if (loopdatabase($queriedTeachers, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultTeachers, $row);
            }
        }

        // rooms
        $resultRooms = [];
        $assignedRoom = $query['room'];
        $queryAvailableRooms = executeNonQuery($connect, "SELECT * from rooms where room_id != '$assignedRoom' order by room_number");

        while ($row = fetchAssoc($connect, $queryAvailableRooms)) {
            $queriedRooms = (int)($row['room_id']);
            if (loopdatabaseroom($queriedRooms, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultRooms, $row);
            }
        }
        $resultArr = [];

        array_push($resultArr, $resultTeachers);
        array_push($resultArr, $resultRooms);

        echo json_encode($resultArr);
    } else {
        $queryVar = executeNonQuery($connect, "SELECT * FROM scheduled_classes where schedule_id = '$schedid'");
        $query = fetchAssoc($connect, $queryVar);
        $assignedWeekday = $query['weekday'];
        $assignedSY = $query['schoolyear'];
        $assignedSem = $query['semester'];
        $assignedStart = timeconvertion($query['start_time']);
        $assignedEnd = timeconvertion($query['end_time']);

        // teachers

        $assignedTeacher = $query['teacher'];
        $deanDept = $_SESSION['userdept'];

        if ($_SESSION['permission'] === '1') {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'active' order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        } else {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'active' and (members.member_department = '$deanDept' or members.member_department = '5') order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        }
        $resultTeachers = [];

        $querySelectedVar = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_id = '$assignedTeacher'");
        $querySelected = fetchAssoc($connect, $querySelectedVar);
        array_push($resultTeachers, $querySelected);

        $self = $_SESSION['id'];
        $querySelfVar = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_id = '$self'");
        $querySelf = fetchAssoc($connect, $querySelfVar);


        if (loopdatabase($querySelf['id'], $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
            array_push($resultTeachers, $querySelf);
        }

        while ($row = fetchAssoc($connect, $queryAvailableTeachers)) {
            $queriedTeachers = (int)($row['id']);
            if (loopdatabase($queriedTeachers, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultTeachers, $row);
            }
        }

        // rooms
        $resultRooms = [];
        $assignedRoom = $query['room'];
        $querySelectedRoomVar = executeNonQuery($connect, "SELECT * from rooms where room_id = '$assignedRoom' order by room_number");
        $querySelectedRoom = fetchAssoc($connect, $querySelectedRoomVar);
        array_push($resultRooms, $querySelectedRoom);

        $queryAvailableRooms = executeNonQuery($connect, "SELECT * from rooms where room_id != '$assignedRoom' order by room_number");

        while ($row = fetchAssoc($connect, $queryAvailableRooms)) {
            $queriedRooms = (int)($row['room_id']);
            if (loopdatabaseroom($queriedRooms, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultRooms, $row);
            }
        }
        $resultArr = [];

        array_push($resultArr, $resultTeachers);
        array_push($resultArr, $resultRooms);

        echo json_encode($resultArr);
    }
}

function loopdatabase($queriedTeachers, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd)
{
    include('../functions/db_connect.php');
    $check = executeNonQuery($connect, "SELECT * FROM scheduled_classes where teacher = '$queriedTeachers' and weekday = '$assignedWeekday' and semester = '$assignedSem' and schoolyear = '$assignedSY'");

    if (numRows($connect, $check) === 0) {
        return false;
    } else {
        while ($row = fetchAssoc($connect, $check)) {
            $start = timeconvertion($row['start_time']);
            $end = timeconvertion($row['end_time']);
            if (checktimes($assignedStart, $assignedEnd, $start, $end) === true) {
                return true;
            }
        }
        return false;
    }
}

function loopdatabaseroom($queriedRooms, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd)
{
    include('../functions/db_connect.php');
    $check = executeNonQuery($connect, "SELECT * FROM scheduled_classes where room = '$queriedRooms' and weekday = '$assignedWeekday' and semester = '$assignedSem' and schoolyear = '$assignedSY'");

    if (numRows($connect, $check) === 0) {
        return false;
    } else {
        while ($row = fetchAssoc($connect, $check)) {
            $start = timeconvertion($row['start_time']);
            $end = timeconvertion($row['end_time']);
            if (checktimes($assignedStart, $assignedEnd, $start, $end) === true) {
                return true;
            }
        }
        return false;
    }
}

if (isset($_GET['getavailableshs'])) {
    $schedid = $_GET['getavailableshs'];
    $schedtype = $_GET['schedtype'];

    if ($schedtype !== 'course') {
        $queryVar = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs where schedule_id = '$schedid'");
        $query = fetchAssoc($connect, $queryVar);
        $assignedWeekday = $query['weekday'];
        $assignedSY = $query['schoolyear'];
        $assignedSem = $query['semester'];
        $assignedStart = timeconvertion($query['start_time']);
        $assignedEnd = timeconvertion($query['end_time']);

        // teachers

        $assignedTeacher = $query['teacher'];
        $deanDept = $_SESSION['userdept'];

        if ($_SESSION['permission'] === '1') {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'customized' order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        } else {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'customized' and (members.member_department = '$deanDept' or members.member_department = '5') order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        }
        $self = $_SESSION['id'];
        $querySelfVar = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_id = '$self'");
        $querySelf = fetchAssoc($connect, $querySelfVar);

        $resultTeachers = [];
        if (loopdatabase($querySelf['id'], $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
            array_push($resultTeachers, $querySelf);
        }

        while ($row = fetchAssoc($connect, $queryAvailableTeachers)) {
            $queriedTeachers = (int)($row['id']);
            if (loopdatabase($queriedTeachers, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultTeachers, $row);
            }
        }

        // rooms
        $resultRooms = [];
        $assignedRoom = $query['room'];
        $queryAvailableRooms = executeNonQuery($connect, "SELECT * from rooms where room_id != '$assignedRoom' order by room_number");

        while ($row = fetchAssoc($connect, $queryAvailableRooms)) {
            $queriedRooms = (int)($row['room_id']);
            if (loopdatabaseroom($queriedRooms, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultRooms, $row);
            }
        }
        $resultArr = [];

        array_push($resultArr, $resultTeachers);
        array_push($resultArr, $resultRooms);

        echo json_encode($resultArr);
    } else {
        $queryVar = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs where schedule_id = '$schedid'");
        $query = fetchAssoc($connect, $queryVar);
        $assignedWeekday = $query['weekday'];
        $assignedSY = $query['schoolyear'];
        $assignedSem = $query['semester'];
        $assignedStart = timeconvertion($query['start_time']);
        $assignedEnd = timeconvertion($query['end_time']);

        // teachers

        $assignedTeacher = $query['teacher'];
        $deanDept = $_SESSION['userdept'];

        if ($_SESSION['permission'] === '1') {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'customized' order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        } else {
            $queryAvailableTeachers = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_superiority = 'faculty' and members.member_id != '$assignedTeacher' and members.member_activity = 'customized' and (members.member_department = '$deanDept' or members.member_department = '5') order by departments.dept_code, members.member_salut, members.member_last, members.member_first");
        }
        $resultTeachers = [];

        $querySelectedVar = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_id = '$assignedTeacher'");
        $querySelected = fetchAssoc($connect, $querySelectedVar);
        array_push($resultTeachers, $querySelected);

        $self = $_SESSION['id'];
        $querySelfVar = executeNonQuery($connect, "SELECT members.member_id as id, concat(members.member_salut, ' ', members.member_last, ', ', members.member_first, ' (', departments.dept_code, ')') as fullname FROM members join departments where members.member_department = departments.dept_id and members.member_id = '$self'");
        $querySelf = fetchAssoc($connect, $querySelfVar);


        if (loopdatabaseshs($querySelf['id'], $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
            array_push($resultTeachers, $querySelf);
        }

        while ($row = fetchAssoc($connect, $queryAvailableTeachers)) {
            $queriedTeachers = (int)($row['id']);
            if (loopdatabaseshs($queriedTeachers, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultTeachers, $row);
            }
        }

        // rooms
        $resultRooms = [];
        $assignedRoom = $query['room'];
        $querySelectedRoomVar = executeNonQuery($connect, "SELECT * from rooms where room_id = '$assignedRoom' order by room_number");
        $querySelectedRoom = fetchAssoc($connect, $querySelectedRoomVar);
        array_push($resultRooms, $querySelectedRoom);

        $queryAvailableRooms = executeNonQuery($connect, "SELECT * from rooms where room_id != '$assignedRoom' order by room_number");

        while ($row = fetchAssoc($connect, $queryAvailableRooms)) {
            $queriedRooms = (int)($row['room_id']);
            if (loopdatabaseroomshs($queriedRooms, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd) !== true) {
                array_push($resultRooms, $row);
            }
        }
        $resultArr = [];

        array_push($resultArr, $resultTeachers);
        array_push($resultArr, $resultRooms);

        echo json_encode($resultArr);
    }
}

function loopdatabaseshs($queriedTeachers, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd)
{
    include('../functions/db_connect.php');
    $check = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs where teacher = '$queriedTeachers' and weekday = '$assignedWeekday' and semester = '$assignedSem' and schoolyear = '$assignedSY'");

    if (numRows($connect, $check) === 0) {
        return false;
    } else {
        while ($row = fetchAssoc($connect, $check)) {
            $start = timeconvertion($row['start_time']);
            $end = timeconvertion($row['end_time']);
            if (checktimes($assignedStart, $assignedEnd, $start, $end) === true) {
                return true;
            }
        }
        return false;
    }
}

function loopdatabaseroomshs($queriedRooms, $assignedWeekday, $assignedSY, $assignedSem, $assignedStart, $assignedEnd)
{
    include('../functions/db_connect.php');
    $check = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs where room = '$queriedRooms' and weekday = '$assignedWeekday' and semester = '$assignedSem' and schoolyear = '$assignedSY'");

    if (numRows($connect, $check) === 0) {
        return false;
    } else {
        while ($row = fetchAssoc($connect, $check)) {
            $start = timeconvertion($row['start_time']);
            $end = timeconvertion($row['end_time']);
            if (checktimes($assignedStart, $assignedEnd, $start, $end) === true) {
                return true;
            }
        }
        return false;
    }
}



function checktimes($assignedStart, $assignedEnd, $start, $end)
{
    // as s e ae || s as ae e || ass aee || as s ae e || s as e ae || ass ae e || sas e ae || s as eae || as s aee
    if (($assignedStart < $start && $start < $end && $end < $assignedEnd) || ($start < $assignedStart && $assignedStart < $assignedEnd && $assignedEnd < $end) || ($assignedStart === $start && $assignedEnd === $end) || ($assignedStart < $start && $start < $assignedEnd && $assignedEnd < $end) || ($start < $assignedStart && $assignedStart < $end && $end < $assignedEnd) || ($assignedStart === $start && $start < $assignedEnd && $assignedEnd < $end) || ($start === $assignedStart && $assignedStart < $end && $end < $assignedEnd) || ($start < $assignedStart && $assignedStart < $end && $end === $assignedEnd) || ($assignedStart < $start && $start < $assignedEnd && $assignedEnd === $end)) {
        return true;
    } else {
        return false;
    }
}

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
    }
    return $time;
}
