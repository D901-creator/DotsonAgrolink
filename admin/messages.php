<?php

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| GET ALL MESSAGES
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$sql = "
    SELECT
        m.*,

        sender.username AS sender_username,
        sender.role AS sender_role,

        receiver.username AS receiver_username,
        receiver.role AS receiver_role

    FROM messages m

    INNER JOIN users sender
        ON m.sender_id = sender.id

    INNER JOIN users receiver
        ON m.receiver_id = receiver.id

    WHERE 1=1
";

$params = [];


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $sql .= "
        AND (
            sender.username LIKE ?
            OR receiver.username LIKE ?
            OR m.message LIKE ?
        )
    ";

    $search_value = "%{$search}%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
}


/*
|--------------------------------------------------------------------------
| ROLE FILTER
|--------------------------------------------------------------------------
*/

if ($role_filter !== '') {

    $sql .= "
        AND (
            sender.role = ?
            OR receiver.role = ?
        )
    ";

    $params[] = $role_filter;
    $params[] = $role_filter;
}


/*
|--------------------------------------------------------------------------
| READ / UNREAD FILTER
|--------------------------------------------------------------------------
*/

if ($status_filter === 'read') {

    $sql .= "
        AND m.is_read = 1
    ";

} elseif ($status_filter === 'unread') {

    $sql .= "
        AND m.is_read = 0
    ";
}


$sql .= "
    ORDER BY m.created_at DESC
";


$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Messages - Admin - Agro-Link Uganda
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body class="bg-light">


<!-- NAVBAR -->

<nav class="navbar navbar-dark bg-success">

    <div class="container-fluid">

        <a
            class="navbar-brand fw-bold"
            href="dashboard.php"
        >

            🌱 AGRO-LINK UGANDA

        </a>


        <span class="text-white">

            <i class="fas fa-user-shield"></i>

            Administrator

        </span>

    </div>

</nav>



<div class="container-fluid">

    <div class="row">


        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">


            <h5 class="mb-4">

                <i class="fas fa-gauge"></i>

                Admin Panel

            </h5>


            <a
                href="dashboard.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-home me-2"></i>

                Dashboard

            </a>


            <a
                href="users.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-users me-2"></i>

                Users

            </a>


            <a
                href="produce.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-seedling me-2"></i>

                Produce

            </a>


            <a
                href="orders.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-cart-shopping me-2"></i>

                Orders

            </a>


            <!-- COMMUNICATION -->

            <div class="text-uppercase text-secondary small fw-bold mt-4 mb-2">

                Communication

            </div>


            <a
                href="messages.php"
                class="d-block text-warning text-decoration-none mb-3"
            >

                <i class="fas fa-comments me-2"></i>

                Messages

            </a>


            <a
                href="conversations.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-comment-dots me-2"></i>

                Conversations

            </a>


            <a
                href="notifications.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-bell me-2"></i>

                Notifications

            </a>


            <a
                href="../hub/index.php"
                class="d-block text-white text-decoration-none mb-3"
            >

                <i class="fas fa-newspaper me-2"></i>

                Agriculture Hub

            </a>


        </div>



        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 p-4">


            <!-- HEADER -->

            <div
                class="d-flex justify-content-between align-items-center mb-4"
            >

                <div>

                    <h2 class="fw-bold">

                        <i class="fas fa-comments text-success"></i>

                        Messages

                    </h2>


                    <p class="text-muted mb-0">

                        Monitor communication between Agro-Link users.

                    </p>

                </div>


                <a
                    href="dashboard.php"
                    class="btn btn-success"
                >

                    <i class="fas fa-arrow-left"></i>

                    Dashboard

                </a>

            </div>



            <!-- STATISTICS -->

            <div class="row g-3 mb-4">


                <div class="col-md-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Total Messages

                                    </h6>

                                    <h3 class="fw-bold">

                                        <?= count($messages) ?>

                                    </h3>

                                </div>


                                <i
                                    class="fas fa-envelope fa-2x text-success"
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="col-md-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Unread Messages

                                    </h6>

                                    <h3 class="fw-bold">

                                        <?php

                                        $unread = 0;

                                        foreach ($messages as $message) {

                                            if ((int)$message['is_read'] === 0) {

                                                $unread++;

                                            }

                                        }

                                        echo $unread;

                                        ?>

                                    </h3>

                                </div>


                                <i
                                    class="fas fa-envelope-open-text fa-2x text-danger"
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="col-md-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">

                                        Communication Status

                                    </h6>

                                    <h5 class="text-success mb-0">

                                        Active

                                    </h5>

                                </div>


                                <i
                                    class="fas fa-circle-check fa-2x text-success"
                                ></i>

                            </div>

                        </div>

                    </div>

                </div>


            </div>


<!-- MESSAGE FILTERS -->

<div class="card shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row g-2">

                <div class="col-md-5">

                    <label class="form-label fw-bold">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search username or message..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label fw-bold">
                        User Role
                    </label>

                    <select name="role" class="form-select">

                        <option value="">
                            All Roles
                        </option>

                        <option value="farmer"
                            <?= (($_GET['role'] ?? '') === 'farmer') ? 'selected' : '' ?>>
                            Farmers
                        </option>

                        <option value="buyer"
                            <?= (($_GET['role'] ?? '') === 'buyer') ? 'selected' : '' ?>>
                            Buyers
                        </option>

                        <option value="admin"
                            <?= (($_GET['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                            Administrators
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label fw-bold">
                        Status
                    </label>

                    <select name="status" class="form-select">

                        <option value="">
                            All
                        </option>

                        <option value="read"
                            <?= (($_GET['status'] ?? '') === 'read') ? 'selected' : '' ?>>
                            Read
                        </option>

                        <option value="unread"
                            <?= (($_GET['status'] ?? '') === 'unread') ? 'selected' : '' ?>>
                            Unread
                        </option>

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <div class="w-100">

                        <button
                            type="submit"
                            class="btn btn-success"
                        >

                            <i class="fas fa-search"></i>

                            Filter

                        </button>


                        <a
                            href="messages.php"
                            class="btn btn-secondary"
                        >

                            <i class="fas fa-rotate-left"></i>

                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>
            <!-- MESSAGES TABLE -->

            <div class="card shadow-sm">


                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-message"></i>

                        Platform Messages

                    </h5>

                </div>


                <div class="card-body">


                    <?php if (count($messages) > 0): ?>


                        <div class="table-responsive">


                            <table
                                class="table table-bordered table-hover align-middle"
                            >


                                <thead class="table-success">

                                    <tr>

                                        <th>#</th>

                                        <th>Sender</th>

                                        <th>Sender Role</th>

                                        <th>Receiver</th>

                                        <th>Receiver Role</th>

                                        <th>Message</th>

                                        <th>Status</th>

                                        <th>Date</th>

                                    </tr>

                                </thead>


                                <tbody>


                                <?php foreach ($messages as $message): ?>


                                    <tr>


                                        <td>

                                            <?= htmlspecialchars(
                                                $message['id']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $message['sender_username']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php

                                            $sender_role =
                                                strtolower(
                                                    $message['sender_role']
                                                );

                                            if ($sender_role === 'farmer') {

                                                echo '<span class="badge bg-success">Farmer</span>';

                                            } elseif ($sender_role === 'buyer') {

                                                echo '<span class="badge bg-primary">Buyer</span>';

                                            } elseif ($sender_role === 'admin') {

                                                echo '<span class="badge bg-danger">Admin</span>';

                                            } else {

                                                echo '<span class="badge bg-secondary">'
                                                    . htmlspecialchars(
                                                        $sender_role
                                                    )
                                                    . '</span>';

                                            }

                                            ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $message['receiver_username']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php

                                            $receiver_role =
                                                strtolower(
                                                    $message['receiver_role']
                                                );

                                            if ($receiver_role === 'farmer') {

                                                echo '<span class="badge bg-success">Farmer</span>';

                                            } elseif ($receiver_role === 'buyer') {

                                                echo '<span class="badge bg-primary">Buyer</span>';

                                            } elseif ($receiver_role === 'admin') {

                                                echo '<span class="badge bg-danger">Admin</span>';

                                            } else {

                                                echo '<span class="badge bg-secondary">'
                                                    . htmlspecialchars(
                                                        $receiver_role
                                                    )
                                                    . '</span>';

                                            }

                                            ?>

                                        </td>


                                        <td style="min-width:250px;">

                                            <?= htmlspecialchars(
                                                $message['message']
                                            ) ?>

                                        </td>


                                        <td>

                                            <?php if (
                                                (int)$message['is_read'] === 1
                                            ): ?>

                                                <span class="badge bg-success">

                                                    Read

                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-warning text-dark">

                                                    Unread

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $message['created_at']
                                            ) ?>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                                </tbody>


                            </table>

                        </div>


                    <?php else: ?>


                        <div class="alert alert-info">

                            <i class="fas fa-info-circle"></i>

                            No messages have been sent yet.

                        </div>


                    <?php endif; ?>


                </div>

            </div>


        </div>

    </div>

</div>


</body>

</html>