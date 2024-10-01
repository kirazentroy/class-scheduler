<?php
include('functions/sessionstart.php');
include('functions/db_connect.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <table class="table">
        <thead>
            <th>Teacher</th>
            <th>Course</th>
            <th>Subject</th>
        </thead>
        <tbody>
            <?php $merged = executeNonQuery($connect, "SELECT * from merged_classes");
            while ($row = fetchAssoc($connect, $merged)) { ?>
                <tr>
                    <td><?= $row['teacher'] ?></td>
                    <td><?= $row['course'] ?></td>
                    <td><?= $row['subject'] ?></td>
                </tr>
            <? }
            ?>
        </tbody>
    </table>
</body>

</html>