<?php
require_once "../config/database.php";

$farmer_id = 1;

// Get farmer's orders
$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE farmer_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$farmer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delivery | Agro-Link Uganda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-light">

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-success text-white min-vh-100 p-3">

            <h4 class="mb-4">
                <i class="fa-solid fa-seedling"></i>
                Agro-Link
            </h4>

            <hr>

            <a href="dashboard.php" class="text-white text-decoration-none d-block mb-3">
                <i class="fa-solid fa-house"></i>
                Dashboard
            </a>

            <a href="my_produce.php" class="text-white text-decoration-none d-block mb-3">
                <i class="fa-solid fa-wheat-awn"></i>
                My Produce
            </a>

            <a href="buyer_requests.php" class="text-white text-decoration-none d-block mb-3">
                <i class="fa-solid fa-list"></i>
                Buyer Requests
            </a>

            <a href="matches.php" class="text-white text-decoration-none d-block mb-3">
                <i class="fa-solid fa-link"></i>
                Matches
            </a>

            <a href="offers.php" class="text-white text-decoration-none d-block mb-3">
                <i class="fa-solid fa-handshake"></i>
                Offers
            </a>

            <a href="orders.php" class="text-white text-decoration-none d-block mb-3">
                <i class="fa-solid fa-cart-shopping"></i>
                Orders
            </a>

            <a href="delivery.php" class="text-white text-decoration-none d-block mb-3 fw-bold">
                <i class="fa-solid fa-truck"></i>
                Delivery
            </a>

            <hr>

            <a href="../index.php" class="text-white text-decoration-none d-block">
                <i class="fa-solid fa-right-from-bracket"></i>
                Back to Home
            </a>

        </div>


        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h2>
                        <i class="fa-solid fa-truck text-success"></i>
                        Delivery
                    </h2>

                    <p class="text-muted mb-0">
                        Track delivery information for your orders.
                    </p>
                </div>

            </div>


            <?php if (count($orders) === 0): ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">

                        <i class="fa-solid fa-truck fa-3x text-muted mb-3"></i>

                        <h4>No Orders Available</h4>

                        <p class="text-muted">
                            Delivery information will appear here when you have orders.
                        </p>

                        <a href="orders.php" class="btn btn-success">
                            <i class="fa-solid fa-cart-shopping"></i>
                            View Orders
                        </a>

                    </div>
                </div>

            <?php else: ?>

                <div class="row g-4">

                    <?php foreach ($orders as $order): ?>

                        <div class="col-md-6 col-xl-4">

                            <div class="card shadow-sm border-0 h-100">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start">

                                        <h5 class="card-title">
                                            Order #<?= htmlspecialchars($order['id']) ?>
                                        </h5>

                                        <?php
                                        $status = strtolower($order['status'] ?? 'pending');

                                        $badge = 'secondary';

                                        if ($status === 'pending') {
                                            $badge = 'warning';
                                        } elseif ($status === 'confirmed') {
                                            $badge = 'primary';
                                        } elseif ($status === 'processing') {
                                            $badge = 'info';
                                        } elseif ($status === 'shipped') {
                                            $badge = 'dark';
                                        } elseif ($status === 'delivered') {
                                            $badge = 'success';
                                        } elseif ($status === 'cancelled') {
                                            $badge = 'danger';
                                        }
                                        ?>

                                        <span class="badge bg-<?= $badge ?>">
                                            <?= htmlspecialchars(ucfirst($status)) ?>
                                        </span>

                                    </div>

                                    <hr>

                                    <p class="mb-2">
                                        <strong>Buyer ID:</strong>
                                        <?= htmlspecialchars($order['buyer_id'] ?? 'N/A') ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Quantity:</strong>
                                        <?= htmlspecialchars($order['quantity'] ?? 'N/A') ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Price:</strong>
                                        UGX <?= number_format((float)($order['price'] ?? 0)) ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>Delivery Terms:</strong><br>

                                        <?= htmlspecialchars(
                                            $order['delivery_terms'] ?? 'Not specified'
                                        ) ?>
                                    </p>

                                    <p class="text-muted small mb-0">
                                        <i class="fa-regular fa-calendar"></i>

                                        <?= htmlspecialchars(
                                            $order['created_at'] ?? ''
                                        ) ?>
                                    </p>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>