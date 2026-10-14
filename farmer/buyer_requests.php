
<?php
session_start();

require_once "../config/database.php";

// Ensure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$requests = [];
$error = "";

try {
    // Use the actual column names from your database
    $sql = "
        SELECT
            id,
            product_name,
            quantity,
            unit,
            required_quality,
            preferred_location,
            maximum_price,
            required_delivery_date,
            description,
            status,
            created_at
        FROM buyer_requests
        WHERE LOWER(status) = 'open'
        ORDER BY created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Buyer requests error: " . $e->getMessage());
    $error = "Unable to load buyer requests. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Requests | Agro-Link Uganda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f7f5;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #198754;
        }

        .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .request-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.07);
            height: 100%;
        }

        .request-card h5 {
            color: #198754;
            font-weight: bold;
        }

        .request-detail {
            margin-bottom: 8px;
            overflow-wrap: anywhere;
        }

        .price {
            color: #198754;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand" href="my_produce.php">
            Agro-Link Uganda
        </a>

        <a href="../logout.php" class="btn btn-light btn-sm">
            Logout
        </a>
    </div>
</nav>

<div class="container pb-5">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="fw-bold">Buyer Requests</h2>
            <p class="text-muted mb-0">
                Find buyers looking for agricultural produce.
            </p>
        </div>

        <a href="my_produce.php" class="btn btn-outline-success">
            My Produce
        </a>
    </div>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif (empty($requests)): ?>
        <div class="alert alert-info">
            There are currently no open buyer requests.
            Please check again later.
        </div>
    <?php else: ?>

        <div class="row g-4">

            <?php foreach ($requests as $request): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card request-card">
                        <div class="card-body">

                            <span class="badge bg-success mb-2">
                                <?= htmlspecialchars($request["status"]) ?>
                            </span>

                            <h5>
                                <?= htmlspecialchars($request["product_name"]) ?>
                            </h5>

                            <div class="request-detail">
                                <strong>Quantity:</strong>
                                <?= htmlspecialchars((string)$request["quantity"]) ?>
                                <?= htmlspecialchars($request["unit"]) ?>
                            </div>

                            <div class="request-detail">
                                <strong>Quality:</strong>
                                <?= htmlspecialchars(
                                    $request["required_quality"] ?: "Not specified"
                                ) ?>
                            </div>

                            <div class="request-detail">
                                <strong>Delivery location:</strong>
                                <?= htmlspecialchars(
                                    $request["preferred_location"] ?: "Not specified"
                                ) ?>
                            </div>

                            <div class="request-detail">
                                <strong>Required by:</strong>
                                <?= htmlspecialchars(
                                    $request["required_delivery_date"] ?: "Not specified"
                                ) ?>
                            </div>

                            <div class="price mb-3">
                                Maximum price:
                                <?php if ($request["maximum_price"] !== null
                                    && $request["maximum_price"] !== ""): ?>
                                    UGX <?= number_format(
                                        (float)$request["maximum_price"]
                                    ) ?>
                                <?php else: ?>
                                    Not specified
                                <?php endif; ?>
                            </div>

                            <p class="text-muted">
                                <?= nl2br(htmlspecialchars(
                                    $request["description"] ?: "No additional details provided."
                                )) ?>
                            </p>

                            <small class="text-muted">
                                Request #<?= (int)$request["id"] ?>
                                &bull;
                                <?= htmlspecialchars($request["created_at"]) ?>
                            </small>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
