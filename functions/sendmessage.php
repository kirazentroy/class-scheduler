<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

require '../vendor/autoload.php';


if (isset($_POST['textcontent'])) {
    $content = join("~kyuuudesu~kirazen", explode("'", $_POST['textcontent']));
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $date = $_POST['date'];


    executeNonQuery($connect, "INSERT INTO `messages`(`sender`, `receiver`, `content`, `message_status_receiver`, `message_status_sender`,`click_status_receiver`, `click_status_sender`, `date_sent`) VALUES ('$sender','$receiver','$content','unread','read','unclicked','clicked','$date')");
}

if (isset($_POST['content'])) {
    $content = join("~kyuuudesu~kirazen", explode("'", $_POST['content']));
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $date = $_POST['date'];

    $queryReceiver = executeNonQuery($connect, "SELECT * FROM members where member_id = '$receiver'");
    $queryReceiver = fetchAssoc($connect, $queryReceiver);
    $nameReceiver = $queryReceiver['member_salut'] . ' ' . $queryReceiver['member_last'];
    $emailReceiver = $queryReceiver['member_email'];

    executeNonQuery($connect, "INSERT INTO `messages`(`sender`, `receiver`, `content`, `message_status_receiver`, `message_status_sender`,`click_status_receiver`, `click_status_sender`, `date_sent`) VALUES ('$sender','$receiver','$content','unread','read','unclicked','clicked','$date')");
    // $unreadmsgs = executeNonQuery($connect, "SELECT * FROM messages WHERE receiver = '$receiver' and message_status_receiver = 'unread'");
    $querySender = executeNonQuery($connect, "SELECT * FROM members where member_id = '$sender'");
    $querySender = fetchAssoc($connect, $querySender);
    $nameSender = $querySender['member_salut'] . ' ' . $querySender['member_last'];
    $concat = '';

    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 1;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'schedulerascbislig@gmail.com';
    $mail->Password = 'sboffswwrexhrlqr';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('schedulerascbislig@gmail.com');
    $mail->addAddress($emailReceiver);
    $mail->isHTML(true);

    $mail->Subject = 'ASCB Scheduler';

    $mail->Body = $nameSender . ' requesting an approval. Visit your message here http://www.ascb-scheduler.com/pages/ascb-messenger.php';

    $mail->send();



    $_SESSION['sentmsgsuccess'] = "Message sent successfully.";
}
