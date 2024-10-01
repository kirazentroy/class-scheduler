
<?php
include('../functions/db_connect.php');


if (isset($_GET['getroomvacancy'])) {
    $room = $_GET['getroomvacancy'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes join rooms WHERE scheduled_classes.room = '$room' AND scheduled_classes.schoolyear = '$schoolyear' and scheduled_classes.semester = '$semester' and scheduled_classes.room = rooms.room_id and scheduled_classes.schedule_process = 'approved' ORDER BY scheduled_classes.start_time");
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}

if (isset($_GET['getteachervacancy'])) {
    $teacher = $_GET['getteachervacancy'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes WHERE teacher = '$teacher' AND schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' ORDER BY start_time");
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}

if (isset($_GET['getcoursevacancy'])) {
    $course = $_GET['getcoursevacancy'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes WHERE course = '$course' AND schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' ORDER BY start_time");
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    $result1 = executeNonQuery($connect, "SELECT * FROM merged_classes WHERE course = '$course' AND schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' ORDER BY start_time");

    while ($row = fetchAssoc($connect, $result1)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}

if (isset($_GET['getassignedvacancy'])) {
    session_start();
    $room = $_GET['getassignedvacancy'];
    $teacher = $_GET['teacher'];
    $course = $_GET['course'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    if ($_SESSION['superiority'] === 'student') {
        $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes WHERE schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' and (room = '$room' or course = '$course') ORDER BY start_time");
        $result = executeNonQuery($connect, "SELECT * FROM merged_classes WHERE schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' and (room = '$room' or course = '$course') ORDER BY start_time");
    } else {
        $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes WHERE schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' and (room = '$room' or teacher = '$teacher' or course = '$course') ORDER BY start_time");
        $result1 = executeNonQuery($connect, "SELECT * FROM merged_classes WHERE schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' and (room = '$room' or teacher = '$teacher' or course = '$course') ORDER BY start_time");
    }
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}

if (isset($_GET['getroomvacancyshs'])) {
    $room = $_GET['getroomvacancyshs'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs join rooms WHERE scheduled_classes_shs.room = '$room' AND scheduled_classes_shs.schoolyear = '$schoolyear' and scheduled_classes_shs.semester = '$semester' and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.schedule_process = 'approved' ORDER BY scheduled_classes_shs.start_time");
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}

if (isset($_GET['getteachervacancyshs'])) {
    $teacher = $_GET['getteachervacancyshs'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs WHERE teacher = '$teacher' AND schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' ORDER BY start_time");
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}

if (isset($_GET['getcoursevacancyshs'])) {
    $course = $_GET['getcoursevacancyshs'];
    $schoolyear = $_GET['schoolyear'];
    $semester = $_GET['semester'];

    $result = executeNonQuery($connect, "SELECT * FROM scheduled_classes_shs WHERE course = '$course' AND schoolyear = '$schoolyear' and semester = '$semester' and schedule_process = 'approved' ORDER BY start_time");
    $array = array();

    while ($row = fetchAssoc($connect, $result)) {
        array_push($array, $row);
    }

    echo json_encode($array);
}
?>