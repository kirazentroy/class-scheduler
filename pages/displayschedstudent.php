<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}
if ($_SESSION['superiority'] != 'student') {
    header('location: ../pages/displaysched.php');
}
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
            font-size: 13px;
        }

        #table {
            max-width: 100%;
        }

        #tableteacher td,
        #tableteacher th,
        #tablecourse td,
        #tablecourse th,
        #tableroom td,
        #tableroom th {
            width: 16.67% !important;
            border: 1px solid black !important;
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
    </style>
</head>

<body>

    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">

        <?php include('../includes/sidebar.php') ?>

        <div id="page-content-wrapper">
            <div class="container-fluid mt-4 px-4">
                <header class="mb-5">
                    <?php
                    $getcourse = executeNonQuery($connect, "SELECT * from students where student_id = '$id'");
                    $result = fetchAssoc($connect, $getcourse);
                    $course = $result['student_section'];
                    ?>
                    <h1>Schedule for your section <?php echo $course ?></h1>
                </header>
                <main>
                    <div class="row mb-5">
                        <div class="col-6">
                            <div class="row-mb-5">
                                <div class="col-6 mb-3">
                                    <!-- pili ug school year -->
                                    <label class="form-label fw-bold" for="sy">School Year</label>
                                    <select class="form-select" id="schoolyear">
                                    </select>
                                </div>
                                <div class="col-6 d-none mb-3" id="parasasem">
                                    <label class="form-label fw-bold" for="semester">Trimester</label>
                                    <select class="form-select" name="semester" id="semester">
                                        <?php include('../includes/semesterselect.php');
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="conttable">
                        <table class="table d-none table-striped w-auto" id="tablecourse">
                            <p class="d-none text-bold text-dark" id="specificschedcourse"><span id="assignedcourse"></span></p>
                            <thead id="schedtheadcourse">
                                <div class="row">
                                    <tr>
                                        <th class="col-2">Mon</th>
                                        <th class="col-2">Tue</th>
                                        <th class="col-2">Wed</th>
                                        <th class="col-2">Thu</th>
                                        <th class="col-2">Fri</th>
                                        <th class="col-2">Sat</th>
                                    </tr>
                                </div>
                            </thead>
                            <tbody id="schedtbodycourse">

                            </tbody>
                        </table>
                    </div>
                </main>
                <button class="btn btn-sm btn-dark mb-5" id="printsched" onclick="PrintElem();">Print Schedule</button>

            </div>
        </div>
    </div>

    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script>
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
            let sudlanan = '<option value="" selected disabled>--</option>';
            schoolyear.forEach(eleSY => {
                sudlanan += `<option value="${eleSY}">${eleSY}</option>`;
            })
            $('#schoolyear').html(sudlanan);
        }

        $(document).ready(function() {
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
            }
            ?>

            schoolyear();

            $('#schoolyear').change(function() {
                $('#parasasem').removeClass('d-none');
                let coursename = '<?php echo $course ?>';
                let schoolyear = $(this).val();
                let semester = $('#semester').val();
                let semfixed;
                if (semester === '1') {
                    semfixed = '1st Trimester';
                } else if (semester === '2') {
                    semfixed = '2nd Trimester';
                } else if (semester === '3') {
                    semfixed = '3rd Trimester';
                }
                let monday = [];
                let tuesday = [];
                let wednesday = [];
                let thursday = [];
                let friday = [];
                let saturday = [];
                let counter = [0, 0, 0, 0, 0, 0];
                let trlength = 0;
                $('#printsched').attr('onclick', `PrintElem('#schedtbodycourse');`);
                $("span[id^='assigned']").html(`Schedule for ${coursename} on ${semfixed} of ${schoolyear} school year.`);
                $.getJSON(`<?php if ($_SESSION['studentstatus'] === 'regular') { ?>../queries/getsched.php?getcoursereg=${coursename}&schoolyear=${schoolyear}&semester=${semester}
                <?php } else if ($_SESSION['studentstatus'] === 'irregular') { ?>../queries/getsched.php?getcourse=${coursename}&schoolyear=${schoolyear}&semester=${semester}
                <?php } ?>`, function(data) {
                    if (data.length === 0) {
                        $('#tablecourse').removeClass('d-none');
                        $('#schedtbodycourse').html('<p class="text-bold text-dark" id="noschedcourse">No schedules assigned yet!</p>');
                    } else {
                        for (let i = 0; i < data.length; i++) {
                            if (data[i].weekday === 'Monday') {
                                monday.push(data[i]);
                                counter[0]++;
                            } else if (data[i].weekday === 'Tuesday') {
                                tuesday.push(data[i]);
                                counter[1]++;
                            } else if (data[i].weekday === 'Wednesday') {
                                wednesday.push(data[i]);
                                counter[2]++;
                            } else if (data[i].weekday === 'Thursday') {
                                thursday.push(data[i]);
                                counter[3]++;
                            } else if (data[i].weekday === 'Friday') {
                                friday.push(data[i]);
                                counter[4]++;
                            } else if (data[i].weekday === 'Saturday') {
                                saturday.push(data[i]);
                                counter[5]++;
                            }
                        }
                        console.log(monday, tuesday, wednesday, thursday, friday, saturday);
                        trlength = Math.max(...counter);
                        if (trlength === 0) {
                            $('#tablecourse').addClass('d-none');
                            $('#specificschedcourse').addClass('d-none');
                        } else if (trlength > 0) {
                            let schedrow = '';
                            $('#specificschedcourse').removeClass('d-none');
                            $('#tablecourse').removeClass('d-none');
                            for (let trow = 0; trow < trlength; trow++) {
                                schedrow += `<tr id="trow_${trow}">
                                                <td id="mon_${trow}" class="col-2 text-start"></td>
                                                <td id="tue_${trow}" class="col-2 text-start"></td>
                                                <td id="wed_${trow}" class="col-2 text-start"></td>
                                                <td id="thu_${trow}" class="col-2 text-start"></td>
                                                <td id="fri_${trow}" class="col-2 text-start"></td>
                                                <td id="sat_${trow}" class="col-2 text-start"></td>
                                            </tr>`;
                            }
                            $('#schedtbodycourse').html(schedrow);
                            insertcd(monday);
                            insertcd(tuesday);
                            insertcd(wednesday);
                            insertcd(thursday);
                            insertcd(friday);
                            insertcd(saturday);
                        }
                    }
                });
            });

            $('#semester').change(function() {
                let coursename = '<?php echo $course ?>';
                let schoolyear = $('#schoolyear').val();
                let semester = $(this).val();
                let semfixed;
                if (semester === '1') {
                    semfixed = '1st Trimester';
                } else if (semester === '2') {
                    semfixed = '2nd Trimester';
                } else if (semester === '3') {
                    semfixed = '3rd Trimester';
                }
                let monday = [];
                let tuesday = [];
                let wednesday = [];
                let thursday = [];
                let friday = [];
                let saturday = [];
                let counter = [0, 0, 0, 0, 0, 0];
                let trlength = 0;
                $('#printsched').attr('onclick', `PrintElem('#schedtbodycourse');`);
                $("span[id^='assigned']").html(`Schedule for ${coursename} on ${semfixed} of ${schoolyear} school year.`);
                $.getJSON(`<?php if ($_SESSION['studentstatus'] === 'regular') { ?>../queries/getsched.php?getcoursereg=${coursename}&schoolyear=${schoolyear}&semester=${semester}
                <?php } else if ($_SESSION['studentstatus'] === 'irregular') { ?>../queries/getsched.php?getcourse=${coursename}&schoolyear=${schoolyear}&semester=${semester}
                <?php } ?>`, function(data) {
                    if (data.length === 0) {
                        $('#tablecourse').removeClass('d-none');
                        $('#schedtbodycourse').html('<p class="text-bold text-dark" id="noschedcourse">No schedules assigned yet!</p>');
                    } else {
                        for (let i = 0; i < data.length; i++) {
                            if (data[i].weekday === 'Monday') {
                                monday.push(data[i]);
                                counter[0]++;
                            } else if (data[i].weekday === 'Tuesday') {
                                tuesday.push(data[i]);
                                counter[1]++;
                            } else if (data[i].weekday === 'Wednesday') {
                                wednesday.push(data[i]);
                                counter[2]++;
                            } else if (data[i].weekday === 'Thursday') {
                                thursday.push(data[i]);
                                counter[3]++;
                            } else if (data[i].weekday === 'Friday') {
                                friday.push(data[i]);
                                counter[4]++;
                            } else if (data[i].weekday === 'Saturday') {
                                saturday.push(data[i]);
                                counter[5]++;
                            }
                        }
                        console.log(monday, tuesday, wednesday, thursday, friday, saturday);
                        trlength = Math.max(...counter);
                        if (trlength === 0) {
                            $('#tablecourse').addClass('d-none');
                            $('#specificschedcourse').addClass('d-none');
                        } else if (trlength > 0) {
                            let schedrow = '';
                            $('#specificschedcourse').removeClass('d-none');
                            $('#tablecourse').removeClass('d-none');
                            for (let trow = 0; trow < trlength; trow++) {
                                schedrow += `<tr id="trow_${trow}">
                                                <td id="mon_${trow}" class="col-2 text-start"></td>
                                                <td id="tue_${trow}" class="col-2 text-start"></td>
                                                <td id="wed_${trow}" class="col-2 text-start"></td>
                                                <td id="thu_${trow}" class="col-2 text-start"></td>
                                                <td id="fri_${trow}" class="col-2 text-start"></td>
                                                <td id="sat_${trow}" class="col-2 text-start"></td>
                                            </tr>`;
                            }
                            $('#schedtbodycourse').html(schedrow);
                            insertcd(monday);
                            insertcd(tuesday);
                            insertcd(wednesday);
                            insertcd(thursday);
                            insertcd(friday);
                            insertcd(saturday);
                        }
                    }
                });
            });

        });

        // pangslice
        function stringcutter(str) {
            let slicer = str.toString().split('');
            let cut = [];
            for (let i = 0; i < 3; i++) {
                cut.push(slicer[i]);
            }
            cut = cut.join('');
            return cut;
        }

        // pang insert sa course
        function insertcd(days) {
            days.sort((a, b) => {
                return timeconvertion(a.start_time) - timeconvertion(b.start_time);
            });
            let wew = '';
            let day = '';
            let finder = '';
            for (let i = 0; i < days.length; i++) {
                day = stringcutter((days[i].weekday)).toLowerCase();
                finder = `td[id='${day}_${i}']`;
                wew =
                    `${insertcolon(days[i].start_time.toString().substr(1))}${days[i].start_time < 20000 ? 'am' : 'pm'} - ${insertcolon(days[i].end_time.toString().substr(1))}${days[i].end_time < 20000 ? 'am' : 'pm'}<br>
                    ${days[i].assigned_teacher}<br>
                    ${days[i].subject_code}<br>
                    ${days[i].roomname}<br>
                    ${(days[i].merged_id === undefined || days[i].merged_id === null) ? '':"<span class='text-success'>"+days[i].merged_from+'</span><br>'}
                    ${(days[i].merged_id === undefined && days[i].merged_with !== undefined) ? "<span class='text-primary'>"+days[i].merged_with+'</span><br>':''}
                    ${days[i].schedule_status === 'Custom' ? '<span style="color: red;">(Customized Sched)</span><br>':''}`;
                $(finder).html(wew);
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
            mywindow.document.write('<html><head><title>Print</title>');
            mywindow.document.write(`<style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            .mx-auto{
                margin-right:auto!important;
                margin-left:auto!important}
            .col-2{
                flex:0 0 auto;
                width:16.66666667%
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
            td a {
                display: none;
            }
            </style>`);
            mywindow.document.write(`</head><body><table class="table w-auto" id="table"><thead id="schedthead">
                                <p style="color:black; margin:10px 0">${data2}</p>
                                <tr>
                                    <th class="col-2 text-start">Mon</th>
                                    <th class="col-2 text-start">Tue</th>
                                    <th class="col-2 text-start">Wed</th>
                                    <th class="col-2 text-start">Thu</th>
                                    <th class="col-2 text-start">Fri</th>
                                    <th class="col-2 text-start">Sat</th>
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