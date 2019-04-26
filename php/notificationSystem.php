<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "add") {
        $content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);
        $link = $_POST["link"];
        $add_notification = $pdo -> prepare("INSERT INTO notifications (Id, UserId, Content, Link) VALUES (NULL, ?, ?, ?)");

        if($add_notification -> execute([$user_data["Id"], $content, $link])) {
            echo true;
            exit;
        }
    } else if($type == "delete") {
        $notificationId = $_POST["notificationId"];

        $delete_notification = $pdo -> prepare("DELETE FROM notifications WHERE Id = ? AND UserId = ?");

        if($delete_notification -> execute([$notificationId, $user_data["Id"]])) {
            if($delete_notification -> rowCount() > 0) {
                echo true;
                exit;
            }else{
                echo "A ocurrido un error";
                exit;
            }
        }
    } else if($type == "get") {
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];

        include("controllers/NotificationController.php");
        $nc = new NotificationController($user_data["Id"]);

        $nc -> getNotifications($pdo, $limit, $offset);
        echo json_encode($nc -> notifications);
        exit;
    }

?>