<?php
session_start();

require_once "../config/database.php";

// Temporary farmer ID
$farmer_id = 1;

// Get all negotiations related to this farmer's offers
$stmt = $pdo->prepare("
    SELECT
        n.*,
        o.buyer_id,
        o.produce_id,
        p.produce_name,
        p.unit
    FROM negotiations n
    INNER JOIN offers o
        ON n.offer_id = o.id
    LEFT JOIN produce p
        ON o.produce_id = p.id
    WHERE o.farmer_id = ?
    ORDER BY n.created_at DESC
");

$stmt->execute([$farmer_id]);

$negotiations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Negotiations - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        body {
            background: #f4f7f5;
        }

        .sidebar {
            min-height: 100vh;
            background: #14532d;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #198754;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
            padding: 20px;
            color: white;
        }

        .content {
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .negotiation-card {
            transition: 0.2s;
        }

        .negotiation-card:hover {
            transform: translateY(-2px);
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .countered {
            background: #fff3cd;
            color: #856404;
        }

        .accepted {
            background: #d1e7dd;
            color: #0f5132;
        }

        .rejected {
            background: #f8d7da;
            color: #842029;
        }

        .pending {
            background: #cff4fc;
            color: #055160;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 p-0 sidebar">

            <div class="brand">
                🌱 Agro-Link Uganda
            </div>

            <a href="dashboard.php">
                <i class="fas fa-home me-2"></i>
                Dashboard
            </a>

            <a href="my_produce.php">
                <i class="fas fa-seedling me-2"></i>
                My Produce
            </a>

            <a href="buyer_requests.php">
                <i class="fas fa-file-alt me-2"></i>
                Buyer Requests
            </a>

            <a href="matches.php">
                <i class="fas fa-handshake me-2"></i>
                Matches
            </a>

            <a href="offers.php">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Offers
            </a>

            <a href="negotiations.php" class="active">
                <i class="fas fa-comments me-2"></i>
                Negotiations
            </a>

            <a href="orders.php">
                <i class="fas fa-shopping-cart me-2"></i>
                Orders
            </a>

            <a href="delivery.php">
                <i class="fas fa-truck me-2"></i>
                Delivery
            </a>

            <a href="market_prices.php">
                <i class="fas fa-chart-line me-2"></i>
                Market Prices
            </a>

            <a href="sales_history.php">
                <i class="fas fa-chart-bar me-2"></i>
                Sales History
            </a>

            <a href="notifications.php">
                <i class="fas fa-bell me-2"></i>
                Notifications
            </a>

            <a href="profile.php">
                <i class="fas fa-user me-2"></i>
                Profile
            </a>

            <a href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>
                Logout
            </a>

        </div>


        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 content">

            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2>
                        <i class="fas fa-comments text-success"></i>
                        Negotiations
                    </h2>

                    <p class="text-muted">
                        View and respond to buyer counter-offers.
                    </p>

                </div>

                <a
                    href="offers.php"
                    class="btn btn-outline-success"
                >
                    <i class="fas fa-file-invoice-dollar"></i>
                    View Offers
                </a>

            </div>


            <!-- NEGOTIATIONS -->

            <?php if (count($negotiations) > 0): ?>

                <div class="row">

                    <?php foreach ($negotiations as $negotiation): ?>

                        <?php

                        $status = $negotiation['status'] ?? 'Pending';

                        $status_class = strtolower($status);

                        ?>

                        <div class="col-md-6 col-xl-4 mb-4">

                            <div class="card negotiation-card h-100">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start mb-3">

                                        <h5 class="mb-0">

                                            <?= htmlspecialchars(
                                                $negotiation['produce_name']
                                                ?? 'Produce'
                                            ) ?>

                                        </h5>

                                        <span
                                            class="status <?= htmlspecialchars(
                                                $status_class
                                            ) ?>"
                                        >

                                            <?= htmlspecialchars($status) ?>

                                        </span>

                                    </div>


                                    <p>

                                        <i class="fas fa-user text-success"></i>

                                        <strong>Buyer ID:</strong>

                                        <?= (int)(
                                            $negotiation['sender_id']
                                        ) ?>

                                    </p>


                                    <p>

                                        <i class="fas fa-weight-hanging text-success"></i>

                                        <strong>Quantity:</strong>

                                        <?= htmlspecialchars(
                                            $negotiation['quantity']
                                            ?? 'N/A'
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $negotiation['unit']
                                            ?? ''
                                        ) ?>

                                    </p>


                                    <p>

                                        <i class="fas fa-money-bill-wave text-success"></i>

                                        <strong>Proposed Price:</strong>

                                        <br>

                                        <span class="text-success fw-bold">

                                            UGX

                                            <?= number_format(
                                                (float)(
                                                    $negotiation['price']
                                                    ?? 0
                                                )
                                            ) ?>

                                        </span>

                                    </p>


                                    <p>

                                        <i class="fas fa-truck text-success"></i>

                                        <strong>Delivery:</strong>

                                        <?= htmlspecialchars(
                                            $negotiation[
                                                'delivery_terms'
                                            ] ?? 'Not specified'
                                        ) ?>

                                    </p>


                                    <?php if (!empty($negotiation['message'])): ?>

                                        <div class="alert alert-light border">

                                            <strong>

                                                <i class="fas fa-comment"></i>

                                                Buyer's Message

                                            </strong>

                                            <p class="mb-0 mt-2">

                                                <?= nl2br(
                                                    htmlspecialchars(
                                                        $negotiation['message']
                                                    )
                                                ) ?>

                                            </p>

                                        </div>

                                    <?php endif; ?>


                                    <small class="text-muted">

                                        <i class="fas fa-clock"></i>

                                        <?= htmlspecialchars(
                                            $negotiation['created_at']
                                            ?? ''
                                        ) ?>

                                    </small>


                                    <hr>


                                    <a
                                        href="negotiate.php?offer_id=<?= (int)(
                                            $negotiation['offer_id']
                                        ) ?>"
                                        class="btn btn-success w-100"
                                    >

                                        <i class="fas fa-comments"></i>

                                        View Negotiation

                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="card">

                    <div class="card-body text-center py-5">

                        <i
                            class="fas fa-comments fa-4x text-muted mb-3"
                        ></i>

                        <h4>
                            No Negotiations Yet
                        </h4>

                        <p class="text-muted">

                            Buyer counter-offers will appear here.

                        </p>

                        <a
                            href="offers.php"
                            class="btn btn-success"
                        >

                            <i class="fas fa-file-invoice-dollar"></i>

                            View Offers

                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>