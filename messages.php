<?php
session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Temporary logged-in user
|--------------------------------------------------------------------------
| Later this will come from the real login session.
*/
$current_user_id = $_SESSION['user_id'] ?? 1;

/*
|--------------------------------------------------------------------------
| Get conversations
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT
        m.*,
        u.username,
        u.role
    FROM messages m
    INNER JOIN users u
        ON u.id =
        CASE
            WHEN m.sender_id = ? THEN m.receiver_id
            ELSE m.sender_id
        END
    WHERE m.sender_id = ?
       OR m.receiver_id = ?
    ORDER BY m.created_at DESC
");

$stmt->execute([
    $current_user_id,
    $current_user_id,
    $current_user_id
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Remove duplicate users so each conversation appears once
|--------------------------------------------------------------------------
*/
$conversations = [];

foreach ($messages as $message) {

    $other_user_id =
        ($message['sender_id'] == $current_user_id)
        ? $message['receiver_id']
        : $message['sender_id'];

    if (!isset($conversations[$other_user_id])) {

        $conversations[$other_user_id] = [
            'user_id' => $other_user_id,
            'username' => $message['username'],
            'role' => $message['role'],
            'message' => $message['message'],
            'created_at' => $message['created_at']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Messages - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-light">

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold text-success">
                <i class="fas fa-comments"></i>
                Messages
            </h2>

            <p class="text-muted mb-0">
                Your conversations with farmers and buyers
            </p>
        </div>

        <a href="../index.php"
           class="btn btn-outline-success">

            <i class="fas fa-home"></i>
            Home

        </a>

    </div>


    <?php if (empty($conversations)): ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>

                <h5>No messages yet</h5>

                <p class="text-muted">
                    Your conversations will appear here.
                </p>

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="list-group list-group-flush">

                <?php foreach ($conversations as $conversation): ?>

                    <a
                        href="index.php?user_id=<?= (int)$conversation['user_id'] ?>"
                        class="list-group-item list-group-item-action p-3"
                    >

                        <div class="d-flex align-items-center">

                            <!-- Avatar -->
                            <div
                                class="rounded-circle bg-success text-white
                                       d-flex align-items-center
                                       justify-content-center me-3"
                                style="width:50px;height:50px;"
                            >

                                <i class="fas fa-user"></i>

                            </div>


                            <!-- User information -->
                            <div class="flex-grow-1">

                                <div class="d-flex justify-content-between">

                                    <h6 class="mb-1 fw-bold">

                                        <?= htmlspecialchars(
                                            $conversation['username']
                                        ) ?>

                                    </h6>

                                    <small class="text-muted">

                                        <?= htmlspecialchars(
                                            $conversation['created_at']
                                        ) ?>

                                    </small>

                                </div>


                                <span class="badge bg-success mb-1">

                                    <?= htmlspecialchars(
                                        ucfirst($conversation['role'])
                                    ) ?>

                                </span>


                                <p class="mb-0 text-muted">

                                    <?= htmlspecialchars(
                                        mb_strimwidth(
                                            $conversation['message'],
                                            0,
                                            70,
                                            "..."
                                        )
                                    ) ?>

                                </p>

                            </div>


                            <i class="fas fa-chevron-right text-muted ms-3"></i>

                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>
</html>