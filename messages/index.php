<?php

session_start();

require_once "../config/database.php";
require_once "conversation_helper.php";


/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit;
}


$current_user_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| GET CURRENT USER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        username,
        fullname,
        role,
        profile_picture
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$current_user_id]);

$current_user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$current_user) {

    session_destroy();

    header("Location: ../login.php");
    exit;
}


$current_role = strtolower(
    trim($current_user['role'])
);


/*
|--------------------------------------------------------------------------
| ONLY FARMERS AND BUYERS
|--------------------------------------------------------------------------
*/

if (!in_array($current_role, ['farmer', 'buyer'], true)) {

    die("Only farmers and buyers can use the messaging system.");
}


/*
|--------------------------------------------------------------------------
| GET OTHER USER
|--------------------------------------------------------------------------
*/

$other_user_id = isset($_GET['user_id'])
    ? (int) $_GET['user_id']
    : 0;


if ($other_user_id <= 0) {

    header("Location: list.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| PREVENT SELF CHAT
|--------------------------------------------------------------------------
*/

if ($other_user_id === $current_user_id) {

    header("Location: list.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET OTHER USER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        username,
        fullname,
        role,
        profile_picture
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$other_user_id]);

$other_user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$other_user) {

    header("Location: list.php");
    exit;
}


$other_role = strtolower(
    trim($other_user['role'])
);


/*
|--------------------------------------------------------------------------
| ONLY FARMER <-> BUYER
|--------------------------------------------------------------------------
*/

if (
    ($current_role === 'farmer' && $other_role !== 'buyer')
    ||
    ($current_role === 'buyer' && $other_role !== 'farmer')
) {

    header("Location: list.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CREATE / GET CONVERSATION
|--------------------------------------------------------------------------
*/

try {

    $conversation_id = getOrCreateConversation(
        $pdo,
        $current_user_id,
        $other_user_id
    );

} catch (Exception $e) {

    die(
        htmlspecialchars(
            $e->getMessage()
        )
    );
}


/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $message = isset($_POST['message'])
        ? trim($_POST['message'])
        : "";


    $image_path = null;


    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES['image'])
        &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['image']['error']
            !== UPLOAD_ERR_OK
        ) {

            die("Image upload failed.");
        }


        /*
        |--------------------------------------------------------------------------
        | MAX SIZE = 5MB
        |--------------------------------------------------------------------------
        */

        if (
            $_FILES['image']['size']
            > 5 * 1024 * 1024
        ) {

            die("Image is too large. Maximum size is 5MB.");
        }


        /*
        |--------------------------------------------------------------------------
        | MIME VALIDATION
        |--------------------------------------------------------------------------
        */

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file(
            $_FILES['image']['tmp_name']
        );


        $allowed_types = [

            'image/jpeg' => 'jpg',

            'image/png' => 'png',

            'image/gif' => 'gif',

            'image/webp' => 'webp'

        ];


        if (!isset($allowed_types[$mime])) {

            die("Invalid image type.");
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE UPLOAD DIRECTORY
        |--------------------------------------------------------------------------
        */

        $upload_directory = "../uploads/chat/";


        if (!is_dir($upload_directory)) {

            mkdir(
                $upload_directory,
                0755,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SAFE RANDOM FILENAME
        |--------------------------------------------------------------------------
        */

        $filename =
            bin2hex(random_bytes(16))
            . "."
            . $allowed_types[$mime];


        $destination =
            $upload_directory
            . $filename;


        if (
            !move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $destination
            )
        ) {

            die("Failed to save image.");
        }


        $image_path = $filename;
    }


    /*
    |--------------------------------------------------------------------------
    | DETERMINE MESSAGE TYPE
    |--------------------------------------------------------------------------
    */

    if (
        $message !== ""
        &&
        $image_path !== null
    ) {

        $message_type = "text_image";

    } elseif ($image_path !== null) {

        $message_type = "image";

    } elseif ($message !== "") {

        $message_type = "text";

    } else {

        header(
            "Location: index.php?user_id="
            . $other_user_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | INSERT MESSAGE
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO messages
        (
            conversation_id,
            sender_id,
            message,
            image,
            message_type
        )
        VALUES (?, ?, ?, ?, ?)
    ");


    $stmt->execute([

        $conversation_id,

        $current_user_id,

        $message !== ""
            ? $message
            : null,

        $image_path,

        $message_type

    ]);


    /*
    |--------------------------------------------------------------------------
    | UPDATE CONVERSATION TIME
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE conversations

        SET updated_at = CURRENT_TIMESTAMP

        WHERE id = ?
    ");

    $stmt->execute([
        $conversation_id
    ]);


    /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

    header(
        "Location: index.php?user_id="
        . $other_user_id
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| GET INITIAL MESSAGES
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

$stmt->execute([
    $conversation_id
]);

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
| DISPLAY NAMES
|--------------------------------------------------------------------------
*/

$current_name = !empty(
    $current_user['fullname']
)
    ? $current_user['fullname']
    : $current_user['username'];


$other_name = !empty(
    $other_user['fullname']
)
    ? $other_user['fullname']
    : $other_user['username'];


$other_role_label = ucfirst(
    $other_role
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Chat with <?= htmlspecialchars($other_name) ?>
        | Agro-Link Uganda
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: #f1f3f5;

            font-family: Arial, sans-serif;

        }


        /*
        |--------------------------------------------------------------------------
        | CHAT PAGE
        |--------------------------------------------------------------------------
        */

        .chat-page {

            max-width: 1000px;

            margin: 0 auto;

            height: 100vh;

            display: flex;

            flex-direction: column;

            background: white;

        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .chat-top {

            background: #198754;

            color: white;

            padding: 12px 16px;

            display: flex;

            align-items: center;

            gap: 12px;

            flex-shrink: 0;

        }


        .back-button {

            color: white;

            text-decoration: none;

            font-size: 20px;

            width: 35px;

            height: 35px;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .back-button:hover {

            color: white;

            background: rgba(
                255,
                255,
                255,
                0.15
            );

            border-radius: 50%;

        }


        .chat-profile {

            width: 45px;

            height: 45px;

            border-radius: 50%;

            object-fit: cover;

            background: white;

        }


        .chat-profile-placeholder {

            width: 45px;

            height: 45px;

            border-radius: 50%;

            background: white;

            color: #198754;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

        }


        .chat-user-info {

            flex: 1;

            min-width: 0;

        }


        .chat-user-name {

            font-weight: bold;

            font-size: 16px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .chat-user-role {

            font-size: 12px;

            opacity: 0.9;

        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGES AREA
        |--------------------------------------------------------------------------
        */

        #messagesBox {

            flex: 1;

            overflow-y: auto;

            padding: 20px;

            background: #f8f9fa;

            scroll-behavior: smooth;

        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGE ROW
        |--------------------------------------------------------------------------
        */

        .message-row {

            display: flex;

            margin-bottom: 12px;

            width: 100%;

        }


        .message-row.sent {

            justify-content: flex-end;

        }


        .message-row.received {

            justify-content: flex-start;

        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGE BUBBLE
        |--------------------------------------------------------------------------
        */
.message-bubble {
    padding: 3px 5px;
    border-radius: 12px;
    max-width: 45%;
    width: fit-content;
    display: inline-block;
    line-height: 1.3;
    word-wrap: break-word;
}


        .sent .message-bubble {

            background: #198754;

            color: white;

            border-bottom-right-radius: 4px;

        }


        .received .message-bubble {

            background: #e9ecef;

            color: #212529;

            border-bottom-left-radius: 4px;

        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGE TEXT
        |--------------------------------------------------------------------------
        */

        .message-text {

            white-space: pre-wrap;

            line-height: 1.4;

        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGE IMAGE
        |--------------------------------------------------------------------------
        */

        .message-image {

            display: block;

            max-width: 280px;

            width: 100%;

            border-radius: 10px;

            margin-bottom: 6px;

            cursor: pointer;

        }


        /*
        |--------------------------------------------------------------------------
        | TIME
        |--------------------------------------------------------------------------
        */

        .message-time {

            font-size: 10px;

            margin-top: 5px;

            text-align: right;

            opacity: 0.75;

        }


        /*
        |--------------------------------------------------------------------------
        | INPUT AREA
        |--------------------------------------------------------------------------
        */

        .chat-input-area {

            border-top: 1px solid #ddd;

            background: white;

            padding: 10px;

            flex-shrink: 0;

        }


        .chat-form {

            display: flex;

            align-items: center;

            gap: 8px;

        }


        .message-input {

            flex: 1;

            border: 1px solid #ced4da;

            border-radius: 22px;

            padding: 10px 15px;

            outline: none;

        }


        .message-input:focus {

            border-color: #198754;

            box-shadow:
                0 0 0 2px rgba(
                    25,
                    135,
                    84,
                    0.1
                );

        }


        .image-button {

            width: 42px;

            height: 42px;

            border: none;

            border-radius: 50%;

            background: #e8f5e9;

            color: #198754;

            cursor: pointer;

            flex-shrink: 0;

        }


        .send-button {

            width: 42px;

            height: 42px;

            border: none;

            border-radius: 50%;

            background: #198754;

            color: white;

            cursor: pointer;

            flex-shrink: 0;

        }


        .send-button:hover {

            background: #157347;

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */

        #imagePreview {

            display: none;

            padding: 5px 10px;

            font-size: 13px;

            color: #198754;

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY CHAT
        |--------------------------------------------------------------------------
        */

        .empty-chat {

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            color: #6c757d;

        }


        .empty-chat i {

            font-size: 45px;

            color: #198754;

            margin-bottom: 15px;

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 576px) {

            .chat-page {

                width: 100%;

            }


            #messagesBox {

                padding: 12px;

            }


            .message-bubble {

                max-width: 82%;

                font-size: 14px;

            }


            .message-image {

                max-width: 220px;

            }


            .chat-top {

                padding: 10px 12px;

            }


            .chat-profile,
            .chat-profile-placeholder {

                width: 40px;

                height: 40px;

            }

        }

    </style>

</head>


<body>


<div class="chat-page">


    <!-- =====================================================
         CHAT HEADER
    ====================================================== -->

    <div class="chat-top">


        <a
            href="list.php"
            class="back-button"
        >

            <i class="fas fa-arrow-left"></i>

        </a>


        <?php if (!empty($other_user['profile_picture'])): ?>

            <img
                src="../uploads/profiles/<?= htmlspecialchars($other_user['profile_picture']) ?>"
                class="chat-profile"
                alt="Profile"
            >

        <?php else: ?>

            <div class="chat-profile-placeholder">

                <i class="fas fa-user"></i>

            </div>

        <?php endif; ?>


        <div class="chat-user-info">

            <div class="chat-user-name">

                <?= htmlspecialchars($other_name) ?>

            </div>

            <div class="chat-user-role">

                <?= htmlspecialchars($other_role_label) ?>

            </div>

        </div>


    </div>


    <!-- =====================================================
         MESSAGES
    ====================================================== -->

    <div id="messagesBox">


        <?php if (empty($messages)): ?>


            <div class="empty-chat">

                <div>

                    <i class="fas fa-comments d-block"></i>

                    <h5>
                        Start the conversation
                    </h5>

                    <p class="mb-0">
                        Send a message to
                        <?= htmlspecialchars($other_name) ?>.
                    </p>

                </div>

            </div>


        <?php else: ?>


            <?php foreach ($messages as $msg): ?>


                <?php

                $is_sent =
                    (int) $msg['sender_id']
                    === $current_user_id;

                ?>


                <div
                    class="message-row
                    <?= $is_sent ? 'sent' : 'received' ?>"
                    data-message-id="<?= (int) $msg['id'] ?>"
                >


                    <div class="message-bubble">


                        <?php if (!empty($msg['image'])): ?>

                            <img
                                src="../uploads/chat/<?= htmlspecialchars($msg['image']) ?>"
                                class="message-image"
                                alt="Sent image"
                                onclick="window.open(this.src, '_blank')"
                            >

                        <?php endif; ?>


                        <?php if (!empty($msg['message'])): ?>

                            <div class="message-text">

                                <?= nl2br(
                                    htmlspecialchars(
                                        $msg['message']
                                    )
                                ) ?>

                            </div>

                        <?php endif; ?>


                        <div class="message-time">

                            <?= date(
                                "H:i",
                                strtotime(
                                    $msg['created_at']
                                )
                            ) ?>

                            <?php if ($is_sent): ?>

                                <span class="ms-1">

                                    <?php if ((int) $msg['is_read'] === 1): ?>

                                        <i class="fas fa-check-double"></i>

                                    <?php else: ?>

                                        <i class="fas fa-check"></i>

                                    <?php endif; ?>

                                </span>

                            <?php endif; ?>

                        </div>


                    </div>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </div>


    <!-- =====================================================
         INPUT
    ====================================================== -->

    <div class="chat-input-area">


        <div id="imagePreview"></div>


        <form
            method="POST"
            enctype="multipart/form-data"
            class="chat-form"
        >


            <input
                type="file"
                name="image"
                id="imageInput"
                accept="image/jpeg,image/png,image/gif,image/webp"
                hidden
            >


            <button
                type="button"
                class="image-button"
                onclick="document.getElementById('imageInput').click()"
                title="Send image"
            >

                <i class="fas fa-image"></i>

            </button>


            <input
                type="text"
                name="message"
                class="message-input"
                placeholder="Type a message..."
                autocomplete="off"
            >


            <button
                type="submit"
                class="send-button"
                title="Send"
            >

                <i class="fas fa-paper-plane"></i>

            </button>


        </form>


    </div>


</div>


<script>

/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

const messagesBox =
    document.getElementById("messagesBox");

const otherUserId =
    <?= (int) $other_user_id ?>;

let latestMessageId = 0;


/*
|--------------------------------------------------------------------------
| GET CURRENT LAST MESSAGE ID
|--------------------------------------------------------------------------
*/

function updateLatestMessageId() {

    const messages =
        messagesBox.querySelectorAll(
            "[data-message-id]"
        );

    messages.forEach(function(message) {

        const id =
            parseInt(
                message.dataset.messageId
            );

        if (id > latestMessageId) {

            latestMessageId = id;

        }

    });

}


/*
|--------------------------------------------------------------------------
| SCROLL TO BOTTOM
|--------------------------------------------------------------------------
*/

function scrollToBottom() {

    messagesBox.scrollTop =
        messagesBox.scrollHeight;

}


/*
|--------------------------------------------------------------------------
| ESCAPE HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(text) {

    const div =
        document.createElement("div");

    div.textContent =
        text ?? "";

    return div.innerHTML;

}


/*
|--------------------------------------------------------------------------
| FORMAT TIME
|--------------------------------------------------------------------------
*/

function formatTime(dateString) {

    const date =
        new Date(
            dateString.replace(" ", "T")
        );

    if (isNaN(date.getTime())) {

        return "";

    }

    return date.toLocaleTimeString(
        [],
        {
            hour: "2-digit",
            minute: "2-digit"
        }
    );

}


/*
|--------------------------------------------------------------------------
| CREATE MESSAGE HTML
|--------------------------------------------------------------------------
*/

function createMessageHtml(message) {

    const isSent =
        parseInt(message.sender_id)
        === <?= $current_user_id ?>;


    let html = "";


    html += `
        <div
            class="message-row ${isSent ? "sent" : "received"}"
            data-message-id="${parseInt(message.id)}"
        >
            <div class="message-bubble">
    `;


    /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
    */

    if (message.image) {

        html += `

            <img
                src="../uploads/chat/${escapeHtml(message.image)}"
                class="message-image"
                alt="Sent image"
                onclick="window.open(this.src, '_blank')"
            >

        `;

    }


    /*
    |--------------------------------------------------------------------------
    | TEXT
    |--------------------------------------------------------------------------
    */

    if (message.message) {

        html += `

            <div class="message-text">

                ${escapeHtml(message.message)
                    .replace(/\n/g, "<br>")}

            </div>

        `;

    }


    /*
    |--------------------------------------------------------------------------
    | TIME + READ STATUS
    |--------------------------------------------------------------------------
    */

    html += `

        <div class="message-time">

            ${formatTime(message.created_at)}

    `;


    if (isSent) {

        html += `

            <span class="ms-1">

                ${
                    parseInt(message.is_read) === 1

                    ? '<i class="fas fa-check-double"></i>'

                    : '<i class="fas fa-check"></i>'
                }

            </span>

        `;

    }


    html += `

        </div>

        </div>

        </div>

    `;


    return html;

}


/*
|--------------------------------------------------------------------------
| AUTO REFRESH
|--------------------------------------------------------------------------
*/

async function refreshMessages() {

    try {

        const response =
            await fetch(
                "fetch_messages.php?user_id="
                + encodeURIComponent(otherUserId)
                + "&t="
                + Date.now(),
                {
                    cache: "no-store"
                }
            );


        if (!response.ok) {

            return;

        }


        const data =
            await response.json();


        if (!data.success) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | CHECK FOR NEW MESSAGES
        |--------------------------------------------------------------------------
        */

        let hasNewMessages = false;


        data.messages.forEach(function(message) {

            const id =
                parseInt(message.id);


            if (id > latestMessageId) {

                messagesBox.insertAdjacentHTML(
                    "beforeend",
                    createMessageHtml(message)
                );


                latestMessageId = id;

                hasNewMessages = true;

            }

        });


        /*
        |--------------------------------------------------------------------------
        | REMOVE EMPTY CHAT MESSAGE
        |--------------------------------------------------------------------------
        */

        const emptyChat =
            messagesBox.querySelector(
                ".empty-chat"
            );


        if (
            emptyChat
            &&
            data.messages.length > 0
        ) {

            emptyChat.remove();

        }


        /*
        |--------------------------------------------------------------------------
        | SCROLL WHEN NEW MESSAGE ARRIVES
        |--------------------------------------------------------------------------
        */

        if (hasNewMessages) {

            scrollToBottom();

        }

    } catch (error) {

        console.log(
            "Message refresh error:",
            error
        );

    }

}


/*
|--------------------------------------------------------------------------
| IMAGE SELECT PREVIEW
|--------------------------------------------------------------------------
*/

document
    .getElementById("imageInput")
    .addEventListener(
        "change",
        function() {

            const preview =
                document.getElementById(
                    "imagePreview"
                );


            if (this.files.length > 0) {

                preview.style.display =
                    "block";

                preview.innerHTML = `

                    <i class="fas fa-image me-1"></i>

                    ${escapeHtml(
                        this.files[0].name
                    )}

                `;

            } else {

                preview.style.display =
                    "none";

                preview.innerHTML = "";

            }

        }
    );


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

updateLatestMessageId();

scrollToBottom();


/*
|--------------------------------------------------------------------------
| CHECK FOR NEW MESSAGES EVERY 3 SECONDS
|--------------------------------------------------------------------------
*/

setInterval(
    refreshMessages,
    3000
);

</script>


</body>

</html>