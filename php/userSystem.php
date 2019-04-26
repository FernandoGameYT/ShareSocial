<?php

    session_start();
    require("connection.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "register") {
        $username = filter_var(addslashes($_POST["username"]), FILTER_SANITIZE_STRING);
        $email = addslashes($_POST["email"]);
        $password = addslashes($_POST["password"]);
        $recaptcha = $_POST["recaptcha"];

        if(strlen($username) < 4 || strlen($username) > 30) {
            echo "A ocurrido un error.";
            exit;
        }else if(strlen($email) > 100) {
            echo "A ocurrido un error.";
            exit;
        }else if(strlen($password) < 6) {
            echo "A ocurrido un error.";
            exit;
        }else if(strlen($recaptcha) == 0) {
            echo "A ocurrido un error.";
            exit;
        }

        $ip = $_SERVER["REMOTE_ADDR"];
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LejImoUAAAAAMgCYUOS_CqWDSpJWGOvS9Hhl-vE&response=$recaptcha&remoteip=$ip");
        $recaptcha_state = json_decode($verify);

        if($recaptcha_state) {
            $check_user = $pdo -> prepare("SELECT Username, Email FROM users WHERE Username = ? OR Email = ?");

            if($check_user -> execute([$username, $email])) {
                if($check_user -> rowCount() > 0) {
                    $data = $check_user -> fetch();

                    if($data["Username"] == $username) {
                        $data = [
                            "state" => false,
                            "msg" => "El nombre de usuario ya existe."
                        ];
                        echo json_encode($data);
                        exit;
                    }else{
                        $data = [
                            "state" => false,
                            "msg" => "El correo electronico ya existe."
                        ];
                        echo json_encode($data);
                        exit;
                    }
                }

                $password = password_hash($password, PASSWORD_ARGON2I);
                $code = md5(time() . $username);

                $register_user = $pdo -> prepare("INSERT INTO users (Id, Username, Email, Password, ActivateCode) VALUES (NULL, ?, ?, ?, ?)");

                if($register_user -> execute([$username, $email, $password, $code])) {
                    $data = [
                        "state" => true,
                        "msg" => "Registro completado con exito. Mandamos un correo de verificacion a tu correo electronico."
                    ];
                    echo json_encode($data);
                    exit;
                }
            }
        }else{
            $data = [
                "state" => false,
                "msg" => "El captcha es invalido."
            ];
            echo json_encode($data);
            exit;
        }
    } else if($type == "login") {
        $username = filter_var(addslashes($_POST["username"]), FILTER_SANITIZE_STRING);
        $password = addslashes($_POST["password"]);

        $find_user = $pdo -> prepare("SELECT Password FROM users WHERE Username = ?");

        if($find_user -> execute([$username])) {
            if($find_user -> rowCount() < 1) {
                $data = [
                    "state" => false,
                    "msg" => "El nombre de usuario no existe."
                ];
                echo json_encode($data);
                exit;
            }

            $hash = $find_user -> fetch()["Password"];

            if(password_verify($password, $hash)) {
                $sessionId = md5(time() . $username);

                $update_sessionId = $pdo -> prepare("UPDATE users SET SessionId = ? WHERE Username = ?");

                if($update_sessionId -> execute([$sessionId, $username])) {
                    $_SESSION["SessionId"] = $sessionId;
                    setcookie("SessionId", $sessionId, time()+3600*24*30, "/", $cookie_url);

                    $data = [
                        "state" => true
                    ];
                    echo json_encode($data);
                    exit;
                }
            }else{
                $data = [
                    "state" => false,
                    "msg" => "La contraseña es incorrecta."
                ];
                echo json_encode($data);
                exit;
            }
        }
    } else if($type == "get") {
        include("security.php");

        $search = $_POST["search"];

        if((int) $search) {
            $get_user = $pdo -> prepare("SELECT Id, Username FROM users WHERE Id = ? AND Id <> ?");

            if($get_user -> execute([$search, $user_data["Id"]])) {
                if($get_user -> rowCount() > 0) {
                    echo json_encode($get_user -> fetchAll());
                    exit;
                } else {
                    echo json_encode(array());
                    exit;
                }
            }
        }else{
            $get_users = $pdo -> prepare("SELECT Id, Username FROM users WHERE Username LIKE ? AND Id <> ? LIMIT 10");

            if($get_users -> execute(['%'.$search.'%', $user_data["Id"]])) {
                if($get_users -> rowCount() > 0) {
                    echo json_encode($get_users -> fetchAll());
                    exit;
                } else {
                    echo json_encode(array());
                    exit;
                }
            }
        }
    }

?>