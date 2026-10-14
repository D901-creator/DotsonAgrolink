<?php

require_once "../config/database.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get buyer information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([$buyer_id]);

$buyer = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Profile - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
               class="btn btn-outline-light w-100 mb-2 text-start">
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
               class="btn btn-success w-100 mb-2 text-start">
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

            <div class="mb-4">

                <h2>
                    <i class="fas fa-user text-success"></i>
                    My Profile
                </h2>

                <p class="text-muted">
                    View your Agro-Link Uganda buyer account information.
                </p>

            </div>


            <?php if (!$buyer): ?>

                <div class="alert alert-danger">

                    <i class="fas fa-exclamation-circle"></i>

                    Buyer account could not be found.

                </div>

            <?php else: ?>

                <div class="row">

                    <!-- PROFILE CARD -->
                    <div class="col-md-4 mb-4">

                        <div class="card shadow-sm text-center">

                            <div class="card-body">

                                <div class="mb-3">

                                    <i class="fas fa-user-circle fa-6x text-success"></i>

                                </div>

                                <h4>

                                    <?= htmlspecialchars(
                                        $buyer['username'] ?? 'Buyer'
                                    ) ?>

                                </h4>

                                <span class="badge bg-success">

                                    Buyer

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- INFORMATION -->
                    <div class="col-md-8">

                        <div class="card shadow-sm">

                            <div class="card-header bg-success text-white">

                                <i class="fas fa-id-card"></i>

                                Account Information

                            </div>

                            <div class="card-body">

                                <div class="mb-3">

                                    <label class="fw-bold">
                                        Username
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        value="<?= htmlspecialchars(
                                            $buyer['username'] ?? ''
                                        ) ?>"
                                        readonly
                                    >

                                </div>


                                <?php if (isset($buyer['phone'])): ?>

                                    <div class="mb-3">

                                        <label class="fw-bold">
                                            Phone Number
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $buyer['phone']
                                            ) ?>"
                                            readonly
                                        >

                                    </div>

                                <?php endif; ?>


                                <?php if (isset($buyer['role'])): ?>

                                    <div class="mb-3">

                                        <label class="fw-bold">
                                            Account Role
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $buyer['role']
                                            ) ?>"
                                            readonly
                                        >

                                    </div>

                                <?php endif; ?>


                                <?php if (isset($buyer['created_at'])): ?>

                                    <div class="mb-3">

                                        <label class="fw-bold">
                                            Account Created
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $buyer['created_at']
                                            ) ?>"
                                            readonly
                                        >

                                    </div>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

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