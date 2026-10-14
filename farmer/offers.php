<?php

require_once "../config/database.php";

$farmer_id = 1;


/*
|--------------------------------------------------------------------------
| GET FARMER OFFERS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.produce_name,
        p.unit
    FROM offers o
    LEFT JOIN produce p
        ON o.produce_id = p.id
    WHERE o.farmer_id = ?
    ORDER BY o.created_at DESC
");

$stmt->execute([
    $farmer_id
]);

$offers = $stmt->fetchAll();

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
        Offers | Agro-Link Uganda
    </title>


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

        .offer-card {
            transition: 0.2s;
        }

        .offer-card:hover {
            transform: translateY(-3px);
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


    <!-- SUCCESS -->

    <?php if (isset($_GET['success'])): ?>

        <?php if ($_GET['success'] === 'created'): ?>

            <div class="alert alert-success">

                <i class="fa-solid fa-circle-check"></i>

                <strong>Offer sent successfully!</strong>

                Your offer has been submitted to the buyer.

            </div>

        <?php endif; ?>

    <?php endif; ?>


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

                <i
                    class="fa-solid fa-handshake text-success"
                ></i>

                My Offers

            </h2>

            <p class="text-muted">

                View and manage offers you have sent to buyers.

            </p>

        </div>


        <a
            href="matches.php"
            class="btn btn-success"
        >

            <i class="fa-solid fa-link"></i>

            Find Matches

        </a>

    </div>


    <!-- OFFERS -->

    <?php if (count($offers) > 0): ?>

        <div class="row g-4">


            <?php foreach ($offers as $offer): ?>

                <?php

                $status =
                    strtolower(
                        $offer['status'] ?? 'pending'
                    );

                ?>


                <div class="col-md-6 col-xl-4">

                    <div
                        class="
                        card
                        offer-card
                        h-100
                        "
                    >

                        <div class="card-body">


                            <!-- TOP -->

                            <div
                                class="
                                d-flex
                                justify-content-between
                                align-items-center
                                mb-3
                                "
                            >

                                <h5 class="fw-bold mb-0">

                                    <?= htmlspecialchars(
                                        $offer['produce_name']
                                        ?? 'Produce'
                                    ) ?>

                                </h5>


                                <?php if (
                                    $status === 'accepted'
                                ): ?>

                                    <span
                                        class="badge bg-success"
                                    >

                                        Accepted

                                    </span>


                                <?php elseif (
                                    $status === 'rejected'
                                ): ?>

                                    <span
                                        class="badge bg-danger"
                                    >

                                        Rejected

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
                                            $status
                                        ) ?>

                                    </span>

                                <?php endif; ?>


                            </div>


                            <!-- DETAILS -->

                            <p class="mb-2">

                                <strong>Buyer:</strong>

                                Buyer #

                                <?= htmlspecialchars(
                                    $offer['buyer_id']
                                ) ?>

                            </p>


                            <p class="mb-2">

                                <strong>Quantity:</strong>

                                <?= htmlspecialchars(
                                    $offer['quantity']
                                ) ?>

                                <?= htmlspecialchars(
                                    $offer['unit'] ?? ''
                                ) ?>

                            </p>


                            <p class="mb-2">

                                <strong>Price:</strong>

                                <span
                                    class="
                                    text-success
                                    fw-bold
                                    "
                                >

                                    UGX

                                    <?= number_format(
                                        (float)$offer['price']
                                    ) ?>

                                </span>

                            </p>


                            <p class="mb-2">

                                <strong>Delivery:</strong><br>

                                <?= htmlspecialchars(
                                    $offer['delivery_terms']
                                ) ?>

                            </p>


                            <p class="text-muted small">

                                <?= htmlspecialchars(
                                    $offer['message']
                                ) ?>

                            </p>


                            <hr>


                            <!-- ACTION -->

                            <?php if (
                                $status !== 'rejected'
                            ): ?>

                                <a
                                    href="negotiate.php?offer_id=<?= urlencode($offer['id']) ?>"
                                    class="
                                    btn
                                    btn-outline-success
                                    w-100
                                    "
                                >

                                    <i
                                        class="fa-solid fa-comments"
                                    ></i>

                                    View / Negotiate

                                </a>

                               

                            <?php endif; ?>


                        </div>

                    </div>

                </div>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <div class="card">

            <div class="card-body text-center py-5">

                <i
                    class="
                    fa-solid
                    fa-handshake
                    text-success
                    "
                    style="font-size: 60px;"
                ></i>


                <h4 class="mt-3">

                    No Offers Yet

                </h4>


                <p class="text-muted">

                    You have not sent any offers to buyers yet.

                </p>


                <a
                    href="matches.php"
                    class="btn btn-success"
                >

                    <i class="fa-solid fa-link"></i>

                    View Matches

                </a>

            </div>

        </div>


    <?php endif; ?>


</div>


</body>

</html>