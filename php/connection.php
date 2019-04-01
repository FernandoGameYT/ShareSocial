<?php

    $pdo = new PDO("mysql:host=localhost;dbname=sharesocial", "root", "");
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $direction = "http://localhost/ShareSocial/";
    $cookie_url = "localhost";

?>