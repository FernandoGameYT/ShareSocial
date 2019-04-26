<?php
    include("Comment.php");

    class Publication {
        public $id = 0;
        public $userId = 0;
        public $content = "";
        public $imageURL = "";
        public $likes = 0;
        public $date = "";
        public $username = "";
        public $liked = false;
        public $userPublication = false;
        public $comments = [];

        public function __construct($publicationId) {
            $this -> id = $publicationId;
        }

        public function getPublication($pdo, $userId) {
            $get_publication = $pdo -> prepare("SELECT * FROM publications WHERE Id = ?");

            if($get_publication -> execute([$this -> id])) {
                if($get_publication -> rowCount() > 0) {
                    $publication = $get_publication -> fetch();

                    $this -> userId = $publication["UserId"];
                    $this -> content = $publication["Content"];
                    $this -> imageURL = $publication["ImageURL"];
                    $this -> likes = $publication["Likes"];
                    $this -> date = $publication["Date"];

                    $get_username = $pdo -> prepare("SELECT Username FROM users WHERE Id = ?");

                    if($get_username -> execute([$this -> userId])) {
                        if($get_username -> rowCount() > 0) {
                            $this -> username = $get_username -> fetch()["Username"];
                        }else{
                            $this -> username = "Unknown";
                        }
                    }

                    $get_liked = $pdo -> prepare("SELECT Id FROM likes WHERE UserId = ? AND PublicationId = ?");

                    if($get_liked -> execute([$userId, $this -> id])) {
                        if($get_liked -> rowCount() > 0) {
                            $this -> liked = true;
                        }
                    }

                    if($this -> userId == $userId) {
                        $this -> userPublication = true;
                    }

                    return true;
                }else{
                    return false;
                }
            }
        }

        public function getComments($pdo, $limit, $offset, $userId) {
            $get_comments = $pdo -> prepare("SELECT * FROM comments WHERE PublicationId = ? ORDER BY Id DESC LIMIT ? OFFSET ?");

            if($get_comments -> execute([$this -> id, $limit, $offset])) {
                if($get_comments -> rowCount() > 0) {
                    $comments = $get_comments -> fetchAll();

                    foreach($comments as $comment) {
                        $this -> comments[] = new Comment(
                            $comment["Id"],
                            $comment["UserId"],
                            $comment["Content"],
                            $comment["Date"],
                            $pdo,
                            $userId
                        );
                    }
                    return true;
                }else{
                    return false;
                }
            }
        }
    }

?>