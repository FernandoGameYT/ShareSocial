<?php
    session_start();

    if(!isset($direction)) {
        require(__DIR__."/../connection.php");
    }

    if(!isset($_SESSION["SessionId"])) {
        if(isset($_COOKIE["SessionId"])) {
            $_SESSION["SessionId"] = $_COOKIE["SessionId"];
        }else{
            header("Location: $direction/register.php");
            exit;
        }
    }

    $check_user = $pdo -> prepare("SELECT * FROM users WHERE SessionId = ?");

    if($check_user -> execute([$_SESSION["SessionId"]])) {
        if($check_user -> rowCount() > 0) {
            $user_data = $check_user -> fetch();
        }else{
            header("Location: $direction/register.php#register");
            exit;
        }
    }

?>

<nav class="navbar navbar-expand-md navbar-light bg-light">
    <a class="navbar-brand font-weight-bold" href="<?php echo $direction;?>">
        <img src="<?php echo $direction;?>img/main-icon.png" width="30" height="30" class="d-inline-block align-top" alt="">
        ShareSocial
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a class="nav-item nav-link" href="<?php echo $direction;?>">
                <span class="fa fa-home"></span>
                <span class="font-weight-bold">Inicio</span>
            </a>
            <a class="nav-item nav-link" href="#">
                <span class="fa fa-bell"></span>
                <span class="font-weight-bold">Notificaciones</span>
            </a>
            <a class="nav-item nav-link" href="#">
                <span class="fa fa-envelope"></span>
                <span class="font-weight-bold">Mensajes Directos</span>
            </a>
            <a class="nav-item nav-link" href="#">
                <span class="fa fa-cog"></span>
                <span class="font-weight-bold">Configuracion</span>
            </a>
        </div>
    </div>
</nav>