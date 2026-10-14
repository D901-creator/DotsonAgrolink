<?php
require_once "../config/database.php";

session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

// Total requests
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM buyer_requests 
    WHERE buyer_id = ?
");
$stmt->execute([$buyer_id]);
$total_requests = $stmt->fetchColumn();

// Pending requests
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM buyer_requests 
    WHERE buyer_id = ?
    AND status = 'open'
");
$stmt->execute([$buyer_id]);
$open_requests = $stmt->fetchColumn();

// Total offers received
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM offers 
    WHERE buyer_id = ?
");
$stmt->execute([$buyer_id]);
$total_offers = $stmt->fetchColumn();

// Total orders
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM orders 
    WHERE buyer_id = ?
");
$stmt->execute([$buyer_id]);
$total_orders = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buyer Dashboard | Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        body {
            background: #f4f7f5;
        }

        .sidebar {
            min-height: 100vh;
            background: #198754;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
        }

        .brand {
            padding: 20px;
            font-size: 21px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .content {
            padding: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e8f5e9;
            color: #198754;
            font-size: 20px;
        }

        .welcome {
            background: linear-gradient(
                135deg,
                #198754,
                #157347
            );
            color: white;
            border-radius: 18px;
            padding: 30px;
        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 sidebar p-0">

            <div class="brand">
                🌱 AGRO-LINK UGANDA
            </div>

            <div class="py-3">

                <a href="dashboard.php">
                    <i class="fa-solid fa-house me-2"></i>
                    Dashboard
                </a>

                <a href="find_produce.php">
                    <i class="fa-solid fa-magnifying-glass me-2"></i>
                    Find Produce
                </a>

                <a href="my_requests.php">
                    <i class="fa-solid fa-file-lines me-2"></i>
                    My Requests
                </a>

                <a href="matches.php">
                    <i class="fa-solid fa-link me-2"></i>
                    Farmer Matches
                </a>

                <a href="offers.php">
                    <i class="fa-solid fa-handshake me-2"></i>
                    Offers & Negotiations
                </a>

                <a href="orders.php">
                    <i class="fa-solid fa-cart-shopping me-2"></i>
                    Orders
                </a>

                <a href="deliveries.php">
                    <i class="fa-solid fa-truck me-2"></i>
                    Deliveries
                </a>

                <a href="market_prices.php">
                    <i class="fa-solid fa-chart-line me-2"></i>
                    Market Prices
                </a>

                <a href="ratings.php">
                    <i class="fa-solid fa-star me-2"></i>
                    Ratings
                </a>
<li class="nav-item">
    <a class="nav-link"
       href="../messages/list.php">

        <i class="fas fa-comments"></i>

        Messages

    </a>
</li>
                <a href="profile.php">
                    <i class="fa-solid fa-user me-2"></i>
                    Profile
                </a>

                <hr>

                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                    Logout
                </a>

            </div>

        </div>


        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 content">

            <!-- TOP BAR -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h4 class="fw-bold mb-1">
                        Buyer Dashboard
                    </h4>

                    <span class="text-muted">
                        Manage your agricultural purchases
                    </span>

                </div>

                <div>

                    <span class="badge bg-success p-2">
                        <i class="fa-solid fa-user"></i>
                        Buyer
                    </span>

                </div>

            </div>


            <!-- WELCOME -->

            <div class="welcome mb-4">

                <h2>
                    GOOD MORNING, BUYER 👋
                </h2>

                <p class="mb-0">
                    Find farmers, request produce, negotiate offers
                    and manage your agricultural orders.
                </p>

            </div>


            <!-- STATISTICS -->

            <div class="row g-4 mb-4">


                <!-- REQUESTS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card stat-card p-3">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    My Requests
                                </small>

                                <h3 class="fw-bold">
                                    <?= $total_requests ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="fa-solid fa-file-lines"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- OPEN REQUESTS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card stat-card p-3">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Open Requests
                                </small>

                                <h3 class="fw-bold">
                                    <?= $open_requests ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="fa-solid fa-bullhorn"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- OFFERS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card stat-card p-3">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Offers Received
                                </small>

                                <h3 class="fw-bold">
                                    <?= $total_offers ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="fa-solid fa-handshake"></i>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ORDERS -->

                <div class="col-md-6 col-xl-3">

                    <div class="card stat-card p-3">

                        <div class="d-flex justify-content-between">

                            <div>

                                <small class="text-muted">
                                    Orders
                                </small>

                                <h3 class="fw-bold">
                                    <?= $total_orders ?>
                                </h3>

                            </div>

                            <div class="stat-icon">

                                <i class="fa-solid fa-cart-shopping"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- QUICK ACTIONS -->

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Quick Actions
                    </h5>

                    <div class="row g-3">

                        <div class="col-md-4">

                            <a
                                href="find_produce.php"
                                class="btn btn-success w-100 p-3"
                            >

                                <i class="fa-solid fa-magnifying-glass me-2"></i>

                                Find Produce

                            </a>

                        </div>


                        <div class="col-md-4">

                            <a
                                href="my_requests.php"
                                class="btn btn-outline-success w-100 p-3"
                            >

                                <i class="fa-solid fa-plus me-2"></i>

                                Create Request

                            </a>

                        </div>


                        <div class="col-md-4">

                            <a
                                href="orders.php"
                                class="btn btn-outline-success w-100 p-3"
                            >

                                <i class="fa-solid fa-cart-shopping me-2"></i>

                                View Orders

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>