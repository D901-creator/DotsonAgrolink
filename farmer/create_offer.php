<?php

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| TEMPORARY FARMER
|--------------------------------------------------------------------------
*/

$farmer_id = 1;


/*
|--------------------------------------------------------------------------
| GET IDs FROM MATCH
|--------------------------------------------------------------------------
*/

$request_id = isset($_GET['request_id'])
    ? (int) $_GET['request_id']
    : 0;

$produce_id = isset($_GET['produce_id'])
    ? (int) $_GET['produce_id']
    : 0;


if ($request_id <= 0 || $produce_id <= 0) {
    die("Invalid buyer request or produce.");
}


/*
|--------------------------------------------------------------------------
| GET BUYER REQUEST
|--------------------------------------------------------------------------
*/

$request_stmt = $pdo->prepare("
    SELECT *
    FROM buyer_requests
    WHERE id = ?
    AND status = 'open'
");

$request_stmt->execute([
    $request_id
]);

$request = $request_stmt->fetch();


if (!$request) {
    die("Buyer request not found or is no longer open.");
}


/*
|--------------------------------------------------------------------------
| GET FARMER PRODUCE
|--------------------------------------------------------------------------
*/

$produce_stmt = $pdo->prepare("
    SELECT *
    FROM produce
    WHERE id = ?
    AND farmer_id = ?
    AND availability = 'available'
");

$produce_stmt->execute([
    $produce_id,
    $farmer_id
]);

$produce = $produce_stmt->fetch();


if (!$produce) {
    die("Your produce could not be found.");
}


/*
|--------------------------------------------------------------------------
| CREATE OFFER
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantity = trim($_POST['quantity'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $delivery_terms = trim($_POST['delivery_terms'] ?? '');
    $message = trim($_POST['message'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $quantity === '' ||
        $price === '' ||
        $delivery_terms === '' ||
        $message === ''
    ) {

        $error = "Please fill in all fields.";

    } elseif (!is_numeric($quantity) || $quantity <= 0) {

        $error = "Enter a valid quantity.";

    } elseif (!is_numeric($price) || $price <= 0) {

        $error = "Enter a valid price.";

    } elseif ((float)$quantity > (float)$produce['quantity']) {

        $error = "You cannot offer more than the quantity you have available.";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | CHECK FOR EXISTING OFFER
            |--------------------------------------------------------------------------
            */

            $check_stmt = $pdo->prepare("
                SELECT id
                FROM offers
                WHERE farmer_id = ?
                AND buyer_id = ?
                AND produce_id = ?
                AND request_id = ?
                AND status NOT IN ('rejected', 'cancelled')
                LIMIT 1
            ");

            $check_stmt->execute([
                $farmer_id,
                $request['buyer_id'],
                $produce_id,
                $request_id
            ]);

            $existing = $check_stmt->fetch();


            if ($existing) {

                $error = "You have already submitted an offer for this buyer request.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | INSERT OFFER
                |--------------------------------------------------------------------------
                */

                $insert_stmt = $pdo->prepare("
                    INSERT INTO offers
                    (
                        farmer_id,
                        buyer_id,
                        produce_id,
                        request_id,
                        quantity,
                        price,
                        delivery_terms,
                        message,
                        status
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?, ?, ?, 'pending'
                    )
                ");

                $insert_stmt->execute([
                    $farmer_id,
                    $request['buyer_id'],
                    $produce_id,
                    $request_id,
                    $quantity,
                    $price,
                    $delivery_terms,
                    $message
                ]);


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                header(
                    "Location: offers.php?success=created"
                );

                exit;
            }


        } catch (PDOException $e) {

            $error =
                "Offer could not be created: " .
                $e->getMessage();
        }
    }
}

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
        Create Offer | Agro-Link Uganda
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
            max-width: 1100px;
            margin: 35px auto;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .section-title {
            font-weight: 700;
        }

        .request-box {
            background: #eef8f2;
            border-radius: 12px;
            padding: 20px;
        }

        .produce-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
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

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">

                <i
                    class="fa-solid fa-handshake text-success"
                ></i>

                Create Offer

            </h2>

            <p class="text-muted">

                Respond to the buyer with your price and terms.

            </p>

        </div>


        <a
            href="matches.php"
            class="btn btn-outline-success"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Matches

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


        <!-- BUYER REQUEST -->

        <div class="col-lg-5">

            <div class="card">

                <div class="card-body">

                    <h5 class="section-title mb-3">

                        <i
                            class="fa-solid fa-bullhorn text-success"
                        ></i>

                        Buyer Request

                    </h5>


                    <div class="request-box">

                        <p>

                            <strong>Product:</strong><br>

                            <?= htmlspecialchars(
                                $request['produce_name']
                            ) ?>

                        </p>


                        <p>

                            <strong>Quantity Needed:</strong><br>

                            <?= htmlspecialchars(
                                $request['quantity']
                            ) ?>

                            <?= htmlspecialchars(
                                $request['unit']
                            ) ?>

                        </p>


                        <p>

                            <strong>Target Price:</strong><br>

                            UGX

                            <?= number_format(
                                (float)$request['target_price']
                            ) ?>

                        </p>


                        <p>

                            <strong>Location:</strong><br>

                            <?= htmlspecialchars(
                                $request['location']
                            ) ?>

                        </p>


                        <p class="mb-0">

                            <strong>Buyer:</strong><br>

                            Buyer #

                            <?= htmlspecialchars(
                                $request['buyer_id']
                            ) ?>

                        </p>

                    </div>

                </div>

            </div>


            <!-- FARMER PRODUCE -->

            <div class="card mt-4">

                <div class="card-body">

                    <h5 class="section-title mb-3">

                        <i
                            class="fa-solid fa-seedling text-success"
                        ></i>

                        Your Produce

                    </h5>


                    <div class="produce-box">

                        <p>

                            <strong>Product:</strong><br>

                            <?= htmlspecialchars(
                                $produce['produce_name']
                            ) ?>

                        </p>


                        <p>

                            <strong>Available:</strong><br>

                            <?= htmlspecialchars(
                                $produce['quantity']
                            ) ?>

                            <?= htmlspecialchars(
                                $produce['unit']
                            ) ?>

                        </p>


                        <p>

                            <strong>Your Listed Price:</strong><br>

                            UGX

                            <?= number_format(
                                (float)$produce['price_per_unit']
                            ) ?>

                            /

                            <?= htmlspecialchars(
                                $produce['unit']
                            ) ?>

                        </p>


                        <p class="mb-0">

                            <strong>Location:</strong><br>

                            <?= htmlspecialchars(
                                $produce['location']
                            ) ?>

                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- OFFER FORM -->

        <div class="col-lg-7">

            <div class="card">

                <div class="card-body p-4">

                    <h5 class="section-title mb-4">

                        <i
                            class="fa-solid fa-file-signature text-success"
                        ></i>

                        Your Offer

                    </h5>


                    <form method="POST">


                        <!-- QUANTITY -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-bold"
                            >

                                Quantity

                            </label>


                            <input
                                type="number"
                                name="quantity"
                                class="form-control"
                                min="0.01"
                                step="0.01"
                                max="<?= htmlspecialchars(
                                    $produce['quantity']
                                ) ?>"
                                value="<?= htmlspecialchars(
                                    $request['quantity']
                                ) ?>"
                                required
                            >


                            <small class="text-muted">

                                Maximum available:

                                <?= htmlspecialchars(
                                    $produce['quantity']
                                ) ?>

                                <?= htmlspecialchars(
                                    $produce['unit']
                                ) ?>

                            </small>

                        </div>


                        <!-- PRICE -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-bold"
                            >

                                Your Price Per Unit (UGX)

                            </label>


                            <input
                                type="number"
                                name="price"
                                class="form-control"
                                min="1"
                                step="0.01"
                                value="<?= htmlspecialchars(
                                    $produce['price_per_unit']
                                ) ?>"
                                required
                            >


                            <small class="text-muted">

                                Buyer target price:

                                UGX

                                <?= number_format(
                                    (float)$request['target_price']
                                ) ?>

                            </small>

                        </div>


                        <!-- DELIVERY -->

                        <div class="mb-3">

                            <label
                                class="form-label fw-bold"
                            >

                                Delivery Terms

                            </label>


                            <input
                                type="text"
                                name="delivery_terms"
                                class="form-control"
                                placeholder="Example: Delivery within 3 days"
                                required
                            >

                        </div>


                        <!-- MESSAGE -->

                        <div class="mb-4">

                            <label
                                class="form-label fw-bold"
                            >

                                Message to Buyer

                            </label>


                            <textarea
                                name="message"
                                class="form-control"
                                rows="5"
                                placeholder="Write your message to the buyer..."
                                required
                            ></textarea>

                        </div>


                        <!-- BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-success btn-lg w-100"
                        >

                            <i
                                class="fa-solid fa-paper-plane"
                            ></i>

                            Send Offer

                        </button>


                    </form>

                </div>

            </div>

        </div>


    </div>

</div>


</body>

</html>