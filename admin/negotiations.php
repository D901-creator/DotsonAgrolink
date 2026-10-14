<?php
require_once "../config/database.php";

$stmt = $pdo->prepare("
    SELECT
        n.*,
        o.farmer_id,
        o.buyer_id,
        o.produce_id,
        p.produce_name,
        p.unit
    FROM negotiations n
    INNER JOIN offers o ON n.offer_id = o.id
    LEFT JOIN produce p ON o.produce_id = p.id
    ORDER BY n.created_at DESC
");

$stmt->execute();

$negotiations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Negotiations - Agro-Link Uganda</title>

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
               class="d-block text-warning text-decoration-none mb-3">
                <i class="fas fa-comments-dollar me-2"></i>
                Negotiations
            </a>

            <a href="orders.php"
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

                        <i class="fas fa-comments-dollar text-success"></i>

                        Negotiations

                    </h2>

                    <p class="text-muted">

                        Monitor negotiations between farmers and buyers.

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

                        All Negotiations

                    </h5>

                </div>


                <div class="card-body">

                    <?php if (count($negotiations) > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-success">

                                <tr>

                                    <th>ID</th>
                                    <th>Offer ID</th>
                                    <th>Farmer ID</th>
                                    <th>Buyer ID</th>
                                    <th>Produce</th>
                                    <th>Sender ID</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Delivery Terms</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php foreach ($negotiations as $negotiation): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($negotiation['id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($negotiation['offer_id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($negotiation['farmer_id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($negotiation['buyer_id']) ?>
                                    </td>

                                    <td class="fw-bold">

                                        <?= htmlspecialchars(
                                            $negotiation['produce_name'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $negotiation['sender_id'] ?? 'N/A'
                                        ) ?>
                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $negotiation['quantity'] ?? 'N/A'
                                        ) ?>

                                        <?= htmlspecialchars(
                                            $negotiation['unit'] ?? ''
                                        ) ?>

                                    </td>

                                    <td>

                                        UGX
                                        <?= number_format(
                                            (float)($negotiation['price'] ?? 0)
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $negotiation['delivery_terms'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $negotiation['message'] ?? 'N/A'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?php
                                        $status = $negotiation['status'] ?? 'N/A';

                                        if ($status === 'Pending') {

                                            echo '<span class="badge bg-warning text-dark">
                                                    Pending
                                                  </span>';

                                        } elseif ($status === 'Accepted') {

                                            echo '<span class="badge bg-success">
                                                    Accepted
                                                  </span>';

                                        } elseif ($status === 'Rejected') {

                                            echo '<span class="badge bg-danger">
                                                    Rejected
                                                  </span>';

                                        } elseif ($status === 'Countered') {

                                            echo '<span class="badge bg-primary">
                                                    Countered
                                                  </span>';

                                        } else {

                                            echo '<span class="badge bg-secondary">'
                                                . htmlspecialchars($status)
                                                . '</span>';
                                        }
                                        ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $negotiation['created_at'] ?? 'N/A'
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

                            No negotiations have been created yet.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>