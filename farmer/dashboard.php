<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Check whether a user is logged in
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit;
}

$farmer_id = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Confirm the logged-in user is a farmer
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

    session_destroy();

    header("Location: ../login.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Unread messages
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM messages m
    INNER JOIN conversations c
        ON m.conversation_id = c.id
    WHERE
        (
            c.user_one_id = ?
            OR c.user_two_id = ?
        )
        AND m.sender_id != ?
        AND m.is_read = 0
");

$stmt->execute([
    $farmer_id,
    $farmer_id,
    $farmer_id
]);

$stmt->execute([
    $farmer_id,
    $farmer_id,
    $farmer_id
]);

$unread_messages = (int) $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Farmer Dashboard | Agro-Link Uganda</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Agro-Link CSS -->
    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>


<!-- TOP NAVBAR -->

<nav class="navbar navbar-dark bg-success">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="../index.php">

            🌱 Agro-Link Uganda

        </a>


        <div class="d-flex align-items-center text-white">

            <span class="me-3">

                <i class="fa-solid fa-bell"></i>

                <span class="badge bg-danger">3</span>

            </span>


            <span>

                <i class="fa-solid fa-user-circle"></i>

                John Doe

            </span>

        </div>

    </div>

</nav>



<div class="container-fluid">

    <div class="row">


        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h6 class="text-uppercase text-secondary">
                Farmer Menu
            </h6>


            <hr>


            <ul class="nav nav-pills flex-column gap-2">


                <li class="nav-item">

                    <a href="dashboard.php"
                       class="nav-link active">

                        <i class="fa-solid fa-house me-2"></i>

                        Dashboard

                    </a>

                </li>


                <li class="nav-item">

                    <a href="#"
                       class="nav-link text-white">

                        <i class="fa-solid fa-seedling me-2"></i>

                        My Produce

                    </a>

                </li>
                <li class="nav-item">
    <a class="nav-link"
       href="farm_information.php">

        <i class="fas fa-tractor"></i>

        Farm Information

    </a>
</li>


                <li class="nav-item">

                    <a href="buyer_requests.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-bullhorn me-2"></i>

                        Buyer Requests

                    </a>

                </li>


                <li class="nav-item">

                    <a href="matches.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-link me-2"></i>

                        Matches

                    </a>

                </li>


                <li class="nav-item">

                    <a href="#"
                       class="nav-link text-white">

                        <i class="fa-solid fa-handshake me-2"></i>

                        Offers

                    </a>

                </li>


                <li class="nav-item">

                    <a href="#"
                       class="nav-link text-white">

                        <i class="fa-solid fa-cart-shopping me-2"></i>

                        Orders

                    </a>

                </li>


                <li class="nav-item">

                    <a href="delivery.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-truck me-2"></i>

                        Delivery

                    </a>

                </li>


                <li class="nav-item">

                    <a href="market_prices.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-chart-line me-2"></i>

                        Market Prices

                    </a>

                </li>


                <li class="nav-item">

                    <a href="sales_history.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-money-bill-trend-up me-2"></i>

                        Sales History

                    </a>

                </li>


                <li class="nav-item">

                    <a href="notifications.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-bell me-2"></i>

                        Notifications

                    <li class="nav-item">

    <a href="../messages/list.php"
       class="nav-link text-white">

        <i class="fas fa-comments me-2"></i>

        Messages

        <?php if ($unread_messages > 0): ?>

            <span class="badge bg-danger ms-2">
                <?= $unread_messages ?>
            </span>

        <?php endif; ?>

    </a>

</li>
                </li>


                <li class="nav-item">

                    <a href="profile.php"
                       class="nav-link text-white">

                        <i class="fa-solid fa-user me-2"></i>

                        Profile

                    </a>

                </li>


                <li class="nav-item mt-3">

                    <a href="../index.php"
                       class="nav-link text-danger">

                        <i class="fa-solid fa-right-from-bracket me-2"></i>

                        Logout

                    </a>

                </li>


            </ul>

        </div>



        <!-- MAIN CONTENT -->

        <main class="col-md-9 col-lg-10 p-4 bg-light">


            <!-- PAGE HEADER -->

            <div class="d-flex justify-content-between
                        align-items-center mb-4">

                <div>

                    <h2 class="fw-bold">

                        Good Morning, John 👋

                    </h2>

                    <p class="text-muted">

                        Welcome to your Agro-Link farmer dashboard.

                    </p>

                </div>


                <a href="#"
                   class="btn btn-success">

                    <i class="fa-solid fa-plus"></i>

                    Add Produce

                </a>

            </div>



            <!-- STATISTICS -->

            <div class="row g-4 mb-4">


                <!-- Produce -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        My Produce
                                    </p>

                                    <h3 class="fw-bold">
                                        12
                                    </h3>

                                </div>

                                <div class="text-success fs-1">

                                    <i class="fa-solid fa-seedling"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Buyer Requests -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        Buyer Requests
                                    </p>

                                    <h3 class="fw-bold">
                                        8
                                    </h3>

                                </div>

                                <div class="text-primary fs-1">

                                    <i class="fa-solid fa-bullhorn"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Orders -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        Active Orders
                                    </p>

                                    <h3 class="fw-bold">
                                        5
                                    </h3>

                                </div>

                                <div class="text-warning fs-1">

                                    <i class="fa-solid fa-cart-shopping"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Sales -->

                <div class="col-sm-6 col-xl-3">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex
                                        justify-content-between">

                                <div>

                                    <p class="text-muted mb-1">
                                        Total Sales
                                    </p>

                                    <h3 class="fw-bold">
                                        UGX 2.4M
                                    </h3>

                                </div>

                                <div class="text-success fs-1">

                                    <i class="fa-solid fa-money-bill-wave"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


            </div>



            <!-- DASHBOARD CONTENT -->

            <div class="row g-4">


                <!-- Recent Produce -->

                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-white">

                            <h5 class="mb-0 fw-bold">

                                My Recent Produce

                            </h5>

                        </div>


                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table
                                              table-hover
                                              align-middle">

                                    <thead>

                                        <tr>

                                            <th>Produce</th>

                                            <th>Quantity</th>

                                            <th>Price</th>

                                            <th>Status</th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr>

                                            <td>
                                                Maize
                                            </td>

                                            <td>
                                                500 kg
                                            </td>

                                            <td>
                                                UGX 1,200/kg
                                            </td>

                                            <td>

                                                <span class="badge bg-success">
                                                    Available
                                                </span>

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                Beans
                                            </td>

                                            <td>
                                                300 kg
                                            </td>

                                            <td>
                                                UGX 3,500/kg
                                            </td>

                                            <td>

                                                <span class="badge bg-success">
                                                    Available
                                                </span>

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                Coffee
                                            </td>

                                            <td>
                                                150 kg
                                            </td>

                                            <td>
                                                UGX 8,000/kg
                                            </td>

                                            <td>

                                                <span class="badge bg-warning text-dark">
                                                    Pending
                                                </span>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Notifications -->

                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-white">

                            <h5 class="mb-0 fw-bold">

                                Notifications

                            </h5>

                        </div>


                        <div class="card-body">


                            <div class="alert alert-success">

                                <i class="fa-solid fa-user-check"></i>

                                A buyer is interested in your maize.

                            </div>


                            <div class="alert alert-info">

                                <i class="fa-solid fa-tag"></i>

                                New market price update available.

                            </div>


                            <div class="alert alert-warning">

                                <i class="fa-solid fa-truck"></i>

                                Delivery update received.

                            </div>


                        </div>

                    </div>

                </div>


            </div>



            <!-- MARKET PRICES -->

            <div class="card border-0 shadow-sm mt-4">

                <div class="card-header bg-white">

                    <h5 class="fw-bold mb-0">

                        Current Market Prices

                    </h5>

                </div>


                <div class="card-body">

                    <div class="row g-3">


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Maize
                                </small>

                                <h5 class="fw-bold">
                                    UGX 1,200/kg
                                </h5>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Beans
                                </small>

                                <h5 class="fw-bold">
                                    UGX 3,500/kg
                                </h5>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Coffee
                                </small>

                                <h5 class="fw-bold">
                                    UGX 8,000/kg
                                </h5>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Rice
                                </small>

                                <h5 class="fw-bold">
                                    UGX 3,000/kg
                                </h5>

                            </div>

                        </div>


                    </div>

                </div>

            </div>


        </main>

    </div>

</div>



<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>