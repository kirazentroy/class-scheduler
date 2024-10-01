<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}
if ($_SESSION['superiority'] === 'student') {
    header('location: ../pages/displayschedstudent.php');
} else if ($_SESSION['superiority'] === 'faculty') {
    header('location: ../pages/displayschedfaculty.php');
}

if (!isset($_SESSION['id'])) {
    header('location: ../index.php');
}

$dept = $_SESSION['userdept'];
$userguide = 'yes';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedules | <?php include('../includes/title.php'); ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        tbody[id^='schedtbody'] {
            font-size: 10px;
        }

        table {
            max-width: 100% !important;
            border: 1px solid black !important;
        }

        #tableteacher td,
        #tablecourse td,
        #tableroom td {
            padding-right: 1rem !important;
            padding-bottom: 1rem !important;
        }

        #tableteacher td,
        #tableteacher th,
        #tablecourse td,
        #tablecourse th,
        #tableroom td,
        #tableroom th {
            width: 14% !important;
            border: 1px solid black !important;
        }

        #tableteacher td:first-child,
        #tableteacher th:first-child,
        #tablecourse td:first-child,
        #tablecourse th:first-child,
        #tableroom td:first-child,
        #tableroom th:first-child {
            width: 16% !important;
        }

        #tableteacher td:first-child,
        #tablecourse td:first-child,
        #tableroom td:first-child {
            padding: auto !important;
        }

        #toggle1 {
            background-color: #000814;
            cursor: pointer;
        }

        #subToggle {
            list-style: none;
        }

        td a {
            color: red;
        }


        .customizedspan {
            color: red;
            text-decoration: underline;
        }

        .customizedspan2 {
            /* color: blue; */
            text-decoration: underline;
        }

        .customizedspan:hover,
        .customizedspan2:hover {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">
        <?php include('../includes/sidebar.php') ?>

        <div id="page-content-wrapper">
            <div class="container-fluid mt-4 px-4">
                <header class="mb-5">
                    <h1>Schedules</h1>
                </header>
                <main>
                    <div class="row mb-5">
                        <div class="col-6">
                            <div class="row-mb-5">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold" for="sy">School Year</label>
                                    <select class="form-select" id="schoolyear" onchange="selectSY();">
                                    </select>
                                </div>
                                <div class="col-6 mb-3" id="parasasem">
                                    <label class="form-label fw-bold" for="semester">Trimester</label>
                                    <select class="form-select" name="semester" id="semester" onchange="selectSY();">
                                        <?php include('../includes/semestershs.php');
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row" id="selectionby">
                                <div class="col-6 mb-2">
                                    <label class="form-label fw-bold" for="selectby">Select by:</label>
                                    <select class="form-select" name="selectby" id="selectby" onchange="choosenfilter();">
                                        <option value="teachers">Teachers</option>
                                        <option value="courses">Programs</option>
                                        <option value="rooms">Rooms</option>
                                    </select>
                                </div>
                                <div class="row mt-2 d-none" id="parasateachers">
                                    <div class="col-6">
                                        <label class="form-label fw-bold" for="teachers">Choose a teacher:</label><br>
                                        <select class="form-select" name="teachers" id="teachers" onchange="teacherEditDone();">
                                            <?php if ($_SESSION['permission'] === '1') {
                                                $teachers = executeNonQuery($connect, "SELECT * FROM members where member_activity = 'active' and (member_superiority = 'admin' or member_superiority = 'faculty') order by member_department, members.member_salut, members.member_last");
                                            } else {
                                                $teachers = executeNonQuery($connect, "SELECT * FROM members where member_activity = 'active' and (member_superiority = 'admin' or member_superiority = 'faculty') and (member_department = '$dept' or member_department = '5') order by member_department, members.member_salut, members.member_last");
                                            }
                                            while ($row = fetchAssoc($connect, $teachers)) {
                                                $deptmem = $row['member_department'];
                                                $varDept = executeNonQuery($connect, "SELECT dept_code from departments where dept_id = '$deptmem'");
                                                $deptname = fetchAssoc($connect, $varDept);
                                            ?>
                                                <option value="<?php echo $row['member_id']; ?>"><?php echo $row['member_salut'] . ' ' . $row['member_last'] . ', ' . $row['member_first'] . ' (' . $deptname['dept_code'] . ')'; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2 d-none" id="parasacourse">
                                    <div class="col-6">
                                        <label class="form-label fw-bold" for="courses">Choose a program:</label><br>
                                        <select class="form-select" name="courses" id="courses" onchange="courseEditDone();">
                                            <?php if ($_SESSION['permission'] === '1') {
                                                $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` group by course order by course");
                                            } else {
                                                if ($_SESSION['userdept'] === '1') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` where course like 'BSIS-%' or course like 'BSIT-%' or course like 'BSCS-%' group by course order by course");
                                                } else if ($_SESSION['userdept'] === '2') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` where course like 'BEED-%' or course like 'BSED%' group by course order by course");
                                                } else if ($_SESSION['userdept'] === '3') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` where course like 'BSCRIM%' group by course order by course");
                                                } else if ($_SESSION['userdept'] === '4') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `scheduled_classes` where course like 'BSA-%' or course like 'BSBA%' group by course order by course");
                                                }
                                            }
                                            $arrayofcourse = [];
                                            while ($row = fetchAssoc($connect, $result)) {
                                                if (!in_array($row['course'], $arrayofcourse)) {
                                                    array_push($arrayofcourse, $row['course']);
                                                }
                                            }
                                            if ($_SESSION['permission'] === '1') {
                                                $result = executeNonQuery($connect, "SELECT * FROM `merged_classes` group by course order by course");
                                            } else {
                                                if ($_SESSION['userdept'] === '1') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `merged_classes` where course like 'BSIS-%' or course like 'BSIT-%' or course like 'BSCS-%' group by course order by course");
                                                } else if ($_SESSION['userdept'] === '2') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `merged_classes` where course like 'BEED-%' or course like 'BSED%' group by course order by course");
                                                } else if ($_SESSION['userdept'] === '3') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `merged_classes` where course like 'BSCRIM%' group by course order by course");
                                                } else if ($_SESSION['userdept'] === '4') {
                                                    $result = executeNonQuery($connect, "SELECT * FROM `merged_classes` where course like 'BSA-%' or course like 'BSBA%' group by course order by course");
                                                }
                                            }
                                            while ($row = fetchAssoc($connect, $result)) {
                                                if (!in_array($row['course'], $arrayofcourse)) {
                                                    array_push($arrayofcourse, $row['course']);
                                                }
                                            }
                                            for ($i = 0; $i < count($arrayofcourse); $i++) { ?>
                                                <option value="<?php echo $arrayofcourse[$i] ?>"><?php echo $arrayofcourse[$i] ?></option>
                                            <?php }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2 d-none" id="parasaroom">
                                    <div class="col-6">
                                        <label class="form-label fw-bold" for="rooms">Choose a room:</label><br>
                                        <select class="form-select" name="rooms" id="rooms" onchange="roomEditDone();">
                                            <?php $result = executeNonQuery($connect, "SELECT scheduled_classes.room as room, rooms.room_number as roomname FROM `scheduled_classes` join `rooms` where scheduled_classes.room = rooms.room_id group by scheduled_classes.room order by rooms.room_number");
                                            while ($row = fetchAssoc($connect, $result)) {
                                            ?>
                                                <option value="<?php echo $row['room'] ?>"><?php echo $row['roomname'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button id="showNewTab" class="btn btn-sm btn-info mb-3 d-none" onclick="showOrganizedTab();">Display Organized Schedule</button>
                    <div id="conttable">
                        <table class="table d-none table-striped position-relative" id="tableteacher">
                            <p class="d-none text-bold text-dark" id="specificschedteacher"><span id="assignedteacher" style="font-weight: bold;"></span></p>
                            <thead id="schedtheadteacher">
                                <div class="row">
                                    <tr>
                                        <th class="">Time</th>
                                        <th class="">Mon</th>
                                        <th class="">Tue</th>
                                        <th class="">Wed</th>
                                        <th class="">Thu</th>
                                        <th class="">Fri</th>
                                        <th class="">Sat</th>
                                    </tr>
                                </div>
                            </thead>
                            <tbody id="schedtbodyteacher">

                            </tbody>
                        </table>
                        <table class="table d-none table-striped position-relative" id="tablecourse">
                            <p class="d-none text-bold text-dark" id="specificschedcourse"><span id="assignedcourse" style="font-weight: bold;"></span></p>
                            <thead id="schedtheadcourse">
                                <div class="row">
                                    <tr>
                                        <th class="">Time</th>
                                        <th class="">Mon</th>
                                        <th class="">Tue</th>
                                        <th class="">Wed</th>
                                        <th class="">Thu</th>
                                        <th class="">Fri</th>
                                        <th class="">Sat</th>
                                    </tr>
                                </div>
                            </thead>
                            <tbody id="schedtbodycourse">

                            </tbody>
                        </table>
                        <table class="table d-none table-striped position-relative" id="tableroom">
                            <p class="d-none text-bold text-dark" id="specificschedroom"><span id="assignedroom" style="font-weight: bold;"></span></p>
                            <thead id="schedtheadroom">
                                <div class="row">
                                    <tr>
                                        <th class="">Time</th>
                                        <th class="">Mon</th>
                                        <th class="">Tue</th>
                                        <th class="">Wed</th>
                                        <th class="">Thu</th>
                                        <th class="">Fri</th>
                                        <th class="">Sat</th>
                                    </tr>
                                </div>
                            </thead>
                            <tbody id="schedtbodyroom">

                            </tbody>
                        </table>
                    </div>
                </main>
                <button class="btn btn-sm btn-dark mb-5" id="printsched" onclick="">Print Schedule</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modaleditsched" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid" id="editschedbody">
                        <input type="hidden" value="" id="scheduleid">
                        <input type="hidden" value="" id="updateeverymerge">
                        <input type="hidden" value="" id="schedtype">
                        <input type="hidden" value="" id="table_id">
                        <label for="available_teachers" class="form-label">Available Instructors</label>
                        <select id="available_teachers" class="form-select mb-1">

                        </select>

                        <label for="available_rooms" class="form-label">Available Rooms</label>
                        <select id="available_rooms" class="form-select">

                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveEdit();">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal user guide -->
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
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script>
        // user's guide
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
        // end of user's guide
        function saveEdit() {
            $.ajax({
                url: "../functions/saveedited.php",
                method: "POST",
                data: {
                    schedid: $('#scheduleid').val(),
                    teacher: $('#available_teachers').val(),
                    room: $('#available_rooms').val(),
                    updatemerge: $('#updateeverymerge').val(),
                    schedtype: $('#schedtype').val()
                },
                success: function(data) {
                    let row = $('#tableid').val();
                    if ($('#schedtype').val() === 'course') {
                        courseEditDone();
                    } else if ($('#schedtype').val() === 'room') {
                        roomEditDone();
                    } else {
                        teacherEditDone();
                    }
                    $('#modaleditsched').modal('hide');
                }
            });
        }

        function deletesched(row, schedid) {
            let [idsched, type] = schedid.split('_');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "../functions/schedsprocessing.php",
                        method: "POST",
                        data: {
                            remove: idsched,
                            type: type
                        },
                        success: function(data) {
                            // location.reload();
                            Swal.fire({
                                icon: 'success',
                                title: data,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    })
                    $(row).html('');
                }
            })
        }

        function courseFused(data) {
            let str = "";
            if (data.length === 1) {
                return data[0];
            } else {
                for (let i = 0; i < data.length; i++) {
                    if (i === 0) {
                        str += data[i];
                    } else if (i === data.length - 1 && i !== 0) {
                        str += `, and ${data[i]}`;
                    } else if (i !== 0) {
                        str += `, ${data[i]}`;
                    }
                }
            }
            return str;
        }

        function getfused2(idschedule) {
            let html = "";
            $.getJSON(`../queries/getfused.php?checkgetfused=${idschedule}`, function(data) {
                if (data.length === 0) {
                    html = courseFused(data);
                } else {
                    html = courseFused(data) + "<br>";
                }
                return html;
            });
        }

        function getFusedSchedules(row, idschedule, schedtype) {
            $.getJSON(`../queries/getfused.php?checkgetfused=${idschedule}`, function(data) {
                if (data.length > 0) {
                    Swal.fire({
                        title: 'This schedule has fused with ' + courseFused(data),
                        text: "Fused schedules will also be edited.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Proceed!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showeditschedmodal(row, idschedule, 'true', schedtype);
                        }
                    });
                } else {
                    showeditschedmodal(row, idschedule, 'false', schedtype);
                }
            });
        }

        function showeditschedmodal(row, idschedule, boolean, schedtype) {
            let teachers = "";
            let rooms = "";
            $('#scheduleid').val(idschedule);
            $('#updateeverymerge').val(boolean);
            $('#schedtype').val(schedtype);
            $('#tableid').val(row);
            $.getJSON(`../queries/editsched.php?getavailable=${idschedule}&schedtype=${schedtype}`, function(data) {
                if (data[0].length === 0) {
                    teachers = `<option value='no'>No Available</option>`;
                } else {
                    data[0].forEach(ele => {
                        teachers += `<option value="${ele.id}">${ele.fullname}</option>`;
                    });
                }
                $('#available_teachers').html(teachers);
                if (data[1].length === 0) {
                    rooms = `<option value='no'>No Available</option>`;
                } else {
                    data[1].forEach(ele => {
                        rooms += `<option value="${ele.room_id}">${ele.room_number}</option>`;
                    });
                }
                $('#available_rooms').html(rooms);
                if (schedtype === 'teacher') {
                    $(`#available_rooms, label[for="available_rooms"]`).addClass('d-none');
                    $(`#available_teachers, label[for="available_teachers"]`).removeClass('d-none');
                } else if (schedtype === 'room') {
                    $(`#available_rooms, label[for="available_rooms"]`).removeClass('d-none');
                    $(`#available_teachers, label[for="available_teachers"]`).addClass('d-none');
                } else {
                    $(`#available_rooms, label[for="available_rooms"]`).removeClass('d-none');
                    $(`#available_teachers, label[for="available_teachers"]`).removeClass('d-none');
                }
            });
            $('#modaleditsched').modal('show');
        }

        function editsched(row, schedid, schedtype) {
            let [idsched, type] = schedid.split('_');
            if (type === 'merge') {
                Swal.fire({
                    title: 'You are not allowed to edit fused schedules',
                    icon: 'warning',
                    timer: 1500
                });
            } else {
                getFusedSchedules(row, idsched, schedtype);
            }
        }

        const noselected = () => {
            $('#selectedby, #selectedcourse, #selectedteacher, #selectedroom').prop('selected', true);
            $('#parasateachers, #parasacourse, #parasaroom').addClass('d-none');
            $('#teachers, #courses, #rooms').attr('value', '');
        }

        const dependselected = (selected) => {
            $('#parasateachers').addClass('d-none');
            $('#parasacourse').addClass('d-none');
            $('#parasaroom').addClass('d-none');
            if (selected === 'teachers') {
                $('#parasateachers').removeClass('d-none');
            } else if (selected === 'courses') {
                $('#parasacourse').removeClass('d-none');
            } else if (selected === 'rooms') {
                $('#parasaroom').removeClass('d-none');
            }
        }

        function timeconvertion(time) {
            if (typeof(time) === 'string') {
                time = Number(time);
            }
            if (time >= 10100 && time <= 11159) {
                time -= 10000;
            } else if (time >= 11200 && time <= 11259) {
                time -= 11200;
            } else if (time >= 20100 && time <= 21159) {
                time -= 20000;
                time += 1200;
            } else if (time >= 21200 && time <= 21259) {
                time -= 20000;
            }
            return time;
        }

        //school year
        function schoolyear() {
            let now = Date().split(' ')[3];
            let schoolyear = [Number(Number(now) - 1) + '-' + now, now + '-' + Number(Number(now) + 1)];
            let sudlanan = '';
            schoolyear.forEach(eleSY => {
                sudlanan += `<option value="${eleSY}">${eleSY}</option>`;
            })
            $('#schoolyear').html(sudlanan);
            // selectSY();
        }

        function selectSY() {
            hideCourseButton();
            dependselected($('#selectby').val());
            choosenfilter();
        }

        function choosenfilter() {
            dependselected($('#selectby').val());
            if ($('#selectby').val() === 'teachers') {
                teacherEditDone();
            } else if ($('#selectby').val() === 'rooms') {
                roomEditDone();
            } else if ($('#selectby').val() === 'courses') {
                courseEditDone();
            }
            // hideCourseButton();
        }

        $(document).ready(function() {
            schoolyear();
            selectSY();
            // teacherEditDone();

            <?php
            if (isset($_SESSION['alert'])) {
                $msg = $_SESSION['alert'];
                echo "Swal.fire({
                position: 'center',
                icon: 'success',
                title: '$msg'+'! You are successfully logged in!',
                showConfirmButton: false,
                timer: 1500
            });";
                unset($_SESSION['alert']);
            } ?>
        });

        function timeconverting(timestart, timeend) {
            timestart + "";
            if (timestart[0] === '1') {
                timestartfixed = timestart[1] + timestart[2] + timestart[3] + timestart[4] + 'am';
            } else {
                timestartfixed = timestart[1] + timestart[2] + timestart[3] + timestart[4] + 'pm';
            }

            timeend + "";
            if (timeend[0] === '1') {
                timeendfixed = timeend[1] + timeend[2] + timeend[3] + timeend[4] + 'am';
            } else {
                timeendfixed = timeend[1] + timeend[2] + timeend[3] + timeend[4] + 'pm';
            }
            return timestartfixed + '-' + timeendfixed;
        }

        function timeconverting2(timestart, timeend) {
            timestart + "";
            if (timestart[0] === '1') {
                timestartfixed = timestart[1] + timestart[2] + ':' + timestart[3] + timestart[4] + 'am';
            } else {
                timestartfixed = timestart[1] + timestart[2] + ':' + timestart[3] + timestart[4] + 'pm';
            }

            timeend + "";
            if (timeend[0] === '1') {
                timeendfixed = timeend[1] + timeend[2] + ':' + timeend[3] + timeend[4] + 'am';
            } else {
                timeendfixed = timeend[1] + timeend[2] + ':' + timeend[3] + timeend[4] + 'pm';
            }
            return timestartfixed + '-' + timeendfixed;
        }

        function teacherEditDone() {
            let teacherid = $('#teachers').val();
            let schoolyear = $('#schoolyear').val();
            let semester = $('#semester').val();
            let semfixed;
            if (semester === '1') {
                semfixed = '1st Trimester';
            } else if (semester === '2') {
                semfixed = '2nd Trimester';
            } else if (semester === '3') {
                semfixed = '3rd Trimester';
            }
            let time = [
                [],
                {}
            ];
            // let monday = [];
            // let tuesday = [];
            // let wednesday = [];
            // let thursday = [];
            // let friday = [];
            // let saturday = [];
            // let counter = [0, 0, 0, 0, 0, 0];
            let trlength = 0;
            let html = `#teachers option[value='${teacherid}']`;
            // alert($(html).html());
            $("span[id='assignedteacher']").html(`${$(html).html()} schedule for ${semfixed}, S.Y ${schoolyear}`);
            $('#printsched').attr('onclick', `PrintElem('#schedtbodyteacher');`);
            $.getJSON(`../queries/getsched.php?getteacher=${teacherid}&schoolyear=${schoolyear}&semester=${semester}`, function(data) {
                $('#schedtbodycourse, #schedtbodyroom').html('');
                $('#tablecourse, #tableroom, #specificschedcourse, #specificschedroom').addClass('d-none');
                if (data.length === 0) {
                    $('#tableteacher').removeClass('d-none');
                    $('#schedtbodyteacher').html('<p class="text-bold text-dark" id="noschedteacher">No schedules assigned yet!</p>');
                } else {
                    data.sort((a, b) => {
                        return timeconvertion(a.start_time) - timeconvertion(b.start_time);
                    });
                    data.forEach(function(ele) {
                        if (!time[0].includes(timeconverting(ele.start_time, ele.end_time))) {
                            time[0].push(timeconverting(ele.start_time, ele.end_time));
                            time[1][timeconverting(ele.start_time, ele.end_time)] = [timeconverting2(ele.start_time, ele.end_time)];
                        }
                    });
                    for (let i = 0; i < data.length; i++) {
                        time[1][timeconverting(data[i].start_time, data[i].end_time)].push(data[i]);
                    }
                    trlength = time[0].length;
                    if (trlength === 0) {
                        $('#tableteacher, #specificschedteacher').addClass('d-none');
                    } else if (trlength > 0) {
                        let schedrow = '';
                        $('#tableteacher, #specificschedteacher').removeClass('d-none');
                        for (let i = 0; i < time[0].length; i++) {
                            schedrow += `<tr id="troy_${i}" class="position-relative">
                                                <td id="${time[0][i]}" class="text-start fw-bold mt-2" style="font-size:15px;">${time[1][time[0][i]][0]}</td>
                                                <td id="mon_${time[0][i]}" class="text-start"></td>
                                                <td id="tue_${time[0][i]}" class="text-start"></td>
                                                <td id="wed_${time[0][i]}" class="text-start"></td>
                                                <td id="thu_${time[0][i]}" class="text-start"></td>
                                                <td id="fri_${time[0][i]}" class="text-start"></td>
                                                <td id="sat_${time[0][i]}" class="text-start"></td>
                                            </tr>`;
                            time[1][time[0][i]].shift();
                        }
                        $('#schedtbodyteacher').html(schedrow);
                        inserttd(time);
                    }
                }

            });
            hideCourseButton();
        }

        function courseEditDone() {
            let coursename = $('#courses').val();
            let schoolyear = $('#schoolyear').val();
            let semester = $('#semester').val();
            let semfixed;
            if (semester === '1') {
                semfixed = '1st Trimester';
            } else if (semester === '2') {
                semfixed = '2nd Trimester';
            } else if (semester === '3') {
                semfixed = '3rd Trimester';
            }
            let time = [
                [], {}
            ];
            let trlength = 0;
            $('#printsched').attr('onclick', `PrintElem('#schedtbodycourse');`);
            let html = "#courses option[value='" + coursename + "'" + "]";
            $("span[id='assignedcourse']").html(`${$(html).html()} schedule for ${semfixed}, S.Y ${schoolyear}`);
            $.getJSON(`../queries/getsched.php?getcourse=${coursename}&schoolyear=${schoolyear}&semester=${semester}`, function(data) {
                $('#schedtbodyteacher , #schedtbodyroom').html('');
                $('#tableteacher, #tableroom, #specificschedteacher, #specificschedroom').addClass('d-none');
                if (data.length === 0) {
                    $('#tablecourse').removeClass('d-none');
                    $('#schedtbodycourse').html(`<p class="text-bold text-dark" id="noschedcourse">No schedules assigned yet!</p>`);
                } else {
                    data.sort((a, b) => {
                        return timeconvertion(a.start_time) - timeconvertion(b.start_time);
                    });
                    data.forEach(function(ele) {
                        if (!time[0].includes(timeconverting(ele.start_time, ele.end_time))) {
                            time[0].push(timeconverting(ele.start_time, ele.end_time));
                            time[1][timeconverting(ele.start_time, ele.end_time)] = [timeconverting2(ele.start_time, ele.end_time)];
                        }
                    });
                    for (let i = 0; i < data.length; i++) {
                        time[1][timeconverting(data[i].start_time, data[i].end_time)].push(data[i]);
                    }
                    trlength = time[0].length;
                    if (trlength === 0) {
                        $('#tablecourse, #specificschedcourse').addClass('d-none');
                    } else if (trlength > 0) {
                        let schedrow = '';
                        $('#specificschedcourse, #tablecourse').removeClass('d-none');
                        for (let i = 0; i < time[0].length; i++) {
                            schedrow += `<tr id="troy_${i}" class="position-relative">
                                                <td id="${time[0][i]}" class="text-start fw-bold mt-2" style="font-size:15px;">${time[1][time[0][i]][0]}</td>
                                                <td id="mon_${time[0][i]}" class="text-start"></td>
                                                <td id="tue_${time[0][i]}" class="text-start"></td>
                                                <td id="wed_${time[0][i]}" class="text-start"></td>
                                                <td id="thu_${time[0][i]}" class="text-start"></td>
                                                <td id="fri_${time[0][i]}" class="text-start"></td>
                                                <td id="sat_${time[0][i]}" class="text-start"></td>
                                            </tr>`;
                            time[1][time[0][i]].shift();
                        }
                        $('#schedtbodycourse').html(schedrow);
                        insertcd(time);
                    }
                }
            });
            showCourseButton();
        }

        function showOrganizedTab() {
            window.open(`./pages/organizedschedule.php?program=${$('#courses').val()}&trimester=${$('#semester').val()}&sy=${$('#schoolyear').val()}`);
        }

        function showCourseButton() {
            $('#showNewTab').removeClass('d-none');
        }

        function hideCourseButton() {
            $('#showNewTab').addClass('d-none');
        }

        function roomEditDone() {
            let room = $('#rooms').val();
            let schoolyear = $('#schoolyear').val();
            let semester = $('#semester').val();
            let semfixed;
            if (semester === '1') {
                semfixed = '1st Trimester';
            } else if (semester === '2') {
                semfixed = '2nd Trimester';
            } else if (semester === '3') {
                semfixed = '3rd Trimester';
            }
            let time = [
                [], {}
            ];
            let trlength = 0;
            $('#printsched').attr('onclick', `PrintElem('#schedtbodyroom');`);
            let html = `#rooms option[value='${room}']`;
            $("span[id='assignedroom']").html(`${$(html).html()} schedule for ${semfixed}, S.Y ${schoolyear}`);
            $.getJSON(`../queries/getsched.php?getroom=${room}&schoolyear=${schoolyear}&semester=${semester}`, function(data) {
                $('#schedtbodycourse').html('');
                $('#schedtbodyteacher').html('');
                $('#tableteacher').addClass('d-none');
                $('#tablecourse').addClass('d-none');
                $('#specificschedcourse').addClass('d-none');
                $('#specificschedteacher').addClass('d-none');
                if (data.length === 0) {
                    $('#tableroom').removeClass('d-none');
                    $('#schedtbodyroom').html('<p class="text-bold text-dark" id="noschedroom">No schedules assigned yet!</p>');
                } else {
                    data.sort((a, b) => {
                        return timeconvertion(a.start_time) - timeconvertion(b.start_time);
                    });
                    data.forEach(function(ele) {
                        if (!time[0].includes(timeconverting(ele.start_time, ele.end_time))) {
                            time[0].push(timeconverting(ele.start_time, ele.end_time));
                            time[1][timeconverting(ele.start_time, ele.end_time)] = [timeconverting2(ele.start_time, ele.end_time)];
                        }
                    });
                    for (let i = 0; i < data.length; i++) {
                        time[1][timeconverting(data[i].start_time, data[i].end_time)].push(data[i]);
                    }
                    trlength = time[0].length;
                    if (trlength === 0) {
                        $('#tableroom').addClass('d-none');
                        $('#specificschedroom').addClass('d-none');
                    } else if (trlength > 0) {
                        let schedrow = '';
                        $('#specificschedroom').removeClass('d-none');
                        $('#tableroom').removeClass('d-none');
                        for (let i = 0; i < time[0].length; i++) {
                            schedrow += `<tr id="troy_${i}" class="position-relative">
                                                <td id="${time[0][i]}" class="text-start fw-bold mt-2" style="font-size:15px;">${time[1][time[0][i]][0]}</td>
                                                <td id="mon_${time[0][i]}" class="text-start"></td>
                                                <td id="tue_${time[0][i]}" class="text-start"></td>
                                                <td id="wed_${time[0][i]}" class="text-start"></td>
                                                <td id="thu_${time[0][i]}" class="text-start"></td>
                                                <td id="fri_${time[0][i]}" class="text-start"></td>
                                                <td id="sat_${time[0][i]}" class="text-start"></td>
                                            </tr>`;
                            time[1][time[0][i]].shift();
                        }
                        $('#schedtbodyroom').html(schedrow);
                        insertrd(time);
                    }
                }

            });
            hideCourseButton();
        }


        // pangslice Monday = mon, Tuesday = tue
        function stringcutter(str) {
            let slicer = str.toString().split('');
            let cut = [];
            for (let i = 0; i < 3; i++) {
                cut.push(slicer[i]);
            }
            cut = cut.join('');
            return cut;
        }

        // pang insert sa teachers
        function inserttd(arrayobj) {
            // console.log(arrayobj);
            for (let i = 0; i < arrayobj[0].length; i++) {
                let wew = '';
                let finder = '';
                for (let j = 0; j < arrayobj[1][arrayobj[0][i]].length; j++) {
                    finder = `#${stringcutter(arrayobj[1][arrayobj[0][i]][j].weekday).toLowerCase()}_${arrayobj[0][i]}`;
                    // console.log(finder);
                    wew =
                        `<span>${arrayobj[1][arrayobj[0][i]][j].course}</span><br>
                        <span>${arrayobj[1][arrayobj[0][i]][j].subject_code}</span><br>
                        <span>${arrayobj[1][arrayobj[0][i]][j].roomname}</span><br>
                        ${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? '':"<span class='text-success'>"+arrayobj[1][arrayobj[0][i]][j].merged_from+'</span><br>'}
                        ${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined && arrayobj[1][arrayobj[0][i]][j].merged_with !== undefined) ? "<span class='text-primary'>"+arrayobj[1][arrayobj[0][i]][j].merged_with+'</span><br>':''}
                        ${arrayobj[1][arrayobj[0][i]][j].schedule_status === 'Custom' ? '<span style="color: red;">(Customized Sched)</span><br>':''}
                        <span onclick="editsched('${finder}', '${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? arrayobj[1][arrayobj[0][i]][j].schedule_id+'_sched':arrayobj[1][arrayobj[0][i]][j].merged_id+'_merge'}', 'teacher');" class="customizedspan2 text-info">Edit</span>&nbsp;&nbsp;
                        <span onclick="deletesched('${finder}', '${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? arrayobj[1][arrayobj[0][i]][j].schedule_id+'_sched':arrayobj[1][arrayobj[0][i]][j].merged_id+'_merge'}');" class="customizedspan">Remove</span>
                        `;
                    $(finder).html(wew);
                }
            }
        }
        // pang insert sa course
        function insertcd(arrayobj) {
            for (let i = 0; i < arrayobj[0].length; i++) {
                let wew = '';
                let finder = '';
                for (let j = 0; j < arrayobj[1][arrayobj[0][i]].length; j++) {
                    finder = `#${stringcutter(arrayobj[1][arrayobj[0][i]][j].weekday).toLowerCase()}_${arrayobj[0][i]}`;
                    wew =
                        `<span>${arrayobj[1][arrayobj[0][i]][j].assigned_teacher}</span><br>
                            <span>${arrayobj[1][arrayobj[0][i]][j].subject_code}</span><br>
                            <span>${arrayobj[1][arrayobj[0][i]][j].roomname}</span><br>
                            ${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? '':"<span class='text-success'>"+arrayobj[1][arrayobj[0][i]][j].merged_from+'</span><br>'}
                            ${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined && arrayobj[1][arrayobj[0][i]][j].merged_with !== undefined) ? "<span class='text-primary'>"+arrayobj[1][arrayobj[0][i]][j].merged_with+'</span><br>':''}
                            ${arrayobj[1][arrayobj[0][i]][j].schedule_status === 'Custom' ? '<span style="color: red;">(Customized Sched)</span><br>':''}
                            <span onclick="editsched('${finder}', '${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? arrayobj[1][arrayobj[0][i]][j].schedule_id+'_sched':arrayobj[1][arrayobj[0][i]][j].merged_id+'_merge'}', 'course');" class="customizedspan2 text-info">Edit</span>&nbsp;&nbsp;
                            <span onclick="deletesched('${finder}', '${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? arrayobj[1][arrayobj[0][i]][j].schedule_id+'_sched':arrayobj[1][arrayobj[0][i]][j].merged_id+'_merge'}');" class="customizedspan">Remove</span>
                            `;
                    $(finder).html(wew);
                }
            }
        }
        // pang insert sa room
        function insertrd(arrayobj) {
            for (let i = 0; i < arrayobj[0].length; i++) {
                let wew = '';
                let finder = '';
                for (let j = 0; j < arrayobj[1][arrayobj[0][i]].length; j++) {
                    finder = `#${stringcutter(arrayobj[1][arrayobj[0][i]][j].weekday).toLowerCase()}_${arrayobj[0][i]}`;
                    wew =
                        `<span>${arrayobj[1][arrayobj[0][i]][j].assigned_teacher}</span><br>
                        <span>${arrayobj[1][arrayobj[0][i]][j].subject_code}</span><br>
                        <span>${arrayobj[1][arrayobj[0][i]][j].course}</span><br>
                        ${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? '':"<span class='text-success'>"+arrayobj[1][arrayobj[0][i]][j].merged_from+'</span><br>'}
                        ${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined && arrayobj[1][arrayobj[0][i]][j].merged_with !== undefined) ? "<span class='text-primary'>"+arrayobj[1][arrayobj[0][i]][j].merged_with+'</span><br>':''}
                        ${arrayobj[1][arrayobj[0][i]][j].schedule_status === 'Custom' ? '<span style="color: red;">(Customized Sched)</span><br>':''}
                        <span onclick="editsched('${finder}', '${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? arrayobj[1][arrayobj[0][i]][j].schedule_id+'_sched':arrayobj[1][arrayobj[0][i]][j].merged_id+'_merge'}', 'room');" class="customizedspan2 text-info">Edit</span>&nbsp;&nbsp;
                        <span onclick="deletesched('${finder}', '${(arrayobj[1][arrayobj[0][i]][j].merged_id === undefined || arrayobj[1][arrayobj[0][i]][j].merged_id === null) ? arrayobj[1][arrayobj[0][i]][j].schedule_id+'_sched':arrayobj[1][arrayobj[0][i]][j].merged_id+'_merge'}');" class="customizedspan">Remove</span>
                        `;
                    $(finder).html(wew);
                }
            }
        }

        // para naa taraw colon sa hour ug minute
        function insertcolon(mid) {
            let arraycolon = [];
            mid = mid.split('');
            for (let i = 0; i < mid.length; i++) {
                if (i === 1) {
                    arraycolon.push(mid[i]);
                    arraycolon.push(':');
                } else {
                    arraycolon.push(mid[i]);
                }
            }
            arraycolon = arraycolon.join('');
            return arraycolon;
        }

        function PrintElem(str) {
            let [, other] = str.split('#schedtbody');
            let otherid = `span[id="assigned${other}"]`;
            Popup($(str).html(), $(otherid).html());
        }

        function Popup(data, data2) {
            var mywindow = window.open('', 'Print', 'height=720,width=1280');
            mywindow.document.write('<html><head><title>Print Schedule</title>');
            mywindow.document.write(`<style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            table th, table td {
                border: 1px solid black;
                padding: 5px 0.5rem 0.5rem 5px;
            }
            .mx-auto{
                margin-right:auto!important;
                margin-left:auto!important
            }
            .w-auto{
                width:auto!important
            }
            .table{
                --bs-table-bg:transparent;--bs-table-accent-bg:transparent;--bs-table-striped-color:#212529;--bs-table-striped-bg:rgba(0, 0, 0, 0.05);--bs-table-active-color:#212529;--bs-table-active-bg:rgba(0, 0, 0, 0.1);--bs-table-hover-color:#212529;--bs-table-hover-bg:rgba(0, 0, 0, 0.075);width:100%;margin-bottom:1rem;color:#212529;vertical-align:top;border-color:#dee2e6
            }
            .table-striped>tbody>tr:nth-of-type(odd){
                --bs-table-accent-bg:var(--bs-table-striped-bg);color:var(--bs-table-striped-color)
            }
            #fixed td, #schedthead th{
                padding-right: 1rem;
                padding-bottom: 1rem;
            }
            .text-start{
                text-align:left!important
            }
            span[onclick^='deletesched'], span[onclick^='editsched'] {
                display: none;
            }
            .text-success{
                color: green !important;
            }
            .text-primary{
                color: blue !important;
            }
            </style>`);
            mywindow.document.write(`</head><body><table class="table w-auto" id="table"><thead id="schedthead">
                                <p style="color:black; margin:10px 0">${data2}</p>
                                <tr>
                                    <th class="text-start">Time</th>
                                    <th class="text-start">Mon</th>
                                    <th class="text-start">Tue</th>
                                    <th class="text-start">Wed</th>
                                    <th class="text-start">Thu</th>
                                    <th class="text-start">Fri</th>
                                    <th class="text-start">Sat</th>
                                </tr>
                            </thead>`);
            mywindow.document.write("<tbody id='fixed'>" + data + '</tbody>');
            mywindow.document.write(`</table>`);
            mywindow.document.write('</body></html>');

            mywindow.print();
            //mywindow.close();

            return true;
        }
    </script>
</body>

</html>