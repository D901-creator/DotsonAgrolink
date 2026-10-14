<?php
session_start();

require_once "../config/database.php";

// Temporary buyer ID
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

// Check offer ID
if (!isset($_GET['offer_id']) || !is_numeric($_GET['offer_id'])) {
    header("Location: offers.php");
    exit;
}

$offer_id = (int) $_GET['offer_id'];


// ======================================================
// GET THE OFFER
// ======================================================

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.produce_name,
        p.unit
    FROM offers o
    LEFT JOIN produce p ON o.produce_id = p.id
    WHERE o.id = ?
    AND o.buyer_id = ?
");

$stmt->execute([$offer_id, $buyer_id]);

$offer = $stmt->fetch(PDO::FETCH_ASSOC);


// Offer does not exist
if (!$offer) {
    die("Offer not found or you do not have permission to view this offer.");
}


// ======================================================
// SUBMIT COUNTER OFFER
// ======================================================

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_price = trim($_POST['price'] ?? '');
    $new_quantity = trim($_POST['quantity'] ?? '');
    $delivery_terms = trim($_POST['delivery_terms'] ?? '');
    $new_message = trim($_POST['message'] ?? '');


    // Validation
    if (
        $new_price === '' ||
        $new_quantity === '' ||
        $delivery_terms === '' ||
        $new_message === ''
    ) {

        $message = '
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                Please fill in all fields.
            </div>
        ';

    } elseif (
        !is_numeric($new_price) ||
        !is_numeric($new_quantity) ||
        $new_price < 0 ||
        $new_quantity <= 0
    ) {

        $message = '
            <div class="alert alert-danger">
                Please enter valid price and quantity values.
            </div>
        ';

    } else {

        try {

            // Save negotiation
            $insert = $pdo->prepare("
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
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'Countered'
                )
            ");

            $insert->execute([
                $offer_id,
                $buyer_id,
                $new_quantity,
                $new_price,
                $delivery_terms,
                $new_message
            ]);


            $message = '
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Your counter offer has been sent successfully to the farmer.
                </div>
            ';


        } catch (PDOException $e) {

            $message = '
                <div class="alert alert-danger">
                    <strong>Database Error:</strong>
                    ' . htmlspecialchars($e->getMessage()) . '
                </div>
            ';
        }
    }
}


// ======================================================
// GET NEGOTIATION HISTORY
// ======================================================

$history_stmt = $pdo->prepare("
    SELECT *
    FROM negotiations
    WHERE offer_id = ?
    ORDER BY created_at ASC
");

$history_stmt->execute([$offer_id]);

$negotiations = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Negotiate Offer - Agro-Link Uganda
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

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

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .offer-summary {
            background: #f8f9fa;
            border-left: 5px solid #198754;
        }

        .negotiation-message {
            border-left: 4px solid #198754;
            background: #f8f9fa;
        }

        .badge-countered {
            background: #fff3cd;
            color: #856404;
        }

        .badge-pending {
            background: #cff4fc;
            color: #055160;
        }

        .badge-accepted {
            background: #d1e7dd;
            color: #0f5132;
        }

        .badge-rejected {
            background: #f8d7da;
            color: #842029;
        }

    </style>

</head>


<body>

<div class="container-fluid">

    <div class="row">


        <!-- ==================================================
             SIDEBAR
        ================================================== -->

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


            <a
                href="offers.php"
                class="active"
            >

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


        <!-- ==================================================
             MAIN CONTENT
        ================================================== -->

        <div class="col-md-9 col-lg-10 content">


            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2>

                        <i class="fas fa-comments text-success"></i>

                        Negotiate Offer

                    </h2>

                    <p class="text-muted">

                        Negotiate price, quantity and delivery terms with the farmer.

                    </p>

                </div>


                <a
                    href="offers.php"
                    class="btn btn-outline-success"
                >

                    <i class="fas fa-arrow-left"></i>

                    Back to Offers

                </a>

            </div>


            <!-- MESSAGE -->

            <?= $message ?>


            <!-- ==================================================
                 CURRENT FARMER OFFER
            ================================================== -->

            <div class="card mb-4">

                <div class="card-header bg-success text-white">

                    <i class="fas fa-file-invoice-dollar"></i>

                    Current Farmer Offer

                </div>


                <div class="card-body">

                    <div class="offer-summary p-3">

                        <h4>

                            <?= htmlspecialchars(
                                $offer['produce_name'] ?? 'Produce'
                            ) ?>

                        </h4>


                        <div class="row">


                            <div class="col-md-4">

                                <strong>

                                    Quantity

                                </strong>

                                <p>

                                    <?= htmlspecialchars(
                                        $offer['quantity'] ?? 'N/A'
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $offer['unit'] ?? ''
                                    ) ?>

                                </p>

                            </div>


                            <div class="col-md-4">

                                <strong>

                                    Price

                                </strong>

                                <p class="text-success fw-bold">

                                    UGX

                                    <?= number_format(
                                        (float)($offer['price'] ?? 0)
                                    ) ?>

                                </p>

                            </div>


                            <div class="col-md-4">

                                <strong>

                                    Delivery Terms

                                </strong>

                                <p>

                                    <?= htmlspecialchars(
                                        $offer['delivery_terms']
                                        ?? 'Not specified'
                                    ) ?>

                                </p>

                            </div>

                        </div>


                        <?php if (!empty($offer['message'])): ?>

                            <hr>

                            <strong>

                                <i class="fas fa-comment"></i>

                                Farmer's Message

                            </strong>


                            <p class="mt-2">

                                <?= nl2br(
                                    htmlspecialchars(
                                        $offer['message']
                                    )
                                ) ?>

                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 COUNTER OFFER FORM
            ================================================== -->

            <div class="card mb-4">

                <div class="card-header">

                    <h5 class="mb-0">

                        <i class="fas fa-handshake text-success"></i>

                        Make a Counter Offer

                    </h5>

                </div>


                <div class="card-body">

                    <form method="POST">


                        <div class="row">


                            <!-- PRICE -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Your Proposed Price (UGX)

                                </label>

                                <input
                                    type="number"
                                    name="price"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= htmlspecialchars(
                                        $offer['price'] ?? ''
                                    ) ?>"
                                    required
                                >

                            </div>


                            <!-- QUANTITY -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Proposed Quantity

                                </label>

                                <input
                                    type="number"
                                    name="quantity"
                                    class="form-control"
                                    min="1"
                                    step="0.01"
                                    value="<?= htmlspecialchars(
                                        $offer['quantity'] ?? ''
                                    ) ?>"
                                    required
                                >

                            </div>


                        </div>


                        <!-- DELIVERY -->

                        <div class="mb-3">

                            <label class="form-label">

                                Delivery Terms

                            </label>

                            <input
                                type="text"
                                name="delivery_terms"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $offer['delivery_terms']
                                    ?? ''
                                ) ?>"
                                placeholder="Example: Delivery to Kampala"
                                required
                            >

                        </div>


                        <!-- MESSAGE -->

                        <div class="mb-3">

                            <label class="form-label">

                                Message to Farmer

                            </label>

                            <textarea
                                name="message"
                                class="form-control"
                                rows="5"
                                placeholder="Write your counter offer or negotiation message..."
                                required
                            ></textarea>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-success"
                        >

                            <i class="fas fa-paper-plane"></i>

                            Send Counter Offer

                        </button>


                        <a
                            href="offers.php"
                            class="btn btn-secondary"
                        >

                            Cancel

                        </a>


                    </form>

                </div>

            </div>


            <!-- ==================================================
                 NEGOTIATION HISTORY
            ================================================== -->

            <div class="card">

                <div class="card-header">

                    <h5 class="mb-0">

                        <i class="fas fa-history text-success"></i>

                        Negotiation History

                    </h5>

                </div>


                <div class="card-body">


                    <?php if (count($negotiations) > 0): ?>


                        <?php foreach ($negotiations as $negotiation): ?>

                            <div class="negotiation-message p-3 mb-3">


                                <div class="d-flex justify-content-between">

                                    <strong>

                                        <i class="fas fa-user"></i>

                                        Sender ID:
                                        <?= (int)$negotiation['sender_id'] ?>

                                    </strong>


                                    <?php

                                    $status =
                                        $negotiation['status']
                                        ?? 'Pending';

                                    $status_class =
                                        strtolower($status);

                                    ?>


                                    <span
                                        class="badge badge-<?= htmlspecialchars(
                                            $status_class
                                        ) ?>"
                                    >

                                        <?= htmlspecialchars($status) ?>

                                    </span>

                                </div>


                                <hr>


                                <div class="row">


                                    <div class="col-md-4">

                                        <strong>
                                            Quantity:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $negotiation['quantity']
                                            ?? ''
                                        ) ?>

                                    </div>


                                    <div class="col-md-4">

                                        <strong>
                                            Price:
                                        </strong>

                                        UGX
                                        <?= number_format(
                                            (float)(
                                                $negotiation['price']
                                                ?? 0
                                            )
                                        ) ?>

                                    </div>


                                    <div class="col-md-4">

                                        <strong>
                                            Delivery:
                                        </strong>

                                        <?= htmlspecialchars(
                                            $negotiation[
                                                'delivery_terms'
                                            ] ?? ''
                                        ) ?>

                                    </div>

                                </div>


                                <p class="mt-3 mb-2">

                                    <strong>
                                        Message:
                                    </strong>

                                </p>


                                <p>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $negotiation['message']
                                            ?? ''
                                        )
                                    ) ?>

                                </p>


                                <small class="text-muted">

                                    <?= htmlspecialchars(
                                        $negotiation['created_at']
                                        ?? ''
                                    ) ?>

                                </small>


                            </div>

                        <?php endforeach; ?>


                    <?php else: ?>


                        <div class="text-center py-4">

                            <i
                                class="fas fa-comments fa-3x text-muted mb-3"
                            ></i>

                            <p class="text-muted">

                                No negotiation messages yet.

                            </p>

                        </div>


                    <?php endif; ?>


                </div>

            </div>


        </div>

    </div>

</div>

</body>

</html>