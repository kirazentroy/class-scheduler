<?php
include('../functions/sessionstart.php');
include('../functions/db_connect.php');

if (isset($_GET['loadunclicked'])) {
    if ($_SESSION['superiority'] !== 'student') {
        $id = $_SESSION['id'];

        $unclicked = executeNonQuery($connect, "SELECT * FROM messages where receiver = '$id' and click_status_receiver = 'unclicked' group by sender");
        echo numRows($connect, $unclicked);
    } else {
        echo "Wala";
    }
}

if (isset($_POST['click'])) {
    $id = $_SESSION['id'];
    executeNonQuery($connect, "UPDATE `messages` SET `click_status_receiver`='clicked' WHERE receiver = '$id'");
}

if (isset($_GET['chatlists'])) {

    $id = $_SESSION['id'];
    $resultArr = [];
    $contacted = [];
    $queryAsSender = executeNonQuery($connect, "SELECT max(messages.message_id) as id FROM messages join members where messages.receiver = members.member_id and sender = '$id' group by receiver order by message_id desc;");


    while ($row = fetchAssoc($connect, $queryAsSender)) {
        $idOfRecentMsg = $row['id'];

        $userAsSender = executeNonQuery($connect, "SELECT messages.message_id as message_id, messages.sender as sender, messages.receiver as receiver, messages.content as content, messages.message_status_receiver as message_status_receiver, messages.message_status_sender as message_status_sender, messages.click_status_receiver as click_status_receiver, messages.click_status_sender as click_status_sender, messages.date_sent as date_sent, concat(members.member_first, ' ', members.member_last) as name, members.imgname as imgname FROM messages join members where messages.receiver = members.member_id and messages.message_id = '$idOfRecentMsg' and sender = '$id' group by receiver order by message_id desc");

        while ($rowsingle = fetchAssoc($connect, $userAsSender)) {
            if (!in_array($rowsingle['receiver'], $contacted)) {
                // verifier
                $contactedid = $rowsingle['receiver'];
                array_push($contacted, $contactedid);

                // latest content
                $varLatestS = executeNonQuery($connect, "SELECT max(message_id) as id from messages where sender = '$contactedid' and receiver = '$id'");
                $sender = fetchAssoc($connect, $varLatestS);
                $sender = (int)($sender['id']);
                $varLatestR = executeNonQuery($connect, "SELECT max(message_id) as id from messages where receiver = '$contactedid' and sender = '$id'");
                $receiver = fetchAssoc($connect, $varLatestR);
                $receiver = (int)($receiver['id']);

                if ($sender > $receiver) {
                    $var = executeNonQuery($connect, "SELECT content from messages where message_id = '$sender'");
                    $latestContent = fetchAssoc($connect, $var);
                    $latestContent = $latestContent['content'];
                } else {
                    $var = executeNonQuery($connect, "SELECT content from messages where message_id = '$receiver'");
                    $latestContent = fetchAssoc($connect, $var);
                    $latestContent = 'You: ' . $latestContent['content'];
                }

                $rowsingle['latest_content'] = $latestContent;

                // image of contacted
                $varImg = executeNonQuery($connect, "SELECT imgname from members where member_id = '$contactedid'");
                $imgcontacted = fetchAssoc($connect, $varImg);
                $rowsingle['contactedimg'] = $imgcontacted['imgname'];

                // push all $rowsingle
                array_push($resultArr, $rowsingle);
            }
        }
    }

    $queryAsReceiver = executeNonQuery($connect, "SELECT max(messages.message_id) as id FROM messages join members where messages.sender = members.member_id and receiver = '$id' group by sender order by message_id desc;");

    while ($row = fetchAssoc($connect, $queryAsReceiver)) {
        $idOfRecentMsg = $row['id'];

        $userAsReceiver = executeNonQuery($connect, "SELECT messages.message_id as message_id, messages.sender as sender, messages.receiver as receiver, messages.content as content, messages.message_status_receiver as message_status_receiver, messages.message_status_sender as message_status_sender, messages.click_status_receiver as click_status_receiver, messages.click_status_sender as click_status_sender, messages.date_sent as date_sent, concat(members.member_first, ' ', members.member_last) as name, members.imgname as imgname FROM messages join members where messages.sender = members.member_id and messages.message_id = '$idOfRecentMsg' and receiver = '$id' group by sender order by message_id desc");

        while ($rowsingle = fetchAssoc($connect, $userAsReceiver)) {
            if (!in_array($rowsingle['sender'], $contacted)) {
                // verifier
                $contactedid = $rowsingle['sender'];
                array_push($contacted, $contactedid);

                // latest content
                $varContactS = executeNonQuery($connect, "SELECT max(message_id) as id from messages where sender = '$contactedid' and receiver = '$id'");
                $sender = fetchAssoc($connect, $varContact);
                $sender = (int)($sender['id']);
                $varContactR = executeNonQuery($connect, "SELECT max(message_id) as id from messages where receiver = '$contactedid' and sender = '$id'");
                $receiver = fetchAssoc($connect, $varContactR);
                $receiver = (int)($receiver['id']);

                if ($sender > $receiver) {
                    $var = executeNonQuery($connect, "SELECT content from messages where message_id = '$sender'");
                    $latestContent = fetchAssoc($connect, $var);
                    $latestContent = $latestContent['content'];
                } else {
                    $var = executeNonQuery($connect, "SELECT content from messages where message_id = '$receiver'");
                    $latestContent = fetchAssoc($connect, $var);
                    $latestContent = 'You: ' . $latestContent['content'];
                }

                $rowsingle['latest_content'] = $latestContent;

                // image of contacted
                $varImg = executeNonQuery($connect, "SELECT imgname from members where member_id = '$contactedid'");
                $imgcontacted = fetchAssoc($connect, $varImg);
                $rowsingle['contactedimg'] = $imgcontacted['imgname'];
                array_push($resultArr, $rowsingle);
            }
        }
    }

    echo json_encode($resultArr);
}

/*if (isset($_POST['maxidConvo'])) {
    $convowith = $_POST['maxidConvo'];
    $self = $_SESSION['id'];

    $senderVar = executeNonQuery($connect, "SELECT max(message_id) as id from messages where sender = '$convowith' and receiver = '$self'");
    $sender = fetchAssoc($connect, $senderVar);
    $sender = (int)($sender['id']);

    $receiverVar = executeNonQuery($connect, "SELECT max(message_id) as id from messages where receiver = '$convowith' and sender = '$self'");
    $receiver = fetchAssoc($connect, $receiverVar);
    $receiver = (int)($receiver['id']);
    if ($sender > $receiver) {
        $maxIdVar = executeNonQuery($connect, "SELECT content from messages where message_id = '$sender'");
        $maxId = fetchAssoc($connect, $maxIdVar);
        $maxId = $maxId['content'];
    } else {
        $maxIdVar = executeNonQuery($connect, "SELECT content from messages where message_id = '$receiver'");
        $maxId = fetchAssoc($connect, $maxIdVar);
        $maxId = 'You: ' . $maxId['content'];
    }

    echo $maxId;
}*/

if (isset($_GET['unreads'])) {
    $convowith = $_GET['unreads'];
    $id = $_GET['id'];
    $query = executeNonQuery($connect, "SELECT * from messages where sender = '$convowith' and receiver = '$id' and message_status_receiver = 'unread'");

    echo numRows($connect, $query);
}

if (isset($_GET['getconvos'])) {
    $contact = $_GET['getconvos'];
    $user = $_GET['id'];

    $userAsReceiver = executeNonQuery($connect, "SELECT messages.message_id as message_id, messages.sender as sender, messages.receiver as receiver, messages.content as content, messages.message_status_receiver as message_status_receiver, messages.message_status_sender as message_status_sender, messages.click_status_receiver as click_status_receiver, messages.click_status_sender as click_status_sender, messages.date_sent as date_sent, concat(members.member_first, ' ', members.member_last) as name, members.imgname as imgname FROM messages join members where messages.sender = members.member_id and messages.receiver = '$user' and messages.sender = '$contact' order by messages.message_id desc");

    $userAsSender = executeNonQuery($connect, "SELECT messages.message_id as message_id, messages.sender as sender, messages.receiver as receiver, messages.content as content, messages.message_status_receiver as message_status_receiver, messages.message_status_sender as message_status_sender, messages.click_status_receiver as click_status_receiver, messages.click_status_sender as click_status_sender, messages.date_sent as date_sent, concat(members.member_first, ' ', members.member_last) as name, members.imgname as imgname FROM messages join members where messages.receiver = members.member_id and messages.sender = '$user' and messages.receiver = '$contact' order by messages.message_id desc");

    $resultArr = [];

    while ($row = fetchAssoc($connect, $userAsReceiver)) {
        array_push($resultArr, $row);
    }

    while ($row = fetchAssoc($connect, $userAsSender)) {
        array_push($resultArr, $row);
    }

    executeNonQuery($connect, "UPDATE `messages` SET `message_status_receiver`='read', `click_status_receiver`='clicked' WHERE receiver = '$user' and sender = '$contact'");

    echo json_encode($resultArr);

    // $inserted = 0;
}
