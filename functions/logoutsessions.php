<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['logoutallexcept'])) {
    [$id, $exempted] = explode('_', $_GET['logoutallexcept']);
    // echo $exempted;

    executeNonQuery($connect, "DELETE FROM devices where member_id='$id' and device_id!='$exempted'");
    executeNonQuery($connect, "UPDATE members SET device_count='1' where member_id='$id'");

    header('location:'.getBaseUrl().'pages/security.php');
}

if (isset($_GET['logoutall'])) {
    $id = $_GET['logoutall'];

    executeNonQuery($connect, "DELETE FROM devices where member_id='$id'");
    executeNonQuery($connect, "UPDATE members SET device_count='0' where member_id='$id'");
    unset($_SESSION['id']);
    $_SESSION['logoutall'] = 'You have been logged out.';
    header('location:'.getBaseUrl());
}
