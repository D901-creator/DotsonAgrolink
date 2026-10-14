<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION['user_id'];

$search = trim($_GET['search'] ?? '');
$location = trim($_GET['location'] ?? '');

$sql = "
    SELECT *
    FROM produce
    WHERE availability = 'available'
";

$params = [];

if ($search !== '') {
    $sql .= " AND produce_name LIKE ?";
    $params[] = "%$search%";
}

if ($location !== '') {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$produce = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Produce | Agro-Link Uganda</title>

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

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255,255,255,0.15);
        }

        .brand {
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .content {
            padding: 30px;
        }

        .produce-card {
            border: none;
            border-radius: 15px;
            transition: 0.2s;
        }

        .produce-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .price {
            color: #198754;
            font-size: 20px;
            font-weight: bold;
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

                <a href="find_produce.php" class="active">
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

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="fw-bold mb-1">
                        Find Produce
                    </h3>

                    <p class="text-muted mb-0">
                        Find agricultural produce from farmers.
                    </p>

                </div>

                <a href="dashboard.php" class="btn btn-outline-success">
                    <i class="fa-solid fa-arrow-left"></i>
                    Dashboard
                </a>

            </div>


            <!-- SEARCH -->

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <form method="GET">

                        <div class="row g-3">

                            <div class="col-md-5">

                                <label class="form-label fw-semibold">
                                    Produce
                                </label>

                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="e.g. Maize, Beans, Coffee"
                                    value="<?= htmlspecialchars($search) ?>"
                                >

                            </div>

                            <div class="col-md-5">

                                <label class="form-label fw-semibold">
                                    Location
                                </label>

                                <input
                                    type="text"
                                    name="location"
                                    class="form-control"
                                    placeholder="e.g. Kampala, Mukono"
                                    value="<?= htmlspecialchars($location) ?>"
                                >

                            </div>

                            <div class="col-md-2 d-flex align-items-end">

                                <button
                                    type="submit"
                                    class="btn btn-success w-100"
                                >
                                    <i class="fa-solid fa-search"></i>
                                    Search
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- RESULTS -->

            <div class="row g-4">

                <?php if (count($produce) > 0): ?>

                    <?php foreach ($produce as $item): ?>

                        <div class="col-md-6 col-lg-4">

                            <div class="card produce-card shadow-sm h-100">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between mb-3">

                                        <span class="badge bg-success">
                                            Available
                                        </span>

                                        <i class="fa-solid fa-seedling text-success fs-4"></i>

                                    </div>

                                    <h5 class="fw-bold">

                                        <?= htmlspecialchars($item['produce_name']) ?>

                                    </h5>
<a href="farmer_profile.php?farmer_id=<?= (int)$item['farmer_id'] ?>"
   class="btn btn-outline-success w-100">

    <i class="fas fa-user"></i>

    View Farmer

</a>
                                    <p class="text-muted mb-2">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <?= htmlspecialchars($item['location']) ?>

                                    </p>

                                    <p class="mb-2">

                                        <strong>Quantity:</strong>

                                        <?= htmlspecialchars($item['quantity']) ?>

                                        <?= htmlspecialchars($item['unit']) ?>

                                    </p>

                                    <p class="price mb-3">

                                        UGX
                                        <?= number_format((float)$item['expected_price']) ?>

                                        <small class="text-muted fs-6">
                                            / <?= htmlspecialchars($item['unit']) ?>
                                        </small>

                                    </p>

                                    <?php if (!empty($item['quality_grade'])): ?>

                                        <p class="mb-3">

                                            <strong>Quality:</strong>

                                            <?= htmlspecialchars($item['quality_grade']) ?>

                                        </p>

                                    <?php endif; ?>

                                    <?php if (!empty($item['description'])): ?>

                                        <p class="text-muted">

                                            <?= htmlspecialchars($item['description']) ?>

                                        </p>

                                    <?php endif; ?>

                                    <button
                                        class="btn btn-success w-100"
                                        type="button"
                                    >
                                        <i class="fa-solid fa-handshake me-1"></i>
                                        Contact Farmer
                                    </button>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12">

                        <div class="alert alert-info text-center">

                            <i class="fa-solid fa-circle-info me-2"></i>

                            No available produce found.

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>