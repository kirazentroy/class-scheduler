
<?php
include('../functions/db_connect.php');

$query = executeNonQuery($connect, "SELECT max(member_id) as id FROM members");

$row = fetchAssoc($connect, $query);

$id = $row['id'];
?>