<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');
if (!isset($_SESSION['id'])) {
    header('location:../');
}
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}

$messengerPage = 'true';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASCB-Messenger | <?php include('../includes/title.php') ?></title>
    <link rel="icon" type="image/x-icon" href="../images/indexlogo.png" />
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../css/main2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" type="text/css">
    <style>
        .message-container {
            width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        .message {
            margin-bottom: 10px;
        }

        .sender {
            font-weight: bold;
            color: blue;
        }

        .receiver {
            font-weight: bold;
            color: red;
        }

        .timestamp {
            font-size: 0.8em;
            color: gray;
        }

        .input-box {
            margin-top: 20px;
        }

        .onhoverarrow:hover {
            cursor: pointer;
        }
    </style>

    <!-- <link rel="stylesheet" href="../assets/css/sweetalert.css"> -->
</head>

<body>

    <?php include('../includes/navbar.php') ?>
    <div class="d-flex bg-light" id="wrapper">

        <?php include('../includes/sidebar-messenger.php'); ?>

        <div id="page-content-wrapper" style="padding-left: 250px;">
            <div class="container-fluid mt-4 px-4">
                <h3 class="text-center" id="h3"></h3>
                <div class="message-container" id="messageconvo">

                </div>
                <div class="message-container" id="textsender">
                    <div class="input-box">
                        <input type="text" placeholder="Type your message here" id="textMessage">
                        <button class="btn btn-dark btn-sm" onclick="sendMessage();" id="messagesenderbutton">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/scripts/bootstrap.js"></script>
    <script src="../assets/scripts/sidebar.js"></script>
    <script src="../assets/scripts/sweetalert.js"></script>
    <script src="../assets/scripts/navbar.js"></script>
    <script>
        let updatelatest = '';
        const months = {
            'Jan': 1,
            'Feb': 2,
            'Mar': 3,
            'Apr': 4,
            'May': 5,
            'Jun': 6,
            'Jul': 7,
            'Aug': 8,
            'Sep': 9,
            'Oct': 10,
            'Nov': 11,
            'Dec': 12
        }
        let talk = false;
        let choiced = undefined;
        let currentScene;
        var unreadmsgs;
        let userId = <?= $id ?>;
        let allqueue;
        var sidebarmessenger = true;
        let phase = 0;
        let timelimit = 0;
        const sidebarmessengerhide = () => {
            if (sidebarmessenger === true) {
                sidebarmessenger = false;
                setTimeout(() => {
                    $('#page-content-wrapper').attr('style', '');
                }, 100);
            } else {
                sidebarmessenger = true;
                $('#page-content-wrapper').attr('style', 'padding-left: 250px;');
            }
        }

        function sidebarmessengerunreads(array) {
            for (let i = 0; i < array.length; i++) {
                $.getJSON(`../functions/messages.php?unreads=${array[i]}&id=${userId}`, function(data) {
                    // console.log(data);
                    if (data > 0) {
                        $(`#convowith_${array[i]}`).addClass('conversationwith');
                        $(`#convowith_${array[i]}`).html(data);
                    }
                });
            }
        }

        function chatlists() {
            let html = '';
            let convoArr = [];
            $.getJSON(`../functions/messages.php?chatlists=`, function(data) {
                if (data.length === 0) {
                    $('h3#h3').html('-- No Current Messages --');
                    $('#textsender, #messageconvo').hide();
                } else if (data.length > 0) {
                    $('h3#h3').html('-- Messages --');
                    let seechatparameter;
                    data.sort((a, b) => {
                        return b.message_id - a.message_id
                    });
                    for (let i = 0; i < data.length; i++) {
                        if (Number(data[i].receiver) === userId) {
                            seechatparameter = Number(data[i].sender);
                        } else {
                            seechatparameter = Number(data[i].receiver);
                        }
                        if (talk === false) {
                            if (i === 0) {
                                currentScene = seechatparameter;
                            }
                            talk = true;
                        }
                        convoArr.push(seechatparameter);

                        html += `<li onclick="changeseechat('${seechatparameter}');" id="chatlist_${seechatparameter}" class="position-relative"><img src="../profileimages/${data[i].contactedimg}" style="height: 30px; width: 30px; border-radius: 50%; margin-top: 10px;">&nbsp;&nbsp;&nbsp;<span ${Number(data[i].sender) === userId ? '':(data[i].message_status_receiver === 'unread' ? "style='color: skyblue;'":'')}>${data[i].name.length > 20 ? data[i].name.slice(0, 17)+'...':data[i].name}</span>&nbsp;&nbsp;<span id="convowith_${seechatparameter}"></span><p style="font-size: 10px; padding-left: 44px;">${data[i].latest_content.split("~kyuuudesu~kirazen").join("'").length > 25 ? data[i].latest_content.split("~kyuuudesu~kirazen").join("'").slice(0, 25)+'...':data[i].latest_content.split("~kyuuudesu~kirazen").join("'")}</p></li>`;
                    }
                    $('#conversation-list').html(html);
                    sidebarmessengerunreads(convoArr);
                }
                if (timelimit <= 720) {
                    setTimeout(() => {
                        chatlists();
                    }, 5000);
                }
            });
        }

        function chatlists1() {
            let html = '';
            let convoArr = [];
            $.getJSON(`../functions/messages.php?chatlists=`, function(data) {
                if (data.length === 0) {
                    $('h3#h3').html('-- No Current Messages --');
                    $('#textsender, #messageconvo').hide();
                } else if (data.length > 0) {
                    $('h3#h3').html('-- Messages --');
                    let seechatparameter;
                    data.sort((a, b) => {
                        return b.message_id - a.message_id
                    });
                    for (let i = 0; i < data.length; i++) {
                        if (Number(data[i].receiver) === userId) {
                            seechatparameter = Number(data[i].sender);
                        } else {
                            seechatparameter = Number(data[i].receiver);
                        }
                        if (talk === false) {
                            if (i === 0) {
                                currentScene = seechatparameter;
                            }
                            talk = true;
                        }
                        convoArr.push(seechatparameter);
                        latestmessage(seechatparameter);
                        html += `<li onclick="changeseechat('${seechatparameter}');" id="chatlist_${seechatparameter}" class="position-relative"><img src="../profileimages/${data[i].contactedimg}" style="height: 30px; width: 30px; border-radius: 50%; margin-top: 10px;">&nbsp;&nbsp;&nbsp;<span ${Number(data[i].sender) === userId ? '':(data[i].message_status_receiver === 'unread' ? "style='color: skyblue;'":'')}>${data[i].name.length > 20 ? data[i].name.slice(0, 17)+'...':data[i].name}</span>&nbsp;&nbsp;<span id="convowith_${seechatparameter}"></span><p style="font-size: 10px; padding-left: 44px;">${data[i].latest_content.split("~kyuuudesu~kirazen").join("'").length > 25 ? data[i].latest_content.split("~kyuuudesu~kirazen").join("'").slice(0, 25)+'...':data[i].latest_content.split("~kyuuudesu~kirazen").join("'")}</p></li>`;
                    }
                    $('#conversation-list').html(html);
                    sidebarmessengerunreads(convoArr);
                }
            });
        }


        function seechat() {
            let changed = false;
            if (choiced === undefined) {
                currentScene = currentScene;
            } else if (typeof Number(choiced) === 'number') {
                if (Number(currentScene) !== Number(choiced)) {
                    currentScene = choiced;
                    phase = 0;
                    changed = true;
                }
            }
            var resultArr = [];
            $.getJSON(`../functions/messages.php?getconvos=${currentScene}&id=${userId}`, function(data) {
                // descending
                data.sort((a, b) => {
                    return Number(b.message_id) - Number(a.message_id);
                });
                let limitArr = [];

                for (let i = 0; i < data.length; i++) {
                    limitArr.unshift(data[i]);
                    if (limitArr.length === 5 || i === data.length - 1) {
                        resultArr.push(limitArr);
                        limitArr = [];
                    }
                }
                if (allqueue === undefined || allqueue === null) {
                    allqueue = resultArr.length;
                }
                if (changed === false) {
                    if (allqueue === resultArr.length) {
                        phase = phase;
                    } else {
                        if (phase === 0) {
                            phase = 0;
                        } else {
                            phase -= resultArr.length - allqueue;
                        }
                        allqueue = resultArr.length;
                    }
                    timelimit++;
                } else if (changed === true) {
                    allqueue = resultArr.length;
                }
                messageconvohtml(resultArr);
            });
        }

        function messageconvohtml(array) {
            var example = `<div class="text-center onhoverarrow${phase === allqueue - 1 ? ' d-none':''}" onclick="showUp();"><i class="fa-solid fa-arrow-up"></i></div>`;
            // console.log(array);
            for (let i = 0; i < array[phase].length; i++) {
                example += `<div class="message">
                        <span class="${Number(array[phase][i].receiver) === userId ? 'sender':'receiver'}">${Number(array[phase][i].receiver) === userId ? array[phase][i].name.split(' ')[0]+': ':'You: '}</span>

                        <p class="text-dark">${array[phase][i].content.split("~kyuuudesu~kirazen").join("'")}</p>
                    </div>`;
            }
            example += `<div class="text-center onhoverarrow${phase === 0 ? ' d-none':''}" onclick="showDown();"><i class="fa-solid fa-arrow-down"></i></div>`;
            $('#messageconvo').html(example);
            setTimeout(() => {
                seechat(currentScene);
            }, 1000);
        }

        function seechat1() {
            let changed = false;
            if (choiced === undefined) {
                currentScene = currentScene;
            } else if (typeof Number(choiced) === 'number') {
                if (Number(currentScene) !== Number(choiced)) {
                    currentScene = choiced;
                    phase = 0;
                    changed = true;
                }
            }
            var resultArr = [];
            $.getJSON(`../functions/messages.php?getconvos=${currentScene}&id=${userId}`, function(data) {
                // descending
                data.sort((a, b) => {
                    return Number(b.message_id) - Number(a.message_id);
                });
                let limitArr = [];

                for (let i = 0; i < data.length; i++) {
                    // 
                    limitArr.unshift(data[i]);
                    if (limitArr.length === 5 || i === data.length - 1) {
                        resultArr.push(limitArr);
                        limitArr = [];
                    }
                }
                if (allqueue === undefined || allqueue === null) {
                    allqueue = resultArr.length;
                }
                if (changed === false) {
                    if (allqueue === resultArr.length) {
                        phase = phase;
                    } else {
                        if (phase === 0) {
                            phase = 0;
                        } else {
                            phase -= resultArr.length - allqueue;
                        }
                        allqueue = resultArr.length;
                    }
                } else if (changed === true) {
                    allqueue = resultArr.length;
                }
                messageconvohtml1(resultArr);
            });
        }

        function messageconvohtml1(array) {
            var example = `<div class="text-center onhoverarrow${phase === allqueue - 1 ? ' d-none':''}" onclick="showUp();"><i class="fa-solid fa-arrow-up"></i></div>`;
            // console.log(array);
            for (let i = 0; i < array[phase].length; i++) {
                example += `<div class="message">
                        <span class="${Number(array[phase][i].receiver) === userId ? 'sender':'receiver'}">${Number(array[phase][i].receiver) === userId ? array[phase][i].name.split(' ')[0]+': ':'You: '}</span>

                        <p class="text-dark">${array[phase][i].content.split("~kyuuudesu~kirazen").join("'")}</p>
                    </div>`;
            }
            example += `<div class="text-center onhoverarrow${phase === 0 ? ' d-none':''}" onclick="showDown();"><i class="fa-solid fa-arrow-down"></i></div>`;
            $('#messageconvo').html(example);
        }

        function emptytextarea(id) {
            // alert();
            $(id).val('');
            msgSenderBtn();
        }

        function sendMessage() {
            $.ajax({
                url: "../functions/sendmessage.php",
                method: "POST",
                data: {
                    textcontent: $('#textMessage').val(),
                    sender: userId,
                    receiver: currentScene,
                    date: Date()
                },
                success: function(data) {
                    emptytextarea('#textMessage');
                }
            });
            phase = 0;
            setTimeout(() => {
                chatlists1();
                seechat1();
            }, 100);
            timelimit = 0;
        }

        function changeseechat(talkwith) {
            choiced = talkwith;
            seechat1();
        }

        function whilenotstart() {
            if (talk === false) {
                setTimeout(() => {
                    whilenotstart();
                }, 500);
            } else if (talk === true) {
                seechat();
            }
        }

        function showUp() {
            timelimit = 0;
            phase++;
            seechat1();
        }

        function showDown() {
            timelimit = 0;
            phase--;
            seechat1();
        }

        function msgSenderBtn() {
            if ($('#textMessage').val().length <= 0) {
                $('#messagesenderbutton').prop('disabled', true);
            } else {
                $('#messagesenderbutton').prop('disabled', false);
            }
        }

        $(document).ready(function() {
            $('#textMessage').on('keyup', function(e) {
                msgSenderBtn();
                if (e.key === 'Enter' || e.keyCode === 13) {
                    sendMessage();
                }
            });
            // console.log(latestmessage(117));
            whilenotstart();
            chatlists();
            msgSenderBtn();
        });
    </script>
</body>

</html>