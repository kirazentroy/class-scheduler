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

if ($_SESSION['superiority'] !== 'student') {
    $varLink = executeNonQuery($connect, "SELECT member_referral from members where member_id ='$id'");
    $invitelink = fetchAssoc($connect, $varLink);
    $invitelink = $invitelink['member_referral'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Info | <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        #idlink {
            top: 31%;
            right: 0;
        }

        #idor {
            top: 88%;
            right: 0;
        }

        i[id*="id"]:hover {
            cursor: pointer;
        }

        #toggle1 {
            background-color: #000814;
            cursor: pointer;
        }

        #subToggle {
            list-style: none;
        }

        /* .borderimg img {
            border-radius: 5px solid black;
            width: 100px;
            height: 100px;
        } */

        #profileimg {
            right: 20px;
            top: 122px;
        }
    </style>
</head>

<body>


    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">

        <?php include('../includes/sidebar.php') ?>

        <div id="page-content-wrapper">
            <div class="container-fluid mt-4 px-4">
                <div class="row gap-3 mb-5">
                    <!-- <div class="col-4">
                        <div class="borderimg p-4">

                        </div>
                    </div> -->
                    <?php $resultprof = executeNonQuery($connect, "SELECT * from members JOIN departments where members.member_id = '$id' and departments.dept_id = (SELECT members.member_department FROM members where members.member_id = '$id')");
                    $profile = fetchAssoc($connect, $resultprof);
                    ?>
                    <div class="col-4 position-relative">
                        <div class="row">
                            <div class="col-8">
                                <h4 class="mb-4">Profile Info</h4>
                            </div>
                            <div class="col-4"><button class="btn btn-primary btn-sm" id="editinfo">Edit <i class="fas fa-edit"></i></button></div>
                        </div>
                        <form action="../functions/updated.php" method="post" class="form-group" enctype="multipart/form-data">
                            <div class="row gy-3">
                                <div class="col-9">
                                    <img src="../profileimages/<?php echo $_SESSION['imgname'] ?>" class="img-fluid" alt="../profileimages/<?php echo $_SESSION['imgname'] ?>" style="width: 100px; height:100px; border-radius:50%;">
                                    <input type="file" class="position-absolute d-none mt-4" id="profileimg" name="profileimg">
                                    <br>
                                    <br>
                                    <input type="hidden" name="infoid" value="<?php echo $id ?>">
                                    <?php if (isset($_SESSION['email_correction'])) { ?>
                                        <p class="text-danger"><?= $_SESSION['email_correction'] ?></p>
                                    <?php
                                        unset($_SESSION['email_correction']);
                                    } ?>
                                    <label for="degree">Degree</label>
                                    <input type="text" id="degree" name="degree" readonly value="<?php echo $profile['member_salut'] ?>" class="form-control"> <br>

                                    <label for="lastname">Lastname</label>
                                    <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo $profile['member_last'] ?>" readonly>

                                    <br>
                                    <label for="firstname">Firstname</label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo $profile['member_first'] ?>" readonly>
                                    <br>
                                    <label for="email">Email</label>&nbsp;<?= $profile['email_status'] === 'verified' ? "<span style='font-size: 15px; border-radius:50%; color:green; padding: 5px;'> <i class='fa-solid fa-check'></i></span>" : "<span style='font-size: 15px; border-radius:50%; color:red; padding: 5px;'> <i class='fa-solid fa-xmark'></i></span>" ?>
                                    <input type="email" name="email" id="email" class="form-control" value="<?php echo $profile['member_email'] ?>" readonly>
                                    <br>
                                    <label for="dept">Department</label>
                                    <input type="text" name="dept" id="dept" class="form-control" value="<?php echo $profile['dept_name'] ?>" readonly>
                                    <select name="selectdept" id="selectdept" class="d-none form-select">
                                        <?php $depts = executeNonQuery($connect, "SELECT * from departments order by dept_name");
                                        while ($dept = fetchAssoc($connect, $depts)) {
                                        ?>
                                            <option value="<?php echo $dept['dept_id'] ?>" <?php echo $dept['dept_name'] === $profile['dept_name'] ? 'selected' : '' ?>><?php echo $dept['dept_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <br>
                                    <?php if ($_SESSION['superiority'] === 'student') {
                                        $studcourse = executeNonQuery($connect, "SELECT * from students where student_id = '$id'");
                                        $getstudinfo = fetchAssoc($connect, $studcourse);
                                    ?>
                                        <label for="course">Course</label>
                                        <input type="text" name="course" id="course" class="form-control" value="<?php echo $getstudinfo['student_course'] ?>" readonly>
                                        <select name="selectcourse" id="selectcourse" class="d-none form-select">
                                            <?php $courses = executeNonQuery($connect, "SELECT * from subjects where department = '$department' group by course_id order by course_id");
                                            while ($course = fetchAssoc($connect, $courses)) {
                                            ?>
                                                <option value="<?php echo $course['course_id'] ?>" <?php echo $course['course_id'] === $getstudinfo['student_course'] ? 'selected' : '' ?>><?php echo $course['course_id'] ?></option>
                                            <?php } ?>
                                        </select>
                                        <br>
                                        <?php if ($getstudinfo['student_course'] === $getstudinfo['student_section']) {
                                            $section = 'No section yet!';
                                        } else {
                                            [, $section] = explode($getstudinfo['student_course'], $getstudinfo['student_section']);
                                        } ?>
                                        <label for="section">Section</label>
                                        <input type="text" name="section" id="section" class="form-control" value="<?php echo $section ?>" readonly>
                                        <select name="selectsection" id="selectsection" class="d-none form-select">
                                            <?php $arr = ['', 'A', 'B', 'C'];
                                            for ($i = 0; $i < count($arr); $i++) {
                                            ?>
                                                <option value="<?php echo $arr[$i] ?>" <?php echo $arr[$i] === $section ? 'selected' : '' ?>><?php echo $arr[$i] === '' ? '(leave blank)' : $arr[$i] ?></option>
                                            <?php } ?>
                                        </select>
                                        <br>
                                    <?php } ?>
                                    <button class="btn btn-success btn-sm d-none" id="savebutton" type="submit" name="updateinfo">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php if ($_SESSION['superiority'] !== 'student') { ?>
                        <div class="col-4 position-relative">
                            <div class="row">
                                <div class="col-8">
                                    <h4 class="mb-4">Invite Links</h4>
                                </div>
                            </div>
                            <div class="row gy-3">
                                <div class="col-9 mt-5">
                                    <div class="position-relative">
                                        <label for="invitelink" class="form-label">(For Students Only)</label>
                                        <input type="text" class="form-control" style="width: 85%;" value="class-scheduler.asc-bislig.com/signup.php?referral=<?= $invitelink ?>" id="invitelink" readonly>
                                        <i class="fas fa-copy fa-2xl position-absolute" id="idlink" onclick="copytext('#invitelink');"></i>
                                        <br>
                                        <label for="inviteor" class="form-label">or</label>
                                        <input type="text" class="form-control" style="width: 85%;" value="<?= $invitelink ?>" id="inviteor" readonly>
                                        <i class="fas fa-copy fa-2xl position-absolute" id="idor" onclick="copytext('#inviteor');"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script>
        const copytext = (id) => {
            // $('#copied').removeClass('d-none');
            navigator.clipboard.writeText($(id).val());
            Swal.fire({
                title: 'Copied to clipboard',
                icon: 'success',
                timer: 500,
                showConfirmButton: false
            });
        }
        $(document).ready(function() {
            $('#editinfo').click(function() {
                $(this).addClass('d-none');
                $('#savebutton, #profileimg').removeClass('d-none');
                $('#degree').prop('readonly', false);
                $('#lastname').prop('readonly', false);
                $('#firstname').prop('readonly', false);
                $('#email').prop('readonly', false);
                <?php if ($_SESSION['superiority'] === 'admin') {
                    if ($_SESSION['permission'] === 1) {
                ?>
                        $('#dept').prop('readonly', false);
                        $('#dept').addClass('d-none');
                        $('#selectdept').removeClass('d-none');
                    <?php }
                } else if ($_SESSION['superiority'] === 'student') { ?>
                    $('#course').addClass('d-none');
                    $('#section').addClass('d-none');
                    $('#selectcourse').removeClass('d-none');
                    $('#selectsection').removeClass('d-none');
                <?php } ?>
            });

            <?php
            if (isset($_SESSION['updated'])) {
                $msg = $_SESSION['updated'];

                echo "Swal.fire({
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                    title: '$msg'
                });";
                unset($_SESSION['updated']);
            }
            ?>
        });
    </script>
</body>

</html>