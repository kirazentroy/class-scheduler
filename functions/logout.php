<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['logout'])) {
    $id = $_SESSION['id'];
    $count = executeNonQuery($connect, "SELECT device_count FROM members WHERE member_id = '$id'");
    $count = fetchAssoc($connect, $count);
    $count = $count['device_count'];
    $count--;
    executeNonQuery($connect, "UPDATE members SET `device_count`= '$count' where member_id = '$id'");
    $_SESSION['logout'] = "You have logged out.";
    unset($_SESSION['id']);
    header('location: ../');
}
