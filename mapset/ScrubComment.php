<?php
    include '../base.php';

    $setId = $_POST['sID'] ?? -1;
    $commentId = $_POST['cID'] ?? -1;
    if ($setId == -1) {
        die("NO - INVALID SET");
    }

    if ($commentId == -1) {
        die("NO - INVALID COMMENT");
    }

    if (!$loggedIn) {
        die("NO");
    }

    if ($userName != "moonpoint") {
        header('HTTP/1.0 403 Forbidden');
        http_response_code(403);
        die("Forbidden");
    }

    $stmt = $conn->prepare("SELECT * FROM `comments` WHERE `CommentID` = ? and `SetID` = ?;");
    $stmt->bind_param("ii", $commentId, $setId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $array = array(
        "type" => "comment_scrubbed",
        "data" => array(
            "CommentID" => $result["CommentID"],
            "UserID" => $result["UserID"],
            "SetID" => $result["SetID"],
            "Comment" => $result["Comment"],
            "Date" => $result["date"],
        ));

    $json = json_encode($array);

    $stmt = $conn->prepare("INSERT INTO logs (UserID, LogData) VALUES (?, ?);");
    $stmt->bind_param("is", $userId, $json);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE `comments` SET `Comment`='[comment removed]' WHERE `CommentID` = ? AND `SetID` = ?;");
    $stmt->bind_param("ii", $commentId, $setId);
    $stmt->execute();
    $stmt->close();

    echo "Hello";
?>