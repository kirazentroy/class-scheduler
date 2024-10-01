<?php
include('../functions/db_connect.php');

if (isset($_POST['grant'])) {
    $id = $_POST['id'];
    executeNonQuery($connect, "UPDATE `members` set `permission`='1' where member_id = '$id'");

    echo "Granted!";
}
