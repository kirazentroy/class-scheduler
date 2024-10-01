
<?php include('../queries/classes.php');
for ($i = 0; $i < count($courseArr); $i++) {
    echo "<option value='" . $courseArr[$i] . "'>" . $courseArr[$i] . "</option>\n";
}
?>
