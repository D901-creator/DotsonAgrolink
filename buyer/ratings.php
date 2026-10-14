<?php

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get ratings made by this buyer
|--------------------------------------------------------------------------
|
| We use SELECT * to avoid assuming columns that may not exist.
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM ratings
    WHERE buyer_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$buyer_id]);

$ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ratings - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h4 class="text-center mb-4">
                🌱 Agro-Link
            </h4>

            <hr>

            <a href="dashboard.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-home"></i>
                Dashboard
            </a>

            <a href="find_produce.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-search"></i>
                Find Produce
            </a>

            <a href="my_requests.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-list"></i>
                My Requests
            </a>

            <a href="matches.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-handshake"></i>
                Farmer Matches
            </a>

            <a href="offers.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-tags"></i>
                Offers & Negotiations
            </a>

            <a href="orders.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-shopping-cart"></i>
                My Orders
            </a>

            <a href="deliveries.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-truck"></i>
                Deliveries
            </a>

            <a href="market_prices.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-chart-line"></i>
                Market Prices
            </a>

            <a href="ratings.php"
               class="btn btn-success w-100 mb-2 text-start">
                <i class="fas fa-star"></i>
                Ratings
            </a>

            <a href="profile.php"
               class="btn btn-outline-light w-100 mb-2 text-start">
                <i class="fas fa-user"></i>
                Profile
            </a>

            <a href="logout.php"
               class="btn btn-danger w-100 mb-2 text-start">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>

        </div>


        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">

                <h2>
                    <i class="fas fa-star text-warning"></i>
                    My Ratings
                </h2>

                <p class="text-muted">
                    View ratings and feedback you have submitted to farmers.
                </p>

            </div>


            <?php if (empty($ratings)): ?>

                <div class="alert alert-info">

                    <i class="fas fa-info-circle"></i>

                    You have not submitted any ratings yet.

                </div>

            <?php else: ?>

                <div class="row">

                    <?php foreach ($ratings as $rating): ?>

                        <div class="col-md-6 col-xl-4 mb-4">

                            <div class="card shadow-sm h-100">

                                <div class="card-header bg-success text-white">

                                    <i class="fas fa-star"></i>

                                    Rating #<?= htmlspecialchars($rating['id']) ?>

                                </div>


                                <div class="card-body">

                                    <?php if (isset($rating['rating'])): ?>

                                        <h4 class="text-warning">

                                            <?php

                                            $stars = (int)$rating['rating'];

                                            for ($i = 1; $i <= 5; $i++):

                                            ?>

                                                <?php if ($i <= $stars): ?>

                                                    <i class="fas fa-star"></i>

                                                <?php else: ?>

                                                    <i class="far fa-star"></i>

                                                <?php endif; ?>

                                            <?php endfor; ?>

                                        </h4>

                                        <p>
                                            <strong>
                                                Rating:
                                            </strong>

                                            <?= htmlspecialchars($rating['rating']) ?>/5
                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($rating['comment'])): ?>

                                        <p>

                                            <strong>
                                                Comment:
                                            </strong>

                                            <?= htmlspecialchars($rating['comment']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($rating['farmer_id'])): ?>

                                        <p>

                                            <strong>
                                                Farmer ID:
                                            </strong>

                                            <?= htmlspecialchars($rating['farmer_id']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($rating['order_id'])): ?>

                                        <p>

                                            <strong>
                                                Order ID:
                                            </strong>

                                            <?= htmlspecialchars($rating['order_id']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($rating['created_at'])): ?>

                                        <p class="text-muted">

                                            <i class="fas fa-calendar"></i>

                                            <?= htmlspecialchars($rating['created_at']) ?>

                                        </p>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

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