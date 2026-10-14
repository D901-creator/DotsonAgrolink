<?php

require_once "../config/database.php";

if (!isset($_GET['farmer_id']) || !is_numeric($_GET['farmer_id'])) {
    die("Invalid farmer.");
}

$farmer_id = (int) $_GET['farmer_id'];



/*
|--------------------------------------------------------------------------
| Get farmer information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    AND role = 'farmer'
    LIMIT 1
");

$stmt->execute([$farmer_id]);

$farmer = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$farmer) {
    die("Farmer not found.");
}


/*
|--------------------------------------------------------------------------
| Get farmer's available produce
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM produce
    WHERE farmer_id = ?
    AND availability = 'available'
    ORDER BY created_at DESC
");

$stmt->execute([$farmer_id]);

$produce = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Farmer Profile - Agro-Link Uganda
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>


<body class="bg-light">


<div class="container py-5">

    <!-- BACK BUTTON -->

    <a href="find_produce.php"
       class="btn btn-outline-success mb-4">

        <i class="fas fa-arrow-left"></i>

        Back to Find Produce

    </a>


    <!-- FARMER PROFILE -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-3 text-center">

                    <i class="fas fa-user-circle fa-7x text-success"></i>

                </div>


                <div class="col-md-9">

                    <h2>

                        <?= htmlspecialchars(
                            $farmer['username'] ?? 'Farmer'
                        ) ?>

                    </h2>


                    <span class="badge bg-success mb-3">

                        <i class="fas fa-seedling"></i>

                        Farmer

                    </span>


                    <?php if (isset($farmer['phone'])): ?>

                        <p>

                            <i class="fas fa-phone text-success"></i>

                            <strong>Phone:</strong>

                            <?= htmlspecialchars(
                                $farmer['phone']
                            ) ?>

                        </p>

                    <?php endif; ?>


                    <?php if (isset($farmer['email'])): ?>

                        <p>

                            <i class="fas fa-envelope text-success"></i>

                            <strong>Email:</strong>

                            <?= htmlspecialchars(
                                $farmer['email']
                            ) ?>

                        </p>
                       <a
    href="../messages/index.php?user_id=<?= urlencode($farmer_id) ?>"
    class="btn btn-success"
>
    <i class="fas fa-comments"></i>
    Message Farmer
</a>

                    <?php endif; ?>


                    <p class="text-muted mb-0">

                        <i class="fas fa-user-check"></i>

                        Agro-Link Uganda Farmer

                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- FARMER PRODUCE -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h3>

                <i class="fas fa-seedling text-success"></i>

                Farmer's Available Produce

            </h3>

            <p class="text-muted">

                Products currently available from this farmer.

            </p>

        </div>

    </div>


    <?php if (empty($produce)): ?>

        <div class="alert alert-info">

            <i class="fas fa-info-circle"></i>

            This farmer currently has no available produce.

        </div>

    <?php else: ?>

        <div class="row">

            <?php foreach ($produce as $item): ?>

                <div class="col-md-6 col-lg-4 mb-4">

                    <div class="card shadow-sm h-100">

                        <?php if (!empty($item['image'])): ?>

                            <img
                                src="../uploads/<?= htmlspecialchars($item['image']) ?>"
                                class="card-img-top"
                                style="height:200px; object-fit:cover;"
                                alt="Produce">

                        <?php else: ?>

                            <div
                                class="bg-success text-white d-flex align-items-center justify-content-center"
                                style="height:200px;">

                                <i class="fas fa-seedling fa-5x"></i>

                            </div>

                        <?php endif; ?>


                        <div class="card-body">

                            <h5 class="card-title">

                                <?= htmlspecialchars(
                                    $item['produce_name']
                                ) ?>

                            </h5>


                            <p>

                                <strong>
                                    Quantity:
                                </strong>

                                <?= htmlspecialchars(
                                    $item['quantity']
                                ) ?>

                                <?= htmlspecialchars(
                                    $item['unit']
                                ) ?>

                            </p>


                            <p>

                                <strong>
                                    Expected Price:
                                </strong>

                                UGX

                                <?= number_format(
                                    (float)$item['expected_price']
                                ) ?>

                            </p>


                            <p>

                                <strong>
                                    Location:
                                </strong>

                                <?= htmlspecialchars(
                                    $item['location']
                                ) ?>

                            </p>


                            <?php if (!empty($item['quality_grade'])): ?>

                                <span class="badge bg-info text-dark">

                                    <?= htmlspecialchars(
                                        $item['quality_grade']
                                    ) ?>

                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>


</body>

</html>