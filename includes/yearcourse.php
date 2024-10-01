<?php include('../queries/classes.php');
for ($i = 0; $i < count($yearcourseArr); $i++) {
    echo "<option value='" . $yearcourseArr[$i] . "'>" . $yearcourseArr[$i] . "</option>\n";
}
