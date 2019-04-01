<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - Inicio</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="d-flex justify-content-center">

    <div class="form-container mt-5" id="login-form">
        <form action="#" method="post">
            <span class="form-title">Iniciar Sesion</span>
            <hr>

            <label for="username-login">Coloca tu nombre de usuario</label>
            <input type="text" name="username" id="username-login" />

            <label for="password-login">Coloca tu contraseña</label>
            <input type="password" name="password" id="password-login" />

            <button type="submit">
                Iniciar Sesion
            </button>
            
            <a href="javascript:void(0)" onclick="changeForm()">
                ¿No tienes una cuenta?. Registrarse
            </a>
        </form>
    </div>

    <div class="form-container d-none mt-5" id="register-form">
        <form action="#" method="post">
            <span class="form-title">Registrar una cuenta</span>
            <hr>

            <label for="username">Coloca tu nombre de usuario</label>
            <input type="text" name="username" id="username" minlength="4" maxlength="30" required />

            <label for="email">Coloca tu correo electronico</label>
            <input type="email" name="email" id="email" maxlength="100" required />
            
            <label for="password">Coloca tu contraseña</label>
            <input type="password" name="password" id="password" minlength="6" required />
            
            <label for="password-confirm">Repite tu contraseña</label>
            <input type="password" name="password-confirm" id="password-confirm" minlength="6" required />

            <div class="g-recaptcha" data-sitekey="6LejImoUAAAAANtXskK3qEho14Tntq_B6ucC-reD"></div>

            <button type="submit">
                Registrarse
            </button>

            <a href="javascript:void(0)" onclick="changeForm()">
                ¿Ya tienes una cuenta?. Iniciar sesion
            </a>
        </form>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
    <script>
        const loginForm = $("#login-form");
        const registerForm = $("#register-form");

        function changeForm() {
            if(loginForm.hasClass("d-none")) {
                loginForm.removeClass("d-none");
                registerForm.addClass("d-none");
            }else{
                loginForm.addClass("d-none");
                registerForm.removeClass("d-none");
            }
        }

        const hash = window.location.hash;

        if(hash == "#register") {
            loginForm.addClass("d-none");
            registerForm.removeClass("d-none");
        }

        loginForm.submit(e => {
            e.preventDefault();

            const username = $("#username-login").val();
            const password = $("#password-login").val();

            const data = {
                type: "login",
                username: username,
                password: password,
            };

            loader.active();

            $.ajax({
                url: "php/userSystem.php",
                method: "post",
                data: data,
                success: data => {
                    data = JSON.parse(data);

                    if(data.state) {
                        location.href = "./";
                    }else{
                        alerts.add("error", data.msg);
                    }
                    loader.desactive();
                }
            })
        });

        registerForm.submit(e => {
            e.preventDefault();

            const username = $("#username").val();
            const email = $("#email").val();
            const password = $("#password").val();
            const passwordConfirm = $("#password-confirm").val();
            const recaptcha = $("#g-recaptcha-response").val();

            if(password != passwordConfirm) {
                alerts.add("error", "Las contraseñas no coinciden.");
            }else if(recaptcha == "") {
                alerts.add("error", "Debes marcar el captcha.");
            }

            const data = {
                type: "register",
                username: username,
                email: email,
                password: password,
                recaptcha: recaptcha,
            };

            loader.active();

            $.ajax({
                url: "php/userSystem.php",
                method: "post",
                data: data,
                success: data => {
                    data = JSON.parse(data);

                    if(data.state) {
                        alerts.add("success", data.msg);
                    }else{
                        alerts.add("error", data.msg);
                    }

                    loader.desactive();
                },
            })
        });
    </script>
</body>
</html>