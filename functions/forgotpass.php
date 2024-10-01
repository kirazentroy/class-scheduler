
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

if (isset($_POST['forgotpass'])) {
    $username = $_POST['forgotUsername'];
    $selected = $_POST['selected'];
    if ($selected === 'username') {
        $query = executeNonQuery($connect, "SELECT * from members where member_username = '$username'");
    } else {
        $query = executeNonQuery($connect, "SELECT * from members where member_email = '$username'");
    }
    $row = numRows($connect, $query);

    if ($row === 1) {
        $getemail = fetchAssoc($connect, $query);
        $id = $getemail['member_id'];
        if ($getemail['email_status'] !== 'verified') {
            $_SESSION['wronguser'] = 'Email not verified!';
            header('location: ../');
        } else {
            $email = $getemail['member_email'];

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->SMTPDebug = 1;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->Username = 'schedulerascbislig@gmail.com';
            $mail->Password = 'sboffswwrexhrlqr';
            $mail->isHTML(true);

            $mail->setFrom('schedulerascbislig@gmail.com');
            $mail->addAddress($email);

            $mail->Subject = 'Reset Password';
            $newemail = explode("@", $email);
            $convertedemail = '';
            for ($i = 0; $i < strlen($newemail[0]); $i++) {
                if ($i === 0 || $i === 1 || $i === (strlen($newemail[0]) - 1)) {
                    $convertedemail .= $newemail[0][$i];
                } else {
                    $convertedemail .= '*';
                }
            }
            $convertedemail .= '@';
            $convertedemail .= $newemail[1];
            $_SESSION['email'] = $convertedemail;

            $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789');
            shuffle($characters);
            $newpassword = '';
            for ($j = 0; $j < 15; $j++) {
                shuffle($characters);
                $newpassword .= $characters[$j];
            }

            $mail->Body = "
        <p>Your new password is <strong>$newpassword</strong></p>
        ";

            $mail->send();
            $hashpass = sha1($newpassword);
            executeNonQuery($connect, "UPDATE members SET member_password='$hashpass' where member_id='$id';");
            $_SESSION['passwordreset'] = "Password has been reset.";
            header('location: ../forgotpassword.php');
        }
    } else if ($row === 0) {
        $_SESSION['wronguser'] = "No username/email found!";
        header('location: ../');
    }
}
?>

