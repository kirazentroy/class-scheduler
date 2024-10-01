<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
} else if (!isset($_SESSION['id'])) {
    header('location:../index.php');
}

if (isset($_SESSION['userdept'])) {
    $department = $_SESSION['userdept'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security | <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        #toggle1 {
            background-color: #000814;
            cursor: pointer;
        }

        #subToggle {
            list-style: none;
        }
    </style>
</head>

<body>


    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">

        <?php include('../includes/sidebar.php') ?>

        <div id="page-content-wrapper">
            <div class="container-fluid mt-4 px-4">
                <div class="row">
                    <div class="col-6">
                        <div class="row mb-4">
                            <div class="col-4">
                                <h4>Password</h4>
                            </div>
                            <div class="col-4"><button class="btn btn-primary btn-sm" id="editpass">Edit <i class="fas fa-edit"></i></button></div>
                        </div>
                        <div>
                            <p id="updatedpass" class="d-none position-absolute text-success">Password Updated</p>
                        </div>

                        <form action="../functions/updated.php" method="post" class="form-group d-none">
                            <div class="row gy-3">
                                <div class="col-6">
                                    <input type="hidden" name="infoid" value="<?php echo $id ?>">
                                    <label class="d-none" for="passold">Old</label>
                                    <label class="d-none" for="passnew">New</label>
                                    <label class="d-none" for="passcon">Confirm</label>
                                    <input required type="password" class="my-3 form-control" placeholder="Old Password" name="passold" id="passold" pattern=".{6,}" title="6 or more characters"><br>
                                    <input required type="password" class="mb-3 form-control" placeholder="New Password" name="passnew" id="passnew" pattern=".{6,}" title="6 or more characters"><br>
                                    <input required type="password" class="form-control" placeholder="Confirm Password" name="passcon" id="passcon" pattern=".{6,}" title="6 or more characters"><br>
                                    <div class="mb-3">
                                        <p id="confirming" class="d-none position-absolute"></p>
                                    </div><br><br>
                                    <button class="btn btn-success btn-sm" id="savebutton" type="submit" name="updatepass" disabled>Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-sm btn-danger" onclick="sessionlogoutall('<?php echo $_SESSION['id'] ?>');">Logout All Sessions</button> <br><br>
                        <button class="btn btn-sm btn-secondary" onclick="sessionlogout('<?php echo $_SESSION['id'] . '_' . $_SESSION['currentdevice'] ?>');">Logout All Sessions Except This Device</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script>
        const sessionlogoutall = (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout all devices!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location = `../functions/logoutsessions.php?logoutall=${id}`;
                }
            })
        }

        const sessionlogout = (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout other devices!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location = `../functions/logoutsessions.php?logoutallexcept=${id}`;
                }
            })
        }

        $(document).ready(function() {
            $('#editpass').click(function() {
                $(this).addClass('d-none');
                $('form').removeClass('d-none');
                $('#updatedpass').addClass('d-none');
            });
            $('#passcon').keyup(function(e) {
                let typing = e.target.value;
                $('#confirming').addClass('d-none');
                if (typing.length === 0 || $('#passnew').val().length === 0) {
                    $('#confirming').addClass('d-none');
                    $('#confirming').html('');
                } else {
                    if (typing != $('#passnew').val()) {
                        $('#confirming').removeClass('text-success');
                        $('#confirming').addClass('text-danger');
                        $('#confirming').html('Password not matched!');
                        setTimeout(() => {
                            $('#confirming').removeClass('d-none');
                        }, 1000);
                        $('#savebutton').prop('disabled', true);
                    } else {
                        $('#confirming').removeClass('text-danger');
                        $('#confirming').addClass('text-success');
                        $('#confirming').html('Password matched!');
                        setTimeout(() => {
                            $('#confirming').removeClass('d-none');
                        }, 1000);
                        $('#savebutton').prop('disabled', false);
                    }
                }
            });
            $('#passnew').keyup(function(e) {
                let typing = e.target.value;
                $('#confirming').addClass('d-none');
                if (typing.length === 0 || $('#passcon').val().length === 0) {
                    $('#confirming').addClass('d-none');
                    $('#confirming').html('');
                } else {
                    if (typing != $('#passcon').val()) {
                        $('#confirming').removeClass('text-success');
                        $('#confirming').addClass('text-danger');
                        $('#confirming').html('Password not matched!');
                        setTimeout(() => {
                            $('#confirming').removeClass('d-none');
                        }, 1000);
                        $('#savebutton').prop('disabled', true);
                    } else {
                        $('#confirming').removeClass('text-danger');
                        $('#confirming').addClass('text-success');
                        $('#confirming').html('Password matched!');
                        setTimeout(() => {
                            $('#confirming').removeClass('d-none');
                        }, 1000);
                        $('#savebutton').prop('disabled', false);
                    }
                }
            });
        });

        <?php if (isset($_SESSION['wrongupdated'])) {
            $msg = $_SESSION['wrongupdated'];
            echo "Swal.fire({
                icon:'question',
                timer: 1500,
                showConfirmButton: false,
                title: '$msg'
            });
            $('form').removeClass('d-none');
            $('#editpass').addClass('d-none');
            ";
            unset($_SESSION['wrongupdated']);
        } ?>
        <?php
        if (isset($_SESSION['updated'])) {
            $msg = $_SESSION['updated'];

            echo "Swal.fire({
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                    title: '$msg'
                });
                $('#updatedpass').removeClass('d-none');
                ";
            unset($_SESSION['updated']);
        }
        ?>
    </script>
</body>

</html>