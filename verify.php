<?php
include('functions/db_connect.php');
include('functions/sessionstart.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $verifier = $_GET['verifier'];

    $query = executeNonQuery($connect, "SELECT * from email_verifications where member_id = '$id' and link = '$verifier'");
    if (numRows($connect, $query) > 0) {
        executeNonQuery($connect, "DELETE from email_verifications where member_id = '$id' and link = '$verifier'");
        executeNonQuery($connect, "UPDATE members set email_status = 'verified' where member_id = '$id'");
        $_SESSION['verified_success'] = 'Email Verified Successfully!';
    }
    header('location: verified.php');
}
