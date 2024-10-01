
<?php include('../functions/db_connect.php');

$semesters = executeNonQuery($connect, "SELECT * FROM subjects order by semester");
$semesterArr = array();

while ($semesterrow = fetchAssoc($connect, $semesters)) {
    if (count($semesterArr) === 0) {
        array_push($semesterArr, $semesterrow['semester']);
    } else if ($semesterrow['semester'] === $semesterArr[count($semesterArr) - 1]) {
        $semesterArr[count($semesterArr) - 1] = $semesterrow['semester'];
    } else {
        array_push($semesterArr, $semesterrow['semester']);
    }
}
?>