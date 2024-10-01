<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');



if (isset($_POST['login'])) {
    $user = strtolower($_POST['username']);
    $pass = sha1($_POST['password']);

    $logincredentials = str_split($user);
    if (!in_array('@', $logincredentials)) {
        $query = executeNonQuery($connect, "SELECT * from members where member_username='$user' and member_password='$pass'");
    } else {
        $query = executeNonQuery($connect, "SELECT * from members where member_email='$user' and member_password='$pass'");
    }


    //sulod
    $counter = numRows($connect, $query);
    $row = fetchAssoc($connect, $query);


    $activity = $row['device_count'];


    if ($counter == 0) {
        $_SESSION['sorry'] = 'Invalid username or password!';
        header('location:' . getBaseUrl() . '');
    } else if ($counter == 1) {
        if ($row['member_activity'] === 'pending') {
            if ($row['member_superiority'] === 'faculty') {
                $_SESSION['pendingid'] = $row['member_id'];
                $_SESSION['alertpendingid'] = $row['member_id'];
                header('location:' . getBaseUrl() . 'pendingaccount.php');
            } else if ($row['member_superiority'] === 'student') {
                $_SESSION['pendingstudent'] = $row['member_id'];
                header('location:' . getBaseUrl() . '');
            }
        } else if ($row['member_activity'] === 'inactive') {
            $_SESSION['userinactive'] = 'Account is disabled';
            header('location:' . getBaseUrl() . '');
        } else {
            if ($activity >= 99999999999) {
                $_SESSION['maxlogin'] = "Account reached the maximum allowed number of logins.";
                header('location: ' . getBaseUrl() . '');
            } else {
                $activity++;

                executeNonQuery($connect, "UPDATE members SET `device_count`='$activity' where `member_username`='$user' and `member_password`='$pass'");

                $name = $row['member_salut'] . " " . $row['member_last'];
                $id = $row['member_id'];

                executeNonQuery($connect, "INSERT INTO `devices`(`member_id`) VALUES ('$id')");
                $currentdevice = executeNonQuery($connect, "SELECT max(device_id) as device_id FROM devices");
                $currentdevice = fetchAssoc($connect, $currentdevice);
                $currentdevice = $currentdevice['device_id'];
                $_SESSION['currentdevice'] = $currentdevice;

                $superiority = $row['member_superiority'];
                $_SESSION['id'] = $id;
                $_SESSION['superiority'] = $superiority;
                $_SESSION['permission'] = $row['permission'];
                $_SESSION['imgname'] = $row['imgname'];
                if ($superiority === 'admin') {
                    $_SESSION['alert'] = "Welcome";
                    $_SESSION['userdept'] = $row['member_department'];
                    header('location: ../pages/displaysched.php');
                } else if ($superiority === 'student') {
                    $getstudentstatus = executeNonQuery($connect, "SELECT * from students where student_id = '$id'");
                    $getstatus = fetchAssoc($connect, $getstudentstatus);
                    $_SESSION['studentstatus'] = $getstatus['student_status'];
                    $_SESSION['alert'] = "Welcome";
                    $_SESSION['userdept'] = $row['member_department'];
                    header('location:../pages/displayschedstudent.php');
                } else if ($superiority === 'faculty') {
                    $_SESSION['userdept'] = $row['member_department'];
                    $_SESSION['alert'] = "Welcome";
                    header('location:../pages/displayschedfaculty.php');
                }
            }
        }
    }
}
