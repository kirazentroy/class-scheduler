<?php
include('functions/sessionstart.php');
include('functions/db_connect.php');
// if ($_SESSION['permission'] === '1') { 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Document</title>
</head>

<body>
    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalId">Reset Database</button>
    <table class="table">
        <caption class="caption-top"></caption>
        <thead>
            <tr>
                <th>Id</th>
                <th>Username</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Usertype</th>
                <th>Permission</th>
            </tr>
        </thead>
        <tbody>
            <?php $members = executeNonQuery($connect, "SELECT * from members");
            while ($row = fetchAssoc($connect, $members)) {
            ?>
                <tr>
                    <td><?php echo $row['member_id']
                        ?></td>
                    <td><?php echo $row['member_username']
                        ?></td>
                    <td><?php echo $row['member_last']
                        ?></td>
                    <td><?php echo $row['member_first']
                        ?></td>
                    <td><?php echo $row['member_superiority']
                        ?></td>
                    <td><?php echo $row['permission'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">Reset Database</h5>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <input type="text" placeholder="Input Foreground Password" id="password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="resetdata();">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var modalId = document.getElementById('modalId');

        modalId.addEventListener('show.bs.modal', function(event) {
            // Button that triggered the modal
            let button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            let recipient = button.getAttribute('data-bs-whatever');

            // Use above variables to manipulate the DOM
        });
    </script>

    <script src="assets/jquery/jquery.js"></script>
    <script src="assets/scripts/sweetalert.js"></script>
    <script src="assets/scripts/bootstrap.js"></script>
    <script>
        const resetdata = () => {
            document.location = `functions/resetdatabase.php?reset=${$('#password').val()}`;
        }
    </script>
</body>

</html>

<?php // }
?>