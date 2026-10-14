<?php
require_once "../config/database.php";

$stmt = $pdo->prepare("
    SELECT *
    FROM deliveries
    ORDER BY created_at DESC
");

$stmt->execute();

$deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Deliveries - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-success">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="dashboard.php">
            🌱 AGRO-LINK UGANDA
        </a>

        <span class="text-white">
            <i class="fas fa-user-shield"></i>
            Administrator
        </span>

    </div>

</nav>


<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h5 class="mb-4">
                <i class="fas fa-gauge"></i>
                Admin Panel
            </h5>

            <a href="dashboard.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-home me-2"></i>
                Dashboard

            </a>

            <a href="users.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-users me-2"></i>
                Users

            </a>

            <a href="produce.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-seedling me-2"></i>
                Produce

            </a>

            <a href="buyer_requests.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-handshake me-2"></i>
                Buyer Requests

            </a>

            <a href="offers.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-file-signature me-2"></i>
                Offers

            </a>

            <a href="negotiations.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-comments-dollar me-2"></i>
                Negotiations

            </a>

            <a href="orders.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-cart-shopping me-2"></i>
                Orders

            </a>

            <a href="deliveries.php"
               class="d-block text-warning text-decoration-none mb-3">

                <i class="fas fa-truck me-2"></i>
                Deliveries

            </a>

            <a href="../hub/index.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-newspaper me-2"></i>
                Agriculture Hub

            </a>

        </div>


        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold">

                        <i class="fas fa-truck text-success"></i>

                        Deliveries Management

                    </h2>

                    <p class="text-muted">

                        Monitor deliveries associated with Agro-Link Uganda orders.

                    </p>

                </div>

                <a href="dashboard.php"
                   class="btn btn-success">

                    <i class="fas fa-arrow-left"></i>
                    Dashboard

                </a>

            </div>


            <div class="card shadow-sm">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-list"></i>

                        All Deliveries

                    </h5>

                </div>


                <div class="card-body">

                    <?php if (count($deliveries) > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-success">

                                <tr>

                                    <?php foreach (array_keys($deliveries[0]) as $column): ?>

                                        <th>
                                            <?= htmlspecialchars(
                                                ucwords(
                                                    str_replace('_', ' ', $column)
                                                )
                                            ) ?>
                                        </th>

                                    <?php endforeach; ?>

                                </tr>

                            </thead>

                            <tbody>

                            <?php foreach ($deliveries as $delivery): ?>

                                <tr>

                                    <?php foreach ($delivery as $value): ?>

                                        <td>

                                            <?= htmlspecialchars(
                                                (string)($value ?? 'N/A')
                                            ) ?>

                                        </td>

                                    <?php endforeach; ?>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <?php else: ?>

                        <div class="alert alert-info">

                            <i class="fas fa-info-circle"></i>

                            No deliveries have been recorded yet.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>