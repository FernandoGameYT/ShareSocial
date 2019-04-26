<?php

    class InboxController {
        public $userId = 0;
        public $inboxId = 0;
        public $messages = [];
        public $chats = [];

        public function __construct($userId, $inboxId) {
            $this -> userId = $userId;
            $this -> inboxId = $inboxId;
        }

        public function getMessages($pdo, $limit, $offset) {
            $get_messages = $pdo -> prepare("SELECT * FROM messages WHERE ForUserId = ? AND ByUserId = ? OR ForUserId = ? AND ByUserId = ? ORDER BY Id DESC LIMIT ? OFFSET ?");

            if($get_messages -> execute([$this -> inboxId, $this -> userId, $this -> userId, $this -> inboxId, $limit, $offset])) {
                if($get_messages -> rowCount() > 0) {
                    foreach($get_messages -> fetchAll() as $message) {
                        $x = count($this -> messages);
                        $this -> messages[] = $message;

                        if($message["ForUserId"] == $this -> inboxId) {
                            $this -> messages[$x]["isMyMessage"] = false;
                        }else{
                            $this -> messages[$x]["isMyMessage"] = true;
                        }
                    }
                    $this -> messages = array_reverse($this -> messages);
                }
            }
        }

        public function getNewMessages($pdo, $finalMessageId) {
            $get_messages = $pdo -> prepare("SELECT * FROM messages WHERE Id > ? AND ForUserId = ? AND ByUserId = ? ORDER BY Id DESC");

            if($get_messages -> execute([$finalMessageId, $this -> userId, $this -> inboxId])) {
                if($get_messages -> rowCount() > 0) {
                    foreach($get_messages -> fetchAll() as $message) {
                        $x = count($this -> messages);
                        $this -> messages[] = $message;

                        if($message["ForUserId"] == $this -> inboxId) {
                            $this -> messages[$x]["isMyMessage"] = false;
                        }else{
                            $this -> messages[$x]["isMyMessage"] = true;
                        }
                    }
                    $this -> messages = array_reverse($this -> messages);
                }
            }
        }

        public function getChats($pdo) {
            $get_chats = $pdo -> prepare("SELECT * FROM chats WHERE UserCreatorId = ? OR UserId = ?");

            if($get_chats -> execute([$this -> userId, $this -> userId])) {
                if($get_chats -> rowCount() > 0) {
                    foreach($get_chats -> fetchAll() as $chat) {

                        if($chat["UserCreatorId"] == $this -> userId) {
                            $id = $chat["UserId"];
                        }else{
                            $id = $chat["UserCreatorId"];
                        }

                        $get_msg = $pdo -> prepare("SELECT Content FROM messages WHERE ForUserId = ? AND ByUserId = ? OR ForUserId = ? AND ByUserId = ? ORDER BY Id DESC LIMIT 1");
                        $msg = "";

                        if($get_msg -> execute([$this -> userId, $id, $id, $this -> userId])) {
                            if($get_msg -> rowCount() > 0) {
                                if($chat["UserCreatorId"] == $this -> userId) {
                                    $msg = "Tu: ".$get_msg -> fetch()["Content"];
                                }else{
                                    $msg = "El: ".$get_msg -> fetch()["Content"];
                                }
                            }else{
                                $msg = "Empezaste una nueva conversación.";
                            }
                        }

                        $get_username = $pdo -> prepare("SELECT Username FROM users WHERE Id = ?");

                        if($get_username -> execute([$id])) {
                            $username = $get_username -> fetch()["Username"];

                            $this -> chats[] = [
                                "username" => $username,
                                "message" => $msg,
                                "inboxId" => $id
                            ];
                        }
                    }
                }
            }
        }
    }

?>