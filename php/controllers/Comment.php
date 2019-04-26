<?php

    class Comment {
        public $id = 0;
        public $userId = 0;
        public $content = "";
        public $date = "";
        public $username = "";
        public $userComment = false;

        public function __construct($id, $userId, $content, $date, $pdo, $user) {
            $get_username = $pdo -> prepare("SELECT Username FROM users WHERE Id = ?");
            $this -> id = $id;
            $this -> userId = $userId;
            $this -> content = $content;
            $this -> date = $date;

            if($this -> userId == $user) {
                $this -> userComment = true;
            }

            if($get_username -> execute([$userId])) {
                if($get_username -> rowCount() > 0) {
                    $this -> username = $get_username -> fetch()["Username"];
                }else{
                    $this -> username = "Unknown";
                }
            }
        }
    }

?>