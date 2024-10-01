
<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['getteacher'])) {
    $teacher = $_GET['getteacher'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` WHERE teacher = '$teacher' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        // merged with
        $schedid = $row['schedule_id'];
        $queryMergeWith = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
        if (numRows($connect, $queryMergeWith) > 0) {
            $fetchmergedwith = [];
            while ($rowmerged = fetchAssoc($connect, $queryMergeWith)) {
                array_push($fetchmergedwith, $rowmerged['course']);
            }
            $row['merged_with'] = 'Merged with: ' . courseFused($fetchmergedwith);
        }

        $subject = $row['subject'];
        $subjectquery = executeNonQuery($connect, "SELECT * FROM subjects where subject_id = '$subject'");
        $getsubject = fetchAssoc($connect, $subjectquery);
        $row['description'] = $getsubject['description'];
        $row['subject_code'] = $getsubject['subject_code'];
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }
    echo json_encode($scheds);
}

if (isset($_GET['getcourse'])) {
    $course = $_GET['getcourse'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` WHERE course = '$course' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        // merged with
        $schedid = $row['schedule_id'];
        $queryMergeWith = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
        if (numRows($connect, $queryMergeWith) > 0) {
            $fetchmergedwith = [];
            while ($rowmerged = fetchAssoc($connect, $queryMergeWith)) {
                array_push($fetchmergedwith, $rowmerged['course']);
            }
            $row['merged_with'] = 'Merged with: ' . courseFused($fetchmergedwith);
        }

        // teacher
        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        // subject
        $subject = $row['subject'];
        $subjectquery = executeNonQuery($connect, "SELECT * FROM subjects where subject_id = '$subject'");
        $getsubject = fetchAssoc($connect, $subjectquery);
        $row['description'] = $getsubject['description'];
        $row['subject_code'] = $getsubject['subject_code'];
        // room
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }

    $mergedquery = executeNonQuery($connect, "SELECT * FROM `merged_classes` WHERE course = '$course' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    while ($row = fetchAssoc($connect, $mergedquery)) {
        $mergeid = $row['merged_id'];
        $varMerged = executeNonQuery($connect, "SELECT scheduled_classes.course as course FROM scheduled_classes join merged_classes where scheduled_classes.schedule_id = merged_classes.schedule_id and merged_classes.merged_id = '$mergeid'");
        $queryfused = fetchAssoc($connect, $varMerged);

        $row['merged_from'] = "Merged from: " . $queryfused['course'];
        // teacher
        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        // subject
        $subject = $row['subject'];
        $subjectquery = executeNonQuery($connect, "SELECT * FROM subjects where subject_id = '$subject'");
        $getsubject = fetchAssoc($connect, $subjectquery);
        $row['description'] = $getsubject['description'];
        $row['subject_code'] = $getsubject['subject_code'];
        // room
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }
    echo json_encode($scheds);
}

if (isset($_GET['getroom'])) {
    $room = $_GET['getroom'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` WHERE room = '$room' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        // merged with
        $schedid = $row['schedule_id'];
        $queryMergeWith = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
        if (numRows($connect, $queryMergeWith) > 0) {
            $fetchmergedwith = [];
            while ($rowmerged = fetchAssoc($connect, $queryMergeWith)) {
                array_push($fetchmergedwith, $rowmerged['course']);
            }
            $row['merged_with'] = 'Merged with: ' . courseFused($fetchmergedwith);
        }

        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        $subject = $row['subject'];
        $subjectquery = executeNonQuery($connect, "SELECT * FROM subjects where subject_id = '$subject'");
        $getsubject = fetchAssoc($connect, $subjectquery);
        $row['description'] = $getsubject['description'];
        $row['subject_code'] = $getsubject['subject_code'];
        array_push($scheds, $row);
    }
    echo json_encode($scheds);
}

if (isset($_GET['getteachershs'])) {
    $teacher = $_GET['getteachershs'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes_shs` WHERE teacher = '$teacher' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }
    echo json_encode($scheds);
}

if (isset($_GET['getcourseshs'])) {
    $course = $_GET['getcourseshs'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes_shs` WHERE course = '$course' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        // teacher
        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        // room
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }

    echo json_encode($scheds);
}

if (isset($_GET['getroomshs'])) {
    $room = $_GET['getroomshs'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes_shs` WHERE room = '$room' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        array_push($scheds, $row);
    }
    echo json_encode($scheds);
}

if (isset($_GET['getcoursereg'])) {
    $course = $_GET['getcoursereg'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];
    $schedsquery = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` WHERE course = '$course' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' and schedule_status = 'Regular' order by start_time");

    $scheds = array();
    while ($row = fetchAssoc($connect, $schedsquery)) {
        // merged with
        $schedid = $row['schedule_id'];
        $queryMergeWith = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
        if (numRows($connect, $queryMergeWith) > 0) {
            $fetchmergedwith = [];
            while ($rowmerged = fetchAssoc($connect, $queryMergeWith)) {
                array_push($fetchmergedwith, $rowmerged['course']);
            }
            $row['merged_with'] = 'Merged with: ' . courseFused($fetchmergedwith);
        }

        // teacher
        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        // subject
        $subject = $row['subject'];
        $subjectquery = executeNonQuery($connect, "SELECT * FROM subjects where subject_id = '$subject'");
        $getsubject = fetchAssoc($connect, $subjectquery);
        $row['description'] = $getsubject['description'];
        $row['subject_code'] = $getsubject['subject_code'];
        // room
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }
    $mergedquery = executeNonQuery($connect, "SELECT * FROM `merged_classes` WHERE course = '$course' and schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' order by start_time");
    while ($row = fetchAssoc($connect, $mergedquery)) {
        $mergeid = $row['merged_id'];
        $varMerged = executeNonQuery($connect, "SELECT scheduled_classes.course as course FROM scheduled_classes join merged_classes where scheduled_classes.schedule_id = merged_classes.schedule_id and merged_classes.merged_id = '$mergeid'");
        $queryfused = fetchAssoc($connect, $varMerged);

        $row['merged_from'] = "Merged from: " . $queryfused['course'];
        // teacher
        $teacherid = $row['teacher'];
        $teacherquery = executeNonQuery($connect, "SELECT concat(member_salut,' ', member_last,', ', member_first) as assigned_teacher FROM members WHERE members.member_id = '$teacherid'");
        $getteacher =  fetchAssoc($connect, $teacherquery);
        $row['assigned_teacher'] = $getteacher['assigned_teacher'];
        // subject
        $subject = $row['subject'];
        $subjectquery = executeNonQuery($connect, "SELECT * FROM subjects where subject_id = '$subject'");
        $getsubject = fetchAssoc($connect, $subjectquery);
        $row['description'] = $getsubject['description'];
        $row['subject_code'] = $getsubject['subject_code'];
        // room
        $roomid = $row['room'];
        $roomquery = executeNonQuery($connect, "SELECT room_number from rooms where room_id = '$roomid';");
        $getroom = fetchAssoc($connect, $roomquery);
        $row['roomname'] = $getroom['room_number'];
        array_push($scheds, $row);
    }
    echo json_encode($scheds);
}

function courseFused($data)
{
    $str = "";
    if (sizeof($data) === 1) {
        return $data[0];
    } else {
        for ($i = 0; $i < sizeof($data); $i++) {
            if ($i === 0) {
                $str .= $data[$i];
            } else if ($i === sizeof($data) - 1 && $i !== 0) {
                $str .= ", and " . $data[$i];
            } else if ($i !== 0) {
                $str .= ", " . $data[$i];
            }
        }
    }
    return $str;
}
?>

