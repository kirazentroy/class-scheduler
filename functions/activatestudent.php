<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

require '../vendor/autoload.php';

if (isset($_POST['activatestudent'])) {
    // echo $_POST['studid'] . ' ' . $_POST['refer'];
    $student = $_POST['studid'];
    $referral = $_POST['refer'];

    $query = executeNonQuery($connect, "SELECT * FROM members where member_superiority != 'student' and member_referral ='$referral' and member_department = (SELECT member_department from members where member_id = '$student')");
    $email = executeNonQuery($connect, "SELECT member_email FROM members WHERE member_id = '$student'");
    $email = fetchAssoc($connect, $email);
    $email = $email['member_email'];

    if (numRows($connect, $query) === 0) {
        // echo "Referral is not from your Dean/Instructor";
        // $_SESSION['wrongreferral'] = true;
        $admin_referral = executeNonQuery($connect, "SELECT * FROM members where permission = '1' and member_referral = '$referral'");
        if (numRows($connect, $admin_referral) === 0) {
            $_SESSION['wrongreferral'] = true;
        } else {
            $_SESSION['wrongreferral'] = false;
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->SMTPDebug = 1;
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'schedulerascbislig@gmail.com';
            $mail->Password = 'sboffswwrexhrlqr';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('schedulerascbislig@gmail.com');
            $mail->addAddress($email);
            $mail->isHTML(true);

            $mail->Subject = 'Welcome to ASCB Scheduler';

            $mail->Body = 'Your account has been approved. You can now login here http://class-scheduler.asc-bislig.com';

            $mail->send();
            executeNonQuery($connect, "UPDATE `members` SET `member_referral` = '0' where member_id = '$student'");
        }
    } else {
        $_SESSION['wrongreferral'] = false;
        // echo "Welcome maot";
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->SMTPDebug = 1;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'schedulerascbislig@gmail.com';
        $mail->Password = 'sboffswwrexhrlqr';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('schedulerascbislig@gmail.com');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Welcome to ASCB Scheduler';

        $mail->Body = 'Your account has been approved. You can now login here http://class-scheduler.asc-bislig.com';

        $mail->send();
        executeNonQuery($connect, "UPDATE `students` SET `student_status`='active' where student_id = '$student'");
    }
    header('location: ../');
}
