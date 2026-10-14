<?php
require_once "../config/database.php";

/* Get agricultural support programs */
$stmt = $pdo->prepare("
    SELECT *
    FROM support_programs
    ORDER BY created_at DESC
");

$stmt->execute();
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Support Programs - Agro-Link Uganda</title>

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
                    <a class="nav-link active" href="support.php">
                        Support Programs
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

            <i class="fas fa-hands-helping"></i>

            Agricultural Support Programs

        </h1>

        <p class="lead text-muted">

            Discover agricultural support, training and
            development opportunities.

        </p>

    </div>

</section>


<!-- PROGRAMS -->
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>
            Available Programs
        </h3>

        <a href="index.php" class="btn btn-outline-success">

            <i class="fas fa-arrow-left"></i>

            Agriculture Hub

        </a>

    </div>


    <?php if (count($programs) > 0): ?>

        <div class="row g-4">

            <?php foreach ($programs as $program): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-body">

                            <div class="mb-3">

                                <i class="fas fa-hands-helping fa-3x text-success"></i>

                            </div>


                            <h5 class="card-title">

                                <?= htmlspecialchars(
                                    $program['title'] ??
                                    'Support Program'
                                ) ?>

                            </h5>


                            <?php if (!empty($program['category'])): ?>

                                <span class="badge bg-success mb-3">

                                    <?= htmlspecialchars(
                                        $program['category']
                                    ) ?>

                                </span>

                            <?php endif; ?>


                            <p class="card-text text-muted">

                                <?= htmlspecialchars(
                                    $program['description'] ??
                                    $program['content'] ??
                                    ''
                                ) ?>

                            </p>


                            <?php if (!empty($program['eligibility'])): ?>

                                <div class="mt-3">

                                    <strong>
                                        <i class="fas fa-user-check"></i>
                                        Eligibility
                                    </strong>

                                    <p class="text-muted mb-0">

                                        <?= htmlspecialchars(
                                            $program['eligibility']
                                        ) ?>

                                    </p>

                                </div>

                            <?php endif; ?>

                        </div>


                        <div class="card-footer bg-white">

                            <small class="text-muted">

                                <i class="fas fa-calendar"></i>

                                <?= htmlspecialchars(
                                    $program['created_at'] ?? ''
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

            No agricultural support programs are available yet.

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