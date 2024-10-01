<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['test']) && $_GET['test'] == 1) {
    executeNonQuery($connect, "DELETE FROM members");
    echo 'Deleted';
    header('location:../');
}
if (isset($_POST['fullsign'])) {
    setForeignKeyChecks($connect, 0);
    $use = strtolower($_SESSION['pre-user']);
    $pas = $_SESSION['pre-pass'];
    $lastname = $_POST['lastname'] . ($_POST['extname'] === '' ? '' : ' ' . $_POST['extname']);
    $firstname = $_POST['firstname'];

    $lastname = join("", explode("'", $lastname));
    $firstname = join("", explode("'", $firstname));
    $superiority = $_POST['superiority'];
    $email = $_POST['email'];
    $salut = '';
    $gender = $_POST['gender'];
    $checkemail = executeNonQuery($connect, "SELECT * FROM members where member_email = '$email'");
    $partialuser = $_SESSION['pre-user'];
    if (numRows($connect, $checkemail) > 0) {
        $_SESSION['email_exist'] = 'Email already exists!';
        header('location: ../pages/signingup.php');
    } else {
        if ($_POST['salut'] === 'others') {
            $salut = join("", explode("'", $_POST['salutothers']));
        } else if ($_POST['salut'] === 'no') {
            if ($gender === 'male') {
                $salut = 'Mr.';
            } else {
                $salut = 'Ms.';
            }
        } else {
            $salut = $_POST['salut'];
        }

        if ($gender === 'male') {
            executeNonQuery($connect, "INSERT INTO members(member_last, member_first, member_salut, member_email, member_username, member_password, member_superiority, permission, imgname, gender) VALUES ('$lastname','$firstname','$salut','$email','$use','$pas','$superiority', '0', 'default_male.jpg', '$gender')");
        } else {
            executeNonQuery($connect, "INSERT INTO members(member_last, member_first, member_salut, member_email, member_username, member_password, member_superiority, permission, imgname, gender) VALUES ('$lastname','$firstname','$salut','$email','$use','$pas','$superiority', '0', 'default.png', '$gender')");
        }

        if ($superiority === 'student') {
            $studentstatus = $_POST['studentstatus'];
            $getstudent = executeNonQuery($connect, "SELECT * FROM members WHERE member_id=(SELECT max(member_id) FROM members)");
            $student = fetchAssoc($connect, $getstudent);
            $course = $_POST['course'];
            $studentid = $student['member_id'];
            $result = executeNonQuery($connect, "SELECT * FROM subjects where course_id = '$course' group by department;");
            $dept = fetchAssoc($connect, $result);
            $getdept = $dept['department'];

            executeNonQuery($connect, "INSERT INTO `students`(`student_id`, `student_course`, `student_section`, `student_status`) VALUES ('$studentid','$course','$course', '$studentstatus');");
            if (isset($_SESSION['referral'])) {
                $member_activity = 'active';
            } else if (!isset($_SESSION['referral'])) {
                $member_activity = 'pending';
            }
            executeNonQuery($connect, "UPDATE `members` SET `member_department`='$getdept', `member_activity`='$member_activity' WHERE member_id = '$studentid'");


            $name = $salut . ' ' . $lastname . ' from ' . $course;

            $_SESSION['successname'] = $name;
            executeNonQuery($connect, "DELETE from partial where user = '$partialuser' and pass = '$pas'");
            unset($_SESSION['pre-user']);
            unset($_SESSION['pre-pass']);
            unset($_SESSION['nocontinue']);
            header('location:../');
        } else if (($superiority === 'admin') || ($superiority === 'faculty')) {
            $getexecutives = executeNonQuery($connect, "SELECT * FROM members WHERE member_id=(SELECT max(member_id) FROM members)");
            $executive = fetchAssoc($connect, $getexecutives);
            $executiveid = $executive['member_id'];
            $department = $_POST['department'];

            $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789');
            shuffle($characters);
            $member_referral = '';
            for ($j = 0; $j < 8; $j++) {
                shuffle($characters);
                $member_referral .= $characters[$j];
            }
            if ($superiority === 'faculty') {
                executeNonQuery($connect, "UPDATE `members` SET `member_department`='$department', `permission`='0', `member_activity`='pending', `member_referral`='$member_referral' WHERE member_id = '$executiveid'");
                $name = $salut . ' ' . $lastname;

                $_SESSION['successname'] = $name;
                executeNonQuery($connect, "DELETE from partial where user = '$partialuser' and pass = '$pas'");
                unset($_SESSION['pre-user']);
                unset($_SESSION['pre-pass']);
                unset($_SESSION['nocontinue']);
                header('location:../');
            } else if ($superiority === 'admin') {
                $getadmins = executeNonQuery($connect, "SELECT * FROM members where member_superiority = 'admin'");
                $code = $_POST['code'];
                $countadmins = numRows($connect, $getadmins);
                if ($countadmins === 1) {
                    if ($code === 'ascb@1952') {
                        executeNonQuery($connect, "UPDATE `members` SET `member_department`='$department', `permission`='1', `member_activity`='active', `member_referral`='$member_referral' WHERE member_id = '$executiveid'");

                        $name = $salut . ' ' . $lastname;
                        $_SESSION['successname'] = $name;
                    } else {
                        $_SESSION['codewrong'] = 'You have the wrong referral code!';
                        executeNonQuery($connect, "DELETE FROM members where member_id = '$executiveid'");
                    }
                    executeNonQuery($connect, "DELETE from partial where user = '$partialuser' and pass = '$pas'");
                    unset($_SESSION['pre-user']);
                    unset($_SESSION['pre-pass']);
                    unset($_SESSION['nocontinue']);
                    header('location:../');
                } else {
                    $getcode = executeNonQuery($connect, "SELECT * from referralcodes where code_text ='$code' and code_status = 'notused'");
                    $countcode = numRows($connect, $getcode);
                    if ($countcode === 0) {
                        $_SESSION['codewrong'] = 'You have the wrong referral code!';
                        executeNonQuery($connect, "DELETE from partial where user = '$partialuser' and pass = '$pas'");
                        unset($_SESSION['pre-user']);
                        unset($_SESSION['pre-pass']);
                        unset($_SESSION['nocontinue']);
                        executeNonQuery($connect, "DELETE FROM members where member_id = (SELECT max(member_id) from members)");
                        header('location:../');
                    } else {
                        executeNonQuery($connect, "UPDATE `members` SET `member_department`='$department', `permission`='0', `member_activity`='active', `member_referral`='$member_referral' WHERE member_id = '$executiveid'");
                        executeNonQuery($connect, "UPDATE `referralcodes` set `code_status`='used' where code_text='$code' and code_status='notused'");
                        $name = $salut . ' ' . $lastname;

                        $_SESSION['successname'] = $name;
                        executeNonQuery($connect, "DELETE from partial where user = '$partialuser' and pass = '$pas'");
                        unset($_SESSION['pre-user']);
                        unset($_SESSION['pre-pass']);
                        unset($_SESSION['nocontinue']);
                        header('location:../');
                    }
                }
            }
        }
    }
}
