<?php
if (isset($_SESSION['currentdevice'])) {
    $currentdevice = $_SESSION['currentdevice'];
}
$current = executeNonQuery($connect, "SELECT * FROM devices WHERE device_id='$currentdevice'");

if (isset($_SESSION['id'])) {
    if (numRows($connect, $current) == 0) {
        $_SESSION['sessionterminated'] = "You have been logged out!";
        unset($_SESSION['id']);
        header('location:../');
    }
    $idnavbar = $_SESSION['id'];
    $checkBar = executeNonQuery($connect, "SELECT * FROM members where member_id = '$idnavbar'");
    if (numRows($connect, $checkBar) === 0) {
        session_unset();
        session_destroy();
        header('location: ../index.php');
    }
}
if (isset($messengerPage)) {
    $messenger = "onclick='sidebarmessengerhide();'";
} else if (!isset($messnngerPage)) {
    $messenger = '';
}

$varEmail = executeNonQuery($connect, "SELECT email_status from members where member_id = '$id'");
$email_verify = fetchAssoc($connect, $varEmail);
$email_verify = $email_verify['email_status'];

?>

<style>
    .deleteduser {
        position: absolute;
        animation: deleteuser 1s 1 ease-in-out;
    }

    @keyframes deleteuser {
        from {
            opacity: 1;
            left: 0;
        }

        to {
            height: 0;
            opacity: 0;
            left: -9000px;
        }
    }

    .navbar {
        background: #000814;
    }

    #showmouseover {
        display: none;
        top: 70px;
        z-index: 5;
    }

    #showmouseover li {
        list-style: none;
    }

    #notifcation {
        position: relative;
    }

    #profile:hover,
    #notification:hover,
    #pendingusers:hover,
    #messages:hover,
    #userguide {
        cursor: pointer;
    }

    @media (orientation: portrait) {
        #navbarNav {
            display: flex !important;
            flex-basis: auto;
        }

        #wrapper {
            display: none !important;
        }

        #sidebar-wrapper {
            display: none !important;
        }

        #landscape {
            display: flex;
            padding-top: 150px;
        }

        #menu-toggle,
        #userinfo,
        #usersecurity {
            display: none !important;
        }

        #messages,
        #pendingusers,
        #adminrefer {
            display: none !important;
        }
    }

    @media (orientation: landscape) {
        #landscape {
            display: none !important;
        }
    }

    @media (min-width: 821px) {
        #landscape {
            display: none !important;
        }
    }

    #unclickedmsgs {
        position: absolute;
        display: flex;
        justify-content: center;
        text-align: center;
        align-items: center;
        top: -5px;
        right: 5px;
    }

    .unclickedmsgs {
        height: 15px;
        width: 15px;
        font-size: 10px;
        border-radius: 50%;
        background-color: red;
        color: white;
    }
</style>

<nav class="navbar navbar-expand-lg py-3 d-flex justify-content-between" style="z-index: 2; position: fixed; width: 100%;">
    <!-- <div class="container"> -->
    <div class="d-flex align-items-center">
        <img src="../images/indexlogo.png" style="width: 60px; height: 60px; border-radius:50%;" id="menu-toggle" <?= $messenger ?>></img>

        <a href="../pages/home.php" style="text-decoration: none;" class="text-white mx-3 text-uppercase fw-bold">ASCB Scheduler</a>
        <?php if ($email_verify !== 'verified') { ?>
            <button class="btn btn-success btn-sm" onclick="verifyEmail(<?= $id; ?>);">Verify Email</button>
        <?php }
        ?>
    </div>
    <div class="navbar pe-5 d-flex justify-content-end" id="navbarNav">
        <?php if (isset($userguide)) { ?>
            <i class="fa-solid fa-circle-exclamation fa-solid fa-message fa-xl" style="color: #ededed; margin-right: 10px" id="userguide" onclick="usersguide();"></i>
        <?php } ?>
        <?php if ($_SESSION['superiority'] !== 'student') { ?>
            <div class="position-relative" onclick="clicked1();">
                <i class="fa-solid fa-message fa-xl" style="color: #ededed; margin-right: 10px" id="messages"></i><span id="unclickedmsgs"></span>
            </div>
        <?php } ?>
        <?php if ($_SESSION['superiority'] === 'admin') { ?>
            <i class=" fa-solid fa-users fa-xl" onclick="pendingusers();" style="color: #ededed; margin-right: 10px;" id="pendingusers" onmouseover="addanimation();" onmouseleave="deleteanimation();" data-bs-toggle="modal" data-bs-target="#pendingusers-modal"></i>
        <?php } ?>
        <?php if ($_SESSION['permission'] === '1') { ?>
            <a href="../pages/adminreferrer.php" class="btn btn-success btn-sm" style="margin-right: 20px;" id="adminrefer">Dean Referral <i class="fa-solid fa-user fa-xl"></i></a>
        <?php } ?>
        <div id="profile" onclick="profilemouseover();">
            <img src="../profileimages/<?php echo $_SESSION['imgname'] ?>" alt="../profileimages/<?php echo $_SESSION['imgname'] ?>" style="border-radius: 50%; width: 40px; height: 40px;">
            <i class="fa-solid fa-chevron-down text-white"></i>
        </div>
        <ul id="showmouseover" class="position-absolute bg-dark p-2 pr-0" style="border-radius: 10px;">
            <li class="mb-2" id="userinfo"><a href="<?= getBaseUrl().'pages/profileinfo.php' ?>" style="color: white;" class="btn-dark btn btn-sm"><i class="fa-solid fa-user"></i> User Info</a></li>
            <li class="mb-2" id="usersecurity"><a href="<?= getBaseUrl().'pages/security.php' ?>" style="color: white;" class="btn-dark btn btn-sm"><i class="fa-solid fa-key"></i> Security</a></li>
            <li>
                <button class="btn btn-dark btn-sm" name="logout" onclick="logout();"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</button>
            </li>
        </ul>
    </div>
</nav>

<!-- force to landscape -->
<div id="landscape" class="text-center">
    <p class="text-dark">Please tilt your phone in landscape mode.</p>
</div>

<!-- Modal trigger button -->
<!-- <button type="button" class="btn btn-primary btn-lg">
    Launch
</button> -->

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="pendingusers-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Pending Faculties</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="pendingusertable">
                        <thead>
                            <tr class="text-center fw-bold">
                                <th scope="col">Fullname</th>
                                <th>Role</th>
                                <th scope="col" colspan="2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <p class="text-dark d-none" id="nofacultypendings">No Faculty Users Pending</p>
                </div>

            </div>
        </div>
    </div>
</div>