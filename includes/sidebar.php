<style>
    .side {
        background: #000814;
    }

    #togglesched,
    #toggle2 {
        background-color: #000814;
        cursor: pointer;
    }

    #subtogglesched,
    #subToggle2 {
        list-style: none;
        display: none;
    }

    #subToggle2 li {
        margin-bottom: 10px;
    }

    .sidebartextsmall {
        font-size: 1rem;
    }
</style>

<div class="side " id="sidebar-wrapper">
    <div class="pichead text-center w-100 px-4 py-3">
        <img src="imgs/doc1.png" alt="">
        <h4 class="fw-bold text-white mt-1">Welcome</h4>
        <div id="user" class="mt-3 mb-3 fw-bold text-white fs-4">
            <?php
            $result = executeNonQuery($connect, "SELECT * from members where member_id = '$id'");
            $row = fetchAssoc($connect, $result);
            echo $row['member_salut'] . " " . $row['member_last'];
            ?>
        </div>
        <!-- <p class="fw-bold">Doctor</p> -->
    </div>
    <div class="divider mb-3"></div>
    <div class="list-group">
        <?php if ($_SESSION['superiority'] === 'admin') { ?>
            <a class="fw-bold text-white" id="togglesched"><i class="fa-solid fa-chart-pie"></i> <span class="sidebartextsmall">Start Schedule</span> <i class="fa-solid fa-chevron-right text-white" id="sidebarschedarrow"></i></a>
            <?php } else if ($_SESSION['superiority'] === 'student') {
            if ($_SESSION['studentstatus'] === 'irregular') { ?>
                <a class="fw-bold text-white" id="togglesched"><i class="fa-solid fa-chart-pie"></i> <span class="sidebartextsmall">Start Schedule</span> <i class="fa-solid fa-chevron-right text-white" id="sidebarschedarrow"></i></a>
        <?php }
        } ?>
        <ul id="subtogglesched">
            <?php if ($_SESSION['superiority'] != 'student') {
                echo "<li class='mb-2'><a href='../pages/home.php' style='color: white;'><span class='sidebartextsmall'>Regulars</span></a></li>";
            }
            ?>
            <li><a href="../pages/irregularshome.php" style="color: white;"><span class="sidebartextsmall">Custom Schedule</span></a></li>
        </ul>
        <?php ?>
        <?php if ($_SESSION['superiority'] === 'admin') { ?>
            <a href="#toggle2" class="fw-bold text-white" id="toggle2"><i class="fa-solid fa-list-check"></i> <span class="sidebartextsmall">Conflict Schedules</span> <i class="fa-solid fa-chevron-right text-white" id="sidebarpendingarrow"></i></a>
            <ul id="subToggle2">
                <?php $conflicts = executeNonQuery($connect, "SELECT * From scheduled_classes where conflict_status = 'conflicted'");
                $conflictcounts = numRows($connect, $conflicts);
                ?>
                <li><a href="../pages/pendingschedsadmin.php" style="color: white;"><span class="sidebartextsmall"><?= ($conflictcounts === 0 ? 'No Conflicts' : ($conflictcounts === 1 ? $conflictcounts . ' Conflict' : $conflictcounts . ' Conflicts')) ?></span></a></li>
                <!-- <li><a href="../pages/pendingschedsfaculty.php" style="color: white;"><span class="sidebartextsmall">By Faculties</span></a></li>
            <li><a href="../pages/pendingschedsstudent.php" style="color: white;"><span class="sidebartextsmall">By Students</span></a></li> -->
            </ul>
            <a href="../pages/mergesubject.php" class="fw-bold text-white"><i class="fa-solid fa-newspaper"></i> <span class="sidebartextsmall">Course Merging</span></a>
        <?php } ?>
        <a href="../pages/displaysched.php" class="fw-bold text-white"><i class="fa-solid fa-calendar-days"></i> <span class="sidebartextsmall">Display Schedules</span></a>
        <?php if ($_SESSION['superiority'] === 'admin') { ?>
            <a href="../pages/displayschedfaculty.php" class="fw-bold text-white"><i class="fa fa-calendar" aria-hidden="true"></i> <span class="sidebartextsmall">Your Schedules</span></a>
        <?php } ?>
    </div>
</div>
<script src="../assets/jquery/jquery.js"></script>
<script>
    let togglescheddetection = 0;
    $('#togglesched').click(function() {
        togglescheddetection++;
        if (togglescheddetection % 2 === 0) {
            $('#subtogglesched').slideUp();
            $('#sidebarschedarrow').removeClass('fa-chevron-down');
            $('#sidebarschedarrow').addClass('fa-chevron-right');
        } else {
            $('#subtogglesched').slideDown();
            $('#sidebarschedarrow').removeClass('fa-chevron-right');
            $('#sidebarschedarrow').addClass('fa-chevron-down');
        }
    });

    let togglependingdetection = 0;
    $('#toggle2').click(function() {
        togglependingdetection++;
        if (togglependingdetection % 2 === 0) {
            $('#subToggle2').slideUp();
            $('#sidebarpendingarrow').removeClass('fa-chevron-down');
            $('#sidebarpendingarrow').addClass('fa-chevron-right');
        } else {
            $('#subToggle2').slideDown();
            $('#sidebarpendingarrow').removeClass('fa-chevron-right');
            $('#sidebarpendingarrow').addClass('fa-chevron-down');
        }
    });
</script>