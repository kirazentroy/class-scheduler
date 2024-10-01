<?php
include('functions/sessionstart.php');
include('functions/db_connect.php');
if (isset($_SESSION['id'])) {
    header('location:pages/home.php');
}
if (isset($_SESSION['pendingid'])) {
    $id = $_SESSION['pendingid'];
    $query = executeNonQuery($connect, "SELECT * FROM members where member_id = '$id'");

    $query = fetchAssoc($connect, $query);
    $getdeans = executeNonQuery($connect, "SELECT * FROM members WHERE (member_department = (SELECT member_department FROM members WHERE member_id = '$id') or permission = '1') and member_superiority = 'admin' group by member_id");
}
if (!isset($_SESSION['pendingid'])) {
    // $id = $_SESSION['pendingid'];
    header('location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Pending | <?php include('includes/title.php'); ?></title>
    <link rel="icon" type="image/x-icon" href="images/indexlogo.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="col-3">
            <h1>Account is still pending.</h1><br>
            <button class="btn btn-sm btn-success" id="sendmessage">You may send a message here</button> <br> <br>
            <a href="index.php" class="btn btn-sm btn-dark"><i class="fa fa-arrow-left" aria-hidden="true"></i> Go back to login page</a>
        </div>
    </div>

    <div class="modal modal-lg fade" id="sendmessagemodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">Send request...</h5>
                </div>
                <div class="modal-body">
                    <div class="col-6">
                        <label for="from" class="fw-bold">From:</label>
                        <input type="text" id="from" value="<?= $query['member_first'] . ' ' . $query['member_last'] ?>" readonly class="form-control">
                        <br>
                        <label for="to" class="fw-bold">To:</label>
                        <select name="to" id="to" class="form-select">
                            <?php while ($row = fetchAssoc($connect, $getdeans)) { ?>
                                <option value="<?= $row['member_id'] ?>"><?= $row['member_salut'] . ' ' . $row['member_last'] . ', ' . $row['member_first'] ?></option>
                            <?php } ?>
                        </select> <br>
                        <textarea name="message" id="message" cols="30" rows="3" style="resize: none;">Please approve my account</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="sendmsg();">Send</button>
                </div>
            </div>
        </div>
    </div>


    <script src="assets/scripts/bootstrap.js"></script>
    <script src="assets/jquery/jquery.js"></script>
    <script src="assets/scripts/sweetalert.js"></script>
    <script>
        <?php if (isset($_SESSION['alertpendingid'])) { ?>
            Swal.fire('Account still pending');
        <?php
            unset($_SESSION['alertpendingid']);
        }
        ?>
        $('#sendmessage').click(function() {
            $('#sendmessagemodal').modal('show');
        });

        function sendmsg() {
            // alert($('textarea').val() + ' '  + ' ' + $('#to').val());
            Swal.fire({
                title: 'Sending...',
                showConfirmButton: false,
            });
            $.ajax({
                url: "functions/sendmessage.php",
                method: "POST",
                data: {
                    content: $('textarea').val(),
                    sender: <?php echo  $id
                            ?>,
                    receiver: $('#to').val(),
                    date: Date()
                },
                success: function(data) {
                    window.location.href = "index.php";
                }
            });
        }
    </script>
</body>

</html>