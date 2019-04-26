<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - Publicacion</title>
    <base href="../../">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">
</head>
<body>
    <?php
        include("php/complements/navbar.php");
        include("php/controllers/Publication.php");

        if(!isset($_GET["publicationId"])) {
            header("Location: $direction");
            exit;
        }

        $publication = new Publication($_GET["publicationId"]);

        if(!$publication -> getPublication($pdo, $user_data["Id"])) {
            header("Location: $direction");
            exit;
        }

        $publication -> getComments($pdo, 10, 0, $user_data["Id"]);
        $heartState = "";

        if($publication -> liked) {
            $heartState = "red";
        }
    ?>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-6 p-0 all-publications-container" id="all-publications-container">
                <div class="col-12 mt-5" id="publication-<?php echo $publication -> id;?>">
                    <div class="profile-container">
                        <a class="h4" href="User/<?php echo $publication -> username;?>"><?php echo $publication -> username;?></a>
                        <span class="float-right"><?php echo $publication -> date?></span>
                    </div>

                    <?php
                    
                        if($publication -> imageURL != "") {
                            echo '<img src="'.$publication -> imageURL.'" class="w-100" alt="imagen de una publicacion" />';
                        }

                    ?>

                    <div class="content-container">
                        <?php echo $publication -> content;?>
                    </div>

                    <div class="like-container">
                        <button class="<?php echo $heartState;?>" id="heart-<?php echo $publication -> id;?>" onclick="like(<?php echo $publication -> id;?>)">
                            <span class="fa fa-heart"></span>
                        </button>

                        <span class="info" id="info-<?php echo $publication -> id;?>">
                            <?php
                            
                                if($publication -> liked) {
                                    if($publication -> likes == 1) {
                                        echo "A ti te gusta esta publicacion.";
                                    }else{
                                        echo "A ti y a ".($publication -> likes - 1)." personas mas le a gustado esta publicacion.";
                                    }
                                }else{
                                    echo "A ".$publication -> likes." personas les a gustado esta publicacion.";
                                }
                            
                            ?>
                        </span>

                        <?php
                        
                            if($publication -> userPublication) {
                                echo '<button class="float-right more-options" title="Eliminar la publicacion" onclick="deletePublication('.$publication -> id.')">
                                        <span class="fa fa-trash"></span>
                                    </button>';
                            }
                        
                        ?>
                    </div>
                </div>

                <div class="col-12 pl-5 mt-2">
                    <form action="#" method="post" class="add-comment-form">
                        <input type="hidden" name="publicationId" value="<?php echo $publication -> id;?>" />
                        <textarea class="w-100" name="content" required></textarea>
                        <input type="hidden" name="username" value="<?php echo $publication -> username;?>" />
                        <button class="btn btn-primary w-100" type="button" onclick="addComment(this)">
                            Comentar
                        </button>
                    </form>
                </div>
                <div id="comments-container-<?php echo $publication -> id;?>">
                    <?php
                    
                        foreach($publication -> comments as $comment) {
                            $deleteButton = "";

                            if($comment -> userComment) {
                                $deleteButton = '
                                <button class="btn btn-link p-0 delete" onclick="deleteComment('.$comment -> id.', '.$publication -> id.')">
                                    Eliminar
                                </button>
                                ';
                            }
                            echo '
                            <div class="col-12 pl-5 mt-3" id="comment-'.$publication -> id.'-'.$comment -> id.'">
                                <div class="comment-profile-container">
                                    <a class="h6" href="User/'.$comment -> username.'">'.$comment -> username.'</a>
                                    <span class="float-right">'.$comment -> date.'</span><br>
                                    <span class="content">'.$comment -> content.'</span>
        
                                    '.$deleteButton.'
        
                                </div>
                            </div>
                            ';
                        }
                    
                    ?>
                </div>
                <?php
                
                    if(count($publication -> comments) >= 10) {
                        echo '<div class="col-12 pl-5 text-center" id="charge-comments-button-'.$publication -> id.'" onclick="chargeMoreComments('.$publication -> id.')">
                                <button class="btn btn-link">
                                    Cargar mas comentarios.
                                </button>
                            </div>';
                    }
                
                ?>
            </div>
        </div>
    </div>
    
    <?php
    
        include("php/complements/footer.php");
    
    ?>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
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

        function deletePublication(id) {
            if(!confirm("¿Desea eliminar esta publicacion?")) {
                return;
            }

            loader.active();

            $.ajax({
            url: "php/publicationSystem.php",
            method: "post",
            data: "type=delete&id="+id,
            success: data => {
                data = JSON.parse(data);

                if(data.state) {
                    alerts.add("success", data.msg);
                }

                $("#publication-"+id).remove();

                loader.desactive();
                location.href = "./";
            },
            });
        }
    </script>
</body>
</html>