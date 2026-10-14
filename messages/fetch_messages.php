<?php

session_start();

require_once "../config/database.php";

header("Content-Type: application/json");


/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "You are not logged in."
    ]);

    exit;
}


$current_user_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| GET OTHER USER
|--------------------------------------------------------------------------
*/

$other_user_id = isset($_GET['user_id'])
    ? (int) $_GET['user_id']
    : 0;


if ($other_user_id <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid user."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| PREVENT SELF CHAT
|--------------------------------------------------------------------------
*/

if ($current_user_id === $other_user_id) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "You cannot chat with yourself."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| FIND CONVERSATION
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM conversations
    WHERE
        (
            user_one_id = ?
            AND user_two_id = ?
        )
        OR
        (
            user_one_id = ?
            AND user_two_id = ?
        )
    LIMIT 1
");

$stmt->execute([
    $current_user_id,
    $other_user_id,
    $other_user_id,
    $current_user_id
]);

$conversation = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| NO CONVERSATION
|--------------------------------------------------------------------------
*/

if (!$conversation) {

    echo json_encode([
        "success" => true,
        "conversation_id" => null,
        "messages" => []
    ]);

    exit;
}


$conversation_id = (int) $conversation['id'];


/*
|--------------------------------------------------------------------------
| GET MESSAGES
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        m.id,
        m.sender_id,
        m.message,
        m.image,
        m.message_type,
        m.is_read,
        m.created_at,

        u.username,
        u.fullname,
        u.role

    FROM messages m

    INNER JOIN users u
        ON u.id = m.sender_id

    WHERE m.conversation_id = ?

    ORDER BY m.id ASC
");

$stmt->execute([$conversation_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| MARK OTHER USER MESSAGES AS READ
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE messages

    SET is_read = 1

    WHERE conversation_id = ?

      AND sender_id = ?

      AND is_read = 0
");

$stmt->execute([
    $conversation_id,
    $other_user_id
]);


/*
|--------------------------------------------------------------------------
| RETURN DATA
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "conversation_id" => $conversation_id,
    "messages" => $messages
]);

exit;