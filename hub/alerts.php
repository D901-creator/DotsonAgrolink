<?php
require_once "../config/database.php";

/* Get agricultural alerts */
$stmt = $pdo->prepare("
    SELECT *
    FROM alerts
    ORDER BY created_at DESC
");

$stmt->execute();
$alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agricultural Alerts - Agro-Link Uganda</title>

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
                    <a class="nav-link" href="index.php">
                        Agriculture Hub
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="alerts.php">
                        Alerts
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


<!-- PAGE HEADER -->
<section class="bg-light py-5">

    <div class="container text-center">

        <h1 class="fw-bold text-danger">

            <i class="fas fa-bell"></i>

            Agricultural Alerts

        </h1>

        <p class="lead text-muted">

            Important agricultural alerts and announcements
            for farmers and stakeholders.

        </p>

    </div>

</section>


<!-- ALERTS -->
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>
            Latest Alerts
        </h3>

        <a href="index.php" class="btn btn-outline-success">

            <i class="fas fa-arrow-left"></i>

            Agriculture Hub

        </a>

    </div>


    <?php if (count($alerts) > 0): ?>

        <div class="row g-4">

            <?php foreach ($alerts as $alert): ?>

                <?php
                $type = strtolower($alert['type'] ?? 'info');

                $badge = 'bg-info';

                if ($type === 'warning') {
                    $badge = 'bg-warning text-dark';
                } elseif ($type === 'danger' || $type === 'critical') {
                    $badge = 'bg-danger';
                } elseif ($type === 'success') {
                    $badge = 'bg-success';
                }
                ?>

                <div class="col-md-6">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <h5 class="card-title mb-0">

                                    <i class="fas fa-triangle-exclamation text-danger"></i>

                                    <?= htmlspecialchars(
                                        $alert['title'] ?? 'Agricultural Alert'
                                    ) ?>

                                </h5>


                                <span class="badge <?= $badge ?>">

                                    <?= htmlspecialchars(
                                        ucfirst($type)
                                    ) ?>

                                </span>

                            </div>


                            <p class="card-text text-muted">

                                <?= htmlspecialchars(
                                    $alert['message'] ??
                                    $alert['description'] ??
                                    $alert['content'] ??
                                    ''
                                ) ?>

                            </p>


                            <?php if (!empty($alert['location'])): ?>

                                <p class="mb-2">

                                    <i class="fas fa-location-dot text-danger"></i>

                                    <strong>Location:</strong>

                                    <?= htmlspecialchars(
                                        $alert['location']
                                    ) ?>

                                </p>

                            <?php endif; ?>


                            <small class="text-muted">

                                <i class="fas fa-clock"></i>

                                <?= htmlspecialchars(
                                    $alert['created_at'] ?? ''
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

            No agricultural alerts are available at the moment.

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