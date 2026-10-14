<?php
session_start();

require_once "../config/database.php";

// Temporary buyer ID for testing
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

// Get offers made to this buyer
$stmt = $pdo->prepare("
    SELECT 
        o.*,
        p.produce_name,
        p.unit
    FROM offers o
    LEFT JOIN produce p ON o.produce_id = p.id
    WHERE o.buyer_id = ?
    ORDER BY o.created_at DESC
");

$stmt->execute([$buyer_id]);
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buyer Offers - Agro-Link Uganda</title>

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

        .offer-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .pending {
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

            <a href="find_produce.php">
                <i class="fas fa-search me-2"></i>
                Find Produce
            </a>

            <a href="my_requests.php">
                <i class="fas fa-file-alt me-2"></i>
                My Requests
            </a>

            <a href="matches.php">
                <i class="fas fa-handshake me-2"></i>
                Farmer Matches
            </a>

            <a href="offers.php" class="active">
                <i class="fas fa-comments-dollar me-2"></i>
                Offers & Negotiations
            </a>

            <a href="orders.php">
                <i class="fas fa-shopping-cart me-2"></i>
                Orders
            </a>

            <a href="deliveries.php">
                <i class="fas fa-truck me-2"></i>
                Deliveries
            </a>

            <a href="market_prices.php">
                <i class="fas fa-chart-line me-2"></i>
                Market Prices
            </a>

            <a href="ratings.php">
                <i class="fas fa-star me-2"></i>
                Ratings
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

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2>
                        <i class="fas fa-comments-dollar text-success"></i>
                        Offers & Negotiations
                    </h2>

                    <p class="text-muted">
                        View offers received from farmers for your produce requests.
                    </p>
                </div>

                <a href="my_requests.php" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    My Requests
                </a>

            </div>


            <?php if (count($offers) > 0): ?>

                <div class="row">

                    <?php foreach ($offers as $offer): ?>

                        <div class="col-md-6 col-xl-4 mb-4">

                            <div class="card offer-card h-100">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between mb-3">

                                        <h5 class="card-title">
                                            <?= htmlspecialchars(
                                                $offer['produce_name'] ?? 'Produce'
                                            ) ?>
                                        </h5>

                                        <?php
                                        $status = strtolower(
                                            $offer['status'] ?? 'pending'
                                        );
                                        ?>

                                        <span class="status <?= htmlspecialchars($status) ?>">
                                            <?= htmlspecialchars(
                                                ucfirst($status)
                                            ) ?>
                                        </span>

                                    </div>


                                    <p>
                                        <i class="fas fa-weight-hanging text-success"></i>

                                        <strong>Quantity:</strong>

                                        <?= htmlspecialchars(
                                            $offer['quantity'] ?? 'N/A'
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $offer['unit'] ?? ''
                                        ) ?>
                                    </p>


                                    <p>
                                        <i class="fas fa-money-bill-wave text-success"></i>

                                        <strong>Price:</strong>

                                        UGX
                                        <?= number_format(
                                            (float)($offer['price'] ?? 0)
                                        ) ?>
                                    </p>


                                    <p>
                                        <i class="fas fa-truck text-success"></i>

                                        <strong>Delivery:</strong>

                                        <?= htmlspecialchars(
                                            $offer['delivery_terms'] ?? 'Not specified'
                                        ) ?>
                                    </p>


                                    <?php if (!empty($offer['message'])): ?>

                                        <div class="alert alert-light border">

                                            <strong>
                                                <i class="fas fa-comment"></i>
                                                Farmer Message:
                                            </strong>

                                            <br>

                                            <?= nl2br(
                                                htmlspecialchars($offer['message'])
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                    <div class="d-flex gap-2">

                                        <a
                                            href="negotiate.php?offer_id=<?= (int)$offer['id'] ?>"
                                            class="btn btn-outline-success btn-sm flex-fill"
                                        >
                                            <i class="fas fa-comments"></i>
                                            Negotiate
                                        </a>

                                    </div>

                                </div>

                                <div class="card-footer text-muted small">

                                    Received:
                                    <?= htmlspecialchars(
                                        $offer['created_at'] ?? ''
                                    ) ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="card offer-card">

                    <div class="card-body text-center py-5">

                        <i
                            class="fas fa-handshake fa-4x text-success mb-3"
                        ></i>

                        <h4>No Offers Yet</h4>

                        <p class="text-muted">
                            Farmers' offers for your requests will appear here.
                        </p>

                        <a
                            href="my_requests.php"
                            class="btn btn-success"
                        >
                            <i class="fas fa-file-alt"></i>
                            View My Requests
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>
</div>

</body>
</html>