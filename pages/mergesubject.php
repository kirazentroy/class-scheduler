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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Merging | <?php include('../includes/title.php'); ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        #mergetable td {
            padding-right: 1rem;
            padding-bottom: 1rem;
        }

        #mergetable td,
        #mergetable th {
            width: 16.67%;
        }

        #mergetbody {
            font-size: 12px;
        }

        #mergetable {
            max-width: 100%;
        }

        .text-primary:hover {
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
                    <h1>Subject Merging</h1>
                </header>
                <main>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="schoolyear" class="form-label">Schoolyear</label>
                            <select name="schoolyear" id="schoolyear" class="form-select w-50" onchange="mergeFromCourses();"></select>
                        </div>
                        <div class="col-6">
                            <label for="semester" class="form-label">Semester</label>
                            <select name="semester" id="semester" class="form-select w-50" onchange="mergeFromCourses();">
                                <option value="1">1st Trimester</option>
                                <option value="2">2nd Trimester</option>
                                <option value="3">3rd Trimester</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-6">
                            <label for="mergeto" class="form-label">Course/Section to Merge</label>
                            <select id="mergeto" class="form-select w-50 mb-3">
                                <?php
                                if ($_SESSION['permission'] === '0') {
                                    $admindept = $_SESSION['userdept'];

                                    $coursestomerge = executeNonQuery($connect, "SELECT * FROM subjects where department = '$admindept' group by course_id order by course_id");
                                } else {
                                    $coursestomerge = executeNonQuery($connect, "SELECT * FROM subjects group by course_id order by course_id");
                                }
                                while ($row = fetchAssoc($connect, $coursestomerge)) { ?>
                                    <option value="<?= $row['course_id'] ?>"><?= $row['course_id'] ?></option>
                                <?php } ?>
                            </select>
                            <label for="checksection" class="form-label">Section</label>
                            <input type="checkbox" name="checksection" id="checksection" class="form-check-input">
                            <span>Check if there is section</span><br>
                            <select id="sections" class="form-select w-25" disabled>
                                <option value="" selected disabled>--</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="mergefrom" class="form-label">Course/Section to Merge From</label>
                            <select id="mergefrom" class="form-select w-50 mb-3">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <table id="mergetable" class="table table-striped table-hover-dark d-none">
                            <thead>
                                <tr>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                </tr>
                            </thead>
                            <tbody id="mergetbody">

                            </tbody>
                        </table>
                        <div class="d-none" id="noofficial">No Official Schedules Yet</div>
                    </div>
                </main>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade modal-md" id="modalmerge" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">Merge Schedule for <span></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <label for="selectsubjecttomerge" class="form-label">Select Subject to Merge</label>
                        <select id="selectsubjecttomerge" class="form-select w-50 mb-3" onchange="selecteddescription();"></select>
                        <p id="getdescription" class="text-capitalize text-dark"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="savemerge">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
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

        let hourconvertions = {
            0: '12',
            1: '01',
            2: '02',
            3: '03',
            4: '04',
            5: '05',
            6: '06',
            7: '07',
            8: '08',
            9: '09',
            10: '10',
            11: '11'
        };
        let minuteconvertion = {
            0: '00',
            1: '01',
            2: '02',
            3: '03',
            4: '04',
            5: '05',
            6: '06',
            7: '07',
            8: '08',
            9: '09'
        };

        function ampmConvertion(time) {
            time = timeconvertion(time);
            let convertion = '';
            if (time < 1200) {
                convertion += hourconvertions[Math.floor(time / 100)];
                convertion += ':';
                convertion += ((time % 100) < 10 ? minuteconvertion[time % 100] : (time % 100));
                convertion += 'am';
            } else {
                time -= 1200;
                convertion += hourconvertions[Math.floor(time / 100)];
                convertion += ':';
                convertion += ((time % 100) < 10 ? minuteconvertion[time % 100] : (time % 100));
                convertion += 'pm';
            }

            return convertion;
        }

        function findmax(array) {
            let max = 0;
            for (let i = 0; i < array.length; i++) {
                let counts = 0;
                for (let j = 0; j < array[i].length; j++) {
                    counts++;
                }
                if (counts > max) max = counts;
            }
            return max;
        }

        function schoolyear() {
            let now = Date().split(' ')[3];
            let schoolyear = [Number(Number(now) - 1) + '-' + now, now + '-' + Number(Number(now) + 1)];
            let sudlanan = '';
            schoolyear.forEach(eleSY => {
                sudlanan += `<option value="${eleSY}">${eleSY}</option>`;
            })
            $('#schoolyear').html(sudlanan);
            mergeFromCourses();
        }

        function mergeFromCourses() {
            let schoolyear = $('#schoolyear').val();
            let semester = $('#semester').val();
            let html = '';
            let coursechosen = '';
            $.getJSON(`../queries/coursemerging.php?source=${schoolyear}&sem=${semester}`, function(data) {
                if (data.length === 0) {
                    html = `<option value="">No Values</option>`;
                } else {
                    for (var i = 0; i < data.length; i++) {
                        if (i === 0) {
                            coursechosen = data[i];
                        }
                        html += `<option value="${data[i]}">${data[i]}</option>`;
                    }
                }
                $('#mergefrom').html(html);
                tableMergeInfo(schoolyear, semester, coursechosen);
            });
        }


        function tableMergeInfo(year, semester, course) {
            $.getJSON(`../queries/coursemerging.php?table=${year}&sem=${semester}&course=${course}`, function(data) {
                if (findmax(data) === 0) {
                    $('#noofficial').removeClass('d-none');
                    $('#mergetable').addClass('d-none');
                } else {
                    let html = '';
                    $('#noofficial').addClass('d-none');
                    $('#mergetable').removeClass('d-none');
                    for (let i = 0; i < findmax(data); i++) {
                        html += `<tr id="merge_${i}">
                                    <td id="Monday${i}"></td>
                                    <td id="Tuesday${i}"></td>
                                    <td id="Wednesday${i}"></td>
                                    <td id="Thursday${i}"></td>
                                    <td id="Friday${i}"></td>
                                    <td id="Saturday${i}"></td>
                                </tr>`;
                    }
                    $('#mergetbody').html('');
                    $('#mergetbody').html(html);
                    datamerger(data);
                }
            });
        }

        function datamerger(array) {
            array.forEach(ele => {
                ele.sort((a, b) => {
                    return timeconvertion(a.start_time) - timeconvertion(b.start_time);
                });
            });
            for (let i = 0; i < array.length; i++) {
                for (let j = 0; j < array[i].length; j++) {
                    $(`#${array[i][j].weekday}${j}`).html(`${ampmConvertion(array[i][j].start_time)} - ${ampmConvertion(array[i][j].end_time)}<br>${array[i][j].teachername}<br>${array[i][j].subcode}<br>${array[i][j].descript}<br><br><span class="text-primary"  onclick="mergenow(${array[i][j].schedule_id});"><u>Merge</u></span>`);
                }
            }
        }

        function mergenow(id) {
            if (($('#mergeto').val() + ($('#sections').val() === null ? '' : $('#sections').val())) === $('#mergefrom').val()) {
                // alert('hello');
                Swal.fire({
                    icon: 'error',
                    title: 'Cannot merge the same course/section!',
                    timer: 1300,
                    showConfirmButton: false
                });
            } else {
                $('#modalmerge').modal('show');

                $('#modalmerge h5 span').html(`${$('#mergeto').val()}${$('#sections').val() === null ? '' : $('#sections').val()}`);
                $('#savemerge').attr('value', id);
                let html = '';
                $.getJSON(`../queries/coursemerging.php?subjects=${$('#mergeto').val()}&sem=${$('#semester').val()}`, function(data) {
                    data.forEach(ele => {
                        html += `<option value="${ele.subject_id}">${ele.subject_code}</option>`;
                    });

                    $('#selectsubjecttomerge').html(html);
                    selecteddescription();
                });
            }
        }

        function selecteddescription() {
            $.getJSON(`../queries/getdescriptioninfo.php?description=${$('#selectsubjecttomerge').val()}`, function(data) {
                $('#getdescription').html(`• ${data}`);
            });
        }

        $(document).ready(function() {
            schoolyear();

            $('#checksection').change(function() {

                let checked = $(this).is(':checked');
                if (checked) {
                    $('#sections').prop('disabled', false);
                    $("#sections option[value='A']").prop('selected', true);
                } else if (!checked) {
                    $('#sections').prop('disabled', true);
                    $("#sections option[value='']").prop('selected', true);
                }

            });

            $('#savemerge').click(function() {

                Swal.fire({
                    title: 'Save merging?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "../functions/save.php",
                            method: "POST",
                            data: {
                                merge: $(this).val(),
                                course: $('#modalmerge h5 span').html(),
                                subject: $('#selectsubjecttomerge').val()
                            },
                            success: function(data) {
                                let entitlement = '';
                                if (data === 'success') {
                                    entitlement = 'Merged Successfully';
                                } else {
                                    entitlement = 'Merged Failed, conflict detected!';
                                }
                                $('#modalmerge').modal('hide');
                                Swal.fire({
                                    icon: data,
                                    title: entitlement,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        });
                    }
                });

            });

            $('#mergefrom').change(function() {
                // mergeFromCourses($(this).val());
                // alert($(this).val());
                tableMergeInfo($('#schoolyear').val(), $('#semester').val(), $(this).val());
            });
        });
    </script>
</body>

</html>