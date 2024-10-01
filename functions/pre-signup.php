<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_POST['pre-signup'])) {
    $username = $_POST['modalUsername'];
    $password = sha1($_POST['modalPassword']);
    $query = executeNonQuery($connect, "SELECT * FROM members WHERE member_username = '$username'");
    $query1 = executeNonQuery($connect, "SELECT * FROM partial WHERE user = '$username'");

    $verifier = 0;
    $accepted = str_split('ABCDEFGHIJKLMNÑOPQRSTUVWXYZ0123456789abcdefghijklmnñopqrstuvwxyz');
    for ($i = 0; $i < strlen($username); $i++) {
        if (in_array($username[$i], $accepted)) {
            $verifier++;
        }
    }
    $row = numRows($connect, $query);
    if ($verifier !== strlen($username)) {
        $_SESSION['taken'] = 'Username must not contain special characters!';
        if (isset($_SESSION['referral'])) {
            header('location:../signup.php?referral=' . $_SESSION['referral']);
        } else {
            header('location:../');
        }
    } else {
        if ($row >= 1 || numRows($connect, $query1) >= 1) {
            $_SESSION['taken'] = 'Username already taken!';
            if (isset($_SESSION['referral'])) {
                header('location:../signup.php?referral=' . $_SESSION['referral']);
            } else {
                header('location:../');
            }
        } else {
            executeNonQuery($connect, "INSERT INTO `partial`(user, pass) VALUES ('$username','$password')");
            $_SESSION['pre-user'] = $username;
            $_SESSION['pre-pass'] = $password;
            header('location:../pages/signingup.php');
        }
    }
}
