<?php

require_once "../config/database.php";

if (!isset($_GET['buyer_id']) || !is_numeric($_GET['buyer_id'])) {
    die("Invalid buyer.");
}

$buyer_id = (int) $_GET['buyer_id'];


/*
|--------------------------------------------------------------------------
| Get buyer information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    AND role = 'buyer'
    LIMIT 1
");

$stmt->execute([$buyer_id]);

$buyer = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$buyer) {
    die("Buyer not found.");
}


/*
|--------------------------------------------------------------------------
| Get buyer's open requests
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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Buyer Profile - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>


<body class="bg-light">


<div class="container py-5">

    <!-- BACK -->

    <a href="buyer_requests.php"
       class="btn btn-outline-success mb-4">

        <i class="fas fa-arrow-left"></i>

        Back to Buyer Requests

    </a>


    <!-- BUYER PROFILE -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-3 text-center">

                    <i class="fas fa-user-circle fa-7x text-primary"></i>

                </div>


                <div class="col-md-9">

                    <h2>

                        <?= htmlspecialchars(
                            $buyer['username'] ?? 'Buyer'
                        ) ?>

                    </h2>


                    <span class="badge bg-primary mb-3">

                        <i class="fas fa-shopping-cart"></i>

                        Buyer

                    </span>


                    <?php if (isset($buyer['phone'])): ?>

                        <p>

                            <i class="fas fa-phone text-success"></i>

                            <strong>Phone:</strong>

                            <?= htmlspecialchars(
                                $buyer['phone']
                            ) ?>

                        </p>

                    <?php endif; ?>


                    <?php if (isset($buyer['email'])): ?>

                        <p>

                            <i class="fas fa-envelope text-success"></i>

                            <strong>Email:</strong>

                            <?= htmlspecialchars(
                                $buyer['email']
                            ) ?>

                        </p>

                        <a
    href="../messages/index.php?user_id=<?= urlencode($buyer_id) ?>"
    class="btn btn-success"
>
    <i class="fas fa-comments"></i>
    Message Buyer
</a>

                    <?php endif; ?>


                    <p class="text-muted">

                        <i class="fas fa-user-check"></i>

                        Agro-Link Uganda Buyer

                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- BUYER REQUESTS -->

    <div class="mb-4">

        <h3>

            <i class="fas fa-clipboard-list text-success"></i>

            Buyer's Open Requests

        </h3>

        <p class="text-muted">

            Agricultural products currently requested by this buyer.

        </p>

    </div>


    <?php if (empty($requests)): ?>

        <div class="alert alert-info">

            <i class="fas fa-info-circle"></i>

            This buyer currently has no open requests.

        </div>

    <?php else: ?>

        <div class="row">

            <?php foreach ($requests as $request): ?>

                <div class="col-md-6 col-lg-4 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-header bg-success text-white">

                            <i class="fas fa-seedling"></i>

                            <?= htmlspecialchars(
                                $request['produce_name'] ?? 'Produce Request'
                            ) ?>

                        </div>


                        <div class="card-body">

                            <?php if (isset($request['quantity'])): ?>

                                <p>

                                    <strong>
                                        Quantity:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $request['quantity']
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $request['unit'] ?? ''
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (isset($request['target_price'])): ?>

                                <p>

                                    <strong>
                                        Target Price:
                                    </strong>

                                    UGX

                                    <?= number_format(
                                        (float)$request['target_price']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (isset($request['location'])): ?>

                                <p>

                                    <strong>
                                        Location:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $request['location']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (isset($request['quality_requirement'])): ?>

                                <p>

                                    <strong>
                                        Quality:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $request['quality_requirement']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (isset($request['required_date'])): ?>

                                <p>

                                    <strong>
                                        Required Date:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $request['required_date']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (isset($request['description'])): ?>

                                <p>

                                    <?= htmlspecialchars(
                                        $request['description']
                                    ) ?>

                                </p>

                            <?php endif; ?>

                        </div>


                        <div class="card-footer bg-white">

                            <span class="badge bg-success">

                                Open Request

                            </span>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>

</html>