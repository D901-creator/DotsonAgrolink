<?php

require_once "../config/database.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get buyer orders
|--------------------------------------------------------------------------
| We use SELECT * because the exact orders table structure may contain
| different columns.
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE buyer_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$buyer_id]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Orders - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h4 class="text-center mb-4">
                🌱 Agro-Link
            </h4>

            <hr>

            <a href="dashboard.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-home"></i>
                Dashboard

            </a>

            <a href="find_produce.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-search"></i>
                Find Produce

            </a>

            <a href="my_requests.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-list"></i>
                My Requests

            </a>

            <a href="matches.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-handshake"></i>
                Farmer Matches

            </a>

            <a href="offers.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-tags"></i>
                Offers & Negotiations

            </a>

            <a href="orders.php"
               class="btn btn-success w-100 mb-2 text-start">

                <i class="fas fa-shopping-cart"></i>
                My Orders

            </a>

            <a href="deliveries.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-truck"></i>
                Deliveries

            </a>

            <a href="market_prices.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-chart-line"></i>
                Market Prices

            </a>

            <a href="ratings.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-star"></i>
                Ratings

            </a>

            <a href="profile.php"
               class="btn btn-outline-light w-100 mb-2 text-start">

                <i class="fas fa-user"></i>
                Profile

            </a>

            <a href="logout.php"
               class="btn btn-danger w-100 mb-2 text-start">

                <i class="fas fa-sign-out-alt"></i>
                Logout

            </a>

        </div>


        <!-- MAIN CONTENT -->
        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2>
                        <i class="fas fa-shopping-cart text-success"></i>
                        My Orders
                    </h2>

                    <p class="text-muted">
                        View and track your agricultural orders.
                    </p>

                </div>

                <a href="find_produce.php"
                   class="btn btn-success">

                    <i class="fas fa-plus"></i>
                    Find More Produce

                </a>

            </div>


            <?php if (empty($orders)): ?>

                <div class="alert alert-info">

                    <i class="fas fa-info-circle"></i>

                    You currently have no orders.

                </div>

            <?php else: ?>

                <div class="row">

                    <?php foreach ($orders as $order): ?>

                        <div class="col-md-6 col-xl-4 mb-4">

                            <div class="card shadow-sm h-100">

                                <div class="card-header bg-success text-white">

                                    <strong>

                                        Order #<?= htmlspecialchars($order['id']) ?>

                                    </strong>

                                </div>


                                <div class="card-body">

                                    <?php if (isset($order['quantity'])): ?>

                                        <p>

                                            <strong>
                                                Quantity:
                                            </strong>

                                            <?= htmlspecialchars($order['quantity']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($order['price'])): ?>

                                        <p>

                                            <strong>
                                                Price:
                                            </strong>

                                            UGX
                                            <?= number_format((float)$order['price']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($order['status'])): ?>

                                        <p>

                                            <strong>
                                                Status:
                                            </strong>

                                            <?php

                                            $status = strtolower($order['status']);

                                            $badge = "secondary";

                                            if ($status == "pending") {
                                                $badge = "warning";
                                            } elseif ($status == "confirmed" || $status == "accepted") {
                                                $badge = "success";
                                            } elseif ($status == "completed" || $status == "delivered") {
                                                $badge = "primary";
                                            } elseif ($status == "cancelled" || $status == "rejected") {
                                                $badge = "danger";
                                            }

                                            ?>

                                            <span class="badge bg-<?= $badge ?>">

                                                <?= htmlspecialchars($order['status']) ?>

                                            </span>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($order['delivery_address'])): ?>

                                        <p>

                                            <strong>
                                                Delivery Address:
                                            </strong>

                                            <?= htmlspecialchars($order['delivery_address']) ?>

                                        </p>

                                    <?php endif; ?>


                                    <?php if (isset($order['created_at'])): ?>

                                        <p class="text-muted">

                                            <i class="fas fa-calendar"></i>

                                            <?= htmlspecialchars($order['created_at']) ?>

                                        </p>

                                    <?php endif; ?>


                                </div>


                                <div class="card-footer bg-white">

                                    <a href="deliveries.php"
                                       class="btn btn-outline-success btn-sm">

                                        <i class="fas fa-truck"></i>

                                        Track Delivery

                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>