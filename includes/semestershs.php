<?php include('../queries/semesters.php');
for ($i = 0; $i < count($semesterArr); $i++) {
    if ($semesterArr[$i] !== '3') {
        echo "<option value='" . $semesterArr[$i] . "'>" . ($semesterArr[$i] === '1' ? '1st ' : ($semesterArr[$i] === '2' ? '2nd ' : '3rd ')) . "Semester" . "</option>\n";
    }
}
