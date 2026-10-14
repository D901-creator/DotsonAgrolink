<?php
require_once "../config/database.php";

/* Get government updates */
$stmt = $pdo->prepare("
    SELECT *
    FROM government_updates
    ORDER BY created_at DESC
");

$stmt->execute();
$updates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Government Updates - Agro-Link Uganda</title>

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
                    <a class="nav-link active" href="government.php">
                        Government Updates
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

        <h1 class="fw-bold text-success">

            <i class="fas fa-landmark"></i>

            Government Updates

        </h1>

        <p class="lead text-muted">

            Agricultural programs, policies and
            government announcements.

        </p>

    </div>

</section>


<!-- CONTENT -->
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>
            Latest Government Updates
        </h3>

        <a href="index.php" class="btn btn-outline-success">

            <i class="fas fa-arrow-left"></i>

            Agriculture Hub

        </a>

    </div>


    <?php if (count($updates) > 0): ?>

        <div class="row g-4">

            <?php foreach ($updates as $update): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-body">

                            <div class="mb-3">

                                <i class="fas fa-landmark fa-2x text-success"></i>

                            </div>


                            <h5 class="card-title">

                                <?= htmlspecialchars(
                                    $update['title'] ?? 'Government Update'
                                ) ?>

                            </h5>


                            <?php if (!empty($update['category'])): ?>

                                <span class="badge bg-success mb-3">

                                    <?= htmlspecialchars(
                                        $update['category']
                                    ) ?>

                                </span>

                            <?php endif; ?>


                            <p class="card-text text-muted">

                                <?= htmlspecialchars(
                                    $update['content'] ??
                                    $update['description'] ??
                                    ''
                                ) ?>

                            </p>

                        </div>


                        <div class="card-footer bg-white">

                            <small class="text-muted">

                                <i class="fas fa-calendar"></i>

                                <?= htmlspecialchars(
                                    $update['created_at'] ?? ''
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

            No government updates are available yet.

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