
<?php
include('../functions/db_connect.php');

$teachers = executeNonQuery($connect, "SELECT * FROM members where member_superiority ='admin' or member_superiority='faculty' order by member_salut AND member_last");
?>