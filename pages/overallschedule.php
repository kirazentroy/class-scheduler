<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (!isset($_SESSION['id'])) {
    // header('location:../');
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        th,
        td {
            border: 1px solid black;
            padding: 5px;
            font-size: 10px;
        }

        th {
            font-weight: 3px;
        }
    </style>
    <!-- <link rel="stylesheet" href="../assets/css/sweetalert.css"> -->
</head>

<body>
    <div class="container">
        <div class="my-4">
            <h3>Overall Schedules</h3>
        </div>
        <div class="col-6">
            <div class="row-mb-5">
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold" for="sy">School Year</label>
                    <select class="form-select" id="schoolyear" onchange="filtersched();">
                    </select>
                </div>
                <div class="col-6 mb-3" id="parasasem">
                    <label class="form-label fw-bold" for="semester">Trimester</label>
                    <select class="form-select" name="semester" id="semester" onchange="filtersched();">
                        <?php include('../includes/semesterselect.php');
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12">
            <table class="table" id="overallsched">
                <thead class="table-primary" id="thead">
                </thead>
                <tbody id="tbody">
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/scripts/bootstrap.js"></script>
    <!-- <script src="../js/bootstrap.min.js"></script>
    <script src="../js/popper.min.js"></script> -->
    <script src="../assets/scripts/fontawesome.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/jquery/jquery.js"></script>
    <script>
        function schoolyear() {
            let now = new Date().getFullYear();
            let schoolyear = [Number(Number(now) - 1) + '-' + now, now + '-' + Number(Number(now) + 1)];
            let sudlanan = '';
            schoolyear.forEach(eleSY => {
                sudlanan += `<option value="${eleSY}">${eleSY}</option>`;
            })
            $('#schoolyear').html(sudlanan);
        }

        function filtersched() {
            $.getJSON(`../queries/getoverall.php?getall=${$('#schoolyear').val()}_${$('#semester').val()}`, function(data) {
                let theadhtml = `<th id="captionroom">Rooms</th>`;
                let roomsscheds = [];
                // console.log(data);
                data.forEach(ele => {
                    if (!roomsscheds.includes(ele.room_number)) {
                        roomsscheds.push(ele.room_number);
                        theadhtml += `<th id="${ele.room}">${ele.room_number.split(" ")[0]}</th>`;
                    }
                });
                $('#thead').html(theadhtml);
            });
        }
        schoolyear();
        $(document).ready(function() {
            filtersched();
        });
    </script>
</body>

</html>