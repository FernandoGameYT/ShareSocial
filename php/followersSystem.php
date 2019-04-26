<?php

    require("security.php");

    if(!isset($_POST["type"])) {
        header("Location: $direction");
        exit;
    }

    $type = $_POST["type"];

    if($type == "add") {
        $userId = $_POST["userId"];

        $insert_follower = $pdo -> prepare("INSERT INTO followers (Id, FollowerId, UserId) VALUES (NULL, ?, ?)");

        if($insert_follower -> execute([$user_data["Id"], $userId])) {
            echo json_encode(["status" => true]);
            exit;
        }
    } else if($type == "delete") {
        $userId = $_POST["userId"];

        $delete_follower = $pdo -> prepare("DELETE FROM followers WHERE FollowerId = ? AND UserId = ?");

        if($delete_follower -> execute([$user_data["Id"], $userId])) {
            echo json_encode(["status" => true]);
            exit;
        }
    } else if($type == "getFollowers") {
        $userId = $_POST["userId"];
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];

        $select_followers = $pdo -> prepare("SELECT * FROM followers WHERE UserId = ? ORDER BY Id DESC LIMIT ? OFFSET ?");

        if($select_followers -> execute([$userId, $limit, $offset])) {
            $followers = [];
            foreach($select_followers -> fetchAll() as $follower) {
                $get_user = $pdo -> prepare("SELECT Username FROM users WHERE Id = ?");

                if($get_user -> execute([$follower["FollowerId"]])) {
                    $followers[] = $get_user -> fetch();
                }
            }
            echo json_encode($followers);
            exit;
        }
    } else if($type == "getFollowing") {
        $userId = $_POST["userId"];
        $limit = $_POST["limit"];
        $offset = $_POST["offset"];

        $select_following = $pdo -> prepare("SELECT * FROM followers WHERE FollowerId = ? ORDER BY Id DESC LIMIT ? OFFSET ?");

        if($select_following -> execute([$userId, $limit, $offset])) {
            $followers = [];
            foreach($select_following -> fetchAll() as $follower) {
                $get_user = $pdo -> prepare("SELECT Username FROM users WHERE Id = ?");

                if($get_user -> execute([$follower["UserId"]])) {
                    $followers[] = $get_user -> fetch();
                }
            }
            echo json_encode($followers);
            exit;
        }
    }

?>