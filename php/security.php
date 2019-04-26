<?php

    if(!isset($direction)) {
        require(__DIR__."/connection.php");
    }

    if(!isset($_SESSION["SessionId"])) {
        if(isset($_COOKIE["SessionId"])) {
            $_SESSION["SessionId"] = $_COOKIE["SessionId"];
        }else{
            header("Location: ".$direction."register.php");
            exit;
        }
    }

    $check_user = $pdo -> prepare("SELECT * FROM users WHERE SessionId = ?");

    if($check_user -> execute([$_SESSION["SessionId"]])) {
        if($check_user -> rowCount() > 0) {
            $user_data = $check_user -> fetch();
        }else{
            header("Location: ".$direction."register.php#register");
            exit;
        }
    }

?>