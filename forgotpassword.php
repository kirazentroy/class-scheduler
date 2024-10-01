<?php include('functions/sessionstart.php');
if (isset($_SESSION['id'])) {
    header('location:pages/home.php');
}
if (!isset($_SESSION['email'])) {
    header('location:index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | <?php include('includes/title.php'); ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="col-3">
            <h1>Forgot Password</h1><br>
            <label for="hashEmail">New Password has been sent to this Email</label>
            <input type="email" readonly value="<?php if (isset($_SESSION['email'])) {
                                                    echo $_SESSION['email'];
                                                } ?>" class="form-control"> <br>
            <a href="index.php" class="btn btn-sm btn-dark"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go back to login page</a>
        </div>
    </div>

    <script src="assets/scripts/sweetalert.js"></script>
    <?php
    if (isset($_SESSION['passwordreset'])) {
        $passwordreset = $_SESSION['passwordreset'];
        echo "<script>Swal.fire({
                position: 'top-center',
                icon: 'success',
                title: '$passwordreset',
                showConfirmButton: false,
                timer: 1500
                });</script>";
        unset($_SESSION['passwordreset']);
    }
    ?>
</body>

</html>