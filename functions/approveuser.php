<?php

include('../functions/sessionstart.php');
include('../functions/db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

require '../vendor/autoload.php';

if (isset($_POST['approveuser'])) {
    $id = $_POST['approveuser'];
    $query = executeNonQuery($connect, "SELECT * from members where member_id = '$id'");
    $query = fetchAssoc($connect, $query);
    if ($query['member_activity'] === 'active') {
        echo "User is already active";
    } else if ($query['member_activity'] === 'inactive') {
        echo "User is already deactivated";
    } else {
        $email = $query['member_email'];

        $name = $query['member_salut'] . ' ' . $query['member_last'] . ', ';
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

        $mail->Body = $name . 'your account has been approved. You can now login here http://class-scheduler.asc-bislig.com';

        $mail->send();

        executeNonQuery($connect, "UPDATE `members` SET `member_activity`='active' where member_id = '$id'");
    }
}
