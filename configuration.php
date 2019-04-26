<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - Configuración</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">
</head>
<body>
    <?php
        include("php/complements/navbar.php");
    ?>

    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-lg-6 configuration-user-container p-0">
                <div class="option p-3 border">
                    <span class="h4">Nombre de usuario</span>
                    <button class="float-right" onclick="openConfiguration('change-username-container')">
                        <span class="fa fa-pencil-alt"></span>
                    </button>
                </div>

                <div class="d-none text-center p-3 configuration-form-container" id="change-username-container">
                    <form action="#" id="change-username-form">
                        <span class="h5 mb-4 d-block font-weight-bold">
                            Coloca el nuevo nombre de usuario:
                        </span>

                        <input type="text" name="username" class="form-control mb-4" value="<?php echo $user_data['Username'];?>" minlength="4" maxlength="30" required />

                        <button type="submit" class="btn btn-primary font-weight-bold py-2 px-5">
                            Aceptar
                        </button>
                        <button type="button" onclick="closeConfiguration('change-username-container')" class="btn btn-danger font-weight-bold py-2 px-5">
                            Cancelar
                        </button>
                    </form>
                </div>

                <div class="option p-3 border">
                    <span class="h4">Contraseña</span>
                    <button class="float-right" onclick="openConfiguration('change-password-container')">
                        <span class="fa fa-pencil-alt"></span>
                    </button>
                </div>

                <div class="d-none text-center p-3 configuration-form-container" id="change-password-container">
                    <form action="#" id="change-password-form">
                        <span class="h5 mb-4 d-block font-weight-bold">
                            Coloca tu antigua contraseña: 
                        </span>
                        <input type="password" name="old-password" id="old-password" class="form-control mb-4" minlength="6" required />
                        
                        <span class="h5 mb-4 d-block font-weight-bold">
                            Coloca la nueva contraseña: 
                        </span>
                        <input type="password" name="new-password" id="new-password" class="form-control mb-4" minlength="6" required />

                        <span class="h5 mb-4 d-block font-weight-bold">
                            Repite la contraseña: 
                        </span>
                        <input type="password" name="repeat-password" id="repeat-password" class="form-control mb-4" minlength="6" required />

                        <button type="submit" class="btn btn-primary font-weight-bold py-2 px-5">
                            Aceptar
                        </button>
                        <button type="button" onclick="closeConfiguration('change-password-container')" class="btn btn-danger font-weight-bold py-2 px-5">
                            Cancelar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
    <script>
        function openConfiguration(id) {
            $("#"+id).removeClass("d-none");
            $("#"+id).addClass("d-block");
        }

        function closeConfiguration(id) {
            $("#"+id).removeClass("d-block");
            $("#"+id).addClass("d-none");
        }

        $("#change-username-form").submit(e => {
            e.preventDefault();

            loader.active();

            $.ajax({
                url: "php/configurationSystem.php",
                method: "post",
                data: "type=changeUsername&"+$("#change-username-form").serialize(),
                success: data => {
                    data = JSON.parse(data);

                    if(data.status) {
                        alerts.add("success", data.msg);
                    }else{
                        alerts.add("error", data.msg);
                    }

                    loader.desactive();
                },
            });
        });
        
        $("#change-password-form").submit(e => {
            e.preventDefault();

            const oldPassword = $("#old-password").val();
            const newPassword = $("#new-password").val();
            const repeatPassword = $("#repeat-password").val();

            if(newPassword != repeatPassword) {
                alerts.add("error", "Las contraseñas no coinciden.");
            }else{
                loader.active();
                
                $.ajax({
                    url: "php/configurationSystem.php",
                    method: "post",
                    data: `type=changePassword&old-password=${oldPassword}&new-password=${newPassword}`,
                    success: data => {
                        data = JSON.parse(data);

                        if(data.status) {
                            alerts.add("success", data.msg);
                        }else{
                            alerts.add("error", data.msg);
                        }

                        loader.desactive();
                    },
                });
            }
        });
    </script>
</body>
</html>