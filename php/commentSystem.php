<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "add") {
        $publicationId = $_POST["publicationId"];
        $content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);

        $insert_comment = $pdo -> prepare("INSERT INTO comments (Id, UserId, PublicationId, Content) VALUES (NULL, ?, ?, ?)");

        if($insert_comment -> execute([$user_data["Id"], $publicationId, $content])) {
            $get_comment_id = $pdo -> prepare("SELECT Id FROM comments WHERE UserId = ? AND PublicationId = ? ORDER BY Id DESC LIMIT 1");
            
            if($get_comment_id -> execute([$user_data["Id"], $publicationId])) {
                include("controllers/NotificationController.php");
                $nc = new NotificationController($user_data["Id"]);

                $nc -> notifyComment($pdo, $publicationId);

                echo $get_comment_id -> fetch()["Id"];
                exit;
            }
        }
    } else if($type == "get") {
        $publicationId = $_POST["publicationId"];
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];

        include("controllers/Publication.php");

        $publication = new Publication($publicationId);
        $publication -> getComments($pdo, $limit, $offset, $user_data["Id"]);
        echo json_encode($publication -> comments);
        exit;
    }
    // else if($type == "edit") {
    //     $commentId = $_POST["commentId"];
    //     $content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);

    //     $update_comment = $pdo -> prepare("UPDATE comments SET Content = ? WHERE Id = ?, UserId = ?");

    //     if($update_comment -> execute([$content, $commentId, $user_data["Id"]])) {
    //         if($update_comment -> rowCount() > 0) {
    //             $msg = [
    //                 "state" => true,
    //                 "msg" => "Comentario editado con exito."
    //             ];
    //             echo json_encode($msg);
    //             exit;
    //         }else{
    //             $msg = [
    //                 "state" => false,
    //                 "msg" => "A ocurrido un error al editar el comentario."
    //             ];
    //             echo json_encode($msg);
    //             exit;
    //         }
    //     }
    // } 
    else if($type == "delete") {
        $commentId = $_POST["commentId"];

        $delete_comment = $pdo -> prepare("DELETE FROM comments WHERE Id = ? AND UserId = ?");

        if($delete_comment -> execute([$commentId, $user_data["Id"]])) {
            if($delete_comment -> rowCount() > 0) {
                echo true;
                exit;
            }else{
                echo "A ocurrido un error al eliminar el comentario.";
                exit;
            }
        }
    }

?>