<?php
    session_start();
    require(__DIR__."/../security.php");

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand font-weight-bold" href="<?php echo $direction;?>">
        <img src="<?php echo $direction;?>img/main-icon.png" width="30" height="30" class="d-inline-block align-top" alt="share social logo">
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
            <a class="nav-item nav-link" href="<?php echo $direction;?>notifications.php">
                <span class="fa fa-bell"></span>
                <span class="font-weight-bold">Notificaciones</span>
            </a>
            <a class="nav-item nav-link" href="<?php echo $direction;?>messages.php">
                <span class="fa fa-envelope"></span>
                <span class="font-weight-bold">Mensajes Directos</span>
            </a>
            <a class="nav-item nav-link" href="<?php echo $direction;?>configuration.php">
                <span class="fa fa-cog"></span>
                <span class="font-weight-bold">Configuracion</span>
            </a>
            <a class="nav-item nav-link" href="<?php echo $direction;?>closeSession.php">
                <span class="fa fa-power-off"></span>
                <span class="font-weight-bold">Cerrar sesion</span>
            </a>
        </div>
    </div>
</nav>