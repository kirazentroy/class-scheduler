
<?php

include('../functions/db_connect.php');

$courses = executeNonQuery($connect, "SELECT * FROM subjects group by course_id order by course_id");
$courseArr = array();

while ($courserow = fetchAssoc($connect, $courses)) {
    array_push($courseArr, $courserow['course_id']);
}
// echo json_encode($courseArr);
?>