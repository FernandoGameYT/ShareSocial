<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - Inicio</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">
</head>
<body>
    <?php
        include("php/complements/navbar.php");
        include("php/controllers/PublicationsController.php");

        $pc = new PublicationsController($user_data["Id"]);

        $pc -> getPublications($pdo, 10, 0);
    ?>

    <div class="container-fluid">
        <div class="row justify-content-center mt-3">
            <div class="col-md-10 col-lg-6 publication-container">
                <form action="#" method="post" id="publication-form">
                    <textarea name="content" id="publication" placeholder="¿Que quieres compartir con tus seguidores?" required></textarea>

                    <label for="img" id="add-image-label" class="btn btn-light add-img">
                        <span class="fa fa-file-image"></span>
                        Agregar imagen
                    </label>

                    <div class="preview-container d-none" id="preview-container">
                        <img src="" id="img-preview" class="preview" alt="preview">

                        <button class="delete" id="quit-image" type="button">
                            <span class="fa fa-times"></span>
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary publish">Publicar</button>

                    <input type="file" name="img" id="img" class="d-none" accept="image/x-png,image/jpeg" />
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-6 p-0 all-publications-container" id="all-publications-container">
                <?php

                    foreach($pc -> publications as $publication) {

                        echo '<div id="publication-'.$publication -> id.'">
                        <div class="col-12 mt-5">
                            <div class="profile-container">
                                <a class="h4" href="User/'.$publication -> username.'">'.$publication -> username.'</a>
                                <span>'.$publication -> date.'</span>
                            </div>
                        ';

                        if($publication -> imageURL != "") {
                            echo '<img src="'.$publication -> imageURL.'" class="w-100" alt="imagen de una publicacion" />';
                        }

                        $heartState = "";
                        $likeMsg = "A ".$publication -> likes." personas les a gustado esta publicacion.";

                        if($publication -> liked) {
                            if($publication -> likes == 1) {
                                $likeMsg = "A ti te gusta esta publicacion.";
                            }else{
                                $likeMsg = "A ti y a ".($publication -> likes - 1)." personas mas le a gustado esta publicacion.";
                            }
                            $heartState = "red";
                        }

                        echo '
                            <div class="content-container">
                            '.$publication -> content.'
                            </div>
            
                            <div class="like-container">
                                <button class="'.$heartState.'" id="heart-'.$publication -> id.'" onclick="like('.$publication -> id.')">
                                    <span class="fa fa-heart"></span>
                                </button>
            
                                <span class="info" id="info-'.$publication -> id.'">'.$likeMsg.'</span>
                        ';
                        
                        if($publication -> userPublication) {
                            echo '<button class="float-right more-options" title="Eliminar la publicacion" onclick="deletePublication('.$publication -> id.')">
                            <span class="fa fa-trash"></span>
                            </button>';
                        }

                        echo '
                            </div>
                        </div>
                        <div class="col-12 pl-5 mt-2">
                            <form action="#" method="post" class="add-comment-form">
                                <input type="hidden" name="publicationId" value="'.$publication -> id.'" />
                                <textarea class="w-100" name="content" required></textarea>
                                <input type="hidden" name="username" value="'.$user_data["Username"].'" />
                                <button class="btn btn-primary w-100" type="button" onclick="addComment(this)">
                                    Comentar
                                </button>
                            </form>
                        </div>
                        <div id="comments-container-'.$publication -> id.'">
                        ';

                        foreach($publication -> comments as $comment) {
                            echo '
                            <div class="col-12 pl-5 mt-3" id="comment-'.$publication -> id.'-'.$comment -> id.'">
                                <div class="comment-profile-container">
                                    <a class="h6" href="User/'.$comment -> username.'">'.$comment -> username.'</a>
                                    <span class="date">'.$comment -> date.'</span>
                                    <span class="content">'.$comment -> content.'</span>
                            ';

                            if($comment -> userComment) {
                                echo '<button class="btn btn-link p-0 delete" onclick="deleteComment('.$comment -> id.', '.$publication -> id.')">
                                        Eliminar
                                    </button>';
                            }

                            echo '
                                </div>
                            </div>
                            ';
                        }

                        echo "</div>";

                        if(count($publication -> comments) >= 10) {
                            echo '<div class="col-12 pl-5 text-center" id="charge-comments-button-'.$publication -> id.'">
                                <button class="btn btn-link" onclick="chargeMoreComments('.$publication -> id.')">
                                    Cargar mas comentarios.
                                </button>
                            </div>';
                        }
                        echo "</div>";
                    }
                
                ?>
            </div>
        </div>
        <?php
        
            if(count($pc -> publications) >= 10) {
                echo '
                <div class="row justify-content-center mt-5">
                    <div class="col-md-10 col-lg-6">
                        <button class="btn btn-primary w-100 py-3 font-weight-bold" id="chargeComments" onclick="chargeMorePublications()">
                            Cargar mas publicaciones
                        </button>
                    </div>
                </div>
                ';
            }
        
        ?>
    </div>

    <?php
    
        include("php/complements/footer.php");
    
    ?>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
    <script src="js/systems/publicationSystem.js"></script>
    <script src="js/systems/commentSystem.js"></script>
    <script>
        function like(id) {
            loader.active();

            $.ajax({
                url: "php/likeSystem.php",
                method: "post",
                data: "type=add&publicationId="+id,
                success: likes => {
                    var heartButton = $("#heart-"+id);

                    if(heartButton.hasClass("red")) {
                        heartButton.removeClass("red");
                        $("#info-"+id).html(`A ${likes} personas les a gustado esta publicacion.`);
                    }else{
                        heartButton.addClass("red");
                        if(likes == 1) {
                            $("#info-"+id).html(`A ti te gusta esta publicacion.`);
                        }else{
                            $("#info-"+id).html(`A ti y a ${likes-1} personas mas le a gustado esta publicacion.`);
                        }
                    }

                    loader.desactive();
                }
            });
        }
    </script>
</body>
</html>