<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| GET BUYER REQUESTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM buyer_requests
    WHERE buyer_id = ?
    AND status = 'open'
    ORDER BY created_at DESC
");

$stmt->execute([$buyer_id]);

$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| GET AVAILABLE FARMER PRODUCE
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM produce
    WHERE availability = 'available'
    ORDER BY created_at DESC
");

$stmt->execute();

$produce = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| CREATE MATCHES
|--------------------------------------------------------------------------
*/

$matches = [];

foreach ($requests as $request) {

    foreach ($produce as $item) {

        $score = 0;

        /*
        ----------------------------------------------------------
        Product Match - 30%
        ----------------------------------------------------------
        */

        if (
            strtolower(trim($request['produce_name'])) ===
            strtolower(trim($item['produce_name']))
        ) {

            $score += 30;
        }


        /*
        ----------------------------------------------------------
        Quantity Match - 20%
        ----------------------------------------------------------
        */

        if (
            isset($request['quantity']) &&
            isset($item['quantity']) &&
            (float)$item['quantity'] >= (float)$request['quantity']
        ) {

            $score += 20;
        }


        /*
        ----------------------------------------------------------
        Location Match - 15%
        ----------------------------------------------------------
        */

        if (
            !empty($request['location']) &&
            !empty($item['location']) &&
            stripos(
                $item['location'],
                $request['location']
            ) !== false
        ) {

            $score += 15;
        }


        /*
        ----------------------------------------------------------
        Price Match - 20%
        ----------------------------------------------------------
        */

        if (
            isset($request['target_price']) &&
            $request['target_price'] !== '' &&
            isset($item['expected_price']) &&
            (float)$item['expected_price'] <=
            (float)$request['target_price']
        ) {

            $score += 20;
        }


        /*
        ----------------------------------------------------------
        Quality Match - 10%
        ----------------------------------------------------------
        */

        if (
            !empty($request['quality_requirement']) &&
            !empty($item['quality_grade']) &&
            strtolower(trim($request['quality_requirement'])) ===
            strtolower(trim($item['quality_grade']))
        ) {

            $score += 10;
        }


        /*
        ----------------------------------------------------------
        Availability - 5%
        ----------------------------------------------------------
        */

        if (
            isset($item['availability']) &&
            $item['availability'] === 'available'
        ) {

            $score += 5;
        }


        /*
        ----------------------------------------------------------
        Only show meaningful matches
        ----------------------------------------------------------
        */

        if ($score >= 30) {

            $matches[] = [

                'request' => $request,

                'produce' => $item,

                'score' => $score

            ];
        }
    }
}


/*
|--------------------------------------------------------------------------
| SORT HIGHEST MATCH FIRST
|--------------------------------------------------------------------------
*/

usort(
    $matches,
    function ($a, $b) {

        return $b['score'] <=> $a['score'];
    }
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Farmer Matches | Agro-Link Uganda</title>

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
            background: #198754;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
        }

        .brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .content {
            padding: 30px;
        }

        .match-card {
            border: none;
            border-radius: 15px;
            transition: 0.2s;
        }

        .match-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .score {
            font-size: 22px;
            font-weight: bold;
            color: #198754;
        }

        .match-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e8f5e9;
            color: #198754;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">


        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 sidebar p-0">

            <div class="brand">
                🌱 AGRO-LINK UGANDA
            </div>

            <div class="py-3">

                <a href="dashboard.php">

                    <i class="fa-solid fa-house me-2"></i>

                    Dashboard

                </a>

                <a href="find_produce.php">

                    <i class="fa-solid fa-magnifying-glass me-2"></i>

                    Find Produce

                </a>

                <a href="my_requests.php">

                    <i class="fa-solid fa-file-lines me-2"></i>

                    My Requests

                </a>

                <a href="matches.php" class="active">

                    <i class="fa-solid fa-link me-2"></i>

                    Farmer Matches

                </a>

                <a href="offers.php">

                    <i class="fa-solid fa-handshake me-2"></i>

                    Offers & Negotiations

                </a>

                <a href="orders.php">

                    <i class="fa-solid fa-cart-shopping me-2"></i>

                    Orders

                </a>

                <a href="deliveries.php">

                    <i class="fa-solid fa-truck me-2"></i>

                    Deliveries

                </a>

                <a href="market_prices.php">

                    <i class="fa-solid fa-chart-line me-2"></i>

                    Market Prices

                </a>

                <a href="ratings.php">

                    <i class="fa-solid fa-star me-2"></i>

                    Ratings

                </a>

                <a href="profile.php">

                    <i class="fa-solid fa-user me-2"></i>

                    Profile

                </a>

                <hr>

                <a href="logout.php">

                    <i class="fa-solid fa-right-from-bracket me-2"></i>

                    Logout

                </a>

            </div>

        </div>


        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 content">


            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="fw-bold mb-1">

                        Farmer Matches

                    </h3>

                    <p class="text-muted mb-0">

                        Farmers whose produce matches your requests.

                    </p>

                </div>

                <a
                    href="dashboard.php"
                    class="btn btn-outline-success"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Dashboard

                </a>

            </div>


            <!-- MATCH INFORMATION -->

            <div class="alert alert-success">

                <i class="fa-solid fa-circle-info me-2"></i>

                <strong>Platform-generated match score:</strong>

                Matches are calculated using product, quantity,
                location, price, quality and availability.

            </div>


            <!-- MATCHES -->

            <?php if (count($matches) > 0): ?>

                <div class="row g-4">

                    <?php foreach ($matches as $match): ?>

                        <?php

                        $item = $match['produce'];
                        $request = $match['request'];
                        $score = $match['score'];

                        ?>

                        <div class="col-md-6 col-xl-4">

                            <div class="card match-card shadow-sm h-100">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <div class="match-icon">

                                            <i class="fa-solid fa-seedling"></i>

                                        </div>

                                        <div class="text-end">

                                            <div class="score">

                                                <?= $score ?>%

                                            </div>

                                            <small class="text-muted">

                                                Match Score

                                            </small>

                                        </div>

                                    </div>


                                    <h5 class="fw-bold">

                                        <?= htmlspecialchars(
                                            $item['produce_name']
                                        ) ?>

                                    </h5>


                                    <p class="mb-2">

                                        <i class="fa-solid fa-box text-success me-2"></i>

                                        <strong>Available:</strong>

                                        <?= htmlspecialchars(
                                            $item['quantity']
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $item['unit']
                                        ) ?>

                                    </p>


                                    <p class="mb-2">

                                        <i class="fa-solid fa-money-bill text-success me-2"></i>

                                        <strong>Expected Price:</strong>

                                        UGX
                                        <?= number_format(
                                            (float)$item['expected_price']
                                        ) ?>

                                    </p>


                                    <p class="mb-2">

                                        <i class="fa-solid fa-location-dot text-success me-2"></i>

                                        <?= htmlspecialchars(
                                            $item['location']
                                        ) ?>

                                    </p>


                                    <?php if (!empty($item['quality_grade'])): ?>

                                        <p class="mb-3">

                                            <i class="fa-solid fa-star text-success me-2"></i>

                                            <strong>Quality:</strong>

                                            <?= htmlspecialchars(
                                                $item['quality_grade']
                                            ) ?>

                                        </p>

                                    <?php endif; ?>


                                    <hr>


                                    <small class="text-muted">

                                        Your request:

                                        <strong>

                                            <?= htmlspecialchars(
                                                $request['produce_name']
                                            ) ?>

                                        </strong>

                                        —
                                        <?= htmlspecialchars(
                                            $request['quantity']
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $request['unit']
                                        ) ?>

                                    </small>


                                    <button
                                        type="button"
                                        class="btn btn-success w-100 mt-3"
                                    >

                                        <i class="fa-solid fa-handshake me-2"></i>

                                        Contact Farmer

                                    </button>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="card border-0 shadow-sm">

                    <div class="card-body text-center py-5">

                        <i class="fa-solid fa-link fs-1 text-muted mb-3"></i>

                        <h5>
                            No farmer matches found
                        </h5>

                        <p class="text-muted">

                            Create an open produce request first,
                            or wait for farmers to list matching produce.

                        </p>

                        <a
                            href="my_requests.php"
                            class="btn btn-success"
                        >

                            <i class="fa-solid fa-plus me-2"></i>

                            Create Request

                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>