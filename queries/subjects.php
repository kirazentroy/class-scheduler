<?php include('../functions/db_connect.php');

if (isset($_GET['subject'])) {
    $kurso = $_GET['subject'];

    if (isset($_GET['semester'])) {
        $sem = $_GET['semester'];

        // $sem = $_GET['semester'];

        $subjects = executeNonQuery($connect, "SELECT * from subjects where course_id = '$kurso' and  semester = '$sem' order by subject_code");
        $subjectArr = array();
        while ($subjectrow = fetchAssoc($connect, $subjects)) {
            array_push($subjectArr, $subjectrow);
        }

        echo json_encode($subjectArr);
    }
}
