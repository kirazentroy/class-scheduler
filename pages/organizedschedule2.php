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
if (isset($_GET['program']) && isset($_GET['semester']) && isset($_GET['sy'])) {

    $course = $_GET['program'];
    $sem = $_GET['semester'];
    $sy = $_GET['sy'];

    $newprogramcode = explode(" ", $course)[0];
    // echo $newprogramcode;
    // $descriptionprogramVar = executeNonQuery($connect, "SELECT course_name from courses where course_code = '$newprogramcode'");
    // $descriptionprogram = fetchAssoc($connect, $descriptionprogramVar)['course_name'];

    // Monday
    $queryMonday = executeNonQuery($connect, "SELECT scheduled_classes_shs.*, scheduled_classes_shs.subject as yow, concat(members.member_salut, ' ', members.member_last) as teachername, rooms.room_number FROM scheduled_classes_shs join members join rooms WHERE scheduled_classes_shs.teacher = members.member_id and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.course = '$course' and scheduled_classes_shs.semester = '$sem' and scheduled_classes_shs.schoolyear = '$sy' and scheduled_classes_shs.schedule_process = 'approved' and scheduled_classes_shs.weekday = 'Monday' order by scheduled_classes_shs.subject asc");

    // Tuesday
    $queryTuesday = executeNonQuery($connect, "SELECT scheduled_classes_shs.*, scheduled_classes_shs.subject as yow, concat(members.member_salut, ' ', members.member_last) as teachername, rooms.room_number FROM scheduled_classes_shs join members join rooms WHERE scheduled_classes_shs.teacher = members.member_id and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.course = '$course' and scheduled_classes_shs.semester = '$sem' and scheduled_classes_shs.schoolyear = '$sy' and scheduled_classes_shs.schedule_process = 'approved' and scheduled_classes_shs.weekday = 'Tuesday' order by scheduled_classes_shs.subject asc");

    // Wednesday
    $queryWednesday = executeNonQuery($connect, "SELECT scheduled_classes_shs.*, scheduled_classes_shs.subject as yow, concat(members.member_salut, ' ', members.member_last) as teachername, rooms.room_number FROM scheduled_classes_shs join members join rooms WHERE scheduled_classes_shs.teacher = members.member_id and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.course = '$course' and scheduled_classes_shs.semester = '$sem' and scheduled_classes_shs.schoolyear = '$sy' and scheduled_classes_shs.schedule_process = 'approved' and scheduled_classes_shs.weekday = 'Wednesday' order by scheduled_classes_shs.subject asc");

    // Thursday
    $queryThursday = executeNonQuery($connect, "SELECT scheduled_classes_shs.*, scheduled_classes_shs.subject as yow, concat(members.member_salut, ' ', members.member_last) as teachername, rooms.room_number FROM scheduled_classes_shs join members join rooms WHERE scheduled_classes_shs.teacher = members.member_id and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.course = '$course' and scheduled_classes_shs.semester = '$sem' and scheduled_classes_shs.schoolyear = '$sy' and scheduled_classes_shs.schedule_process = 'approved' and scheduled_classes_shs.weekday = 'Thursday' order by scheduled_classes_shs.subject asc");

    // Friday
    $queryFriday = executeNonQuery($connect, "SELECT scheduled_classes_shs.*, scheduled_classes_shs.subject as yow, concat(members.member_salut, ' ', members.member_last) as teachername, rooms.room_number FROM scheduled_classes_shs join members join rooms WHERE scheduled_classes_shs.teacher = members.member_id and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.course = '$course' and scheduled_classes_shs.semester = '$sem' and scheduled_classes_shs.schoolyear = '$sy' and scheduled_classes_shs.schedule_process = 'approved' and scheduled_classes_shs.weekday = 'Friday' order by scheduled_classes_shs.subject asc");

    // Saturday
    $querySaturday = executeNonQuery($connect, "SELECT scheduled_classes_shs.*, scheduled_classes_shs.subject as yow, concat(members.member_salut, ' ', members.member_last) as teachername, rooms.room_number FROM scheduled_classes_shs join members join rooms WHERE scheduled_classes_shs.teacher = members.member_id and scheduled_classes_shs.room = rooms.room_id and scheduled_classes_shs.course = '$course' and scheduled_classes_shs.semester = '$sem' and scheduled_classes_shs.schoolyear = '$sy' and scheduled_classes_shs.schedule_process = 'approved' and scheduled_classes_shs.weekday = 'Saturday' order by scheduled_classes_shs.subject asc");
    $resultArr = [];
    while ($row = fetchAssoc($connect, $queryMonday)) {
        array_push($resultArr, $row);
    }
    while ($row = fetchAssoc($connect, $queryTuesday)) {
        array_push($resultArr, $row);
    }
    while ($row = fetchAssoc($connect, $queryWednesday)) {
        array_push($resultArr, $row);
    }
    while ($row = fetchAssoc($connect, $queryThursday)) {
        array_push($resultArr, $row);
    }
    while ($row = fetchAssoc($connect, $queryFriday)) {
        array_push($resultArr, $row);
    }
    while ($row = fetchAssoc($connect, $querySaturday)) {
        array_push($resultArr, $row);
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $_GET['program'] . ' ' . returnsemester($_GET['semester']) . ' ' . $_GET['sy'] ?> Organized Schedule</title>
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
                <p class="fw-bold text-dark" id="programtype"></p>
                <p class="fw-bold text-dark">Section: <?= $_GET['program'] ?></p>
                <p class="fw-bold text-dark">Semester: <?= returnsemester($_GET['semester']) ?></p>
                <p class="fw-bold text-dark">S.Y: <?= $_GET['sy'] ?></p>
            </div>
            <div class="text-start">
                <table class="table table-striped" id="organizedscheds">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Instructor</th>
                            <th>Day/Time</th>
                            <th>Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // print_r($resultArr);

                        $filteredcourse = [];
                        for ($i = 0; $i < sizeof($resultArr); $i++) {
                            if (!in_array($resultArr[$i]['yow'], $filteredcourse)) {
                                array_push($filteredcourse, $resultArr[$i]['yow']);
                            }
                        }
                        $filteredteachers = [];
                        $filteredday = [];
                        $filteredroom = [];
                        $empty = [];
                        for ($i = 0; $i < sizeof($filteredcourse); $i++) {
                            array_push($filteredteachers, $empty);
                            array_push($filteredday, $empty);
                            array_push($filteredroom, $empty);
                        }

                        for ($i = 0; $i < sizeof($filteredcourse); $i++) {
                            $fcourse = $filteredcourse[$i];
                            for ($j = 0; $j < sizeof($resultArr); $j++) {
                                if ($fcourse === $resultArr[$j]['yow']) {
                                    if (!in_array($resultArr[$j]['teachername'], $filteredteachers[$i])) {
                                        array_push($filteredteachers[$i], $resultArr[$j]['teachername']);
                                    }
                                    if (!in_array(weekdayconvertion($resultArr[$j]['weekday']) . ' ' . timeconverting($resultArr[$j]['start_time'], $resultArr[$j]['end_time']), $filteredday[$i])) {
                                        array_push($filteredday[$i], weekdayconvertion($resultArr[$j]['weekday']) . ' ' . timeconverting($resultArr[$j]['start_time'], $resultArr[$j]['end_time']));
                                    }
                                    if (!in_array($resultArr[$j]['room_number'], $filteredroom[$i])) {
                                        array_push($filteredroom[$i], $resultArr[$j]['room_number']);
                                    }
                                }
                            }
                        }

                        $filteredschedule = [$filteredcourse, $filteredteachers, $filteredday, $filteredroom];
                        // print_r($filteredschedule);

                        for ($i = 0; $i < sizeof($filteredcourse); $i++) {
                        ?>
                            <tr class="align-items-center">
                                <td><?= $filteredschedule[0][$i] ?></td>
                                <td><?= notsamesched($filteredschedule[1][$i]); ?></td>
                                <td><?= notsameschedday($filteredschedule[2][$i]); ?></td>
                                <td><?= notsamesched($filteredschedule[3][$i]); ?></td>
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

        const programtypes = {
            "HUMMS": "Humanities and Social Sciences",
            "STEM": "Science, Technology, Engineering, and Mathematics",
            "ABM": "Accountancy, Business, and Management",
            "ICT": "Information Communication and Technology",
            "GAS": "General Academic Strand"
        };

        $(document).ready(function() {
            // $('#organizedscheds').dataTable();
            $("#programtype").html(`Strand: ${programtypes['<?= $newprogramcode ?>']}`);
        });
    </script>

    </html>

<?php }
?>