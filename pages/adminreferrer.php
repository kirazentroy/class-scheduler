<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (!isset($_SESSION['id'])) {
    header('location:../index.php');
}
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}

if ($_SESSION['superiority'] != 'admin') {
    header('location: ../pages/displaysched.php');
}

if (isset($_SESSION['userdept'])) {
    $dept = $_SESSION['userdept'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Referral Settings | <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        #copytext {
            top: 32%;
            right: 13px;
        }

        #copytext:hover {
            cursor: pointer;
        }

        .deletednote {
            position: absolute;
            animation: deletednote 1s 1 ease-in-out;
        }

        @keyframes deletednote {
            from {
                /* top: 250px; */
                opacity: 1;
                left: 0;
                /* left: 100px; */
            }

            to {
                height: 0;
                opacity: 0;
                left: -9000px;
            }
        }
    </style>
    <!-- <link rel="stylesheet" href="../assets/css/sweetalert.css"> -->
</head>

<body>

    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">

        <?php include('../includes/sidebar.php') ?>

        <div id="page-content-wrapper">
            <div class="container-fluid mt-4 px-4">
                <h3>Dean Code Referral</h3>
                <div class="row">
                    <div class="col-3 position-relative">
                        <input type="text" readonly id="generator" class="form-control">
                        <i class="fas fa-copy fa-2xl position-absolute" id="copytext" onclick="copytext();"></i>
                        <br>
                    </div>
                    <div id="copied" class="col-2 d-none">
                        <p class="text-success">Text Copied!</p>
                    </div>
                </div>
                <div class=" row">
                    <div class="col-3">
                        <button onclick="generate();" class="btn btn-success btn-sm">Generate</button>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-12">
                        <table class="table table-striped">
                            <caption class="caption-top">Deans w/o Foreground Access</caption>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Grant Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $admins = executeNonQuery($connect, "SELECT * from `members` join departments where members.member_department = departments.dept_id and members.member_superiority = 'admin' and members.permission = '0' order by member_salut, member_last");
                                if (numRows($connect, $admins) === 0) { ?>
                            </tbody>
                        </table>
                        <p class="text-center text-dark">-- No data results --</p>
                        <?php } else {
                                    while ($row = fetchAssoc($connect, $admins)) { ?>
                            <tr id="row_<?php echo $row['member_id'] ?>">
                                <td><?php echo $row['member_salut'] . ' ' . $row['member_last'] ?></td>
                                <td><?php echo $row['dept_name'] . ' (' . $row['dept_code'] . ')' ?></td>
                                <td><button class="btn btn-success btn-sm" onclick="grantdeans(<?php echo $row['member_id'] ?>);">Grant</button></td>
                            </tr>
                        <?php }

                        ?>
                        </tbody>
                        </table>
                    <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/popper.js"></script>
    <!-- <script src="../js/bootstrap.min.js"></script>
    <script src="../js/popper.min.js"></script> -->
    <script src="../assets/scripts/fontawesome.js"></script>
    <script src="../assets/jquery/jquery.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script>
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789'.split('');

        const insertcode = (str) => {
            $.ajax({
                url: "../functions/insertreferralcode.php",
                method: "POST",
                data: {
                    insertcode: 'success',
                    codetext: str,
                    codestatus: 'notused'
                },
                success: function(data) {

                }
            })
        }

        const generate = () => {
            let pushedCharacters = '';
            for (let i = 0; i < 10; i++) {
                let randomize = characters.sort(() => Math.random() - 0.5);
                pushedCharacters += randomize[Math.floor(Math.random() * Number(Date().split(' ')[4].split(':')[2]))];
            }
            $('#generator').attr('value', pushedCharacters);
            $('#copied').addClass('d-none');
            insertcode(pushedCharacters);
        }

        const copytext = () => {
            $('#copied').removeClass('d-none');
            navigator.clipboard.writeText($('#generator').val());
        }

        const grantdeans = (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, grant it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "../functions/grant.php",
                        method: "POST",
                        data: {
                            grant: 'success',
                            id: id
                        },
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: data,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                    let datahide = '#row_' + id;
                    deletedrow(datahide);
                }
            })
        }

        function deletedrow(id) {
            setTimeout(() => {
                $(id).addClass('deletednote');
                setInterval(() => {
                    $(id).remove();
                }, 500);
            }, 500);
        }
    </script>
</body>

</html>