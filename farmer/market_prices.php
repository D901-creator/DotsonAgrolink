<?php
require_once "../config/database.php";

// Get market prices
$stmt = $pdo->prepare("
    SELECT *
    FROM market_prices
    ORDER BY created_at DESC
");

$stmt->execute();
$prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Market Prices | Agro-Link Uganda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

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
               class="text-white text-decoration-none d-block mb-3 fw-bold">
                <i class="fa-solid fa-chart-line"></i>
                Market Prices
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
                    <i class="fa-solid fa-chart-line text-success"></i>
                    Market Prices
                </h2>

                <p class="text-muted">
                    View agricultural market prices to help you make informed selling decisions.
                </p>
            </div>


            <?php if (count($prices) === 0): ?>

                <div class="card shadow-sm border-0">

                    <div class="card-body text-center py-5">

                        <i class="fa-solid fa-chart-line fa-3x text-muted mb-3"></i>

                        <h4>No Market Prices Available</h4>

                        <p class="text-muted">
                            Market price information will appear here when it is added.
                        </p>

                    </div>

                </div>

            <?php else: ?>

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead class="table-success">

                                    <tr>
                                        <th>#</th>
                                        <th>Produce</th>
                                        <th>Price</th>
                                        <th>Unit</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                    </tr>

                                </thead>

                                <tbody>

                                <?php foreach ($prices as $price): ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($price['id'] ?? '') ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $price['produce_name'] ?? 'N/A'
                                                ) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <span class="badge bg-success">
                                                UGX
                                                <?= number_format(
                                                    (float)($price['price'] ?? 0)
                                                ) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $price['unit'] ?? 'N/A'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $price['location'] ?? 'N/A'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $price['created_at'] ?? ''
                                            ) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>