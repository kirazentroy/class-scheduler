<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (isset($_SESSION['pre-user'])) {
    $_SESSION['nocontinue'] = "No registers recorded";
} else if (!$_SESSION['pre-user']) {
    $_SESSION['userreset'] = 'wew';
    header('location:../');
}

$count = executeNonQuery($connect, "SELECT * from members where member_superiority = 'admin'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <style>
        /*
        body {
            background: url('../images/ascb.jpeg') no-repeat;
            background-size: cover;
        }
        */
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center vh-100 mx-auto">
            <form action="../functions/signedup.php" method="post" class="form-control w-100 p-5">
                <div class="h3 mb-5">Signing up...</div>
                <div class="row gy-3 mx-auto">
                    <div class="col-md-6 col-sm-12">
                        <label for="preuser">Username</label>
                        <input type="text" value="<?php echo isset($_SESSION['pre-user']) ? strtolower($_SESSION['pre-user']) : '' ?>" name="preuser" readonly class="form-control" id="preuser">
                        <label for="email">E-mail</label>
                        <input class="form-control" type="email" id="email" name="email" required>
                        <label for="lastname">Last Name</label>
                        <input class="form-control" type="text" id="lastname" name="lastname" required>
                        <label for="firstname">First Name</label>
                        <input class="form-control" type="text" id="firstname" name="firstname" required>
                        <label for="extname">Suffix</label>
                        <input class="form-control" type="text" id="extname" name="extname">
                    </div>
                    <div class="col-md-6 col-sm-12 mx-auto">
                        <label for="gender" class="mb-1">Gender</label> <br>
                        <input type="radio" name="gender" value="male" id="male" required>&nbsp;&nbsp;<label for="male">Male</label>
                        &nbsp;&nbsp;&nbsp;
                        <input type="radio" name="gender" value="female" id="female" required>&nbsp;&nbsp;<label for="female">Female</label><br>
                        <label for="salut" style="margin-top: 10px;">Degree</label>
                        <select class="form-select" name="salut" id="salut" required>
                            <option value="no">(Leave Blank)</option>
                            <option value="Doc.">Doc.</option>
                            <option value="Engr.">Engr.</option>
                            <option value="Atty.">Atty.</option>
                            <option value="others">Others</option>
                        </select>
                        <div class="d-none row mx-auto" id="specify">
                            <label for="specifying">Please specify:</label>
                            <input class="form-control" type="text" name="salutothers" id="specifying">
                        </div>
                        <label for="superiority">User Type</label>
                        <select class="form-select" name="superiority" id="superiority" required>
                            <option value="" selected disabled>--</option>
                            <?php if (!isset($_SESSION['referral'])) { ?>
                                <option value="admin"><?= numRows($connect, $count) === 0 ? 'Superadmin' : 'Dean' ?></option>
                                <option value="faculty">Faculty</option>
                            <?php } ?>
                            <option value="student">Student</option>
                        </select>
                        <div class="d-none row-gy-2" id="referral">
                            <label for="code">Dean Referral Code</label>
                            <input type="text" name="code" id="code" class="form-control">
                        </div>
                        <div class="d-none" id="specificdepartment">
                            <label for="department">Select a Department: </label><br>
                            <select class="form-select" name="department" id="department">
                                <?php $row = executeNonQuery($connect, "SELECT * From departments order by dept_name");
                                while ($depts = fetchAssoc($connect, $row)) {
                                ?>
                                    <option value="<?php echo $depts['dept_id'] ?>" <?= (int)($depts['dept_id']) === 5 ? "id='notforadmins'" : "" ?>><?php echo $depts['dept_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="d-none" id="specificstudent">
                            <label for="course">Select a Course: </label><br>
                            <select class="form-select" name="course" id="course">
                                <?php if (isset($_SESSION['referral'])) {
                                    $specificcourse = $_SESSION['referral'];
                                    $speccourse = executeNonQuery($connect, "SELECT * FROM subjects where department = (SELECT member_department from members where member_referral = '$specificcourse') group by course_id order by course_id");
                                    while ($coursefetch = fetchAssoc($connect, $speccourse)) { ?>
                                        <option value="<?= $coursefetch['course_id'] ?>"><?= $coursefetch['course_id'] ?></option>
                                <?php }
                                } else if (!isset($_SESSION['referral'])) {
                                    include('../includes/course.php');
                                } ?>
                            </select>
                        </div>
                        <div class="d-none" id="specificstatus">
                            <label for="studentstatus">Are you irregular? </label><br>
                            <select class="form-select" name="studentstatus" id="studentstatus">
                                <option value="" selected disabled>--</option>
                                <option value="irregular">Yes</option>
                                <option value="regular">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-5 mx-auto">
                    <div class="col-md-6 col-sm-12">
                        <p class="btn btn-sm btn-dark" id="cancelled">Cancel</p>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <button type="submit" class="btn btn-primary btn-sm" name="fullsign" id="fullsign">Sign-up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/signingup2.js"></script>
    <script>
        document.querySelector('#cancelled').addEventListener('click', function() {
            document.location = '../index.php';
        });
        <?php
        if (isset($_SESSION['email_exist'])) {
            $msg = $_SESSION['email_exist'];
            echo "Swal.fire({
                title: '$msg',
                icon: 'error',
                timer: 1500,
                showConfirmButton: false
            });";
            unset($_SESSION['email_exist']);
        }
        ?>
    </script>
</body>

</html>