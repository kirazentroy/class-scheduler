<?php
include('functions/sessionstart.php');
include('functions/db_connect.php');

if (isset($_SESSION['nocontinue'])) {
    $user = $_SESSION['pre-user'];
    $pass = $_SESSION['pre-pass'];
    executeNonQuery($connect, "DELETE from partial where user = '$user' and pass = '$pass'");
}

if (isset($_SESSION['pendingstudent'])) {
    $pendingstudent = $_SESSION['pendingstudent'];
} else {
    $pendingstudent = '';
}

if (isset($_SESSION['id'])) {
    header('location: pages/home.php');
}
if (isset($_SESSION['pendingid'])) {
    unset($_SESSION['pendingid']);
}
if (isset($_SESSION['referral'])) {
    unset($_SESSION['referral']);
}
?>
<!doctype html>
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

    <title>Login - <?php include('includes/title.php') ?></title>
    <style>
        label[for^="forgot_"]:hover {
            cursor: pointer;
        }

        .selection_forgot {
            padding: 5px;
            background-color: gray !important;
            border-radius: 10px !important;
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-6" style="margin-top: -20px;">
                    <img src="images/ascb-logo.png" alt="Image" class="img-fluid">
                </div>
                <div class="col-md-6 contents">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h3>Sign In</h3>
                                <p class="mb-4">Start your session here.</p>
                            </div>
                            <form action="functions/login.php" method="post">
                                <div class="form-group first" style="border: solid black 1px">
                                    <label for="username">Username/Email</label>
                                    <input required type="text" class="form-control" id="username" name="username" pattern=".{6,}" title="6 or more characters">
                                </div>
                                <div class="form-group last mb-4 position-relative" style="border: solid black 1px">
                                    <label for="password">Password</label>
                                    <input required type="password" class="form-control" id="password" name="password" pattern=".{6,}" title="6 or more characters"><i class="fa-solid fa-eye position-absolute" style="top:40%; right:10px;" onmousedown="showpass('login');" onmouseleave="hidepass('login');" onmouseup="hidepass('login');"></i>
                                </div>

                                <div class="d-flex mb-5 align-items-center">
                                    <span class="ml-auto forgot-pass" data-bs-toggle="modal" data-bs-target="#modalForgotPass" style="text-decoration:underline;" id="forgot">Forgot Password</span>
                                </div>

                                <input type="submit" value="Log In" class="btn btn-block btn-primary" style="background-color: black;" name="login">
                                <br>
                                <p>No account? Sign up <span id="signup" name="signup" data-bs-toggle="modal" data-bs-target="#modalSignup">here</span></p>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Modal Signup -->
    <div class="modal fade" id="modalSignup" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Signing up...</h5>
                </div>
                <div class="modal-body">
                    <form action="functions/pre-signup.php" method="post">
                        <div class="row gy-3 w-75 mx-auto">
                            <div class="row mb-2">
                                <div class="position-relative mb-2">
                                    <input type="text" class="form-control" name="modalUsername" id="modalUsername" placeholder="Enter username" required onkeyup="keyupall();" pattern=".{6,}" title="6 or more characters">
                                </div><br>
                                <div class="position-relative mb-2">
                                    <input type="password" class="form-control" name="modalPassword" id="modalPassword" placeholder="Enter password" required onkeyup="passkeyup();" pattern=".{6,}" title="6 or more characters"><i class="fa-solid fa-eye position-absolute" style="top:30%; right:10px;" onmousedown="showpass('modalPassword');" onmouseleave="hidepass('modalPassword');" onmouseup="hidepass('modalPassword');"></i>
                                </div><br>
                                <div class="position-relative mb-2">
                                    <input type="password" class="form-control" name="modalPasswordC" id="modalPasswordC" placeholder="Confirm password" required onkeyup="passkeyup();" pattern=".{6,}" title="6 or more characters"><i class="fa-solid fa-eye position-absolute" style="top:30%; right:10px;" onmousedown="showpass('modalPasswordC');" onmouseleave="hidepass('modalPasswordC');" onmouseup="hidepass('modalPasswordC');"></i>
                                </div>
                            </div>
                            <div id="confirmed"></div>
                            <div class="row gx-3">
                                <div class="col-6">
                                    <button type="submit" name="pre-signup" class="btn btn-primary" id="signupbutton" disabled>Signup</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Request Forgot Pass -->
    <div class="modal fade" id="modalForgotPass" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="exampleModalLabel">Forgot Password...</h5>
                </div>
                <div class="modal-body">
                    <form action="functions/forgotpass.php" method="post">
                        <div class="row gy-3 w-75 mx-auto">
                            <div class="row">
                                <!-- <label for="forgotUsername">Enter username</label> -->
                                <div class="p-1">
                                    <input type="radio" name="selected" id="forgot_user" class="d-none" value="username" checked required>
                                    <label for="forgot_user" class="selection_forgot">Username</label>&nbsp;&nbsp;|&nbsp;&nbsp;
                                    <input type="radio" name="selected" id="forgot_email" class="d-none" value="email" required>
                                    <label for="forgot_email">Email</label>
                                </div>
                                <input type="text" class="form-control mb-3" name="forgotUsername" id="forgotUsername" required>
                            </div>
                            <div class="row gx-3">
                                <div class="col-6">

                                    <button type="submit" name="forgotpass" class="btn btn-primary" id="forgotpass">Submit</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- modal referral student -->
    <div class="modal fade" id="modalSendReferral" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <form action="functions/activatestudent.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Account still pending</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="studid" value="<?= $pendingstudent ?>">
                        <label for="refer" class="form-label">Please enter your dean/instructor's referral code</label>
                        <input type="text" id="refer" class="form-control" name="refer" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="activatestudent">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Optional: Place to the bottom of scripts -->
    <script>
        const myModal = new bootstrap.Modal(document.getElementById('modalId'), options)
    </script>

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
        $(document).ready(function() {
            $("label[for^='forgot_']").click(function() {
                // alert();
                if ($(this).attr('for').split('_')[1] === 'email') {
                    $(`label[for='${$(this).attr('for').split('_')[0]}_email']`).addClass('selection_forgot');
                    $(`label[for='${$(this).attr('for').split('_')[0]}_user']`).removeClass('selection_forgot');
                } else if ($(this).attr('for').split('_')[1] === 'user') {
                    $(`label[for='${$(this).attr('for').split('_')[0]}_user']`).addClass('selection_forgot');
                    $(`label[for='${$(this).attr('for').split('_')[0]}_email']`).removeClass('selection_forgot');
                }

            });
        });
    </script>
    <?php
    if (isset($_SESSION['pendingstudent'])) {
    ?>
        <script>
            $('#modalSendReferral').modal('show');
        </script>
    <?php unset($_SESSION['pendingstudent']);
    }
    if (isset($_SESSION['successname'])) {
        $successname = $_SESSION['successname'];
        echo "<script>Swal.fire({
        position: 'top-center',
        icon: 'success',
        title: '$successname you are registered!',
        showConfirmButton: false,
        timer: 1500
        });</script>";
        unset($_SESSION['successname']);
    }

    if (isset($_SESSION['sorry'])) {
        $sorry = $_SESSION['sorry'];
        echo "<script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: '$sorry',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
        unset($_SESSION['sorry']);
    }

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
    }
    if (isset($_SESSION['nocontinue'])) {
        echo "<script>Swal.fire({icon:'error', title:'" . $_SESSION['nocontinue'] . "'});</script>";
        session_unset();
    }
    if (isset($_SESSION['userreset'])) {
        session_unset();
    }
    if (isset($_SESSION['logout'])) {
        $msg = $_SESSION['logout'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'success',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        session_unset();
        session_destroy();
    }
    if (isset($_SESSION['sessionterminated'])) {
        $msg = $_SESSION['sessionterminated'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'info',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        session_unset();
        session_destroy();
    }
    if (isset($_SESSION['codewrong'])) {
        $msg = $_SESSION['codewrong'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'error',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        unset($_SESSION['codewrong']);
    }
    if (isset($_SESSION['wronguser'])) {
        $msg = $_SESSION['wronguser'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'error',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        unset($_SESSION['wronguser']);
    }
    if (isset($_SESSION['maxlogin'])) {
        $msg = $_SESSION['maxlogin'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'error',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        session_unset();
    }

    if (isset($_SESSION['logoutall'])) {
        $msg = $_SESSION['logoutall'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'info',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        session_unset();
    }
    if (isset($_SESSION['userinactive'])) {
        $msg = $_SESSION['userinactive'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'info',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        session_unset();
    }
    if (isset($_SESSION['sentmsgsuccess'])) {
        $msg = $_SESSION['sentmsgsuccess'];
        echo "<script>Swal.fire({
                position: 'center',
                icon: 'success',
                title: '$msg',
                showConfirmButton: false,
                timer: 1500
            });</script>";
        session_unset();
    }

    if (isset($_SESSION['wrongreferral'])) {
        if ($_SESSION['wrongreferral'] === true) {
            $msg = 'Referral is not from your Dean/Instructor';
            echo "<script>Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: '$msg',
                    showConfirmButton: false,
                    timer: 1500
                });</script>";
        } else if ($_SESSION['wrongreferral'] === false) {
            $msg = 'Account activated';
            echo "<script>Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '$msg',
                    showConfirmButton: false,
                    timer: 1500
                });</script>";
        }
        unset($_SESSION['wrongreferral']);
    }
    ?>
</body>

</html>