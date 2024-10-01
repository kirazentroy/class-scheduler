<?php
// program=${$('#courses').val()}&trimester=${$('#semester').val()}&sy=${$('#schoolyear').val()}
include('../functions/db_connect.php');
include('../functions/sessionstart.php');

function timeconverting($timestart, $timeend)
{
    $timestart .= "";
    if ($timestart[0] === '1') {
        $timestartfixed = $timestart[1] . $timestart[2] . ':' . $timestart[3] . $timestart[4] . 'am';
    } else {
        $timestartfixed = $timestart[1] . $timestart[2] . ':' . $timestart[3] . $timestart[4] . 'pm';
    }

    $timeend .= "";
    if ($timeend[0] === '1') {
        $timeendfixed = $timeend[1] . $timeend[2] . ':' . $timeend[3] . $timeend[4] . 'am';
    } else {
        $timeendfixed = $timeend[1] . $timeend[2] . ':' . $timeend[3] . $timeend[4] . 'pm';
    }
    return $timestartfixed . '-' . $timeendfixed;
}
function returnsemester($sem)
{
    $sem .= "";
    if ($sem === "1") {
        return $sem . 'st';
    } else if ($sem === "2") {
        return $sem . 'nd';
    } else if ($sem === "3") {
        return $sem . 'rd';
    }
}

function weekdayconvertion($weekday)
{
    if ($weekday === "Thursday") {
        return $weekday[0] . 'H';
    } else {
        return $weekday[0];
    }
}

function notsamesched($array)
{
    $str = "";
    if (sizeof($array) > 1) {
        $str = "<span>" . $array[0] . "</span>";
        for ($i = 1; $i < sizeof($array); $i++) {
            $str .=
                "<br><span>" . $array[$i] . "</span>";
        }
    } else {
        $str = "<span>" . $array[0] . "</span>";
    }
    return $str;
}
function notsameschedday($array)
{
    $str = "";
    if (sizeof($array) > 1) {
        $str = "<span>" . $array[0] . "</span>";
        for ($i = 1; $i < sizeof($array); $i++) {
            $str .= "<br><span>" . $array[$i] . "</span>";
        }
    } else {
        $str = "<span>" . $array[0] . "</span>";
    }
    return $str;
}

function splitter($array)
{
    $str = "";
}

if (!isset($_SESSION['id'])) {
    $name = "Anonymous!";
} else if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    $nameVar = executeNonQuery($connect, "SELECT concat(member_salut, ' ', member_first) as name from members WHERE member_id = '$id'");
    $name = fetchAssoc($connect, $nameVar)['name'] . '!';
}
if (isset($_GET['program']) && isset($_GET['trimester']) && isset($_GET['sy'])) {

    $course = $_GET['program'];
    $sem = $_GET['trimester'];
    $sy = $_GET['sy'];

    $newprogramcode = explode("(", explode("-", $course)[0])[0];
    // echo $newprogramcode;
    $descriptionprogramVar = executeNonQuery($connect, "SELECT course_name from courses where course_code = '$newprogramcode'");
    $descriptionprogram = fetchAssoc($connect, $descriptionprogramVar)['course_name'];

    // Monday
    $queryMonday = executeNonQuery($connect, "SELECT scheduled_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM scheduled_classes join members join subjects join rooms WHERE scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.course = '$course' and scheduled_classes.semester = '$sem' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.conflict_status != 'conflicted' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.weekday = 'Monday' order by subjects.subject_code");
    $queryMonday2 = executeNonQuery($connect, "SELECT merged_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM merged_classes join members join subjects join rooms WHERE merged_classes.teacher = members.member_id and merged_classes.subject = subjects.subject_id and merged_classes.room = rooms.room_id and merged_classes.course = '$course' and merged_classes.semester = '$sem' and merged_classes.schoolyear = '$sy' and merged_classes.weekday = 'Monday' order by subjects.subject_code");

    // Tuesday
    $queryTuesday = executeNonQuery($connect, "SELECT scheduled_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM scheduled_classes join members join subjects join rooms WHERE scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.course = '$course' and scheduled_classes.semester = '$sem' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.conflict_status != 'conflicted' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.weekday = 'Tuesday' order by subjects.subject_code");
    $queryTuesday2 = executeNonQuery($connect, "SELECT merged_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM merged_classes join members join subjects join rooms WHERE merged_classes.teacher = members.member_id and merged_classes.subject = subjects.subject_id and merged_classes.room = rooms.room_id and merged_classes.course = '$course' and merged_classes.semester = '$sem' and merged_classes.schoolyear = '$sy' and merged_classes.weekday = 'Tuesday' order by subjects.subject_code");

    // Wednesday
    $queryWednesday = executeNonQuery($connect, "SELECT scheduled_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM scheduled_classes join members join subjects join rooms WHERE scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.course = '$course' and scheduled_classes.semester = '$sem' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.conflict_status != 'conflicted' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.weekday = 'Wednesday' order by subjects.subject_code");
    $queryWednesday2 = executeNonQuery($connect, "SELECT merged_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM merged_classes join members join subjects join rooms WHERE merged_classes.teacher = members.member_id and merged_classes.subject = subjects.subject_id and merged_classes.room = rooms.room_id and merged_classes.course = '$course' and merged_classes.semester = '$sem' and merged_classes.schoolyear = '$sy' and merged_classes.weekday = 'Wednesday' order by subjects.subject_code");

    // Thursday
    $queryThursday = executeNonQuery($connect, "SELECT scheduled_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM scheduled_classes join members join subjects join rooms WHERE scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.course = '$course' and scheduled_classes.semester = '$sem' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.conflict_status != 'conflicted' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.weekday = 'Thursday' order by subjects.subject_code");
    $queryThursday2 = executeNonQuery($connect, "SELECT merged_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM merged_classes join members join subjects join rooms WHERE merged_classes.teacher = members.member_id and merged_classes.subject = subjects.subject_id and merged_classes.room = rooms.room_id and merged_classes.course = '$course' and merged_classes.semester = '$sem' and merged_classes.schoolyear = '$sy' and merged_classes.weekday = 'Thursday' order by subjects.subject_code");

    // Friday
    $queryFriday = executeNonQuery($connect, "SELECT scheduled_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM scheduled_classes join members join subjects join rooms WHERE scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.course = '$course' and scheduled_classes.semester = '$sem' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.conflict_status != 'conflicted' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.weekday = 'Friday' order by subjects.subject_code");
    $queryFriday2 = executeNonQuery($connect, "SELECT merged_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM merged_classes join members join subjects join rooms WHERE merged_classes.teacher = members.member_id and merged_classes.subject = subjects.subject_id and merged_classes.room = rooms.room_id and merged_classes.course = '$course' and merged_classes.semester = '$sem' and merged_classes.schoolyear = '$sy' and merged_classes.weekday = 'Friday' order by subjects.subject_code");

    // Saturday
    $querySaturday = executeNonQuery($connect, "SELECT scheduled_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM scheduled_classes join members join subjects join rooms WHERE scheduled_classes.teacher = members.member_id and scheduled_classes.subject = subjects.subject_id and scheduled_classes.room = rooms.room_id and scheduled_classes.course = '$course' and scheduled_classes.semester = '$sem' and scheduled_classes.schoolyear = '$sy' and scheduled_classes.conflict_status != 'conflicted' and scheduled_classes.schedule_process = 'approved' and scheduled_classes.weekday = 'Saturday' order by subjects.subject_code");
    $querySaturday2 = executeNonQuery($connect, "SELECT merged_classes.*, concat(members.member_salut, ' ', members.member_last) as teachername, members.member_first as firstname, subjects.subject_code, rooms.room_number FROM merged_classes join members join subjects join rooms WHERE merged_classes.teacher = members.member_id and merged_classes.subject = subjects.subject_id and merged_classes.room = rooms.room_id and merged_classes.course = '$course' and merged_classes.semester = '$sem' and merged_classes.schoolyear = '$sy' and merged_classes.weekday = 'Saturday' order by subjects.subject_code");
    $resultArr = [];

    if (numRows($connect, $queryMonday) > 0 || numRows($connect, $queryMonday2) > 0 || numRows($connect, $queryTuesday) > 0 || numRows($connect, $queryTuesday2) > 0 || numRows($connect, $queryWednesday) > 0 || numRows($connect, $queryWednesday2) > 0 || numRows($connect, $queryThursday) > 0 || numRows($connect, $queryThursday2) > 0 || numRows($connect, $queryFriday) > 0 || numRows($connect, $queryFriday2) > 0 || numRows($connect, $querySaturday) > 0 || numRows($connect, $querySaturday2) > 0) {
        while ($row = fetchAssoc($connect, $queryMonday)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryMonday2)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course from scheduled_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryTuesday)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryTuesday2)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course from scheduled_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryWednesday)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryWednesday2)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course from scheduled_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryThursday)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryThursday2)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course from scheduled_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryFriday)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $queryFriday2)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course from scheduled_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $querySaturday)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course FROM merged_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
        while ($row = fetchAssoc($connect, $querySaturday2)) {
            $schedid = $row['schedule_id'];
            $querymerged = executeNonQuery($connect, "SELECT course from scheduled_classes where schedule_id = '$schedid'");
            $arraymergedwith = [];
            while ($row1 = fetchAssoc($connect, $querymerged)) {
                array_push($arraymergedwith, $row1['course']);
            }
            $row['mergingarray'] = $arraymergedwith;
            array_push($resultArr, $row);
        }
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $_GET['program'] . ' ' . returnsemester($_GET['trimester']) . ' ' . $_GET['sy'] ?> Organized Schedule</title>
            <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
            <link rel="stylesheet" href="../assets/css/bootstrap.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
            <style>
                table th,
                table td {
                    border: 1px solid black;
                }
            </style>
        </head>

        <body class="d-flex justify-content-center align-items-center text-start px-auto">
            <div class="container text-center mt-5">
                <div class="text-start">
                    <p class="fw-bold text-dark">Andres Soriano Colleges of Bislig Scheduler</p>
                    <p class="fw-bold text-dark">Program: <?= $descriptionprogram ?></p>
                    <p class="fw-bold text-dark">Section: <?= $_GET['program'] ?></p>
                    <p class="fw-bold text-dark">Trimester: <?= returnsemester($_GET['trimester']) ?></p>
                    <p class="fw-bold text-dark">S.Y: <?= $_GET['sy'] ?></p>
                </div>
                <div class="text-start">
                    <table class="table table-striped" id="organizedscheds">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Instructor</th>
                                <th>Day/Time</th>
                                <th>Room</th>
                                <th>Fused</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $filteredcourse = [];
                            for ($i = 0; $i < sizeof($resultArr); $i++) {
                                if (!in_array($resultArr[$i]['subject_code'], $filteredcourse)) {
                                    array_push($filteredcourse, $resultArr[$i]['subject_code']);
                                }
                            }
                            $filteredteachers = [];
                            $filteredday = [];
                            $filteredroom = [];
                            $filteredmerged = [];
                            $empty = [];
                            for ($i = 0; $i < sizeof($filteredcourse); $i++) {
                                array_push($filteredteachers, $empty);
                                array_push($filteredday, $empty);
                                array_push($filteredroom, $empty);
                                array_push($filteredmerged, $empty);
                            }

                            for ($i = 0; $i < sizeof($filteredcourse); $i++) {
                                $fcourse = $filteredcourse[$i];
                                for ($j = 0; $j < sizeof($resultArr); $j++) {
                                    if ($fcourse === $resultArr[$j]['subject_code']) {
                                        if (!in_array($resultArr[$j]['teachername'] . ', ' . $resultArr[$j]['firstname'][0], $filteredteachers[$i])) {
                                            array_push($filteredteachers[$i], $resultArr[$j]['teachername'] . ', ' . $resultArr[$j]['firstname'][0]);
                                        }
                                        if (!in_array(weekdayconvertion($resultArr[$j]['weekday']) . ' ' . timeconverting($resultArr[$j]['start_time'], $resultArr[$j]['end_time']), $filteredday[$i])) {
                                            array_push($filteredday[$i], weekdayconvertion($resultArr[$j]['weekday']) . ' ' . timeconverting($resultArr[$j]['start_time'], $resultArr[$j]['end_time']));
                                        }
                                        if (!in_array($resultArr[$j]['room_number'], $filteredroom[$i])) {
                                            array_push($filteredroom[$i], $resultArr[$j]['room_number']);
                                        }
                                        for ($l = 0; $l < sizeof($resultArr[$j]['mergingarray']); $l++) {
                                            if (!in_array($resultArr[$j]['mergingarray'][$l], $filteredmerged[$i])) {
                                                array_push($filteredmerged[$i], $resultArr[$j]['mergingarray'][$l]);
                                            }
                                        }
                                    }
                                }
                            }

                            $filteredschedule = [$filteredcourse, $filteredteachers, $filteredday, $filteredroom, $filteredmerged];

                            for ($i = 0; $i < sizeof($filteredcourse); $i++) {
                            ?>
                                <tr class="align-items-center">
                                    <td><?= $filteredschedule[0][$i] ?></td>
                                    <td><?= notsamesched($filteredschedule[1][$i]); ?></td>
                                    <td><?= notsameschedday($filteredschedule[2][$i]); ?></td>
                                    <td><?= notsamesched($filteredschedule[3][$i]); ?></td>
                                    <td><?= notsamesched($filteredschedule[4][$i]); ?></td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>

                    <button class="btn btn-info btn-sm text-center" onclick="printschedule(this);">Print</button>
                </div>

            </div>
        </body>
        <script src="../assets/jquery/jquery.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
        <script>
            const printschedule = (id) => {
                setTimeout(() => {
                    setInterval(() => {
                        $(id).removeClass('d-none');
                    }, 2000);
                    $(id).addClass('d-none');
                    window.print();
                }, 0);
            }

            $(document).ready(function() {
                // $('#organizedscheds').dataTable();
            });
        </script>

        </html>
    <?php } else { ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Organized Schedule | <?php include('../includes/title.php') ?></title>
            <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
            <link rel="stylesheet" href="../assets/css/bootstrap.css">
            <link rel="stylesheet" href="../assets/css/main.css">
            <link rel="stylesheet" href="../css/main2.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
            <style>

            </style>
        </head>

        <body>

            <div class="container">
                <h1>No Schedules Yet</h1>
            </div>

        </body>

        </html>
    <?php }
    ?>

<?php }
?>