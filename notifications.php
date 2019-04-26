<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - Notificaciones</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">
</head>
<body style="overflow-x: hidden;">
    <?php
        include("php/complements/navbar.php");
        include("php/controllers/NotificationController.php");
        $nc = new NotificationController($user_data["Id"]);
        $nc -> getNotifications($pdo, 10, 0);
    ?>

    <div class="container-fluid"></div> 
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6 text-center bg-light p-3">
                <span class="h3 font-weight-bold">Notificaciones</span>
            </div>
            <div class="w-100"></div>
            <div class="col-md-8 col-lg-6 border text-center" id="notifications-container" style="height: 500px;overflow-y: scroll;">
                <?php
                
                    foreach($nc -> notifications as $notification) {
                        echo '
                        <div class="w-100 p-3 text-left">
                            <a href="'.$notification["Link"].'">'.$notification["Content"].'</a>
                            <span class="float-right">'.$notification["Date"].'</span>
                        </div>
                        ';
                    }

                    if(count($nc -> notifications) < 1) {
                        echo '<span class="font-weight-bold d-block my-3">
                            No se han encontrado notificaciones.
                        </span>';
                    }
                
                ?>
            </div>
            <?php
            
                if(count($nc -> notifications) >= 10) {
                    echo '<div class="w-100"></div>
                    <div class="col-md-8 col-lg-6 border text-center p-0" onclick="chargeMoreNotifications()" id="chargeNotifications">
                        <button class="btn btn-link font-weight-bold">
                            Cargar mas notificaciones
                        </button>
                    </div>';
                }
            
            ?>
        </div>
    <?php
    
        include("php/complements/footer.php");
    
    ?>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
    <script>
        var notificationOffset = 0;
        function chargeMoreNotifications() {
            notificationOffset += 10;

            loader.active();

            $.ajax({
                url: "php/notificationSystem.php",
                method: "post",
                data: "type=get&limit=10&offset="+notificationOffset,
                success: notifications => {
                    notifications = JSON.parse(notifications);

                    notifications.forEach(notification => {
                        $("#notifications-container").append(`
                        <div class="w-100 p-3 text-left">
                            <a href="${notification['Link']}">${notification["Content"]}</a>
                            <span class="float-right">${notification["Date"]}</span>
                        </div>
                        `);
                    });

                    if(notifications.length < 10) {
                        $("#chargeNotifications").remove();
                    }

                    loader.desactive();
                },
            })
        }
    </script>
</body>
</html>