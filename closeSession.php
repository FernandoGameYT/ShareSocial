<?php

    session_start();
    include("php/connection.php");

    setcookie("SessionId", "", time() - 1, "/", $cookie_url);

    session_destroy();

    header("Location: $direction");

?>