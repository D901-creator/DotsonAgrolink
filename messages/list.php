<?php

session_start();

require_once "../config/database.php";

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
|
| We need the logged-in user's role so that:
|
| Farmer -> sees Buyers
| Buyer  -> sees Farmers
|
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


/*
|--------------------------------------------------------------------------
| CHECK CURRENT USER
|--------------------------------------------------------------------------
*/

if (!$current_user) {

    session_destroy();

    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| NORMALIZE CURRENT ROLE
|--------------------------------------------------------------------------
*/

$current_role = strtolower(trim($current_user['role']));


/*
|--------------------------------------------------------------------------
| ONLY FARMERS AND BUYERS CAN USE THIS CHAT
|--------------------------------------------------------------------------
*/

if (!in_array($current_role, ['farmer', 'buyer'], true)) {

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
        '>
            <h2>Chat Access Denied</h2>
            <p>Only farmers and buyers can use the messaging system.</p>
            <a href='../index.php'>Return Home</a>
        </div>
    ");
}


/*
|--------------------------------------------------------------------------
| DETERMINE WHO THE CURRENT USER CAN CHAT WITH
|--------------------------------------------------------------------------
|
| Farmer -> Buyer
| Buyer  -> Farmer
|
*/

$target_role = ($current_role === 'farmer')
    ? 'buyer'
    : 'farmer';


/*
|--------------------------------------------------------------------------
| GET CONVERSATIONS
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| We do NOT assume that the current user is always
| user_one_id or user_two_id.
|
| The query checks both sides:
|
| c.user_one_id = current user
| OR
| c.user_two_id = current user
|
| Then we determine the OTHER person.
|
*/

$stmt = $pdo->prepare("
    SELECT
        c.id AS conversation_id,

        c.user_one_id,
        c.user_two_id,

        c.updated_at,

        u.id AS other_user_id,
        u.username AS other_username,
        u.fullname AS other_fullname,
        u.role AS other_role,
        u.profile_picture AS other_profile_picture,

        (
            SELECT m.message
            FROM messages m
            WHERE m.conversation_id = c.id
            ORDER BY m.id DESC
            LIMIT 1
        ) AS last_message,

        (
            SELECT m.image
            FROM messages m
            WHERE m.conversation_id = c.id
            ORDER BY m.id DESC
            LIMIT 1
        ) AS last_image,

        (
            SELECT m.message_type
            FROM messages m
            WHERE m.conversation_id = c.id
            ORDER BY m.id DESC
            LIMIT 1
        ) AS last_message_type,

        (
            SELECT m.created_at
            FROM messages m
            WHERE m.conversation_id = c.id
            ORDER BY m.id DESC
            LIMIT 1
        ) AS last_message_time,

        (
            SELECT COUNT(*)
            FROM messages m
            WHERE m.conversation_id = c.id
              AND m.sender_id != ?
              AND m.is_read = 0
        ) AS unread_count

    FROM conversations c

    INNER JOIN users u
        ON u.id =
            CASE
                WHEN c.user_one_id = ?
                THEN c.user_two_id
                ELSE c.user_one_id
            END

    WHERE
        (
            c.user_one_id = ?
            OR c.user_two_id = ?
        )

        AND LOWER(TRIM(u.role)) = ?

    ORDER BY
        COALESCE(last_message_time, c.updated_at) DESC,
        c.id DESC
");


$stmt->execute([
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $current_user_id,
    $target_role
]);


$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| DISPLAY NAME OF CURRENT USER
|--------------------------------------------------------------------------
*/

$current_name = !empty($current_user['fullname'])
    ? $current_user['fullname']
    : $current_user['username'];


/*
|--------------------------------------------------------------------------
| ROLE LABEL
|--------------------------------------------------------------------------
*/

$current_role_label = ucfirst($current_role);

$target_role_label = ucfirst($target_role);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Messages | Agro-Link Uganda
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

        body {

            background: #f4f7f5;

            font-family: Arial, sans-serif;

        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .chat-header {

            background: #198754;

            color: white;

            padding: 18px 25px;

        }


        .chat-header h4 {

            margin: 0;

            font-weight: bold;

        }


        .chat-header small {

            opacity: 0.9;

        }


        /*
        |--------------------------------------------------------------------------
        | CHAT CONTAINER
        |--------------------------------------------------------------------------
        */

        .messages-container {

            max-width: 900px;

            margin: 30px auto;

            padding: 0 15px;

        }


        /*
        |--------------------------------------------------------------------------
        | CONVERSATION CARD
        |--------------------------------------------------------------------------
        */

        .conversation-card {

            background: white;

            border-radius: 12px;

            padding: 15px;

            margin-bottom: 12px;

            text-decoration: none;

            color: inherit;

            display: flex;

            align-items: center;

            transition: background 0.2s,
                        transform 0.2s;

            box-shadow:
                0 2px 8px rgba(0,0,0,0.05);

        }


        .conversation-card:hover {

            background: #f0f8f3;

            transform: translateY(-1px);

            color: inherit;

        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE IMAGE
        |--------------------------------------------------------------------------
        */

        .profile-picture {

            width: 55px;

            height: 55px;

            border-radius: 50%;

            object-fit: cover;

            margin-right: 15px;

            border: 2px solid #198754;

        }


        .profile-placeholder {

            width: 55px;

            height: 55px;

            border-radius: 50%;

            background: #e8f5e9;

            color: #198754;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-right: 15px;

            font-size: 22px;

            flex-shrink: 0;

        }


        /*
        |--------------------------------------------------------------------------
        | USER INFORMATION
        |--------------------------------------------------------------------------
        */

        .conversation-info {

            flex: 1;

            min-width: 0;

        }


        .user-name {

            font-weight: bold;

            font-size: 17px;

            margin-bottom: 3px;

        }


        .user-role {

            font-size: 12px;

            color: #198754;

            font-weight: bold;

            margin-bottom: 4px;

        }


        .last-message {

            color: #6c757d;

            font-size: 14px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

            max-width: 100%;

        }


        /*
        |--------------------------------------------------------------------------
        | TIME
        |--------------------------------------------------------------------------
        */

        .conversation-time {

            font-size: 12px;

            color: #888;

            margin-left: 10px;

            white-space: nowrap;

        }


        /*
        |--------------------------------------------------------------------------
        | UNREAD BADGE
        |--------------------------------------------------------------------------
        */

        .unread-badge {

            background: #dc3545;

            color: white;

            border-radius: 50%;

            min-width: 23px;

            height: 23px;

            padding: 0 6px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            font-size: 11px;

            font-weight: bold;

            margin-left: 10px;

        }


        /*
        |--------------------------------------------------------------------------
        | EMPTY STATE
        |--------------------------------------------------------------------------
        */

        .empty-box {

            background: white;

            border-radius: 15px;

            padding: 60px 20px;

            text-align: center;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.05);

        }


        .empty-box i {

            font-size: 55px;

            color: #198754;

            margin-bottom: 20px;

        }


        /*
        |--------------------------------------------------------------------------
        | MOBILE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 576px) {

            .messages-container {

                margin-top: 15px;

            }


            .conversation-card {

                padding: 12px;

            }


            .profile-picture,
            .profile-placeholder {

                width: 48px;

                height: 48px;

            }


            .user-name {

                font-size: 15px;

            }


            .last-message {

                font-size: 13px;

            }


            .conversation-time {

                font-size: 10px;

            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

<div class="chat-header">

    <div class="container">

        <div class="d-flex justify-content-between
                    align-items-center">

            <div>

                <h4>

                    <i class="fas fa-comments me-2"></i>

                    Messages

                </h4>

                <small>

                    <?= htmlspecialchars($current_role_label) ?>

                    • Chat with

                    <?= htmlspecialchars($target_role_label) ?>s

                </small>

            </div>


            <a
                href="../index.php"
                class="btn btn-light btn-sm"
            >

                <i class="fas fa-home me-1"></i>

                Home

            </a>

        </div>

    </div>

</div>


<!-- =========================================================
     MAIN
========================================================= -->

<div class="messages-container">


    <!-- ROLE INFORMATION -->

    <div class="alert alert-success">

        <i class="fas fa-circle-info me-2"></i>

        You are logged in as a

        <strong>
            <?= htmlspecialchars($current_role_label) ?>
        </strong>.

        You can chat with

        <strong>
            <?= htmlspecialchars($target_role_label) ?>s
        </strong>.

    </div>


    <?php if (!empty($conversations)): ?>


        <!-- =================================================
             CONVERSATIONS
        ================================================== -->

        <?php foreach ($conversations as $conversation): ?>


            <?php

            /*
             * Get the other user's name.
             */

            $other_name = !empty($conversation['other_fullname'])
                ? $conversation['other_fullname']
                : $conversation['other_username'];


            /*
             * Normalize the other user's role.
             */

            $other_role = ucfirst(
                strtolower(
                    trim($conversation['other_role'])
                )
            );


            /*
             * Last message preview.
             */

            if (!empty($conversation['last_message'])) {

                $preview = $conversation['last_message'];

            } elseif (!empty($conversation['last_image'])) {

                $preview = "📷 Image";

            } else {

                $preview = "No messages yet.";

            }


            /*
             * Format time.
             */

            if (!empty($conversation['last_message_time'])) {

                $time = date(
                    "d M, H:i",
                    strtotime(
                        $conversation['last_message_time']
                    )
                );

            } else {

                $time = date(
                    "d M, H:i",
                    strtotime(
                        $conversation['updated_at']
                    )
                );

            }


            /*
             * Profile picture.
             */

            $profile_picture =
                $conversation['other_profile_picture'];

            ?>


            <a
                href="index.php?user_id=<?= (int) $conversation['other_user_id'] ?>"
                class="conversation-card"
            >


                <!-- PROFILE -->

                <?php if (!empty($profile_picture)): ?>

                    <img
                        src="../uploads/profiles/<?= htmlspecialchars($profile_picture) ?>"
                        alt="Profile"
                        class="profile-picture"
                    >

                <?php else: ?>

                    <div class="profile-placeholder">

                        <i class="fas fa-user"></i>

                    </div>

                <?php endif; ?>


                <!-- USER DETAILS -->

                <div class="conversation-info">


                    <div class="user-name">

                        <?= htmlspecialchars($other_name) ?>

                    </div>


                    <div class="user-role">

                        <i class="fas fa-user-tag me-1"></i>

                        <?= htmlspecialchars($other_role) ?>

                    </div>


                    <div class="last-message">

                        <?= htmlspecialchars($preview) ?>

                    </div>


                </div>


                <!-- TIME -->

                <div class="text-end">


                    <div class="conversation-time">

                        <?= htmlspecialchars($time) ?>

                    </div>


                    <?php if ((int) $conversation['unread_count'] > 0): ?>

                        <span class="unread-badge">

                            <?= (int) $conversation['unread_count'] ?>

                        </span>

                    <?php endif; ?>


                </div>


            </a>


        <?php endforeach; ?>


    <?php else: ?>


        <!-- =================================================
             NO CONVERSATIONS
        ================================================== -->

        <div class="empty-box">


            <i class="fas fa-comments"></i>


            <h4 class="fw-bold">

                No conversations yet

            </h4>


            <p class="text-muted">

                You currently have no conversations with
                <?= htmlspecialchars($target_role_label) ?>s.

            </p>


            <?php if ($current_role === 'farmer'): ?>

                <p class="text-muted">

                    When a buyer starts a conversation with you,
                    it will appear here.

                </p>

            <?php else: ?>

                <p class="text-muted">

                    When you start a conversation with a farmer,
                    it will appear here.

                </p>

            <?php endif; ?>


            <a
                href="../index.php"
                class="btn btn-success mt-2"
            >

                <i class="fas fa-arrow-left me-1"></i>

                Back

            </a>


        </div>


    <?php endif; ?>


</div>


<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>