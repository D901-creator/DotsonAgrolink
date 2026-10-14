<?php

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| TEMPORARY FARMER ID
|--------------------------------------------------------------------------
*/

$farmer_id = 1;


/*
|--------------------------------------------------------------------------
| GET FARMER ORDERS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.produce_name,
        p.unit
    FROM orders o
    LEFT JOIN produce p
        ON o.produce_id = p.id
    WHERE o.farmer_id = ?
    ORDER BY o.created_at DESC
");

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE farmer_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$farmer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$orders = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Orders | Agro-Link Uganda</title>

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
            background: #f5f7f6;
        }

        .page-container {
            max-width: 1200px;
            margin: 35px auto;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .order-card {
            transition: 0.2s;
        }

        .order-card:hover {
            transform: translateY(-3px);
        }

        .order-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8f5e9;
            color: #198754;
            font-size: 22px;
        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-dark bg-success">

    <div class="container">

        <a
            href="dashboard.php"
            class="navbar-brand fw-bold"
        >

            🌱 AGRO-LINK UGANDA

        </a>

        <span class="text-white">

            <i class="fa-solid fa-user"></i>

            Farmer

        </span>

    </div>

</nav>


<div class="container page-container">


    <!-- HEADER -->

    <div
        class="
        d-flex
        justify-content-between
        align-items-center
        mb-4
        "
    >

        <div>

            <h2 class="fw-bold">

                <i class="fa-solid fa-cart-shopping text-success"></i>

                My Orders

            </h2>

            <p class="text-muted mb-0">

                View orders created from your accepted offers.

            </p>

        </div>


        <a
            href="offers.php"
            class="btn btn-outline-success"
        >

            <i class="fa-solid fa-handshake"></i>

            My Offers

        </a>

    </div>


    <!-- ORDERS -->

    <?php if (count($orders) > 0): ?>

        <div class="row g-4">


            <?php foreach ($orders as $order): ?>

                <?php

                $status = strtolower(
                    $order['status'] ?? 'pending'
                );

                ?>


                <div class="col-md-6 col-xl-4">

                    <div class="card order-card h-100">

                        <div class="card-body">


                            <!-- HEADER -->

                            <div
                                class="
                                d-flex
                                justify-content-between
                                align-items-center
                                mb-3
                                "
                            >

                                <div
                                    class="order-icon"
                                >

                                    <i
                                        class="fa-solid fa-box"
                                    ></i>

                                </div>


                                <?php if (
                                    $status === 'completed'
                                ): ?>

                                    <span
                                        class="badge bg-success"
                                    >

                                        Completed

                                    </span>


                                <?php elseif (
                                    $status === 'delivered'
                                ): ?>

                                    <span
                                        class="badge bg-success"
                                    >

                                        Delivered

                                    </span>


                                <?php elseif (
                                    $status === 'cancelled'
                                ): ?>

                                    <span
                                        class="badge bg-danger"
                                    >

                                        Cancelled

                                    </span>


                                <?php elseif (
                                    $status === 'in_transit'
                                ): ?>

                                    <span
                                        class="badge bg-primary"
                                    >

                                        In Transit

                                    </span>


                                <?php elseif (
                                    $status === 'processing'
                                ): ?>

                                    <span
                                        class="badge bg-info text-dark"
                                    >

                                        Processing

                                    </span>


                                <?php else: ?>

                                    <span
                                        class="
                                        badge
                                        bg-warning
                                        text-dark
                                        "
                                    >

                                        <?= ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $status
                                            )
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </div>


                            <!-- ORDER NUMBER -->

                            <h5 class="fw-bold">

                                Order #

                                <?= htmlspecialchars(
                                    $order['id']
                                ) ?>

                            </h5>


                            <!-- PRODUCE -->

                            <p class="mb-2">

                                <strong>Produce:</strong>

                                <?= htmlspecialchars(
                                    $order['produce_name']
                                    ?? 'Produce'
                                ) ?>

                            </p>


                            <!-- BUYER -->

                            <p class="mb-2">

                                <strong>Buyer:</strong>

                                Buyer #

                                <?= htmlspecialchars(
                                    $order['buyer_id']
                                ) ?>

                            </p>


                            <!-- QUANTITY -->

                            <p class="mb-2">

                                <strong>Quantity:</strong>

                                <?= htmlspecialchars(
                                    $order['quantity']
                                ) ?>

                                <?= htmlspecialchars(
                                    $order['unit'] ?? ''
                                ) ?>

                            </p>


                            <!-- PRICE -->

                            <p class="mb-2">

                                <strong>Price:</strong>

                                <span
                                    class="text-success fw-bold"
                                >

                                    UGX

                                    <?= number_format(
                                        (float)$order['price']
                                    ) ?>

                                </span>

                            </p>


                            <!-- DELIVERY -->

                            <p class="mb-2">

                                <strong>Delivery:</strong><br>

                                <?= htmlspecialchars(
                                    $order['delivery_terms']
                                    ?? 'Not specified'
                                ) ?>

                            </p>


                            <!-- MESSAGE -->

                            <?php if (
                                !empty($order['message'])
                            ): ?>

                                <p class="text-muted small">

                                    <?= htmlspecialchars(
                                        $order['message']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <hr>


                            <!-- DATE -->

                            <p class="text-muted small mb-0">

                                <i
                                    class="fa-regular fa-clock"
                                ></i>

                                Created:

                                <?= htmlspecialchars(
                                    $order['created_at']
                                ) ?>

                            </p>


                        </div>

                    </div>

                </div>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <!-- NO ORDERS -->

        <div class="card">

            <div class="card-body text-center py-5">

                <i
                    class="
                    fa-solid
                    fa-cart-shopping
                    text-success
                    "
                    style="font-size: 60px;"
                ></i>


                <h4 class="mt-3">

                    No Orders Yet

                </h4>


                <p class="text-muted">

                    Orders will appear here after an offer has
                    been accepted.

                </p>


                <a
                    href="offers.php"
                    class="btn btn-success"
                >

                    <i class="fa-solid fa-handshake"></i>

                    View My Offers

                </a>

            </div>

        </div>

    <?php endif; ?>


</div>


</body>

</html>