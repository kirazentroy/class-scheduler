<?php
include('../functions/db_connect.php');

if (isset($_POST['insertcode'])) {
    $codetext = $_POST['codetext'];
    $codestatus = $_POST['codestatus'];

    executeNonQuery($connect, "INSERT INTO `referralcodes`(`code_text`, `code_status`) VALUES ('$codetext','$codestatus');");
}
