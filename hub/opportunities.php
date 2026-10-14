<?php
require_once "../config/database.php";

/* Get open buyer opportunities */
$stmt = $pdo->prepare("
    SELECT *
    FROM buyer_requests
    WHERE status = 'open'
    ORDER BY created_at DESC
");

$stmt->execute();
$opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buyer Opportunities - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">

    <div class="container">

        <a class="navbar-brand fw-bold" href="../index.php">
            🌱 AGRO-LINK UGANDA
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        Agriculture Hub
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../farmer/dashboard.php">
                        Farmer
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../buyer/dashboard.php">
                        Buyer
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- HEADER -->
<section class="bg-light py-5">

    <div class="container text-center">

        <h1 class="fw-bold text-success">

            <i class="fas fa-handshake"></i>

            Buyer Opportunities

        </h1>

        <p class="lead text-muted">

            Find buyers looking for agricultural produce
            on Agro-Link Uganda.

        </p>

    </div>

</section>


<!-- OPPORTUNITIES -->
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>
            Available Buyer Requests
        </h3>

        <a href="index.php" class="btn btn-outline-success">

            <i class="fas fa-arrow-left"></i>

            Agriculture Hub

        </a>

    </div>


    <?php if (count($opportunities) > 0): ?>

        <div class="row g-4">

            <?php foreach ($opportunities as $opportunity): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-3">

                                <h5 class="card-title">

                                    <i class="fas fa-seedling text-success"></i>

                                    <?= htmlspecialchars(
                                        $opportunity['produce_name'] ?? 'Produce'
                                    ) ?>

                                </h5>

                                <span class="badge bg-success">
                                    Open
                                </span>

                            </div>


                            <p>

                                <strong>
                                    <i class="fas fa-weight-hanging"></i>
                                    Quantity:
                                </strong>

                                <?= htmlspecialchars(
                                    $opportunity['quantity'] ?? ''
                                ) ?>

                                <?= htmlspecialchars(
                                    $opportunity['unit'] ?? ''
                                ) ?>

                            </p>


                            <?php if (isset($opportunity['target_price'])): ?>

                                <p>

                                    <strong>
                                        <i class="fas fa-money-bill-wave"></i>
                                        Target Price:
                                    </strong>

                                    UGX
                                    <?= number_format(
                                        (float)$opportunity['target_price']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($opportunity['location'])): ?>

                                <p>

                                    <strong>
                                        <i class="fas fa-location-dot"></i>
                                        Location:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $opportunity['location']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($opportunity['quality_requirement'])): ?>

                                <p>

                                    <strong>
                                        <i class="fas fa-star"></i>
                                        Quality:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $opportunity['quality_requirement']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($opportunity['required_date'])): ?>

                                <p>

                                    <strong>
                                        <i class="fas fa-calendar"></i>
                                        Required Date:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $opportunity['required_date']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <?php if (!empty($opportunity['description'])): ?>

                                <p class="text-muted">

                                    <?= htmlspecialchars(
                                        $opportunity['description']
                                    ) ?>

                                </p>

                            <?php endif; ?>

                        </div>


                        <div class="card-footer bg-white">

                            <small class="text-muted">

                                <i class="fas fa-clock"></i>

                                Posted:
                                <?= htmlspecialchars(
                                    $opportunity['created_at'] ?? ''
                                ) ?>

                            </small>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-info">

            <i class="fas fa-info-circle"></i>

            There are currently no open buyer opportunities.

        </div>

    <?php endif; ?>

</div>


<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-4">

    <div class="container">

        <h5>
            🌱 Agro-Link Uganda
        </h5>

        <p class="mb-0">
            Connecting Ugandan Farmers to Better Markets
        </p>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>