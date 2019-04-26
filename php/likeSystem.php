<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "add") {
        $publicationId = $_POST["publicationId"];

        $check_publication = $pdo -> prepare("SELECT Likes FROM publications WHERE Id = ?");

        if($check_publication -> execute([$publicationId])) {
            if($check_publication -> rowCount() > 0) {
                $likes = $check_publication -> fetch()["Likes"];
            }else{
                echo "A ocurrido un error";
                exit;
            }
        }

        $check_like = $pdo -> prepare("DELETE FROM likes WHERE UserId = ? AND PublicationId = ?");

        if($check_like -> execute([$user_data["Id"], $publicationId])) {
            $update_publication = $pdo -> prepare("UPDATE publications SET Likes = ? WHERE Id = ?");

            if($check_like -> rowCount() > 0) {
                if($update_publication -> execute([$likes - 1, $publicationId])) {
                    echo $likes - 1;
                    exit;
                }
            }else{
                $insert_like = $pdo -> prepare("INSERT INTO likes (Id, UserId, PublicationId) VALUES (NULL, ?, ?)");

                if($insert_like -> execute([$user_data["Id"], $publicationId])) {
                    if($update_publication -> execute([$likes + 1, $publicationId])) {
                        include("controllers/NotificationController.php");
                        $nc = new NotificationController($user_data["Id"]);
        
                        $nc -> notifyLike($pdo, $publicationId, $user_data["Username"]);
                        
                        echo $likes + 1;
                        exit;
                    }
                }
            }
        }
    }

?>