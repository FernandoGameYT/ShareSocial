<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - <?php echo $_GET["username"];?></title>
    <base href="../">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">
</head>
<body>
    <?php
        include("php/complements/navbar.php");

        if(!isset($_GET["username"])) {
            header("Location: $direction");
        }

        $username = $_GET["username"];
        
        $get_user = $pdo -> prepare("SELECT * FROM users WHERE Username = ?");

        if($get_user -> execute([$username])) {
            if($get_user -> rowCount() < 1) {
                header("Location: $direction");
            }else{
                $user = $get_user -> fetch();
            }
        }

    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6 col-lg-8 col-sm-10 col-12 p-3">
                <span class="d-block h3 font-weight-bold">
                    <?php echo $user["Username"];?>
                </span>
                <span class="pr-2 h6 d-block d-md-inline">
                    Seguidores: <?php echo $user["Followers"];?>
                </span>
                <span class="pr-2 h6 d-block d-md-inline">
                    Siguiendo: <?php echo $user["Following"];?>
                </span>
                <span class="pr-2 h6 d-block d-md-inline">
                    Publicaciones: <?php echo $user["Publications"];?>
                </span>
            </div>
            <?php
            
                if($user["Id"] == $user_data["Id"]) {
                    echo '
                    <div class="col-xl-6 col-lg-4 col-sm-2 col-12 text-sm-right">
                        <a class="btn btn-primary m-sm-3 px-5 px-sm-4 py-3 font-weight-bold" href="configuration.php">
                            <span class="fa fa-cog"></span>
                        </a>
                    </div>';
                }else{
                    $check_follow = $pdo -> prepare("SELECT * FROM followers WHERE FollowerId = ? AND UserId = ?");

                    if($check_follow -> execute([$user_data["Id"], $user["Id"]])) {
                        if($check_follow -> rowCount() > 0) {
                            $unfollow = "";
                            $follow = "d-none";
                        }else{
                            $unfollow = "d-none";
                            $follow = "";
                        }
                        echo '
                        <div class="col-xl-6 col-lg-4 col-12 text-lg-right">
                            <button class="btn btn-primary m-lg-3 px-5 py-3 font-weight-bold '.$unfollow.'" id="unfollow-button" onclick="unfollow()">
                                DEJAR DE SEGUIR
                            </button>
                            <button class="btn btn-primary m-lg-3 px-5 py-3 font-weight-bold '.$follow.'" id="follow-button" onclick="follow()">
                                SEGUIR
                            </button>
                        </div>';
                    }
                }
            
            ?>
        </div>

        <div class="row justify-content-center mt-3">
            <div class="col-12 bg-light pt-4 pb-1">
                <div class="select-menu">
                    <ul>
                        <li onclick="changeMenu('publications')" id="menu-publications" class="menu-option d-block d-md-inline">
                            Publicaciones
                        </li>
                        <li onclick="changeMenu('followers')" id="menu-followers" class="menu-option d-block d-md-inline">
                            Seguidores
                        </li>
                        <li onclick="changeMenu('following')" id="menu-following" class="menu-option d-block d-md-inline">
                            Siguiendo
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row justify-content-center d-none" id="publications-element-container">
            <div class="col-md-10 col-lg-6 p-0 all-publications-container" id="all-publications-container"></div>
            
            <div class="w-100"></div>

            <div class="col-md-10 col-lg-6 mt-5">
                <button class="btn btn-primary w-100 py-3 font-weight-bold" id="chargeComments" onclick="chargeMorePublications()">
                    Cargar mas publicaciones
                </button>
            </div>
        </div>

        <div class="row mt-4" id="followers-element-container">
            <div class="col-12" id="followers-container"></div>
            <div class="col-12 text-center" id="followers-offset" onclick="followersOffset()">
                <button class="btn btn-link">Cargar mas seguidores</button>
            </div>
        </div>
        
        <div class="row mt-4 d-none" id="following-element-container">
            <div class="col-12" id="following-container"></div>
            <div class="col-12 text-center" id="following-offset" onclick="followingOffset()">
                <button class="btn btn-link">Cargar mas seguidores</button>
            </div>
        </div>
    </div>
    
    <?php

        echo "<script>const userId = ".$user["Id"].";</script>"
    
    ?>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
    <script src="js/systems/publicationSystem.js"></script>
    <script src="js/systems/commentSystem.js"></script>
    <script src="js/systems/followersSystem.js"></script>
    <script>
        const followerLimit = 20;
        var followersOffsetNumber = 0;

        changeMenu("publications");

        function changeMenu(id) {
            $("#publications-element-container").addClass("d-none");
                $("#followers-element-container").addClass("d-none");
                $("#following-element-container").addClass("d-none");

            if(id == "publications") {
                $("#publications-element-container").removeClass("d-none");
                $("#all-publications-container").html("");
                getPublicationsById(0, 10, userId);
            } else if(id == "followers") {
                $("#followers-element-container").removeClass("d-none");
                $("#followers-container").html("");
                getFollowers();
            } else if(id == "following") {
                $("#following-element-container").removeClass("d-none");
                $("#followers-container").html("");
                getFollowing();
            }
            
            $(".menu-option").removeClass("active");
            $("#menu-"+id).addClass("active");
            followersOffsetNumber = 0;
        }

        function follow() {
            $.ajax({
                url: "php/followersSystem.php",
                method: "post",
                data: `type=add&userId=${userId}`,
                success: data => {
                    data = JSON.parse(data);

                    if(data.status) {
                        $("#unfollow-button").removeClass("d-none");
                        $("#follow-button").addClass("d-none");
                    }
                }
            });
        }

        function unfollow() {
            $.ajax({
                url: "php/followersSystem.php",
                method: "post",
                data: `type=delete&userId=${userId}`,
                success: data => {
                    data = JSON.parse(data);

                    if(data.status) {
                        $("#unfollow-button").addClass("d-none");
                        $("#follow-button").removeClass("d-none");
                    }
                }
            });
        }

        function getPublicationsById(offset, limit, id) {
            loader.active();
            $.ajax({
                url: "php/publicationSystem.php",
                method: "post",
                data: `type=getById&offset=${offset}&limit=${limit}&id=${id}`,
                success: data => {
                    data = JSON.parse(data);

                    if(data.publications.length < 10) {
                        $("#chargeComments").remove();
                    }

                    if(data.publications.length == 0) {
                        $("#all-publications-container").html("<h3 class='text-center mt-4'>No a publicado nada.</h3>");
                    }

                    appendPublications(data.publications, data.username);
                    loader.desactive();
                },
            });
        }
        
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