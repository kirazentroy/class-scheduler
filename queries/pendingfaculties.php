<?php

include('../functions/sessionstart.php');
include('../functions/db_connect.php');


if (isset($_GET['pendingfaculties'])) {
    $admindept = $_SESSION['userdept'];
    if ($_SESSION['permission'] === '0') {
        $pendingusers = executeNonQuery($connect, "SELECT * FROM members join departments where members.member_department = departments.dept_id and members.member_activity = 'pending' and members.member_department = '$admindept' and members.member_superiority = 'faculty' order by members.member_id desc");
    } else {
        $pendingusers = executeNonQuery($connect, "SELECT * FROM members join departments where members.member_department = departments.dept_id and members.member_activity = 'pending' and members.member_superiority = 'faculty'");
    }
    $resultArr = [];

    while ($row = fetchAssoc($connect, $pendingusers)) {
        array_push($resultArr, $row);
    }

    echo json_encode($resultArr);
}
