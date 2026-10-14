
<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$buyer_id = (int) $_SESSION["user_id"];
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_name = trim($_POST["produce_name"] ?? "");
    $quantity = trim($_POST["quantity"] ?? "");
    $unit = trim($_POST["unit"] ?? "");
    $maximum_price = trim($_POST["target_price"] ?? "");
    $preferred_location = trim($_POST["location"] ?? "");
    $required_quality = trim($_POST["quality_requirement"] ?? "");
    $required_date = trim($_POST["required_date"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (
        $product_name === "" ||
        $quantity === "" ||
        $unit === "" ||
        $preferred_location === ""
    ) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($quantity) || (float)$quantity <= 0) {
        $error = "Quantity must be a number greater than zero.";
    } elseif (
        $maximum_price !== "" &&
        (!is_numeric($maximum_price) || (float)$maximum_price < 0)
    ) {
        $error = "Please enter a valid target price.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO buyer_requests (
                    buyer_id,
                    product_name,
                    quantity,
                    unit,
                    required_quality,
                    preferred_location,
                    maximum_price,
                    required_delivery_date,
                    description,
                    status
                ) VALUES (
                    :buyer_id,
                    :product_name,
                    :quantity,
                    :unit,
                    :quality,
                    :location,
                    :price,
                    :required_date,
                    :description,
                    'open'
                )
            ");

            $stmt->execute([
                ":buyer_id" => $buyer_id,
                ":product_name" => $product_name,
                ":quantity" => (float)$quantity,
                ":unit" => $unit,
                ":quality" => $required_quality !== ""
                    ? $required_quality : null,
                ":location" => $preferred_location,
                ":price" => $maximum_price !== ""
                    ? (float)$maximum_price : null,
                ":required_date" => $required_date !== ""
                    ? $required_date : null,
                ":description" => $description !== ""
                    ? $description : null
            ]);

            // Prevent accidental duplicate submissions on refresh
            header("Location: my_requests.php?success=created");
            exit;

        } catch (PDOException $e) {
            error_log("Create buyer request: " . $e->getMessage());
            $error = "Could not save your request. Check the database configuration and try again.";
        }
    }
}

if (isset($_GET["success"]) && $_GET["success"] === "created") {
    $success = "Produce request created successfully.";
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM buyer_requests
        WHERE buyer_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$buyer_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Load buyer requests: " . $e->getMessage());
    $requests = [];
    $error = "Could not load your requests.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Requests | Agro-Link Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { background: #f4f7f5; }
        .sidebar { min-height: 100vh; background: #198754; color: white; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px 20px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,.15); }
        .brand { padding: 20px; font-size: 18px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,.2); }
        .content { padding: 30px; }
        .card { border: none; border-radius: 15px; }
        .section-title { color: #198754; font-weight: 700; }
        @media(max-width:767px) {
            .sidebar { min-height: auto; }
            .content { padding: 16px; }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 col-lg-2 sidebar p-0">
            <div class="brand">🌱 AGRO-LINK UGANDA</div>
            <nav class="py-3">
                <a href="dashboard.php"><i class="fa-solid fa-house me-2"></i>Dashboard</a>
                <a href="find_produce.php"><i class="fa-solid fa-magnifying-glass me-2"></i>Find Produce</a>
                <a href="my_requests.php" class="active"><i class="fa-solid fa-file-lines me-2"></i>My Requests</a>
                <a href="matches.php"><i class="fa-solid fa-link me-2"></i>Farmer Matches</a>
                <a href="offers.php"><i class="fa-solid fa-handshake me-2"></i>Offers & Negotiations</a>
                <a href="orders.php"><i class="fa-solid fa-cart-shopping me-2"></i>Orders</a>
                <a href="deliveries.php"><i class="fa-solid fa-truck me-2"></i>Deliveries</a>
                <a href="market_prices.php"><i class="fa-solid fa-chart-line me-2"></i>Market Prices</a>
                <a href="ratings.php"><i class="fa-solid fa-star me-2"></i>Ratings</a>
                <a href="profile.php"><i class="fa-solid fa-user me-2"></i>Profile</a>
                <hr>
                <a href="../logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
            </nav>
        </aside>

        <main class="col-md-9 col-lg-10 content">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">My Requests</h3>
                    <p class="text-muted mb-0">Create and manage your produce requests.</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline-success">
                    <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
                </a>
            </div>

            <?php if ($error !== ""): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success !== ""): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <section class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="section-title mb-4">
                        <i class="fa-solid fa-plus-circle me-2"></i>Create Produce Request
                    </h5>

                    <form method="POST" action="my_requests.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Produce Name *</label>
                                <input type="text" name="produce_name" class="form-control" placeholder="e.g. Maize" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Quantity *</label>
                                <input type="number" name="quantity" min="0.01" step="0.01" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Unit *</label>
                                <select name="unit" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="kg">Kg</option>
                                    <option value="bags">Bags</option>
                                    <option value="tons">Tons</option>
                                    <option value="pieces">Pieces</option>
                                    <option value="litres">Litres</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Maximum Price (UGX)</label>
                                <input type="number" name="target_price" min="0" step="0.01" class="form-control" placeholder="Optional">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Preferred Location *</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Kampala" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Quality Requirement</label>
                                <select name="quality_requirement" class="form-select">
                                    <option value="">Any quality</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Good">Good</option>
                                    <option value="Average">Average</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Required Delivery Date</label>
                                <input type="date" name="required_date" min="<?= date('Y-m-d') ?>" class="form-control">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Additional Details</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Describe what you are looking for..."></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Submit Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <section class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="section-title mb-4">
                        <i class="fa-solid fa-list me-2"></i>My Submitted Requests
                    </h5>

                    <?php if (count($requests) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produce</th>
                                        <th>Quantity</th>
                                        <th>Maximum Price</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($request["product_name"] ?? "") ?></td>
                                        <td><?= htmlspecialchars((string)($request["quantity"] ?? "")) ?> <?= htmlspecialchars($request["unit"] ?? "") ?></td>
                                        <td>
                                            <?php if (isset($request["maximum_price"]) && $request["maximum_price"] !== null && $request["maximum_price"] !== ""): ?>
                                                UGX <?= number_format((float)$request["maximum_price"]) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not specified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($request["preferred_location"] ?? "") ?></td>
                                        <td>
                                            <?php $status = strtolower($request["status"] ?? "open"); ?>
                                            <span class="badge <?= $status === "open" ? "bg-success" : ($status === "closed" ? "bg-secondary" : "bg-warning text-dark") ?>">
                                                <?= htmlspecialchars(ucfirst($status)) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($request["created_at"] ?? "") ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa-solid fa-file-circle-plus fs-1 text-muted mb-3"></i>
                            <h5>No requests yet</h5>
                            <p class="text-muted">Create your first produce request above.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</div>
</body>
</html>
