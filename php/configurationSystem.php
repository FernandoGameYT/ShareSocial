<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "changeUsername") {
        $username = $_POST["username"];

        if(strlen($username) < 4 || strlen($username) > 30) {
            $msg = [
                "status" => false,
                "msg" => "A ocurrido un error."
            ];
            echo json_encode($msg);
            exit;
        }

        if($username == $user_data["Username"]) {
            $msg = [
                "status" => false,
                "msg" => "El nombre de usuario no a sido cambiado."
            ];
            echo json_encode($msg);
            exit;
        }

        $check_username = $pdo -> prepare("SELECT Id FROM users WHERE Username = ?");

        if($check_username -> execute([$username])) {
            if($check_username -> rowCount() > 0) {
                $msg = [
                    "status" => false,
                    "msg" => "El nombre de usuario ya existe."
                ];
                echo json_encode($msg);
                exit;
            }else{
                $update_username = $pdo -> prepare("UPDATE users SET Username = ? WHERE Id = ?");

                if($update_username -> execute([$username, $user_data["Id"]])) {
                    $msg = [
                        "status" => true,
                        "msg" => "El nombre de usuario se cambio con exito."
                    ];
                    echo json_encode($msg);
                    exit;
                }
            }
        }
    } else if($type == "changePassword") {
        $oldPassword = $_POST["old-password"];
        $newPassword = $_POST["new-password"];

        if(strlen($oldPassword) < 6 && strlen($newPassword) < 6) {
            $msg = [
                "status" => false,
                "msg" => "A ocurrido un error."
            ];
            echo json_encode($msg);
            exit;
        }

        if(password_verify($oldPassword, $user_data["Password"])) {
            $update_password = $pdo -> prepare("UPDATE users SET Password = ? WHERE Id = ?");
            $hash = password_hash($newPassword, PASSWORD_ARGON2I);

            if($update_password -> execute([$hash, $user_data["Id"]])) {
                $msg = [
                    "status" => true,
                    "msg" => "La contraseña se actualizo con exito."
                ];
                echo json_encode($msg);
                exit;
            }
        }else{
            $msg = [
                "status" => false,
                "msg" => "La contraseña es incorrecta."
            ];
            echo json_encode($msg);
            exit;
        }
    }

?>