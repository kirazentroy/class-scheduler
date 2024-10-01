<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_POST['updateinfo'])) {
    $id = $_POST['infoid'];
    $last = $_POST['lastname'];
    $first = $_POST['firstname'];
    $degree = $_POST['degree'];

    $nothingChanges = executeNonQuery($connect, "SELECT * FROM members where member_salut = '$degree' and member_first = '$first' and member_last = '$last'");

    $nothing = numRows($connect, $nothingChanges);
    $email = $_POST['email'];
    // echo $email;
    $varEmail = executeNonQuery($connect, "SELECT member_email FROM members WHERE member_id = '$id'");
    $getEmail = fetchAssoc($connect, $varEmail);
    $getEmail = $getEmail['member_email'];
    if ($email !== $getEmail) {
        $checkAllEmails = executeNonQuery($connect, "SELECT member_email FROM members where member_email = '$email'");
        if (numRows($connect, $checkAllEmails) > 0) {
            $checkerAll = false;
            $_SESSION['email_correction'] = 'Email is already in use';
        } else {
            $checkerAll = true;
            executeNonQuery($connect, "UPDATE members SET `member_email` = '$email', `email_status` = '' WHERE member_id = '$id'");
        }
    } else {
        $checkerAll = false;
    }
    if (isset($_FILES['profileimg'])) {
        $img = $_FILES['profileimg'];
        $name = $img['name'];
        $tmpname = $img['tmp_name'];
        $error = $img['error'];
        $size = $img['size'];
        if ($error === 0) {
            if ($size > 1250000 * 5) {
            } else {
                $imgex = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allow = array("jpg", "png", "jpeg");
                // echo $imgex; 
                if (in_array($imgex, $allow)) {
                    // echo $allow[2];
                    $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789');
                    shuffle($characters);
                    $newname = '';
                    for ($j = 0; $j < 6; $j++) {
                        shuffle($characters);
                        $newname .= $characters[$j];
                    }
                    $newname .= uniqid("", true) . '.' . $imgex;
                    $uploadpath = "../profileimages/" . $newname;
                    move_uploaded_file($tmpname, $uploadpath);
                    executeNonQuery($connect, "UPDATE `members` SET `imgname`='$newname' where member_id = '$id'");
                    $_SESSION['imgname'] = $newname;
                }
            }
        } else if ($error === 4) {
            // if ($nothing === 1 && $checkerAll === false) {
            //     if ($_SESSION['superiority'] === 'student') {
            //         $course = $_POST['selectcourse'];
            //         $selectsection = ($_POST['selectsection']);
            //         $section = $course . $_POST['selectsection'];
            //         $sectionchanges = executeNonQuery($connect, "SELECT * FROM students WHERE student_course = '$course' and student_section = '$section' and student_id = '$id'");
            //         if (numRows($connect, $sectionchanges) === 1) {
            //             $_SESSION['updated'] = "Nothing Changed";
            //         } else {
            //             // $dept = $_SESSION['userdept'];
            //             executeNonQuery($connect, "UPDATE `members` SET `member_salut`='$degree', `member_last`='$last',`member_first`='$first', `member_department`='$dept' WHERE member_id = '$id'");
            //             executeNonQuery($connect, "UPDATE `students` SET `student_course`='$course', `student_section`='$section' WHERE student_id = '$id'");
            //             $_SESSION['updated'] = "Changed successfully!";
            //         }
            //     } else {
            //         $_SESSION['updated'] = "Nothing Changed";
            //     }
            // } else {
            //     if ($_SESSION['superiority'] === 'faculty' || $_SESSION['superiority'] === 'admin') {
            //         $dept = $_POST['selectdept'];
            //         executeNonQuery($connect, "UPDATE `members` SET `member_salut`='$degree', `member_last`='$last',`member_first`='$first', `member_department`='$dept' WHERE member_id = '$id'");
            //     } else if ($_SESSION['superiority'] === 'student') {
            //         $course = $_POST['selectcourse'];
            //         $section = $course . $_POST['selectsection'];
            //         $dept = $_SESSION['userdept'];
            //         executeNonQuery($connect, "UPDATE `members` SET `member_salut`='$degree', `member_last`='$last',`member_first`='$first', `member_department`='$dept' WHERE member_id = '$id'");
            //         executeNonQuery($connect, "UPDATE `students` SET `student_course`='$course', `student_section`='$section' WHERE student_id = '$id'");
            //     }
            //     $_SESSION['updated'] = "Changed successfully!";
            //     $_SESSION['userdept'] = $dept;

        }
    }
    if ($_SESSION['superiority'] === 'faculty' || $_SESSION['superiority'] === 'admin') {
        $dept = $_POST['selectdept'];
        executeNonQuery($connect, "UPDATE `members` SET `member_salut`='$degree', `member_last`='$last',`member_first`='$first', `member_department`='$dept' WHERE member_id = '$id'");
    } else if ($_SESSION['superiority'] === 'student') {
        $course = $_POST['selectcourse'];
        $section = $course . $_POST['selectsection'];
        $dept = $_SESSION['userdept'];
        executeNonQuery($connect, "UPDATE `members` SET `member_salut`='$degree', `member_last`='$last',`member_first`='$first', `member_department`='$dept' WHERE member_id = '$id'");
        executeNonQuery($connect, "UPDATE `students` SET `student_course`='$course', `student_section`='$section' WHERE student_id = '$id'");
    }
    $_SESSION['updated'] = 'Changed successfully!';
    $_SESSION['userdept'] = $dept;
    header('location:' . getBaseUrl().'pages/profileinfo.php');
}

if (isset($_POST['updatepass'])) {
    $id = $_POST['infoid'];
    $passold = sha1($_POST['passold']);
    $query = executeNonQuery($connect, "SELECT * from `members` WHERE member_password = '$passold' and member_id = '$id'");
    $row = numRows($connect, $query);
    if ($row === 0) {
        $_SESSION['wrongupdated'] = 'Wrong old password!';
    } else {
        $passnew = sha1($_POST['passnew']);
        executeNonQuery($connect, "UPDATE `members` SET `member_password`='$passnew' where member_id = '$id'");
        $_SESSION['updated'] = 'Changed successfully!';
    }
    header('location:'.getBaseUrl().'pages/security.php');
}
