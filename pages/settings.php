<?php
include('../functions/sessionstart.php');

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
</head>

<body>

    <button class="btn btn-success btn-sm" id="updateinfo" data-bs-toggle="modal" data-bs-target="#modalUpdate">Update your info</button>

    <div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update your info</h1>

                    <form action="../functions/updated.php" method="post">
                        <input type="hidden" value="<?php echo $id ?>" name="modalid">
                        <input type="text" id="modallast" name="modallast">
                        <input type="text" id="modalfirst" name="modalfirst">
                        <button type="submit" name="updateinfo" class="btn btn-sm btn-dark">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script>
        $(document).ready(function() {
            $('#updateinfo').click(function() {
                $.getJSON(`../queries/getupdateinfo.php?getupdateinfo=<?php echo $id ?>`, function(data) {
                    data.forEach(ele => {
                        let last = ele.member_last;
                        let first = ele.member_first;
                        $('#modallast').attr('value', `${last}`);
                        $('#modalfirst').attr('value', `${first}`);
                    })
                });
            });

            <?php
            if (isset($_SESSION['updated'])) {
                $msg = $_SESSION['updated'];

                echo "Swal.fire({
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                    title: '$msg'
                });";
                unset($_SESSION['updated']);
            }
            ?>
        });
    </script>
</body>

</html>