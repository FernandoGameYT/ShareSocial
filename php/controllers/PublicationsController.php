<?php

    if(!class_exists("Publication")) {
        include("Publication.php");
    }

    class PublicationsController {
        public $userId = 0;
        public $publications = [];

        public function __construct($userId) {
            $this -> userId = $userId;
        }

        public function getPublications($pdo, $limit, $offset) {
            $get_publications = $pdo -> prepare("SELECT * FROM publications, followers WHERE publications.UserId = followers.UserId AND followers.FollowerId = ? OR publications.UserId = ? ORDER By publications.Id DESC LIMIT ? OFFSET ?");

            if($get_publications -> execute([$this -> userId, $this -> userId, $limit, $offset])) {
                if($get_publications -> rowCount() > 0) {
                    $publications = $get_publications -> fetchAll();

                    foreach($publications as $publication) {
                        $x = count($this -> publications);

                        if($x > 0) {
                            if($this -> publications[$x - 1] -> id == $publication[0]) {
                                continue;
                            }
                        }

                        $this -> publications[] = new Publication($publication[0]);
                        $this -> publications[$x] -> getPublication($pdo, $this -> userId);
                        $this -> publications[$x] -> getComments($pdo, 10, 0, $this -> userId);
                    }
                    return true;
                }else{
                    return false;
                }
            }
        }

        public function getPublicationsById($pdo, $limit, $offset) {
            $get_publications = $pdo -> prepare("SELECT * FROM publications WHERE UserId = ? ORDER BY Id DESC LIMIT ? OFFSET ?");

            if($get_publications -> execute([$this -> userId, $limit, $offset])) {
                if($get_publications -> rowCount() > 0) {
                    $publications = $get_publications -> fetchAll();

                    foreach($publications as $publication) {
                        $x = count($this -> publications);
                        $this -> publications[] = new Publication($publication[0]);
                        $this -> publications[$x] -> getPublication($pdo, $this -> userId);
                        $this -> publications[$x] -> getComments($pdo, 10, 0, $this -> userId);
                    }
                    return true;
                }else{
                    return false;
                }
            }
        }
    }

?>