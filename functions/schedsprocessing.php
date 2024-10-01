<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_POST['approve'])) {
    $id = $_POST['approve'];
    $teacher = $_POST['teacher'];
    // $assigner = $_POST['assigner'];
    executeNonQuery($connect, "UPDATE scheduled_classes SET `schedule_process`='approved', `teacher`='$teacher', `conflict_status` = '' where schedule_id='$id'");

    // $type = executeNonQuery($connect, "SELECT member_superiority FROM members where member_id = '$assigner'");
    // $type = fetchAssoc($connect, $type);
    // $type = $type['member_superiority'];

    // if ($type !== 'admin') {
    //     executeNonQuery($connect, "INSERT INTO `notifications`(`notification_for`, `notification_type`, `notification_status`) VALUES ('$teacher','$id','unopened')");
    // }

    echo "Schedule has been approved!";
}

if (isset($_POST['disapprove'])) {
    $id = $_POST['disapprove'];

    // executeNonQuery($connect, "UPDATE scheduled_classes SET `schedule_process`='unapproved' where schedule_id='$id'");
    executeNonQuery($connect, "DELETE from scheduled_classes WHERE `schedule_id`='$id'");
    echo "Schedule has been deleted!";
}

if (isset($_GET['conflictedadmin'])) {
    [$id, $conflict] = explode('_/', $_GET['conflictedadmin']);

    executeNonQuery($connect, "UPDATE scheduled_classes SET `conflict_status`='conflicted' where schedule_id='$id'");
    $_SESSION['conflicteddata'] = "$conflict";
    header('location:../pages/pendingschedsadmin.php');
}

if (isset($_GET['conflictedfaculty'])) {
    $id = $_GET['conflictedfaculty'];

    executeNonQuery($connect, "UPDATE scheduled_classes SET `conflict_status`='conflicted' where schedule_id='$id'");
    $_SESSION['conflicteddata'] = "The system detected conflict for this schedule.";
    header('location:../pages/pendingschedsfaculty.php');
}

if (isset($_GET['conflictedstudent'])) {
    $id = $_GET['conflictedstudent'];

    executeNonQuery($connect, "UPDATE scheduled_classes SET `conflict_status`='conflicted' where schedule_id='$id'");
    $_SESSION['conflicteddata'] = "The system detected conflict for this schedule.";
    header('location:../pages/pendingschedsstudent.php');
}

if (isset($_POST['remove'])) {
    $id = $_POST['remove'];
    $type = $_POST['type'];
    if ($type === 'sched') {
        executeNonQuery($connect, "DELETE from scheduled_classes where schedule_id='$id'");
        executeNonQuery($connect, "DELETE from merged_classes where schedule_id='$id'");
    } else {
        executeNonQuery($connect, "DELETE from merged_classes where merged_id='$id'");
    }
    echo "Schedule has been removed!";
}

if (isset($_POST['remove2'])) {
    $id = $_POST['remove2'];
    $type = $_POST['type'];
    if ($type === 'sched') {
        executeNonQuery($connect, "DELETE from scheduled_classes_shs where schedule_id='$id'");
        // executeNonQuery($connect, "DELETE from merged_classes where schedule_id='$id'");
    } else {
        // executeNonQuery($connect, "DELETE from merged_classes where merged_id='$id'");
    }
    echo "Schedule has been removed!";
}

if (isset($_POST['removemerged'])) {
    $id = $_POST['removemerged'];
    $type = $_POST['type'];

    executeNonQuery($connect, "DELETE from merged_classes where schedule_id = '$id'");

    echo "Schedule has been removed!";
}
