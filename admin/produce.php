<?php
require_once "../config/database.php";

$stmt = $pdo->prepare("
    SELECT *
    FROM produce
    ORDER BY created_at DESC
");

$stmt->execute();

$produce = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Produce - Agro-Link Uganda</title>

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
               class="d-block text-warning text-decoration-none mb-3">

                <i class="fas fa-seedling me-2"></i>
                Produce

            </a>

            <a href="#"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-cart-shopping me-2"></i>
                Orders

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

                        <i class="fas fa-seedling text-success"></i>

                        Manage Produce

                    </h2>

                    <p class="text-muted">

                        View agricultural produce listed by farmers.

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

                        All Produce Listings

                    </h5>

                </div>


                <div class="card-body">

                    <?php if (count($produce) > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-success">

                                <tr>

                                    <th>#</th>

                                    <th>Produce</th>

                                    <th>Quantity</th>

                                    <th>Unit</th>

                                    <th>Quality</th>

                                    <th>Expected Price</th>

                                    <th>Location</th>

                                    <th>Availability</th>

                                    <th>Date</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php foreach ($produce as $item): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($item['id']) ?>
                                    </td>

                                    <td class="fw-bold">

                                        <?= htmlspecialchars(
                                            $item['produce_name'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item['quantity'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item['unit'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item['quality_grade'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        UGX
                                        <?= number_format(
                                            (float)($item['expected_price'] ?? 0)
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item['location'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?php
                                        $availability =
                                            $item['availability'] ?? 'N/A';

                                        if ($availability === 'available') {

                                            echo '<span class="badge bg-success">
                                                    Available
                                                  </span>';

                                        } else {

                                            echo '<span class="badge bg-secondary">'
                                                . htmlspecialchars($availability)
                                                . '</span>';
                                        }
                                        ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item['created_at'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <?php else: ?>

                        <div class="alert alert-info">

                            <i class="fas fa-info-circle"></i>

                            No produce has been listed yet.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>