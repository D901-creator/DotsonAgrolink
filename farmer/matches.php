<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| REQUIRE LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| GET CURRENT FARMER
|--------------------------------------------------------------------------
*/

$farmer_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| VERIFY THAT THE LOGGED-IN USER IS A FARMER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, username, fullname, role, profile_picture
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([
    $farmer_id
]);

$current_farmer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current_farmer) {
    session_destroy();

    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK ROLE
|--------------------------------------------------------------------------
*/

if (strtolower(trim($current_farmer['role'])) !== 'farmer') {
    header("Location: ../index.php");
    exit;
}
/*
|--------------------------------------------------------------------------
| GET FARMER PRODUCE
|--------------------------------------------------------------------------
*/

$produce_stmt = $pdo->prepare("
    SELECT *
    FROM produce
    WHERE farmer_id = ?
    AND availability = 'available'
    ORDER BY created_at DESC
");

$produce_stmt->execute([
    $farmer_id
]);

$farmer_produce = $produce_stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| GET OPEN BUYER REQUESTS
|--------------------------------------------------------------------------
*/

$request_stmt = $pdo->prepare("
    SELECT *
    FROM buyer_requests
    WHERE status = 'open'
    ORDER BY created_at DESC
");

$request_stmt->execute();

$buyer_requests = $request_stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| CALCULATE MATCHES
|--------------------------------------------------------------------------
*/

$matches = [];

foreach ($farmer_produce as $produce) {

    foreach ($buyer_requests as $request) {

        $score = 0;


        /*
        |--------------------------------------------------------------------------
        | PRODUCT MATCH - 30%
        |--------------------------------------------------------------------------
        */

        if (
            isset($produce['produce_name']) &&
            isset($request['produce_name']) &&
            strtolower(trim($produce['produce_name'])) ===
            strtolower(trim($request['produce_name']))
        ) {

            $score += 30;

        }


        /*
        |--------------------------------------------------------------------------
        | QUANTITY MATCH - 20%
        |--------------------------------------------------------------------------
        */

        if (
            isset($produce['quantity']) &&
            isset($request['quantity']) &&
            (float)$produce['quantity'] >=
            (float)$request['quantity']
        ) {

            $score += 20;

        }


        /*
        |--------------------------------------------------------------------------
        | LOCATION MATCH - 15%
        |--------------------------------------------------------------------------
        */

        if (
            !empty($produce['location']) &&
            !empty($request['location']) &&
            strtolower(trim($produce['location'])) ===
            strtolower(trim($request['location']))
        ) {

            $score += 15;

        }


        /*
        |--------------------------------------------------------------------------
        | PRICE MATCH - 20%
        |--------------------------------------------------------------------------
        */

        if (
            isset($request['target_price']) &&
            isset($produce['price_per_unit']) &&
            (float)$produce['price_per_unit'] <=
            (float)$request['target_price']
        ) {

            $score += 20;

        }


        /*
        |--------------------------------------------------------------------------
        | AVAILABILITY - 5%
        |--------------------------------------------------------------------------
        */

        if (
            isset($produce['availability']) &&
            $produce['availability'] === 'available'
        ) {

            $score += 5;

        }


        /*
        |--------------------------------------------------------------------------
        | ONLY SHOW MATCHES
        |--------------------------------------------------------------------------
        */

        if ($score >= 30) {

            $matches[] = [

                'produce' => $produce,

                'request' => $request,

                'produce_id' => $produce['id'],

                'request_id' => $request['id'],

                'score' => $score

            ];

        }

    }

}


/*
|--------------------------------------------------------------------------
| SORT MATCHES
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

    <title>
        Matches | Agro-Link Uganda
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >


    <style>

        body {
            background: #f5f7f6;
        }

        .sidebar {
            min-height: 100vh;
            background: #212529;
        }

        .sidebar-title {
            color: #adb5bd;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
        }

        .sidebar .nav-link {
            color: white;
            border-radius: 8px;
            padding: 11px 14px;
        }

        .sidebar .nav-link:hover {
            background: #343a40;
        }

        .sidebar .nav-link.active {
            background: #0d6efd;
        }

        .match-card {
            border: none;
            border-radius: 15px;
            transition: 0.2s;
        }

        .match-card:hover {
            transform: translateY(-3px);
        }

        .match-score {
            font-size: 17px;
        }

        .produce-box {
            background: #eef8f2;
            border-radius: 10px;
            padding: 15px;
        }

        .request-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-dark bg-success">

    <div class="container-fluid">

        <a
            href="dashboard.php"
            class="navbar-brand fw-bold"
        >

            🌱 Agro-Link Uganda

        </a>


        <div class="text-white">

            <i class="fa-solid fa-bell me-3"></i>

            <i class="fa-solid fa-user-circle"></i>

            John Doe

        </div>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">


        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 sidebar text-white p-3">


            <div class="sidebar-title mb-3">

                Farmer Menu

            </div>


            <hr>


            <ul class="nav nav-pills flex-column gap-2">


                <li class="nav-item">

                    <a
                        href="dashboard.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-house me-2"></i>

                        Dashboard

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="my_produce.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-seedling me-2"></i>

                        My Produce

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="buyer_requests.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-bullhorn me-2"></i>

                        Buyer Requests

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="matches.php"
                        class="nav-link active"
                    >

                        <i class="fa-solid fa-link me-2"></i>

                        Matches

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="offers.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-handshake me-2"></i>

                        Offers

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="orders.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-cart-shopping me-2"></i>

                        Orders

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="delivery.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-truck me-2"></i>

                        Delivery

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="market_prices.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-chart-line me-2"></i>

                        Market Prices

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="sales_history.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-money-bill me-2"></i>

                        Sales History

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="notifications.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-bell me-2"></i>

                        Notifications

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="profile.php"
                        class="nav-link"
                    >

                        <i class="fa-solid fa-user me-2"></i>

                        Profile

                    </a>

                </li>


                <li class="nav-item mt-3">

                    <a
                        href="../index.php"
                        class="nav-link text-danger"
                    >

                        <i
                            class="fa-solid fa-right-from-bracket me-2"
                        ></i>

                        Logout

                    </a>

                </li>


            </ul>

        </div>


        <!-- MAIN CONTENT -->

        <main class="col-md-9 col-lg-10 p-4">


            <!-- HEADER -->

            <div class="mb-4">

                <h2 class="fw-bold">

                    <i
                        class="fa-solid fa-link text-success"
                    ></i>

                    Farmer Matches

                </h2>


                <p class="text-muted">

                    Buyer requests that match your available produce.

                </p>

            </div>


            <!-- MATCH RULES -->

            <div class="alert alert-success">

                <i
                    class="fa-solid fa-circle-info me-2"
                ></i>

                Matches are generated using product, quantity,
                location, price and availability.

            </div>


            <!-- NO PRODUCE -->

            <?php if (count($farmer_produce) === 0): ?>

                <div class="card border-0 shadow-sm">

                    <div class="card-body text-center py-5">

                        <i
                            class="fa-solid fa-seedling text-success"
                            style="font-size: 55px;"
                        ></i>


                        <h4 class="mt-3">

                            No Available Produce

                        </h4>


                        <p class="text-muted">

                            You currently have no available produce
                            to match with buyer requests.

                        </p>


                        <a
                            href="my_produce.php"
                            class="btn btn-success"
                        >

                            <i class="fa-solid fa-plus"></i>

                            Add Produce

                        </a>

                    </div>

                </div>


            <!-- NO BUYER REQUESTS -->

            <?php elseif (count($buyer_requests) === 0): ?>

                <div class="card border-0 shadow-sm">

                    <div class="card-body text-center py-5">

                        <i
                            class="fa-solid fa-bullhorn text-success"
                            style="font-size: 55px;"
                        ></i>


                        <h4 class="mt-3">

                            No Open Buyer Requests

                        </h4>


                        <p class="text-muted">

                            There are currently no open buyer
                            requests to match with your produce.

                        </p>


                        <a
                            href="buyer_requests.php"
                            class="btn btn-success"
                        >

                            <i class="fa-solid fa-eye"></i>

                            View Buyer Requests

                        </a>

                    </div>

                </div>


            <!-- MATCHES -->

            <?php elseif (count($matches) > 0): ?>

                <div class="row g-4">


                    <?php foreach ($matches as $match): ?>

                        <?php

                        $produce = $match['produce'];

                        $request = $match['request'];

                        $score = $match['score'];

                        ?>


                        <div class="col-md-6 col-xl-4">


                            <div
                                class="card match-card shadow-sm h-100"
                            >

                                <div class="card-body">


                                    <!-- SCORE -->

                                    <div
                                        class="
                                        d-flex
                                        justify-content-between
                                        align-items-center
                                        mb-3
                                        "
                                    >

                                        <span
                                            class="
                                            badge
                                            bg-success
                                            match-score
                                            "
                                        >

                                            <?= $score ?>% Match

                                        </span>


                                        <i
                                            class="
                                            fa-solid
                                            fa-link
                                            text-success
                                            fs-4
                                            "
                                        ></i>

                                    </div>


                                    <!-- PRODUCE -->

                                    <div class="produce-box mb-3">

                                        <h5 class="fw-bold">

                                            <i
                                                class="
                                                fa-solid
                                                fa-seedling
                                                text-success
                                                me-2
                                                "
                                            ></i>

                                            <?= htmlspecialchars(
                                                $produce['produce_name']
                                            ) ?>

                                        </h5>


                                        <p class="mb-1">

                                            <strong>
                                                Your Quantity:
                                            </strong>

                                            <?= htmlspecialchars(
                                                $produce['quantity']
                                            ) ?>

                                            <?= htmlspecialchars(
                                                $produce['unit']
                                            ) ?>

                                        </p>


                                        <p class="mb-1">

                                            <strong>
                                                Your Price:
                                            </strong>

                                            UGX

                                            <?= number_format(
                                                (float)
                                                $produce[
                                                    'price_per_unit'
                                                ]
                                            ) ?>

                                            /

                                            <?= htmlspecialchars(
                                                $produce['unit']
                                            ) ?>

                                        </p>


                                        <p class="mb-0">

                                            <strong>
                                                Your Location:
                                            </strong>

                                            <?= htmlspecialchars(
                                                $produce['location']
                                            ) ?>

                                        </p>

                                    </div>


                                    <!-- BUYER REQUEST -->

                                    <div class="request-box">

                                        <h6 class="fw-bold">

                                            Buyer Request

                                        </h6>


                                        <p class="mb-1">

                                            <strong>
                                                Quantity Needed:
                                            </strong>

                                            <?= htmlspecialchars(
                                                $request['quantity']
                                            ) ?>

                                            <?= htmlspecialchars(
                                                $request['unit']
                                            ) ?>

                                        </p>


                                        <p class="mb-1">

                                            <strong>
                                                Target Price:
                                            </strong>

                                            UGX

                                            <?= number_format(
                                                (float)
                                                $request[
                                                    'target_price'
                                                ]
                                            ) ?>

                                        </p>


                                        <p class="mb-0">

                                            <strong>
                                                Location:
                                            </strong>

                                            <?= htmlspecialchars(
                                                $request['location']
                                            ) ?>

                                        </p>

                                    </div>


                                    <hr>


                                    <!-- RESPOND -->

                                    <a
                                        href="create_offer.php?request_id=<?= urlencode($match['request_id']) ?>&produce_id=<?= urlencode($match['produce_id']) ?>"
                                        class="btn btn-success w-100"
                                    >

                                        <i
                                            class="
                                            fa-solid
                                            fa-paper-plane
                                            "
                                        ></i>

                                        Respond to Buyer

                                    </a>


                                </div>

                            </div>

                        </div>


                    <?php endforeach; ?>


                </div>


            <!-- NO MATCH -->

            <?php else: ?>

                <div class="card border-0 shadow-sm">

                    <div class="card-body text-center py-5">

                        <i
                            class="
                            fa-solid
                            fa-link
                            text-success
                            "
                            style="font-size: 55px;"
                        ></i>


                        <h4 class="mt-3">

                            No Matches Found

                        </h4>


                        <p class="text-muted">

                            Your available produce does not currently
                            meet the matching conditions for the open
                            buyer requests.

                        </p>


                        <a
                            href="my_produce.php"
                            class="btn btn-success"
                        >

                            <i class="fa-solid fa-seedling"></i>

                            Manage My Produce

                        </a>

                    </div>

                </div>

            <?php endif; ?>


        </main>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>