<?php
require_once "../config/database.php";

try {
    // ==============================
    // SYSTEM COUNTS
    // ==============================

    $users_count = $pdo->query("
        SELECT COUNT(*) FROM users
    ")->fetchColumn();

    $farmers_count = $pdo->query("
        SELECT COUNT(*) FROM users
        WHERE role = 'farmer'
    ")->fetchColumn();

    $buyers_count = $pdo->query("
        SELECT COUNT(*) FROM users
        WHERE role = 'buyer'
    ")->fetchColumn();

    $produce_count = $pdo->query("
        SELECT COUNT(*) FROM produce
    ")->fetchColumn();

    $requests_count = $pdo->query("
        SELECT COUNT(*) FROM buyer_requests
    ")->fetchColumn();

    $offers_count = $pdo->query("
        SELECT COUNT(*) FROM offers
    ")->fetchColumn();

    $orders_count = $pdo->query("
        SELECT COUNT(*) FROM orders
    ")->fetchColumn();

    $deliveries_count = $pdo->query("
        SELECT COUNT(*) FROM deliveries
    ")->fetchColumn();

    $messages_count = $pdo->query("
        SELECT COUNT(*) FROM messages
    ")->fetchColumn();

    $notifications_count = $pdo->query("
        SELECT COUNT(*) FROM notifications
    ")->fetchColumn();

    $market_prices_count = $pdo->query("
        SELECT COUNT(*) FROM market_prices
    ")->fetchColumn();

    $news_count = $pdo->query("
        SELECT COUNT(*) FROM news
    ")->fetchColumn();

} catch (PDOException $e) {

    $error = "Unable to load dashboard statistics.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Control Center | Agro-Link Uganda</title>

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
            background: #f5f7f9;
            font-family: Arial, sans-serif;
        }

        .admin-sidebar {
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #dee2e6;
            position: sticky;
            top: 0;
        }

        .admin-logo {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #198754;
            border-bottom: 1px solid #dee2e6;
        }

        .admin-section {
            padding: 15px 20px 5px;
            font-size: 11px;
            font-weight: bold;
            color: #6c757d;
            letter-spacing: 1px;
        }

        .admin-sidebar a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #343a40;
            font-size: 14px;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: #e9f7ef;
            color: #198754;
        }

        .admin-sidebar i {
            width: 22px;
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9f7ef;
            color: #198754;
            font-size: 20px;
        }

        .stat-number {
            font-size: 25px;
            font-weight: bold;
        }

        .control-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            height: 100%;
        }

        .control-card a {
            text-decoration: none;
        }

        .control-icon {
            font-size: 28px;
            color: #198754;
        }

        .small-text {
            font-size: 13px;
            color: #6c757d;
        }

        @media (max-width: 768px) {

            .admin-sidebar {
                min-height: auto;
                position: relative;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- =====================================
             ADMIN SIDEBAR
        ====================================== -->

        <div class="col-lg-2 col-md-3 p-0 admin-sidebar">

            <div class="admin-logo">

                <i class="fas fa-leaf"></i>

                Agro-Link Admin

            </div>

            <a href="dashboard.php" class="active">

                <i class="fas fa-gauge"></i>

                Dashboard

            </a>


            <div class="admin-section">
                USER MANAGEMENT
            </div>

            <a href="users.php">

                <i class="fas fa-users"></i>

                Users

            </a>

            <a href="users.php?role=farmer">

                <i class="fas fa-tractor"></i>

                Farmers

            </a>

            <a href="users.php?role=buyer">

                <i class="fas fa-user-tie"></i>

                Buyers

            </a>


            <div class="admin-section">
                AGRICULTURE
            </div>

            <a href="produce.php">

                <i class="fas fa-seedling"></i>

                Produce

            </a>

            <a href="farm_information.php">

                <i class="fas fa-tractor"></i>

                Farm Information

            </a>

            <a href="buyer_requests.php">

                <i class="fas fa-file-signature"></i>

                Buyer Requests

            </a>

            <a href="negotiations.php">

                <i class="fas fa-handshake"></i>

                Negotiations

            </a>


            <div class="admin-section">
                TRANSACTIONS
            </div>

            <a href="offers.php">

                <i class="fas fa-tags"></i>

                Offers

            </a>

            <a href="orders.php">

                <i class="fas fa-cart-shopping"></i>

                Orders

            </a>

            <a href="deliveries.php">

                <i class="fas fa-truck"></i>

                Deliveries

            </a>


            <div class="admin-section">
                COMMUNICATION
            </div>

            <a href="messages.php">

                <i class="fas fa-comments"></i>

                Messages

            </a>

            <a href="notifications.php">

                <i class="fas fa-bell"></i>

                Notifications

            </a>


            <div class="admin-section">
                AGRICULTURE HUB
            </div>

            <a href="market_prices.php">

                <i class="fas fa-chart-line"></i>

                Market Prices

            </a>

            <a href="news.php">

                <i class="fas fa-newspaper"></i>

                News

            </a>

            <a href="weather.php">

                <i class="fas fa-cloud-sun"></i>

                Weather

            </a>

            <a href="government_updates.php">

                <i class="fas fa-building-columns"></i>

                Government Updates

            </a>

            <a href="support_programs.php">

                <i class="fas fa-hand-holding-heart"></i>

                Support Programs

            </a>

            <a href="alerts.php">

                <i class="fas fa-triangle-exclamation"></i>

                Alerts

            </a>


            <div class="admin-section">
                REPORTS
            </div>

            <a href="reports.php">

                <i class="fas fa-chart-pie"></i>

                Reports & Analytics

            </a>


            <div class="admin-section">
                SYSTEM
            </div>

            <a href="settings.php">

                <i class="fas fa-gear"></i>

                Settings

            </a>

            <a href="audit_logs.php">

                <i class="fas fa-shield-halved"></i>

                Audit Logs

            </a>

            <hr>

            <a href="../index.php">

                <i class="fas fa-arrow-left"></i>

                Back to Agro-Link

            </a>

            <a href="../index.php">

                <i class="fas fa-right-from-bracket"></i>

                Logout

            </a>

        </div>


        <!-- =====================================
             MAIN CONTENT
        ====================================== -->

        <div class="col-lg-10 col-md-9 p-0">

            <!-- TOP BAR -->

            <div class="topbar d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">

                        <i class="fas fa-shield-halved text-success"></i>

                        Admin Control Center

                    </h5>

                    <small class="text-muted">

                        Manage and monitor Agro-Link Uganda

                    </small>

                </div>

                <div>

                    <span class="badge bg-success">

                        Administrator

                    </span>

                </div>

            </div>


            <div class="p-4">

                <?php if (isset($error)): ?>

                    <div class="alert alert-danger">

                        <?php echo htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <!-- =====================================
                     STATISTICS
                ====================================== -->

                <div class="row g-3 mb-4">


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Total Users
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $users_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-users"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Farmers
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $farmers_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-tractor"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Buyers
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $buyers_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-user-tie"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Produce Listings
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $produce_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-seedling"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Buyer Requests
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $requests_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-file-signature"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Offers
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $offers_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-tags"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Orders
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $orders_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-cart-shopping"></i>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-xl-3 col-md-6">

                        <div class="card stat-card p-3">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <div class="small-text">
                                        Deliveries
                                    </div>

                                    <div class="stat-number">
                                        <?php echo $deliveries_count ?? 0; ?>
                                    </div>

                                </div>

                                <div class="stat-icon">

                                    <i class="fas fa-truck"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================================
                     MANAGEMENT MODULES
                ====================================== -->

                <h5 class="mb-3">

                    Management Center

                </h5>


                <div class="row g-4">


                    <!-- USERS -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card control-card p-4">

                            <div class="control-icon mb-3">

                                <i class="fas fa-users"></i>

                            </div>

                            <h5>User Management</h5>

                            <p class="small-text">

                                Manage farmers, buyers and platform users.

                            </p>

                            <a href="users.php"
                               class="btn btn-success btn-sm">

                                Manage Users

                            </a>

                        </div>

                    </div>


                    <!-- AGRICULTURE -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card control-card p-4">

                            <div class="control-icon mb-3">

                                <i class="fas fa-seedling"></i>

                            </div>

                            <h5>Agriculture Management</h5>

                            <p class="small-text">

                                Manage produce, farms, requests and agricultural activities.

                            </p>

                            <a href="produce.php"
                               class="btn btn-success btn-sm">

                                Manage Agriculture

                            </a>

                        </div>

                    </div>


                    <!-- TRANSACTIONS -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card control-card p-4">

                            <div class="control-icon mb-3">

                                <i class="fas fa-cart-shopping"></i>

                            </div>

                            <h5>Transactions</h5>

                            <p class="small-text">

                                Monitor offers, negotiations, orders and deliveries.

                            </p>

                            <a href="orders.php"
                               class="btn btn-success btn-sm">

                                Manage Transactions

                            </a>

                        </div>

                    </div>


                    <!-- COMMUNICATION -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card control-card p-4">

                            <div class="control-icon mb-3">

                                <i class="fas fa-comments"></i>

                            </div>

                            <h5>Communication</h5>

                            <p class="small-text">

                                Monitor platform messages and notifications.

                            </p>

                            <a href="messages.php"
                               class="btn btn-success btn-sm">

                                Manage Communication

                            </a>

                        </div>

                    </div>


                    <!-- HUB -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card control-card p-4">

                            <div class="control-icon mb-3">

                                <i class="fas fa-newspaper"></i>

                            </div>

                            <h5>Agriculture Hub</h5>

                            <p class="small-text">

                                Manage news, prices, government updates and alerts.

                            </p>

                            <a href="news.php"
                               class="btn btn-success btn-sm">

                                Manage Hub

                            </a>

                        </div>

                    </div>


                    <!-- REPORTS -->

                    <div class="col-lg-4 col-md-6">

                        <div class="card control-card p-4">

                            <div class="control-icon mb-3">

                                <i class="fas fa-chart-pie"></i>

                            </div>

                            <h5>Reports & Analytics</h5>

                            <p class="small-text">

                                Analyze platform activity, users, produce and transactions.

                            </p>

                            <a href="reports.php"
                               class="btn btn-success btn-sm">

                                View Reports

                            </a>

                        </div>

                    </div>


                </div>


                <!-- =====================================
                     QUICK STATUS
                ====================================== -->

                <div class="card control-card mt-4 p-4">

                    <h5>

                        <i class="fas fa-chart-line text-success"></i>

                        Platform Overview

                    </h5>

                    <p class="small-text mb-3">

                        Current activity across the Agro-Link Uganda platform.

                    </p>


                    <div class="row g-3">

                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Messages
                                </small>

                                <h5 class="mb-0">

                                    <?php echo $messages_count ?? 0; ?>

                                </h5>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Notifications
                                </small>

                                <h5 class="mb-0">

                                    <?php echo $notifications_count ?? 0; ?>

                                </h5>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Market Prices
                                </small>

                                <h5 class="mb-0">

                                    <?php echo $market_prices_count ?? 0; ?>

                                </h5>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    News Articles
                                </small>

                                <h5 class="mb-0">

                                    <?php echo $news_count ?? 0; ?>

                                </h5>

                            </div>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>