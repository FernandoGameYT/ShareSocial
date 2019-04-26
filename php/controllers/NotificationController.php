<?php

    class NotificationController {
        public $userId = 0;
        public $notifications = [];

        public function __construct($userId) {
            $this -> userId = $userId;
        }

        public function getNotifications($pdo, $limit, $offset) {
            $get_notifications = $pdo -> prepare("SELECT * FROM notifications WHERE UserId = ? ORDER BY Id DESC LIMIT ? OFFSET ?");

            if($get_notifications -> execute([$this -> userId, $limit, $offset])) {
                if($get_notifications -> rowCount() > 0) {
                    $this -> notifications = $get_notifications -> fetchAll();
                }
            }
        }

        public function notifyPublication($pdo, $username) {
            $get_followers = $pdo -> prepare("SELECT FollowerId FROM followers WHERE UserId = ?");

            if($get_followers -> execute([$this -> userId])) {
                if($get_followers -> rowCount() > 0) {
                    foreach($get_followers -> fetchAll() as $follower) {
                        $get_publication_id = $pdo -> prepare("SELECT Id FROM publications WHERE UserId = ? ORDER BY Id DESC LIMIT 1");

                        if($get_publication_id -> execute([$this -> userId])) {
                            $publicationId = $get_publication_id -> fetch()["Id"];
                            $insert_notification = $pdo -> prepare("INSERT INTO notifications (Id, UserId, Content, Link) VALUES (NULL, ?, ?, ?)");
                            $content = "$username a subido una nueva publicación.";
                            $link = "Publication/$publicationId/";

                            if(!$insert_notification -> execute([$follower["FollowerId"], $content, $link])) {
                                echo "A ocurrido un error";
                                exit;
                            }
                        }
                    }
                }
            }
        }

        public function notifyComment($pdo, $publicationId) {
            $get_user_id = $pdo -> prepare("SELECT UserId FROM publications WHERE Id = ?");

            if($get_user_id -> execute([$publicationId])) {
                $userId = $get_user_id -> fetch()["UserId"];
                $content = "Alguien a comentado en tu publicación.";
                $link = "Publication/$publicationId/";

                $insert_notification = $pdo -> prepare("INSERT INTO notifications (Id, UserId, Content, Link) VALUES (NULL, ?, ?, ?)");
                if(!$insert_notification -> execute([$userId, $content, $link])) {
                    echo "A ocurrido un error";
                    exit;
                }
            }
        }

        public function notifyLike($pdo, $publicationId, $username) {
            $get_user_id = $pdo -> prepare("SELECT UserId FROM publications WHERE Id = ?");

            if($get_user_id -> execute([$publicationId])) {
                $userId = $get_user_id -> fetch()["UserId"];
                $content = "$username a dado like en tu publicación.";
                $link = "Publication/$publicationId/";

                $insert_notification = $pdo -> prepare("INSERT INTO notifications (Id, UserId, Content, Link) VALUES (NULL, ?, ?, ?)");
                if(!$insert_notification -> execute([$userId, $content, $link])) {
                    echo "A ocurrido un error";
                    exit;
                }
            }
        }
    }

?>