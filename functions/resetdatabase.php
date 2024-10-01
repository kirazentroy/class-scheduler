<?php

include('../functions/sessionstart.php');
include('../functions/db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

require '../vendor/autoload.php';

if (isset($_GET['reset'])){

    $password = $_GET['reset'];

    if ($password === 'kirazen'){

    }

    header('Location: ../database.php');
}