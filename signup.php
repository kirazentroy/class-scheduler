<?php
include('functions/sessionstart.php');
include('functions/db_connect.php');
if (isset($_SESSION['id'])) {
    header('location:pages/home.php');
}
if (isset($_GET['referral'])) {
    $referral = $_GET['referral'];

    $query = executeNonQuery($connect, "SELECT * FROM members where member_referral = '$referral' and member_superiority != 'student'");
    if (numRows($connect, $query) > 0) {
        $_SESSION['referral'] = $referral; ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <link rel="icon" type="image/x-icon" href="images/indexlogo.png" />
            <link rel="stylesheet" href="fonts/icomoon/style.css">

            <link rel="stylesheet" href="css/owl.carousel.min.css">

            <!-- Bootstrap CSS -->
            <link rel="stylesheet" href="css/bootstrap.min.css">

            <!-- Style -->
            <link rel="stylesheet" href="css/style.css">

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="assets/css/login.css">

            <title>Sign-Up | <?php include('includes/title.php') ?></title>
        </head>

        <body>
            <div class="container d-flex justify-content-center align-items-center vh-100">
                <!-- <h1>Sign Up</h1> -->
                <form action="functions/pre-signup.php" method="post" style="border-radius: 20px; background-color: white; padding: 50px; box-shadow: 10px 10px green;">
                    <legend>Sign Up (Students Only)</legend><br>
                    <div class="col-12 gy-3 mx-auto">
                        <div class="row position-relative">
                            <input type="text" class="form-control mb-3" name="modalUsername" id="modalUsername" placeholder="Enter username" required onkeyup="keyupall();" pattern=".{6,}" title="6 or more characters">
                        </div>
                        <div class="row position-relative">
                            <input type="password" class="form-control mb-3" name="modalPassword" id="modalPassword" placeholder="Enter password" required onkeyup="passkeyup();" pattern=".{6,}" title="6 or more characters"><i class="fa-solid fa-eye position-absolute" style="top:20%; right:10px;" onmousedown="showpass('modalPassword');" onmouseleave="hidepass('modalPassword');" onmouseup="hidepass('modalPassword');"></i>
                        </div>
                        <div class="row position-relative">
                            <input type="password" class="form-control mb-3" name="modalPasswordC" id="modalPasswordC" placeholder="Confirm password" required onkeyup="passkeyup();" pattern=".{6,}" title="6 or more characters"><i class="fa-solid fa-eye position-absolute" style="top:20%; right:10px;" onmousedown="showpass('modalPasswordC');" onmouseleave="hidepass('modalPasswordC');" onmouseup="hidepass('modalPasswordC');"></i>
                        </div>
                        <div id="confirmed"></div>
                        <div class="row gx-3">
                            <div class="col-3"></div>
                            <div class="col-6">
                                <button type="submit" name="pre-signup" class="btn btn-primary" id="signupbutton" disabled>Sign Up</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script src="js/jquery-3.3.1.min.js"></script>
            <script src="js/popper.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/main.js"></script>
            <script src="assets/scripts/bootstrap.js"></script>
            <script src="assets/scripts/signup.js"></script>
            <script src="assets/scripts/sweetalert.js"></script>
            <script>
                function showpass(type) {
                    if (type === 'login') {
                        $('#password').attr('type', 'text');
                    } else {
                        $(`#${type}`).attr('type', 'text');
                    }
                }

                function hidepass(type) {
                    if (type === 'login') {
                        $('#password').attr('type', 'password');
                    } else {
                        $(`#${type}`).attr('type', 'password');
                    }
                }
            </script>
            <?php
            if (isset($_SESSION['taken'])) {
                $taken = $_SESSION['taken'];
                echo "<script>
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: '$taken',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>";
                unset($_SESSION['taken']);
            } ?>
        </body>

        </html>
    <?php } else { ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error 404</title>
        </head>

        <body>
            <h1>Site not found</h1>
        </body>

        </html>
    <?php }
} else if (!isset($_GET['referral'])) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="images/indexlogo.png" />
        <title>Error 404</title>
    </head>

    <body>
        <h1>Site not found</h1>
    </body>

    </html>
<?php }
?>