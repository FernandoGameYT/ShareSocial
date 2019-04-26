<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShareSocial - Mensajes directos</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/main-icon-white.png" type="image/x-icon">
</head>
<body>
    <?php
        include("php/complements/navbar.php");
        include("php/controllers/InboxController.php");

        $ic = new InboxController($user_data["Id"], 0);
        $ic -> getChats($pdo);
    ?>

    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6 p-0" id="chats">
                <div class="col-12 text-center bg-light p-3">
                    <span class="h3 font-weight-bold">Mensajes</span>
                </div>
                <div class="chats-container" id="chats-container">
                    <?php
                    
                        foreach($ic -> chats as $chat) {
                            echo '
                            <div class="col-12 p-3 chat" onclick="changeChat('.$chat["inboxId"].', \''.$chat["username"].'\')">
                                <span class="h5 font-weight-bold">'.$chat["username"].'</span>
                                <span class="d-block mt-2">'.substr($chat["message"], 0, 50).'</span>
                            </div>';
                        }
                    
                    ?>
                </div>
                <button class="btn btn-primary new-chat-button" onclick="openSearchUsers()">
                    <span class="fa fa-envelope"></span>
                </button>
            </div>
            <div class="col-md-8 p-0 col-lg-6 ml-4 messages-container d-none" id="chat">
                <div class="col-12 text-center bg-light p-3">
                    <button class="btn backward-button" onclick="closeChat()">
                        <span class="fa fa-angle-left"></span>
                    </button>
                    <span class="h3 font-weight-bold" id="chatName"></span>
                </div>

                <div id="messages"></div>

                <div class="send-message">
                    <form action="#" id="send-message-form">
                        <textarea name="content" required></textarea>
                        <input type="hidden" value="" id="message-user-id" name="userId" />
                        <button type="submit" class="btn btn-primary">
                            <span class="fa fa-paper-plane"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="search-users-container container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 form text-center p-4">
                <form action="#" method="post" id="search-users-form">
                    <span class="h3 font-weight-bold">Buscar un usuario</span>
                    <input type="search" name="search" class="form-control mt-4" placeholder="Coloca un nombre de usuario o el id" required />
                    <button type="submit" class="btn btn-primary mt-4 w-100 py-3 font-weight-bold">
                        Buscar Usuarios
                    </button>
                </form>
            </div>
            <div class="w-100"></div>
            <div class="col-lg-6 col-md-8 users-container text-center p-4">
                <span class="h3 font-weight-bold">Resultado: </span>

                <div id="users-container">
                    <h5 class="font-weight-bold mt-4">Busca a uno o mas usuarios.</h5>
                </div>
            </div>
        </div>

        <button class="close-button" onclick="closeSearchUsers()">
            <span class="fa fa-times"></span>
        </button>
    </div>

    <?php
    
        include("php/complements/footer.php");
    
    ?>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/complements.js"></script>
    <script>
        $("#search-users-form").submit(e => {
            e.preventDefault();

            loader.active();
            $.ajax({
                url: "php/userSystem.php",
                method: "post",
                data: "type=get&"+$("#search-users-form").serialize(),
                success: data => {
                    var users = JSON.parse(data);
                    $("#users-container").html('');

                    if(users.length > 0) {
                        users.forEach(user => {
                            $("#users-container").append(`
                            <div class="user mt-4 text-left">
                                <span class="h5">${user.Username}</span>

                                <button class="btn btn-primary float-right" onclick="newChat(${user.Id})">
                                    <span class="fa fa-envelope"></span>
                                </button>
                            </div>
                            `);
                        });
                    }else{
                        $("#users-container").html(`
                            <h5 class="mt-4 font-weight-bold">
                                No se a encontrado a ningún usuario.
                            </h5>
                        `);
                    }

                    loader.desactive();
                }
            })
        });

        function newChat(userId) {
            loader.active();
        
            $.ajax({
                url: "php/inboxSystem.php",
                method: "post",
                data: `type=newChat&userId=${userId}`,
                success: () => {
                    location.reload();
                }
            })
        }

        function openSearchUsers() {
            $(".search-users-container").addClass("active");
        }

        function closeSearchUsers() {
            $(".search-users-container").removeClass("active");
        }

        var chatOffset = 0;
        var chatId = 0;
        var finalMessageId = 0;

        function changeChat(inboxId, chatName) {
            $.ajax({
                url: "php/inboxSystem.php",
                method: "post",
                data: `type=get&inboxId=${inboxId}&limit=10&offset=${chatOffset}`,
                success: data => {
                    var messages = JSON.parse(data);
                    $("#messages").html("");
                    messages.forEach((msg, index) => {
                        var userMessage = "";

                        if(!msg.isMyMessage) {
                            userMessage = "user-message";
                        }

                        $("#messages").append(`
                            <div class="message ${userMessage}">${msg["Content"]}</div>
                        `);

                        if(index == messages.length - 1) {
                            finalMessageId = msg["Id"];
                        }
                    });

                    $("#chats").addClass("d-none");
                    $("#chat").removeClass("d-none");
                    $("#chatName").html(chatName);
                    $("#message-user-id").val(inboxId);
                    chatId = inboxId;
                },
            })
        }

        function closeChat() {
            $("#chats").removeClass("d-none");
            $("#chat").addClass("d-none");
            chatId = 0;
            finalMessageId = 0;
            updateChats();
        }

        $("#send-message-form").submit(e => {
            e.preventDefault();

            $.ajax({
                url: "php/inboxSystem.php",
                method: "post",
                data: "type=add&"+$("#send-message-form").serialize(),
                success: data => {
                    $("#messages").append(`
                        <div class="message user-message">${$("#send-message-form textarea").val()}</div>
                    `);
                    $("#send-message-form textarea").val("");
                },
            })
        });

        setInterval(() => {
            if(chatId != 0 && finalMessageId != 0) {
                $.ajax({
                    url: "php/inboxSystem.php",
                    method: "post",
                    data: `type=getNews&inboxId=${chatId}&finalMessageId=${finalMessageId}`,
                    success: data => {
                        var messages = JSON.parse(data);

                        messages.forEach((msg, index) => {
                            var userMessage = "";

                            if(!msg.isMyMessage) {
                                userMessage = "user-message";
                            }

                            $("#messages").append(`
                                <div class="message ${userMessage}">${msg["Content"]}</div>
                            `);

                            if(index == messages.length - 1) {
                                finalMessageId = msg["Id"];
                            }
                        });
                    }
                })
            }
        }, 2000);

        function updateChats() {
            $.ajax({
               url: "php/inboxSystem.php",
               method: "post",
               data: "type=getChats",
               success: data => {
                   var chats = JSON.parse(data);
                   $("#chats-container").html("");

                   chats.forEach(chat => {
                       var message = chat.message.substr(0, 50);
                       
                       $("#chats-container").append(`
                       <div class="col-12 p-3 chat" onclick="changeChat(${chat.inboxId}, '${chat.username}')">
                            <span class="h5 font-weight-bold">${chat.username}</span>
                            <span class="d-block mt-2">${message}</span>
                        </div>
                       `);
                   });
               } 
            });
        }
    </script>
</body>
</html>