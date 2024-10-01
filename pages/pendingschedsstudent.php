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
                    <h1 class="mb-5">Pending Schedules</h1>
                    <table class="table table-striped">
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
                            $getscheds = executeNonQuery($connect, "SELECT *, scheduled_classes.semester as schedsem, CONCAT(members.member_salut, ' ', members.member_last) as assignername, subjects.department as dept FROM scheduled_classes JOIN members JOIN subjects JOIN rooms WHERE scheduled_classes.assigner = members.member_id and scheduled_classes.room = rooms.room_id and subjects.department = '$dept' and scheduled_classes.subject = subjects.subject_id and scheduled_classes.schedule_process = 'processing' and members.member_superiority = 'student' order by scheduled_classes.schedule_id desc;");
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
                                                <?php $getself = executeNonQuery($connect, "SELECT member_id, CONCAT(member_salut, ' ',member_last) as teacher FROM members where member_id = '$id'");
                                                $selfrow = fetchAssoc($connect, $getself);
                                                ?>
                                                <option value="<?php echo $selfrow['member_id'] ?>"><?php echo $selfrow['teacher'] ?></option>
                                                <?php $faculties = executeNonQuery($connect, "SELECT member_id, CONCAT(member_salut, ' ',member_last) as teacher FROM members where member_superiority = 'faculty' order by member_salut, member_last");
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
    <script>
        var subjectcount = {};
        const minuteconvertion = (time) => {
            time = time.toString().split('');
            let partial = time[time.length - 2] + time[time.length - 1];
            let stringednum = '';
            if (partial === '30') {
                partial = '50';
            } else {
                partial = '00';
            }
            for (let i = 0; i < time.length - 2; i++) {
                stringednum += time[i];
            }
            let timeresult = stringednum + partial;
            return timeresult;
        }

        const timedifference = (end, start) => {
            return minuteconvertion(end) - minuteconvertion(start);
        }

        const timeconvertion = (time) => {
            if (time >= 11200 && time <= 11259) {
                time -= 1200;
            } else if (time >= 20100 && time <= 21159) {
                time -= 10000;
                time += 1200;
            } else if (time >= 21200 && time < 21259) {
                time -= 10000
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
            $.getJSON(`../queries/getconflicts.php?getconflictsaprs=${sy}&semester=${sem}&weekday=${certainday}&room=${room}&schedule_process=approved`, function(data) {
                if (data.length === 0) { // kung walay sulod then loop napud nga dili kapareho ug room
                    loopdatabase2(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject)
                } else if (data.length > 0) { // kung naay sulod then loop the data that got from database
                    for (let i = 0; i < data.length; i++) {
                        let starttimedb = timeconvertion(Number(data[i].start_time));
                        let endtimedb = timeconvertion(Number(data[i].end_time));
                        let maestradb = Number(data[i].teacher);
                        if (maestradb === maestra) {
                            if ((data[i].course === klasehanan) || data[i].course != klasehanan) {
                                if (starttimedb === start && endtimedb === end) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                } else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                } else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                } else if ((end <= starttimedb && end < endtimedb && starttimedb < endtimedb) || (endtimedb <= start && endtimedb < end && start < end)) {

                                    continue;
                                }
                            }
                        } else if (maestradb != maestra) {
                            if (klasehanan === data[i].course) {
                                if (starttimedb === start && endtimedb === end) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s vse ve or vs sve e
                                else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s evs ve or vs ves e
                                else if ((end <= starttimedb && end < endtimedb && starttimedb < endtimedb) || (endtimedb <= start && endtimedb < end && start < end)) {
                                    continue;
                                }
                            } else if (klasehanan != data[i].course) {
                                if (starttimedb === start && endtimedb === end) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s vse ve or vs sve e
                                else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s evs ve or vs ves e
                                else if ((end <= starttimedb && end < endtimedb && starttimedb < endtimedb) || (endtimedb <= start && endtimedb < end && start < end)) {
                                    continue;
                                }
                            }
                        }
                    }
                    databasesubjects(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject);
                }
            });
        }

        function loopdatabase2(sy, sem, certainday, maestra, klasehanan, schedid, rowid, start, end, timeofsubject, subject) {
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
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                } else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                } else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                } else if ((end <= starttimedb && end < endtimedb && starttimedb < endtimedb) || (endtimedb <= start && endtimedb < end && start < end)) {
                                    continue;
                                }
                            }
                        } else if (maestradb != maestra) {
                            if (klasehanan === data2[i].course) {
                                if (starttimedb === start && endtimedb === end) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s vse ve or vs sve e
                                else if ((start < starttimedb && starttimedb < end && end < endtimedb) || (starttimedb < start && start < endtimedb && endtimedb < end)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
                                    return;
                                }
                                // s vs ve e or vs s e ve
                                else if ((start <= starttimedb && start < endtimedb && end >= endtimedb && end > starttimedb) || (starttimedb <= start && starttimedb < end && endtimedb >= end && endtimedb > start)) {
                                    document.location.href = `../functions/schedsprocessing.php?conflictedstudent=${schedid}`;
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
                confirmButtonText: 'Yes, unapprove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "../functions/schedsprocessing.php",
                        method: "POST",
                        data: {
                            disapprove: schedid
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
                    // alert('entering databasesubjects no data, now processing');
                    goodstogo(schedid, maestra, rowid);
                } else {
                    let counter = 0;
                    for (let i = 0; i < data.length; i++) {
                        let dbstart = timeconvertion(Number(data[i].start_time));
                        let dbend = timeconvertion(Number(data[i].end_time));
                        counter += timedifference(dbend, dbstart);
                        if (counter > 400) {
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
                                    subjectcount[sy + '_' + sem + '_' + klasehanan + '_' + subject] -= timedifference(end, start);
                                }
                            });
                            return;
                        }
                    }
                    counter += timetoadd;
                    if (counter > 400) {
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
            // alert($('tbody tr').length);
            // time fixing
            if ($('tbody tr').length === 0) {
                $('tbody').html('<div class="position-absolute mx-auto"><p class="fw-bold h4 text-center text-dark">No pending schedules.</p></div>');
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
            echo "Swal.fire({icon:'error', title:'$msg', showConfirmButton: false, timer:1500});";
            unset($_SESSION['conflicteddata']);
        }
        ?>
    </script>
</body>

</html>