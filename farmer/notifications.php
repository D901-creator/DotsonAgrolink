<?php
require_once "../config/database.php";

$farmer_id = 1;

/* Mark notification as read */
if (isset($_GET['read'])) {
    $notification_id = (int) $_GET['read'];

    $stmt = $pdo->prepare("
        UPDATE notifications
        SET is_read = 1
        WHERE id = ?
        AND user_id = ?
    ");

    $stmt->execute([$notification_id, $farmer_id]);

    header("Location: notifications.php");
    exit;
}

/* Get farmer notifications */
$stmt = $pdo->prepare("
    SELECT *
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$farmer_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Notifications - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h4 class="mb-4">
                🌱 Agro-Link
            </h4>

            <ul class="nav flex-column">

                <li class="nav-item mb-2">
                    <a href="dashboard.php" class="nav-link text-white">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="my_produce.php" class="nav-link text-white">
                        <i class="fas fa-seedling"></i> My Produce
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="buyer_requests.php" class="nav-link text-white">
                        <i class="fas fa-file-invoice"></i> Buyer Requests
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="matches.php" class="nav-link text-white">
                        <i class="fas fa-handshake"></i> Matches
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="offers.php" class="nav-link text-white">
                        <i class="fas fa-tags"></i> Offers
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="negotiations.php" class="nav-link text-white">
                        <i class="fas fa-comments"></i> Negotiations
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="orders.php" class="nav-link text-white">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="delivery.php" class="nav-link text-white">
                        <i class="fas fa-truck"></i> Delivery
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="market_prices.php" class="nav-link text-white">
                        <i class="fas fa-chart-line"></i> Market Prices
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="sales_history.php" class="nav-link text-white">
                        <i class="fas fa-history"></i> Sales History
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="notifications.php" class="nav-link text-white active">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a href="profile.php" class="nav-link text-white">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>

                <li class="nav-item mt-4">
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>

            </ul>
        </div>


        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2>
                        <i class="fas fa-bell"></i>
                        Notifications
                    </h2>

                    <p class="text-muted">
                        Stay updated about your Agro-Link activities.
                    </p>
                </div>

            </div>


            <?php if (count($notifications) > 0): ?>

                <?php foreach ($notifications as $notification): ?>

                    <?php
                    $is_unread = empty($notification['is_read']);
                    ?>

                    <div class="card mb-3 shadow-sm
                        <?= $is_unread ? 'border-success' : '' ?>">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h5 class="card-title">

                                        <i class="fas fa-bell text-success"></i>

                                        <?= htmlspecialchars(
                                            $notification['title'] ?? 'Notification'
                                        ) ?>

                                        <?php if ($is_unread): ?>

                                            <span class="badge bg-success">
                                                New
                                            </span>

                                        <?php endif; ?>

                                    </h5>

                                    <p class="card-text">
                                        <?= htmlspecialchars(
                                            $notification['message'] ?? ''
                                        ) ?>
                                    </p>

                                    <small class="text-muted">

                                        <i class="fas fa-clock"></i>

                                        <?= htmlspecialchars(
                                            $notification['created_at'] ?? ''
                                        ) ?>

                                    </small>

                                </div>


                                <?php if ($is_unread): ?>

                                    <div>

                                        <a
                                            href="notifications.php?read=<?= (int)$notification['id'] ?>"
                                            class="btn btn-sm btn-outline-success"
                                        >
                                            <i class="fas fa-check"></i>
                                            Mark as Read
                                        </a>

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="alert alert-info">

                    <i class="fas fa-info-circle"></i>

                    You currently have no notifications.

                </div>

            <?php endif; ?>

        </div>

    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>