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
    <title>Pendings | <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <!-- <link rel="stylesheet" href="../assets/css/datatable.css> -->
    <style>
        #toggle1 {
            background-color: #000814;
            cursor: pointer;
        }

        #subToggle {
            list-style: none;
        }

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
</head>

<body>

    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">

        <?php include('../includes/sidebar.php') ?>
        <div id="page-content-wrapper">
            <div class="container-fluid mt-4 px-4">
                <div class="row">
                    <h1 class="mb-5">Conflicted Schedules</h1>
                    <table class="table table-striped" id="adminPendingTable">
                        <!-- kuhaon tanan mga processing na mga schedules dapat -->
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" colspan="2">Time</th>
                                <th class="text-center">Weekday</th>
                                <th class="text-center">Room</th>
                                <th class="text-center">Subject Code</th>
                                <th class="text-center">Course</th>
                                <th class="text-center">Teacher</th>
                                <th class="text-center">Trimester</th>
                                <th class="text-center">S.Y</th>
                                <th class="text-center">Proposed by</th>
                                <th class="text-center" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($_SESSION['permission'] == '1') {
                                $getscheds = executeNonQuery($connect, "SELECT *, scheduled_classes.semester as schedsem, CONCAT(members.member_salut, ' ', members.member_last) as assignername, subjects.department as dept FROM scheduled_classes JOIN members JOIN subjects JOIN rooms WHERE scheduled_classes.assigner = members.member_id and scheduled_classes.room = rooms.room_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.conflict_status = 'conflicted' and members.member_superiority = 'admin' order by scheduled_classes.schedule_id desc;");
                            } else {
                                $getscheds = executeNonQuery($connect, "SELECT *, scheduled_classes.semester as schedsem, CONCAT(members.member_salut, ' ', members.member_last) as assignername, subjects.department as dept FROM scheduled_classes JOIN members JOIN subjects JOIN rooms WHERE scheduled_classes.assigner = members.member_id and scheduled_classes.room = rooms.room_id and subjects.department = '$dept' and scheduled_classes.subject = subjects.subject_id and scheduled_classes.conflict_status = 'conflicted' and members.member_superiority = 'admin' order by scheduled_classes.schedule_id desc;");
                            }
                            $i = 0;
                            while ($rows = fetchAssoc($connect, $getscheds)) {
                                if ($rows['teacher'] === '0') {
                                    $teacherresult = 'To be assigned';
                                    // kung to be assigned ang teacher dapat sa td naay select didto na mamili ug teacher
                            ?>
                                    <tr id="tbodyrow_<?php echo $i ?>" <?php echo $rows['conflict_status'] === 'conflicted' ? 'style="background:red;"' : '' ?>>
                                        <td id="conflictchecking_<?php echo $i ?>" class="d-none"><?php echo $rows['start_time'] . '_/' . $rows['end_time'] . '_/' . $rows['teacher'] . '_/' . $rows['weekday'] . '_/' . $rows['room_id'] . '_/' . $rows['course'] . '_/' . $rows['schedsem'] . '_/' . $rows['schoolyear'] . '_/' . $rows['subject'] ?></td>
                                        <td class="text-center" id="start_<?php echo $i ?>"><?php echo $rows['start_time'] ?></td>
                                        <td class="text-center" id="end_<?php echo $i ?>"><?php echo $rows['end_time'] ?></td>
                                        <td class="text-center"><?php echo $rows['weekday'] ?></td>
                                        <td class="text-center"><?php echo $rows['room_number'] ?></td>
                                        <td class="text-center"><?php echo $rows['subject_code'] ?></td>
                                        <td class="text-center"><?php echo $rows['course'] ?></td>
                                        <td class="text-center" id="specifyteacher_<?php echo $i ?>">
                                            <!-- dinhi magselect ug teacher-->
                                            <select name="selecteacher" id="selectteacher_<?php echo $i ?>" class="form-select">
                                                <option value="0" selected disabled><?php echo $teacherresult ?></option>
                                                <?php $faculties = executeNonQuery($connect, "SELECT member_id, CONCAT(member_salut, ' ',member_last) as teacher FROM members where member_superiority != 'student' order by member_salut, member_last");
                                                while ($rowfaculties = fetchAssoc($connect, $faculties)) {
                                                ?>
                                                    <option value="<?php echo $rowfaculties['member_id'] ?>"><?php echo $rowfaculties['teacher'] ?></option>
                                                <?php }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="text-center"><?php echo ($rows['schedsem'] === '1' ? '1st' : ($rows['schedsem'] === '2' ? '2nd' : '3rd')) ?></td>
                                        <td class="text-center"><?php echo $rows['schoolyear'] ?></td>
                                        <td class="text-center"><?php echo $rows['member_salut'] . ' ' . $rows['member_last'] ?></td>
                                        <td class="text-center"><button class="btn btn-sm btn-success" onclick="approve(<?php echo $rows['schedule_id'] ?>,<?php echo $i ?>);"><i class="fa-solid fa-check"></i></button></td>
                                        <td class="text-center"><button class="btn btn-sm btn-danger" onclick="disapprove(<?php echo $rows['schedule_id'] ?>, <?php echo $i ?>);"><i class="fas fa-thumbs-down"></i></button></td>
                                    </tr>
                                <?php
                                } else {
                                    $teacher = $rows['teacher'];
                                    $getteacher = executeNonQuery($connect, "SELECT CONCAT(member_salut, ' ',member_last) as assignedteacher FROM members WHERE member_id = '$teacher'");
                                    $resultteacher = fetchAssoc($connect, $getteacher);
                                    $teacherresult = $resultteacher['assignedteacher']; ?>
                                    <tr id="tbodyrow_<?php echo $i ?>" <?php echo $rows['conflict_status'] === 'conflicted' ? 'style="background:red;"' : '' ?>>
                                        <td id="conflictchecking_<?php echo $i ?>" class="d-none"><?php echo $rows['start_time'] . '_/' . $rows['end_time'] . '_/' . $rows['teacher'] . '_/' . $rows['weekday'] . '_/' . $rows['room_id'] . '_/' . $rows['course'] . '_/' . $rows['schedsem'] . '_/' . $rows['schoolyear'] . '_/' . $rows['subject'] ?></td>
                                        <td class="text-center" id="start_<?php echo $i ?>"><?php echo $rows['start_time'] ?></td>
                                        <td class="text-center" id="end_<?php echo $i ?>"><?php echo $rows['end_time'] ?></td>
                                        <td class="text-center"><?php echo $rows['weekday'] ?></td>
                                        <td class="text-center"><?php echo $rows['room_number'] ?></td>
                                        <td class="text-center"><?php echo $rows['subject_code'] ?></td>
                                        <td class="text-center"><?php echo $rows['course'] ?></td>
                                        <td class="text-center"><?php echo $teacherresult ?></td>
                                        <td class="text-center"><?php echo ($rows['schedsem'] === '1' ? '1st' : ($rows['schedsem'] === '2' ? '2nd' : '3rd')) ?></td>
                                        <td class="text-center"><?php echo $rows['schoolyear'] ?></td>
                                        <td class="text-center"><?php echo $rows['member_salut'] . ' ' . $rows['member_last'] ?></td>
                                        <td class="text-center"><button class="btn btn-sm btn-success" onclick="approve(<?php echo $rows['schedule_id'] ?>,<?php echo $i ?>);"><i class="fa-solid fa-check"></i></button></td>
                                        <td class="text-center"><button class="btn btn-sm btn-danger" onclick="disapprove(<?php echo $rows['schedule_id'] ?>, <?php echo $i ?>);"><i class="fas fa-thumbs-down"></i></button></td>
                                    </tr>
                                <?php } ?>
                            <?php
                                $i++;
                            } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/popper.js"></script>
    <script src="../assets/scripts/fontawesome.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <!-- <script src="../assets/scripts/datatable.js"></script> -->
    <script>
        var subjectcount = {};
        let timecap = 0;

        function timetoseconds(time) {
            let timestring = time.toString();
            let seconds = 0;
            if (timestring.length >= 3) {
                seconds += Math.floor(time / 100) * 60 * 60;
                seconds += (time % 100) * 60;
            } else {
                seconds += (time % 100) * 60;
            }
            return seconds;
        }

        function timedifference(end, start) {
            return timetoseconds(Number(end)) - timetoseconds(Number(start));
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

        function approve(schedid, rowid) {
            Swal.fire({
                title: 'Do you want to save the changes?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Save',
                denyButtonText: `Don't save`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // alert(schedid);
                    let checking = '#conflictchecking_' + rowid;
                    // $rows['start_time'] . '_/' . $rows['end_time'] . '_/' . $rows['teacher'] . '_/' . $rows['weekday'] . '_/' . $rows['room_id'] . '_/' . $rows['course'] . '_/' . $rows['schedsem'] . '_/' . $rows['schoolyear']
                    let [start, end, maestra, certainday, room, klasehanan, sem, sy, subject] = $(checking).html().split('_/');
                    let teacherselectid = '#specifyteacher_' + rowid;
                    if (maestra === '0') {
                        // alert('Please assign a teacher!');
                        setTimeout(() => {
                            setInterval(() => {
                                $(teacherselectid).addClass('conflictdetected');

                            }, 0);
                            $(teacherselectid).removeClass('conflictdetected');
                        }, 0);
                        Swal.fire({
                            icon: 'info',
                            title: 'Please assign a teacher.',
                            timer: 1500
                        });
                    } else {
                        start = timeconvertion(Number(start));
                        end = timeconvertion(Number(end));
                        let timeofsubject = timedifference(end, start);
                        maestra = Number(maestra);
                        loopdatabase(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject, room);
                    }
                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
                }
            })
        }

        function loopdatabase(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject, room) {
            $.getJSON(`../queries/getconflicts.php?getconflicts=${sy}&semester=${sem}&weekday=${certainday}&room=${room}&teacher=${maestra}&course=${klasehanan}`, function(data) {
                if (data[0] === 'error') {
                    conflictName =
                        `Swal.fire({
                            icon: 'error',
                            title: 'Same time conflict',
                            showConfirmButton: false,
                            timer: 1500
                        });`;
                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}_/${conflictName}`
                } else {
                    for (let i = 0; i < data.length; i++) {
                        let conflictName = '';
                        if (data[i].length === 0) {
                            continue;
                        } else {
                            if (checkconflicts(i, data, start, end, subject) === 0) {
                                conflictName =
                                    `Swal.fire({
                                    icon: 'error',
                                    title: 'Room already occupied!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });`;
                                document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}_/${conflictName}`;
                            } else if (checkconflicts(i, data, start, end, subject) === 1) {
                                conflictName =
                                    `Swal.fire({
                                    icon: 'error',
                                    title: 'Teacher already assigned the stipulated schedule!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });`;
                                document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}_/${conflictName}`;
                            } else if (checkconflicts(i, data, start, end, subject) === 2 || checkconflicts(i, data, start, end, subject) === 3) {
                                conflictName =
                                    `Swal.fire({
                                    icon: 'error',
                                    title: 'Course already assigned the stipulated schedule!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });`;
                                document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}_/${conflictName}`;
                            } else if (checkconflicts(i, data, start, end, subject) === 'samesubject') {
                                conflictName =
                                    `Swal.fire({
                                    icon: 'info',
                                    title: 'Subject already assigned the stipulated schedule!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });`;
                                document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}_/${conflictName}`;
                            } else if (checkconflicts(i, data, start, end, subject) === 'noconflict') {
                                continue;
                            }
                        }
                    }
                    databasesubjects(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject);
                }
            });
        }

        /* function loopdatabase2(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject) {
            $.getJSON(`../queries/getconflicts.php?getconflictsapr=${sy}&semester=${sem}&weekday=${certainday}&schedule_process=approved`, function(data2) {
                if (data2.length === 0) {
                    databasesubjects(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject);
                } else if (data2.length > 0) {
                    for (let i = 0; i < data2.length; i++) {
                        let starttimedb = timeconvertion(Number(data2[i].start_time));
                        let endtimedb = timeconvertion(Number(data2[i].end_time));
                        let maestradb = Number(data2[i].teacher);
                        if (maestradb === maestra) {
                            if ((data2[i].course === klasehanan) || data2[i].course != klasehanan) {
                                if (starttimedb === start && endtimedb === end) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}`;
                                    return;
                                } else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}`;
                                    return;
                                } else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}`;
                                    return;
                                } else if ((end <= starttimedb && end < endtimedb && starttimedb < endtimedb) || (endtimedb <= start && endtimedb < end && start < end)) {
                                    continue;
                                }
                            }
                        } else if (maestradb != maestra) {
                            if (klasehanan === data2[i].course) {
                                if (starttimedb === start && endtimedb === end) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}`;
                                    return;
                                }
                                // s vse ve or vs sve e
                                else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}`;
                                    return;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedadmin=${schedid}`;
                                    return;
                                }
                                // s evs ve or vs ves e
                                else if ((end <= starttimedb && end < endtimedb && starttimedb < endtimedb) || (endtimedb <= start && endtimedb < end && start < end)) {
                                    continue;
                                }
                            } else if (klasehanan != data2[i].course) {
                                continue;
                            }
                        }
                    }
                    databasesubjects(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject);
                }
            });
        }
        */

        function goodstogo(sched, teach, row) {
            $.ajax({
                url: "../functions/schedsprocessing.php",
                method: "POST",
                data: {
                    approve: sched,
                    teacher: teach
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
            let datahide = '#tbodyrow_' + row;
            deletedrow(datahide);

        }

        function disapprove(schedid, row) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "../functions/schedsprocessing.php",
                        method: "POST",
                        data: {
                            disapprove: schedid
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
                    let datahide = '#tbodyrow_' + row;
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

        function databasesubjects(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject) {
            let timetoadd = timeofsubject;
            $.getJSON(`../queries/getconflicts.php?timesubjectsconfirm=${sy}&semester=${sem}&course=${klasehanan}&subject=${subject}`, function(data) {
                // console.log(data);
                if (data.length === 0) {
                    goodstogo(schedid, maestra, rowid);
                } else {
                    $.getJSON(`../queries/getunit.php?subject=${subject}`, function(data2) {
                        timecap = (Number(data2[0]) * (4 * 60 * 60)) / 3;
                    });
                    let counter = 0;
                    for (let i = 0; i < data.length; i++) {
                        let dbstart = timeconvertion(Number(data[i].start_time));
                        let dbend = timeconvertion(Number(data[i].end_time));
                        counter += timedifference(dbend, dbstart);
                        if (counter > timecap) {
                            Swal.fire({
                                title: 'Subject of this course/section already reached the time limit!',
                                text: "Do you want to proceed?",
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, proceed it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    goodstogo(schedid, maestra, rowid);
                                } else {
                                    subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] = 0;
                                }
                            });
                            return;
                        }
                    }
                    counter += timetoadd;
                    if (counter > timecap) {
                        Swal.fire({
                            title: 'Subject of this course/section will reach its time limit.',
                            text: "Do you want to proceed?",
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, proceed it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                goodstogo(schedid, maestra, rowid);
                            }
                        })
                        return;
                    } else {
                        // alert('walay gasgas');
                        goodstogo(schedid, maestra, rowid);
                    }
                }
            });
        }

        $(document).ready(function() {
            // let table = new DataTable('#adminPendingTable');
            // alert($('tbody tr').length);
            // time fixing
            if ($('tbody tr').length === 0) {
                $('tbody').html('<div class="position-absolute mx-auto"><p class="fw-bold h4 text-center text-dark">No conflicted schedules.</p></div>');
            } else {
                for (let i = 0; i < $('tbody tr').length; i++) {
                    let start = $("td[id='start_" + i + "']").html();
                    if (start <= 11259 && start >= 11200) {
                        start = start.toString();
                        let newstart = (start - 10000).toString();
                        let x = start[0];
                        newstart = insertcolon(newstart);
                        if (x === '2') {
                            x = 'pm';
                        } else {
                            x = 'am';
                        }
                        $("td[id='start_" + i + "']").html(`${newstart}${x}`);
                        $("td[id='start_" + i + "']").removeClass('d-none');
                    } else {
                        start = start.toString();
                        let x = start[0];
                        start = start.substring(1);
                        start = insertcolon(start);
                        if (x === '2') {
                            x = 'pm';
                        } else {
                            x = 'am';
                        }
                        $("td[id='start_" + i + "']").html(`${start}${x}`);
                        $("td[id='start_" + i + "']").removeClass('d-none');
                    }
                    let end = $("td[id='end_" + i + "']").html();
                    if (end <= 11259 && end >= 11200) {
                        end = end.toString();
                        let newend = (end - 10000).toString();
                        let x = end[0];
                        newend = insertcolon(newend);
                        if (x === '2') {
                            x = 'pm';
                        } else {
                            x = 'am';
                        }
                        $("td[id='end_" + i + "']").html(`${newend}${x}`);
                        $("td[id='end_" + i + "']").removeClass('d-none');
                    } else {
                        end = end.toString();
                        let x = end[0];
                        end = end.substring(1);
                        end = insertcolon(end);
                        if (x === '2') {
                            x = 'pm';
                        } else {
                            x = 'am';
                        }
                        $("td[id='end_" + i + "']").html(`${end}${x}`);
                        $("td[id='end_" + i + "']").removeClass('d-none');
                    }
                }
            }

            $("select[id^='selectteacher_']").change(function() {
                let [, id] = $(this).attr('id').split('_');
                let checking = '#conflictchecking_' + id;

                // $rows['start_time'] . '_/' . $rows['end_time'] . '_/' . $rows['teacher'] . '_/' . $rows['weekday'] . '_/' . $rows['room_id'] . '_/' . $rows['course'] . '_/' . $rows['schedsem'] . '_/' . $rows['schoolyear']
                let [start, end, teacher, weekday, rows, course, schedsem, schoolyear] = $(checking).html().split('_/');
                teacher = $(this).val();
                let array = [start, end, teacher, weekday, rows, course, schedsem, schoolyear];
                $(checking).html(array.join('_/'));
            });
        });

        <?php
        if (isset($_SESSION['conflicteddata'])) {
            $msg = $_SESSION['conflicteddata'];
            echo "$msg";
            unset($_SESSION['conflicteddata']);
        }
        ?>

        function checkconflicts(counts, data, start, end, subject) {
            for (let i = 0; i < data[counts].length; i++) {
                let starttimedb = Number(data[counts][i].start_time);
                let endtimedb = Number(data[counts][i].end_time);
                starttimedb = timeconvertion(starttimedb);
                endtimedb = timeconvertion(endtimedb);
                if ((starttimedb < start && start < end && end < endtimedb) || (start < starttimedb && starttimedb < endtimedb && endtimedb < end) || (starttimedb < start && start < endtimedb && endtimedb < end) || (start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb === start && endtimedb === end && start === starttimedb && end === endtimedb) || (starttimedb === start && start < endtimedb && endtimedb < end) || (start === starttimedb && starttimedb < end && end < endtimedb) || (start < starttimedb && starttimedb < end && end === endtimedb) || (starttimedb < start && start < endtimedb && endtimedb === end)) {
                    return counts;
                }
            }
            return 'noconflict';
        }
    </script>
</body>

</html>