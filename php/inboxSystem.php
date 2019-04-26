<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "add") {
        $userId = $_POST["userId"];
        $content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);

        $insert_message = $pdo -> prepare("INSERT INTO messages (Id, ForUserId, ByUserId, Content) VALUES (NULL, ?, ?, ?)");

        if($insert_message -> execute([$userId, $user_data["Id"], $content])) {
            $get_message_id = $pdo -> prepare("SELECT Id FROM messages WHERE ByUserId = ?");

            if($get_message_id -> execute([$user_data["Id"]])) {
                echo $get_message_id -> fetch()["Id"];
                exit;
            }
        }
    } else if($type == "get") {
        $inboxId = $_POST["inboxId"];
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];

        include("controllers/InboxController.php");
        $ic = new InboxController($user_data["Id"], $inboxId);
        $ic -> getMessages($pdo, $limit, $offset);

        echo json_encode($ic -> messages);
        exit;
    } else if($type == "getNews") {
        $inboxId = $_POST["inboxId"];
        $finalMessageId = $_POST["finalMessageId"];

        include("controllers/InboxController.php");
        $ic = new InboxController($user_data["Id"], $inboxId);
        $ic -> getNewMessages($pdo, $finalMessageId);

        echo json_encode($ic -> messages);
        exit;
    } else if($type == "newChat") {
        $userId = $_POST["userId"];

        if($userId == $user_data["Id"]) {
            echo false;
            exit;
        }

        $insert_chat = $pdo -> prepare("INSERT INTO chats (Id, UserCreatorId, UserId) VALUES (NULL, ?, ?)");

        if($insert_chat -> execute([$user_data["Id"], $userId])) {
            echo true;
            exit;
        }
    } else if($type == "getChats") {
        include("controllers/InboxController.php");
        $ic = new InboxController($user_data["Id"], 0);
        $ic -> getChats($pdo);

        echo json_encode($ic -> chats);
        exit;
    }

?>