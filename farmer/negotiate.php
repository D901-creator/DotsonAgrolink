<?php

require_once "../config/database.php";

// Temporary demo farmer
$farmer_id = 1;

// Get offer ID
$offer_id = isset($_GET['offer_id'])
    ? (int) $_GET['offer_id']
    : 0;

if ($offer_id <= 0) {
    die("Invalid offer.");
}


/*
|--------------------------------------------------------------------------
| GET OFFER
|--------------------------------------------------------------------------
*/

$offer_stmt = $pdo->prepare("
    SELECT
        o.*,
        p.produce_name,
        p.unit
    FROM offers o
    LEFT JOIN produce p
        ON o.produce_id = p.id
    WHERE o.id = ?
    AND o.farmer_id = ?
");

$offer_stmt->execute([
    $offer_id,
    $farmer_id
]);

$offer = $offer_stmt->fetch();

if (!$offer) {
    die("Offer not found or you do not have permission to view it.");
}


/*
|--------------------------------------------------------------------------
| SEND NEGOTIATION MESSAGE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantity = trim($_POST['quantity'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $delivery_terms = trim($_POST['delivery_terms'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (
        $quantity === '' ||
        $price === '' ||
        $delivery_terms === '' ||
        $message === ''
    ) {

        $error = "Please fill in all negotiation fields.";

    } elseif (!is_numeric($quantity) || $quantity <= 0) {

        $error = "Please enter a valid quantity.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Please enter a valid price.";

    } else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO negotiations
                (
                    offer_id,
                    sender_id,
                    quantity,
                    price,
                    delivery_terms,
                    message,
                    status
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, 'active'
                )
            ");

            $stmt->execute([
                $offer_id,
                $farmer_id,
                $quantity,
                $price,
                $delivery_terms,
                $message
            ]);

            // Update the main offer with the latest negotiated terms
            $update_offer = $pdo->prepare("
                UPDATE offers
                SET
                    quantity = ?,
                    price = ?,
                    delivery_terms = ?,
                    message = ?,
                    status = 'pending',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
                AND farmer_id = ?
            ");

            $update_offer->execute([
                $quantity,
                $price,
                $delivery_terms,
                $message,
                $offer_id,
                $farmer_id
            ]);

            header(
                "Location: negotiate.php?offer_id=" .
                urlencode($offer_id)
            );

            exit;

        } catch (PDOException $e) {

            $error = "Negotiation could not be saved: " .
                     $e->getMessage();

        }

    }
}


/*
|--------------------------------------------------------------------------
| GET NEGOTIATION HISTORY
|--------------------------------------------------------------------------
*/

$negotiation_stmt = $pdo->prepare("
    SELECT *
    FROM negotiations
    WHERE offer_id = ?
    ORDER BY created_at ASC
");

$negotiation_stmt->execute([
    $offer_id
]);

$negotiations = $negotiation_stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Negotiation - Agro-Link Uganda</title>

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
            background: #f4f7f4;
        }

        .navbar {
            background: #198754;
        }

        .page-container {
            max-width: 1100px;
            margin: 30px auto;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .offer-summary {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 20px;
        }

        .price {
            font-size: 22px;
            font-weight: bold;
            color: #198754;
        }

        .negotiation-area {
            background: #ffffff;
            border-radius: 15px;
            padding: 20px;
            min-height: 400px;
        }

        .message {
            max-width: 75%;
            padding: 14px 18px;
            border-radius: 15px;
            margin-bottom: 15px;
        }

        .message-farmer {
            margin-left: auto;
            background: #d1e7dd;
        }

        .message-buyer {
            margin-right: auto;
            background: #e9ecef;
        }

        .message-role {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .message-time {
            font-size: 11px;
            color: #6c757d;
            margin-top: 8px;
        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar navbar-dark">

    <div class="container-fluid">

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

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">

                <i class="fa-solid fa-comments text-success"></i>

                Negotiation

            </h2>

            <p class="text-muted mb-0">

                Discuss price, quantity and delivery terms with the buyer.

            </p>

        </div>

        <a
            href="offers.php"
            class="btn btn-outline-success"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Offers

        </a>

    </div>


    <!-- ERROR -->

    <?php if (isset($error)): ?>

        <div class="alert alert-danger">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="row g-4">


        <!-- LEFT: OFFER SUMMARY -->

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">

                        <i class="fa-solid fa-file-invoice-dollar text-success"></i>

                        Offer Details

                    </h5>


                    <div class="offer-summary">

                        <p class="mb-2">

                            <strong>Offer:</strong>

                            #<?= htmlspecialchars($offer['id']) ?>

                        </p>


                        <p class="mb-2">

                            <strong>Produce:</strong>

                            <?= htmlspecialchars(
                                $offer['produce_name'] ?? 'Produce'
                            ) ?>

                        </p>


                        <p class="mb-2">

                            <strong>Buyer:</strong>

                            Buyer #<?= htmlspecialchars(
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

                            <span class="price">

                                UGX
                                <?= number_format(
                                    (float)$offer['price']
                                ) ?>

                            </span>

                        </p>


                        <p class="mb-2">

                            <strong>Delivery:</strong><br>

                            <?= htmlspecialchars(
                                $offer['delivery_terms'] ??
                                'Not specified'
                            ) ?>

                        </p>


                        <p class="mb-0">

                            <strong>Status:</strong>

                            <span class="badge bg-warning text-dark">

                                <?= htmlspecialchars(
                                    ucfirst(
                                        $offer['status'] ??
                                        'pending'
                                    )
                                ) ?>

                            </span>

                        </p>

                    </div>

                </div>

            </div>


            <!-- ACTIONS -->

            <div class="card mt-3">

                <div class="card-body">

                    <h6 class="fw-bold">

                        Offer Actions

                    </h6>

                    <p class="text-muted small">

                        Once both parties agree, the offer can later be
                        accepted and converted into an order.

                    </p>

                    <button
                        type="button"
                        class="btn btn-success w-100"
                        disabled
                    >

                        <i class="fa-solid fa-check"></i>

                        Accept Offer

                    </button>

                    <small class="text-muted d-block mt-2">

                        Order creation will be connected in the next step.

                    </small>

                </div>

            </div>

        </div>


        <!-- RIGHT: NEGOTIATION -->

        <div class="col-lg-8">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">

                        <i class="fa-solid fa-comments text-success"></i>

                        Negotiation History

                    </h5>


                    <div class="negotiation-area mb-4">


                        <?php if (count($negotiations) > 0): ?>


                            <?php foreach ($negotiations as $negotiation): ?>


                                <?php

                                $is_farmer =
                                    ((int)$negotiation['sender_id'] ===
                                    (int)$farmer_id);

                                ?>


                                <div
                                    class="message
                                    <?= $is_farmer
                                        ? 'message-farmer'
                                        : 'message-buyer' ?>"
                                >

                                    <div class="message-role">

                                        <?php if ($is_farmer): ?>

                                            <i class="fa-solid fa-user"></i>

                                            You — Farmer

                                        <?php else: ?>

                                            <i class="fa-solid fa-user-tie"></i>

                                            Buyer

                                        <?php endif; ?>

                                    </div>


                                    <div>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $negotiation['message']
                                            )
                                        ) ?>

                                    </div>


                                    <hr class="my-2">


                                    <div class="small">

                                        <strong>
                                            Quantity:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $negotiation['quantity']
                                        ) ?>


                                        &nbsp; | &nbsp;


                                        <strong>
                                            Price:
                                        </strong>

                                        UGX
                                        <?= number_format(
                                            (float)$negotiation['price']
                                        ) ?>

                                    </div>


                                    <div class="small mt-1">

                                        <strong>
                                            Delivery:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $negotiation['delivery_terms']
                                        ) ?>

                                    </div>


                                    <div class="message-time">

                                        <?= htmlspecialchars(
                                            $negotiation['created_at']
                                        ) ?>

                                    </div>

                                </div>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <div class="text-center text-muted py-5">

                                <i
                                    class="fa-solid fa-comments fa-3x mb-3"
                                ></i>

                                <h5>No negotiation messages yet</h5>

                                <p>

                                    Send your first negotiation message below.

                                </p>

                            </div>


                        <?php endif; ?>


                    </div>


                    <!-- SEND NEGOTIATION -->

                    <h5 class="fw-bold">

                        Send Counter Offer

                    </h5>

                    <p class="text-muted">

                        Change the price, quantity or delivery terms and
                        send your response to the buyer.

                    </p>


                    <form method="POST">


                        <div class="row">


                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Quantity

                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    name="quantity"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $offer['quantity']
                                    ) ?>"
                                    required
                                >

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label fw-bold">

                                    Price per Unit (UGX)

                                </label>

                                <input
                                    type="number"
                                    step="0.01"
                                    name="price"
                                    class="form-control"
                                    value="<?= htmlspecialchars(
                                        $offer['price']
                                    ) ?>"
                                    required
                                >

                            </div>


                        </div>


                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Delivery Terms

                            </label>

                            <input
                                type="text"
                                name="delivery_terms"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $offer['delivery_terms'] ?? ''
                                ) ?>"
                                placeholder="Example: Delivery within 5 days"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label fw-bold">

                                Message

                            </label>

                            <textarea
                                name="message"
                                class="form-control"
                                rows="4"
                                placeholder="Example: I can supply at UGX 1,300 per KG."
                                required
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-success"
                        >

                            <i class="fa-solid fa-paper-plane"></i>

                            Send Counter Offer

                        </button>


                    </form>

                </div>

            </div>

        </div>


    </div>

</div>


</body>

</html>