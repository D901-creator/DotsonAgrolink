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


// Calculate total sales
$total_sales = 0;
$completed_sales = 0;

foreach ($orders as $order) {

    $status = strtolower($order['status'] ?? '');

    if ($status === 'completed' || $status === 'delivered') {

        $quantity = (float)($order['quantity'] ?? 0);
        $price = (float)($order['price'] ?? 0);

        $total_sales += ($quantity * $price);
        $completed_sales++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Sales History | Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
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

            <a href="dashboard.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-house"></i>
                Dashboard

            </a>

            <a href="my_produce.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-wheat-awn"></i>
                My Produce

            </a>

            <a href="buyer_requests.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-list"></i>
                Buyer Requests

            </a>

            <a href="matches.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-link"></i>
                Matches

            </a>

            <a href="offers.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-handshake"></i>
                Offers

            </a>

            <a href="orders.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-cart-shopping"></i>
                Orders

            </a>

            <a href="delivery.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-truck"></i>
                Delivery

            </a>

            <a href="market_prices.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-chart-line"></i>
                Market Prices

            </a>

            <a href="sales_history.php"
               class="text-white text-decoration-none d-block mb-3 fw-bold">

                <i class="fa-solid fa-chart-column"></i>
                Sales History

            </a>

            <hr>

            <a href="../index.php"
               class="text-white text-decoration-none d-block">

                <i class="fa-solid fa-right-from-bracket"></i>
                Back to Home

            </a>

        </div>


        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">

                <h2>
                    <i class="fa-solid fa-chart-column text-success"></i>
                    Sales History
                </h2>

                <p class="text-muted">
                    View your completed sales and sales records.
                </p>

            </div>


            <!-- SUMMARY CARDS -->

            <div class="row g-4 mb-4">

                <div class="col-md-6">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">
                                        Completed Sales
                                    </h6>

                                    <h2>
                                        <?= $completed_sales ?>
                                    </h2>

                                </div>

                                <div class="text-success fs-1">

                                    <i class="fa-solid fa-cart-check"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h6 class="text-muted">
                                        Total Sales Value
                                    </h6>

                                    <h2 class="text-success">

                                        UGX
                                        <?= number_format($total_sales) ?>

                                    </h2>

                                </div>

                                <div class="text-success fs-1">

                                    <i class="fa-solid fa-money-bill-trend-up"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- SALES TABLE -->

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h5 class="mb-3">

                        <i class="fa-solid fa-clock-rotate-left"></i>

                        Sales Records

                    </h5>


                    <?php if (count($orders) === 0): ?>

                        <div class="text-center py-5">

                            <i class="fa-solid fa-chart-column fa-3x text-muted mb-3"></i>

                            <h4>No Sales Records</h4>

                            <p class="text-muted">

                                Your sales history will appear here after you complete orders.

                            </p>

                            <a href="orders.php"
                               class="btn btn-success">

                                <i class="fa-solid fa-cart-shopping"></i>

                                View Orders

                            </a>

                        </div>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead class="table-success">

                                    <tr>

                                        <th>Order ID</th>

                                        <th>Buyer</th>

                                        <th>Quantity</th>

                                        <th>Price</th>

                                        <th>Total</th>

                                        <th>Status</th>

                                        <th>Date</th>

                                    </tr>

                                </thead>


                                <tbody>

                                <?php foreach ($orders as $order): ?>

                                    <?php

                                    $quantity = (float)($order['quantity'] ?? 0);

                                    $price = (float)($order['price'] ?? 0);

                                    $total = $quantity * $price;

                                    $status = strtolower(
                                        $order['status'] ?? 'pending'
                                    );

                                    $badge = 'secondary';

                                    if ($status === 'completed') {
                                        $badge = 'success';
                                    } elseif ($status === 'delivered') {
                                        $badge = 'success';
                                    } elseif ($status === 'pending') {
                                        $badge = 'warning';
                                    } elseif ($status === 'cancelled') {
                                        $badge = 'danger';
                                    } elseif ($status === 'processing') {
                                        $badge = 'info';
                                    }

                                    ?>

                                    <tr>

                                        <td>

                                            <strong>
                                                #<?= htmlspecialchars($order['id']) ?>
                                            </strong>

                                        </td>

                                        <td>

                                            Buyer #<?= htmlspecialchars(
                                                $order['buyer_id'] ?? 'N/A'
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $order['quantity'] ?? '0'
                                            ) ?>

                                        </td>

                                        <td>

                                            UGX
                                            <?= number_format($price) ?>

                                        </td>

                                        <td>

                                            <strong>
                                                UGX
                                                <?= number_format($total) ?>
                                            </strong>

                                        </td>

                                        <td>

                                            <span class="badge bg-<?= $badge ?>">

                                                <?= htmlspecialchars(
                                                    ucfirst($status)
                                                ) ?>

                                            </span>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $order['created_at'] ?? ''
                                            ) ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>