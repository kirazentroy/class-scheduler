
<?php
include('../functions/db_connect.php');

if (isset($_GET['subject'])) {
    $subject = $_GET['subject'];

    $result = executeNonQuery($connect, "SELECT * from subjects where subject_id = '$subject'");

    $resultArr = array();
    $getunit = fetchAssoc($connect, $result);
    array_push($resultArr, $getunit['units']);

    echo json_encode($resultArr);
}
?>