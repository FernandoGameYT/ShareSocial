<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "add") {
        $content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);

        if($_POST["hasImage"] == "ok") {
            $img = $_FILES["img"];

            if($img["type"] == "image/png" || $img["type"] == "image/jpg" || $img["type"] == "image/jpeg") {
                $sourcePath = $img["tmp_name"];

                $fileName = time()."_".$img["name"];

                $targetPath = "../img/publications/".$fileName;

                if(move_uploaded_file($sourcePath, $targetPath)) {
                    $add_publication = $pdo -> prepare("INSERT INTO publications (Id, UserId, Content, ImageURL) VALUES (NULL, ?, ?, ?)");
                    $imageURL = "img/publications/".$fileName;

                    if($add_publication -> execute([$user_data["Id"], $content, $imageURL])) {
                        include("controllers/NotificationController.php");
                        $nc = new NotificationController($user_data["Id"]);

                        $nc -> notifyPublication($pdo, $user_data["Username"]);

                        $update_publication_count = $pdo -> prepare("UPDATE users SET Publications = ? WHERE Id = ?");

                        if($update_publication_count -> execute([$user_data["Publications"] + 1, $user_data["Id"]])) {
                            $msg = [
                                "state" => true,
                                "msg" => "La publicacion se publico con exito.",
                            ];
                            echo json_encode($msg);
                            exit;
                        }
                    }
                }else{
                    $msg = [
                        "state" => false,
                        "msg" => "No se a podido subir la imagen a el servidor. Intentelo mas tarde.",
                    ];
                    echo json_encode($msg);
                    exit;
                }
            }else{
                echo "A ocurrido un error";
                exit;
            }
        }else{
            $add_publication = $pdo -> prepare("INSERT INTO publications (Id, UserId, Content) VALUES (NULL, ?, ?)");

            if($add_publication -> execute([$user_data["Id"], $content])) {
                include("controllers/NotificationController.php");
                $nc = new NotificationController($user_data["Id"]);

                $nc -> notifyPublication($pdo, $user_data["Username"]);
                
                $update_publication_count = $pdo -> prepare("UPDATE users SET Publications = ? WHERE Id = ?");

                if($update_publication_count -> execute([$user_data["Publications"] + 1, $user_data["Id"]])) {
                    $msg = [
                        "state" => true,
                        "msg" => "La publicacion se publico con exito.",
                    ];
                    echo json_encode($msg);
                    exit;
                }
            }
        }
    }else if($type == "delete") {
        $id = $_POST["id"];

        $delete_publication = $pdo -> prepare("DELETE FROM publications WHERE Id = ? AND UserId = ?");

        if($delete_publication -> execute([$id, $user_data["Id"]])) {
            if($delete_publication -> rowCount() < 1) {
                echo "A ocurrido un error";
                exit;
            }else{
                $delete_comments = $pdo -> prepare("DELETE FROM comments WHERE PublicationId = ?");
                $delete_likes = $pdo -> prepare("DELETE FROM likes WHERE PublicationId = ?");

                if($delete_comments -> execute([$id]) && $delete_likes -> execute([$id])) {
                    $update_publication_count = $pdo -> prepare("UPDATE users SET Publications = ? WHERE Id = ?");

                    if($update_publication_count -> execute([$user_data["Publications"] - 1, $user_data["Id"]])) {
                        $msg = [
                            "state" => true,
                            "msg" => "Publicacion borrada con exito."
                        ];
                        echo json_encode($msg);
                        exit;
                    }
                }
            }
        }
    } else if($type == "get") {
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];

        include("controllers/PublicationsController.php");
        $pc = new PublicationsController($user_data["Id"]);
        $pc -> getPublications($pdo, $limit, $offset);
        $data = [
            "username" => $user_data["Username"],
            "publications" => $pc -> publications
        ];
        echo json_encode($data);
        exit;
    } else if($type == "getById") {
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];
        $id = $_POST["id"];

        include("controllers/PublicationsController.php");
        $pc = new PublicationsController($id);
        $pc -> getPublicationsById($pdo, $limit, $offset);
        $data = [
            "username" => $user_data["Username"],
            "publications" => $pc -> publications
        ];
        echo json_encode($data);
        exit;
    }
    // else if($type == "edit") {
    //     $id = $_POST["Id"];
    //     $content = filter_var($_POST["content"], FILTER_SANITIZE_STRING);

    //     $get_publication = $pdo -> prepare("SELECT ImageURL FROM publications WHERE Id = ? AND UserId = ?");

    //     if($get_publication -> execute([$id, $user_data["Id"]])) {
    //         if($get_publication -> rowCount() < 1) {
    //             echo "A ocurrido un error";
    //             exit;
    //         }else{
    //             $publication = $get_publication -> fetch();
    //         }
    //     }else{
    //         echo "A ocurrido un error";
    //         exit;
    //     }

    //     if($_POST["hasImage"] == "ok") {
    //         $img = $_FILES["img"];

    //         if($img["type"] == "image/png" || $img["type"] == "image/jpg" || $img["type"] == "image/jpeg") {
    //             $sourcePath = $img["tmp_name"];

    //             $fileName = time()."_".$img["name"];

    //             $targetPath = "../img/publications/".$fileName;

    //             if(move_uploaded_file($sourcePath, $targetPath)) {
    //                 if($publication["ImageURL"] != "") {
    //                     unlink("../" . $publication["ImageURL"]);
    //                 }
                    
    //                 $update_publication = $pdo -> prepare("UPDATE publications SET Content = ?, ImageURL = ? WHERE Id = ?");
    //                 $imageURL = "img/publications/".$fileName;

    //                 if($update_publication -> execute([$content, $imageURL, $id])) {
    //                     $msg = [
    //                         "state" => true,
    //                         "msg" => "La publicacion se edito con exito.",
    //                     ];
    //                     echo json_encode($msg);
    //                     exit;
    //                 }
    //             }else{
    //                 $msg = [
    //                     "state" => false,
    //                     "msg" => "No se a podido subir la imagen a el servidor. Intentelo mas tarde.",
    //                 ];
    //                 echo json_encode($msg);
    //                 exit;
    //             }
    //         }else{
    //             echo "A ocurrido un error";
    //             exit;
    //         }
    //     }else{
    //         if($publication["ImageURL"] != "") {
    //             unlink("../" . $publication["ImageURL"]);
    //         }

    //         $update_publication = $pdo -> prepare("UPDATE publications SET Content = ?, ImageURL = NULL WHERE Id = ?");
            
    //         if($update_publication -> execute([$content, $id])) {
    //             $msg = [
    //                 "state" => true,
    //                 "msg" => "La publicacion se edito con exito.",
    //             ];
    //             echo json_encode($msg);
    //             exit;
    //         }
    //     }
    // }

?>