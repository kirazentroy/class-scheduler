<?php
include('functions/sessionstart.php');

// if (!isset($_SESSION['verified_success'])) {
//     header('location:./');
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <script src="assets/jquery/jquery.js"></script>
    <script src="assets/scripts/sweetalert.js"></script>
    <script>
        <?php if (isset($_SESSION['verified_success'])) {
            $msg = $_SESSION['verified_success'];
            echo "Swal.fire({
                    title: '$msg',
                    icon: 'success',
                    showConfirmButton: true
                    }).then((result) => {
                    if (result.isConfirmed) {
                        document.location = './';
                    }
                });";
            // unset($_SESSION['verified_success']);
        } ?>
    </script>
</body>

</html>