<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (!isset($_SESSION['id'])) {
    header('location:../index.php');
}
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}

if ($_SESSION['superiority'] !== 'admin') {
    header('location: ../pages/displaysched.php');
}

if (isset($_SESSION['userdept'])) {
    $dept = $_SESSION['userdept'];
}
$userguide = 'yes';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Schedule (Regs) | <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .conflictdetected {
            animation: conflict 1s 3;
        }

        @keyframes conflict {
            0% {
                background-color: white;
            }

            50% {
                background-color: #e14b59;
            }

            100% {
                background-color: white;
            }
        }


        #assignatoriescont {
            right: 0;
            position: fixed;
            height: 70%;
            overflow: hidden;
            overflow-y: scroll;
            border: solid #000814 1px;
            border-radius: 25px;
            box-shadow: -4px 4px gray;
            margin-right: 20px;
            padding: 20px;
        }

        #assignatoriescont::-webkit-scrollbar {
            width: 10px;
            /* height: 10px; */
            max-height: 20%;
            height: 20%;
        }

        #assignatoriescont::-webkit-scrollbar-track {
            margin: 20px 0;
        }


        #assignatoriescont::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        #assignatoriescont::-webkit-scrollbar-thumb:hover {
            background: #555;
        }



        #scheduler thead th:nth-child(1),
        #scheduler tbody#tbody td:nth-child(1) {
            width: 40%;
        }

        #scheduler thead th,
        #scheduler tbody#tbody td {
            width: 5.25%;
        }

        #scheduler thead th:nth-child(8),
        #scheduler tbody#tbody td:nth-child(8) {
            width: 20%;
        }

        #scheduler tbody#tbody tr:last-child td:nth-child(8) {
            border-radius: 0 0 15px 0;
        }

        #scheduler tbody#tbody tr:last-child td:first-child {
            border-radius: 0 0 0 15px;
        }

        #scheduler #tbody tr:nth-child(odd) {
            background-color: #369b94;
        }

        #scheduler #tbody tr:nth-child(even) {
            background-color: #067a3d;
        }

        .deletedrow {
            position: absolute;
            animation: deleted 1s 1 ease-in-out;
        }

        @keyframes deleted {
            0% {
                top: 250px;
                opacity: 1;
                left: 100px;
            }

            25% {
                left: 100px;
                top: 500px;
            }

            100% {
                top: 500px;
                height: 0;
                opacity: 0;
                left: -9000px;
            }
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

        .customopacity {
            animation: customopacity 1s 1 ease-in-out;
        }

        @keyframes customopacity {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #previmg:hover,
        #nextimg:hover {
            cursor: pointer;
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
                <div class="row">
                    <div class="col-9">
                        <h4 class="fw-bold mb-5">Schedule a class (Regulars)</h4>
                        <div class="mx-auto mb-3 row">
                            <div class="col-1">
                                <button class="btn btn-sm btn-dark" id="addsched"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <div class="col-1">
                                <button class="btn btn-sm btn-success" id="addbookmark"><i class="fa-solid fa-bookmark"></i></button>
                            </div>
                            <div class="col-7">
                                <p id="instructorCounts" class="text-dark"></p>
                            </div>
                            <div class="col-2">
                                <p class="btn btn-sm btn-danger" id="uncheckall">Uncheck All</p>
                            </div>
                            <div class="col-1">
                                <button id="tosave" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalSave">Save</button>
                            </div>
                        </div>
                        <div class="mx-auto mb-3 row">
                            <div class="col-12">
                                <p id="getdescription" class="text-capitalize text-dark"></p>
                            </div>
                        </div>
                        <table class="table" id="scheduler">
                            <thead class="table-primary">
                                <tr>
                                    <th style="border-radius:15px 0 0 0">Time</th>
                                    <th>M</th>
                                    <th>T</th>
                                    <th>W</th>
                                    <th>Th</th>
                                    <th>F</th>
                                    <th>S</th>
                                    <th style="border-radius:0 15px 0 0" class="text-center">Delete Row</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                    <div class="col-1"></div>
                    <div class="col-2" id="assignatoriescont">
                        <h4 class="text-center fw-bold">Assign</h4>

                        <div class="row">
                            <!-- Teachers -->
                            <label class="form-label fw-bold" for="teachers" style="color: blue;">Teacher</label>
                            <?php
                            if ($_SESSION['superiority'] === 'admin' || $_SESSION['superiority'] === 'student') { ?>
                                <select onchange="instructorSubjectCount();" class="form-select mb-3" name="teachers" id="teachers" <?php echo $_SESSION['superiority'] === 'student' ? 'disabled' : '' ?>>
                                    <?php
                                    if ($_SESSION['superiority'] === 'admin') {
                                        $getself = executeNonQuery($connect, "SELECT * from members join departments where members.member_department = departments.dept_id and members.member_id = '$id';");
                                        $self = fetchAssoc($connect, $getself);
                                    ?>
                                        <option value="<?php echo $id ?>"><?php echo $self['member_salut'] . ' ' . $self['member_last'] . ', ' . $self['member_first'][0] . ' (' . $self['dept_code'] . ')'; ?></option>
                                    <?php }
                                    $teachers = executeNonQuery($connect, "SELECT * FROM members join departments where members.member_department = departments.dept_id and members.member_superiority='faculty' and members.member_activity = 'active' order by members.member_salut, members.member_last, members.member_first");
                                    while ($teachersRow = fetchAssoc($connect, $teachers)) {
                                    ?>
                                        <option value="<?php echo $teachersRow['member_id'] ?>"><?php echo $teachersRow['member_salut'] . ' ' . $teachersRow['member_last'] . ', ' . $teachersRow['member_first'][0] . ' (' . $teachersRow['dept_code'] . ')'; ?>
                                        </option>
                                    <?php }
                                } else if ($_SESSION['superiority'] === 'faculty') {
                                    $getself = executeNonQuery($connect, "SELECT * from members where member_id = '$id'");
                                    $self = fetchAssoc($connect, $getself);
                                    $valueself = "You";
                                    ?>
                                    <input type="hidden" name="teachers" id="teachers" value="<?php echo $id ?>">
                                    <input type="text" readonly value="<?php echo $valueself ?>" class="form-control mb-3">
                                <?php
                                }
                                ?>
                                </select>
                                <!-- Unsa na class ang klasehan -->
                                <label class="form-label fw-bold" for="course" style="color: green;">Program & Year</label>
                                <select class="form-select mb-2" name="course" id="course" onchange="selectsubject();">
                                    <?php if ($_SESSION['permission'] === '1') {
                                        $resultcourse = executeNonQuery($connect, "SELECT * FROM subjects group by course_id order by course_id");
                                    } else {
                                        $resultcourse = executeNonQuery($connect, "SELECT * FROM subjects where department = '$dept' group by course_id order by course_id");
                                    }
                                    while ($subrow = fetchAssoc($connect, $resultcourse)) {
                                    ?>
                                        <option value="<?php echo $subrow['course_id'] ?>"><?php echo $subrow['course_id'] ?></option>
                                    <?php  } ?>
                                </select>
                                <div class="mt-1">
                                    <label class="form-label fw-bold" for="checksection" style="color: green;">Section</label>
                                    <input type="checkbox" id="checksection" class="form-check-input"> (Optional)
                                </div>
                                <select class="form-select mb-2" name="section" id="section" disabled>
                                    <option value="" disabled selected>--</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                                <!-- pili ug semester -->
                                <label class="form-label fw-bold" for="semester" style="color: orange;">Trimester</label>
                                <select class="form-select mb-2" name="semester" id="semester" onchange="instructorSubjectCount();">
                                    <?php include('../includes/semesterselect.php');
                                    ?>
                                </select>
                                <!-- pili ug subject -->
                                <label class="form-label fw-bold" for="subject" style="color: pink;">Course</label>
                                <select class="form-select mb-2" name="subject" id="subject" onchange="selecteddescription();">
                                </select>
                                <!-- pili ug school year -->
                                <label class="form-label fw-bold" for="schoolyear" style="color: violet;">School Year</label>
                                <select class="form-select mb-2" id="schoolyear" onchange="instructorSubjectCount();">
                                </select>
                                <!-- pili ug building -->
                                <label class="form-label fw-bold" for="roombuilding" style="color: red;">Room Building</label>
                                <select class="form-select mb-2" name="roombuilding" id="roombuilding" onchange="selectroom();">
                                    <?php $roombuilding = executeNonQuery($connect, "SELECT * FROM rooms group by building_id order by room_building");
                                    while ($rowrb = fetchAssoc($connect, $roombuilding)) {
                                    ?>
                                        <option value="<?php echo $rowrb['building_id'] ?>"><?php echo $rowrb['room_building'] ?></option>
                                    <?php } ?>
                                </select>
                                <!-- pili ug room -->
                                <label class="form-label fw-bold" for="room" style="color: red;">Room</label>
                                <select class="form-select mb-2" name="room" id="room">

                                </select>
                        </div>
                        <br>
                        <?php if ($_SESSION['superiority'] != 'student') { ?>
                            <div class="row px-3 d-none">
                                <p class="btn btn-sm btn-success" onclick="checkassigned();" id="checkassigned">Check All Vacancy</p>
                            </div>
                            <div class="row px-3">
                                <p class="btn btn-sm btn-primary" onclick="checkteacher();" id="checkroom">Check Teacher's Vacancy</p>
                            </div>
                        <?php } ?>
                        <div class="row px-3">
                            <p class="btn btn-sm btn-dark" onclick="checkcourse();" id="checkroom">Check Course/Section's Vacancy</p>
                        </div>
                        <div class="row px-3">
                            <p class="btn btn-sm btn-info" onclick="checkroom();" id="checkroom">Check Room's Vacancy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="container d-flex justify-content-center row mx-auto">
    </div> -->
    <!-- modalform -->
    <div class="modal fade" id="modalSave" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="../functions/save.php" method="post">
                <div class="modal-content">
                    <div class="modal-body">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Do you want to save this schedule?</h1>
                        <label for="allvaluescontainer" class="d-none">All Values</label>
                        <input type="hidden" id="allvaluescontainer" name="allvaluescontainer">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="save">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-xl fade" id="showTable" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <span id="timeVacancyTitle"></span>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-hover">
                        <thead class="text-center">
                            <th class="d-none"></th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                        </thead>
                        <tbody id="tbodyTimeCheck">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade modal-lg" id="modaluserguide" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleuser">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div id="userdescription">

                        </div>
                        <div class="row" id="imgtitle"></div>
                    </div>
                    <div class="container-fluid">
                        <div class="row mt-3">
                            <div class="col-6 text-center"><i class="fa-solid fa-circle-left" onclick="usersguideprev();" id="previmg"></i></div>
                            <div class="col-6 text-center"><i class="fa-solid fa-circle-right" onclick="usersguidenext();" id="nextimg"></i></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/popper.js"></script>
    <!-- <script src="../js/bootstrap.min.js"></script>
    <script src="../js/popper.min.js"></script> -->
    <script src="../assets/scripts/fontawesome.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script src="../assets/scripts/adminhome.js"></script>
    <script>
        var imguser = 0;
        var counttitle = ["Choose any kind of schedule", "Course Detector", "Course Detector", "Time Scheduler", "Time Scheduler", "Bookmark Button", "Bookmark Button", "Assigner Tab", "Check Vacancy Buttons", "Check Vacancy Buttons", "Conflict Detectors", "Processing Session", "Save Button", "Done"];
        var countdescription = [
            `<p class="text-dark"><strong>1. Regulars</strong></p>
            <p class="text-dark">- For only regular programs.</p>
            <br>
            <p class="text-dark"><strong>1. Custom Scheduling</strong></p>
            <p class="text-dark">- If you need to open a course in a certain program.</p><br>
            `,
            `<p class="text-dark">It will automatically tally the total of instructor's assigned courses.</p><br>`,
            `<p class="text-dark">When the tally reached 8 or more, the indicator will display.</p><br>`,
            `<p class="text-dark">If you click this plus sign button.</p><br>`,
            `<p class="text-dark">A table row appears along with its check box and also the button that will delete its entire row.</p><br>`,
            `<p class="text-dark">Clicking this button will make a bookmark row appears.</p><br>`,
            `<p class="text-dark">Scheduling is really hard so the system will make sure that deans doesn't lost their trackings of scheduling.</p><br>`,
            `<p class="text-dark">This tab is section of schedule choices and it includes Instructors, Program/Year, Course, Trimester, Schoolyear, and Rooms.</p><br>`,
            `<p class="text-dark">This tab is to check a the time vacancy of a certain instructor, program/year, and room.</p><br>`,
            `<p class="text-dark">This is the view of their vacant schedules.</p><br>`,
            `<p class="text-dark">If an assigned instructors, program/year, and room detects a conflict. It will tell the user that a certain choice has a conflicted schedule.</p><br>`,
            `<p class="text-dark">If "Processing" appears, then the selected schedule has no conflicts detected.</p><br>`,
            `<p class="text-dark">Save button is to save the user's stipuled schedules.</p><br>`,
            `<p class="text-dark">Your schedule is a complete success!</p><br>`
        ];

        function usersguide() {
            imguser = 0;
            $('#modaluserguide').modal('show');
            usersguideview();
        }

        function usersguideprev() {
            if (imguser !== 0) {
                imguser--;
            }
            usersguideview();
        }

        function usersguidenext() {
            if (imguser !== 13) {
                imguser++;
            }
            usersguideview();
        }

        function usersguideview() {
            $('#titleuser').html(`${imguser+1}. ${counttitle[imguser]}`);
            $('#imgtitle').html(`<img src="../userguide/${imguser}.png" />`);
            $('#userdescription').html(countdescription[imguser]);
            if (imguser === 0) {
                $('#previmg').addClass('d-none');
            } else if (imguser === 13) {
                $('#nextimg').addClass('d-none');
            } else {
                $('#previmg, #nextimg').removeClass('d-none');
            }
        }
    </script>
    <?php
    if (isset($_SESSION['doneassign'])) {
        $done = $_SESSION['doneassign'];
        // print($done);
        echo "<script>Swal.fire({
            position: 'center',
            icon: 'success',
            title: '$done',
            showConfirmButton: false,
            timer: 2000
            });</script>";
        unset($_SESSION['doneassign']);
    }
    ?>
</body>

</html>