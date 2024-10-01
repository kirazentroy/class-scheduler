<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_POST['pre-signup'])) {
    $username = strtolower($_POST['modalUsername']);
    $password = sha1($_POST['modalPassword']);
    $query = executeNonQuery($connect, "SELECT * FROM members WHERE member_username LIKE '$username'");

    $row = numRows($connect, $query);

    if ($row >= 1) {
        $_SESSION['taken'] = 'Username already taken!';
        header('location:../');
    } else {
        executeNonQuery($connect, "INSERT INTO members(member_last, member_first, member_salut, member_username, member_password, member_superiority) VALUES ('','','','$username','$password','')");
        header('location:../pages/signingup.php');
    }
}
