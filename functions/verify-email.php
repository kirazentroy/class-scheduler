<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

require '../vendor/autoload.php';

if (isset($_POST['verify'])) {
    $id = $_POST['verify'];
    $varEmail = executeNonQuery($connect, "SELECT member_email FROM members where member_id = '$id'");
    $email = fetchAssoc($connect, $varEmail);
    $email = $email['member_email'];
    $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789');
    shuffle($characters);
    $char = '';
    for ($j = 0; $j < 10; $j++) {
        shuffle($characters);
        $char .= $characters[$j];
    }

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

    $mail->Subject = 'ASCB Scheduler';

    $mail->Body = "
    <h1>Email Verification</h1>
    <br/>
    <p style='font-size: 1rem;'>To verify your email, please click the button below</p>
    <br/>
    <div style='display: flex; justify-content: center; align-items: center; text-align: center;'>
        <a href='http://class-scheduler.asc-bislig.com/verify.php?id=$id&verifier=$char' style='padding: 5px; background-color:green;font-size: 1.5rem; color: white; font-style: none;'>Verify</a>
    </div>
    ";

    $mail->send();
    executeNonQuery($connect, "INSERT INTO `email_verifications`(`member_id`, `link`) VALUES ('$id','$char')");
}
