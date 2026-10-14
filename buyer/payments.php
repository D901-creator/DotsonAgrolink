
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
$demo_reference = "";

// Demo payment methods. No real money is transferred.
$methods = [
    "MTN Mobile Money",
    "Airtel Money",
    "Bank Transfer",
    "Visa / Mastercard"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $method = trim($_POST["payment_method"] ?? "");
    $phone = trim($_POST["phone_number"] ?? "");
    $amount_input = trim($_POST["amount"] ?? "");
    $order_input = trim($_POST["order_id"] ?? "");

    if (!in_array($method, $methods, true)) {
        $error = "Please select a valid payment method.";
    } elseif (!is_numeric($amount_input) || (float)$amount_input <= 0) {
        $error = "Enter a valid amount greater than zero.";
    } elseif (
        in_array($method, ["MTN Mobile Money", "Airtel Money"], true)
        && !preg_match('/^\+?[0-9]{9,15}$/', $phone)
    ) {
        $error = "Enter a valid mobile money phone number.";
    } else {
        try {
            /*
             * Demo only:
             * Amount is supplied by the buyer and is NOT verified
             * against an order. Do not use for real transactions.
             */
            $amount = (float)$amount_input;
            $order_id = null;

            if ($order_input !== "") {
                if (!ctype_digit($order_input)) {
                    throw new RuntimeException("Invalid order number.");
                }

                $check = $pdo->prepare(
                    "SELECT id FROM orders WHERE id = ? AND buyer_id = ? LIMIT 1"
                );
                $check->execute([(int)$order_input, $buyer_id]);
                $order = $check->fetch(PDO::FETCH_ASSOC);

                if (!$order) {
                    throw new RuntimeException(
                        "That order was not found for your account."
                    );
                }

                $order_id = (int)$order["id"];
            }

            $demo_reference = "AGRO-DEMO-" . strtoupper(bin2hex(random_bytes(5)));

            $stmt = $pdo->prepare("
                INSERT INTO payments
                    (buyer_id, order_id, transaction_ref, payment_method,
                     phone_number, amount, currency, status)
                VALUES
                    (:buyer_id, :order_id, :reference, :method,
                     :phone, :amount, 'UGX', 'pending')
            ");

            $stmt->execute([
                ":buyer_id" => $buyer_id,
                ":order_id" => $order_id,
                ":reference" => $demo_reference,
                ":method" => $method,
                ":phone" => $phone !== "" ? $phone : null,
                ":amount" => $amount
            ]);

            // Mark it successful only as an explicit demo simulation.
            if (isset($_POST["simulate_success"])) {
                $update = $pdo->prepare("
                    UPDATE payments
                    SET status = 'successful'
                    WHERE transaction_ref = ?
                      AND buyer_id = ?
                      AND status = 'pending'
                ");
                $update->execute([$demo_reference, $buyer_id]);
                $success = "Demo payment simulated successfully. No real money was transferred.";
            } else {
                $success = "Demo payment record created with pending status. No real money was transferred.";
            }

        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            error_log("Demo payment error: " . $e->getMessage());
            $error = "Could not save the payment. Check your payments table and orders table structure.";
        }
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT id, transaction_ref, payment_method, amount,
               currency, status, created_at
        FROM payments
        WHERE buyer_id = ?
        ORDER BY created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$buyer_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Payment history error: " . $e->getMessage());
    $payments = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payments | Agro-Link Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7f5; }
        .topbar { background:#198754; color:white; }
        .card { border:0; border-radius:14px; }
        .method-option {
            display:block; padding:14px; border:1px solid #dee2e6;
            border-radius:10px; cursor:pointer; height:100%;
        }
        .method-option:has(input:checked) {
            border:2px solid #198754; background:#f0fff5;
        }
        .method-option input { margin-right:8px; }
    </style>
</head>
<body>
<nav class="topbar py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <strong>🌱 Agro-Link Uganda</strong>
        <a href="dashboard.php" class="btn btn-light btn-sm">Buyer Dashboard</a>
    </div>
</nav>

<main class="container py-4">
    <h2 class="fw-bold">Payments</h2>
    <p class="text-muted">Choose how you would like to pay for your produce.</p>

    <div class="alert alert-warning">
        <strong>Hackathon demo mode:</strong> This page records simulated payments.
        It does not initiate real mobile-money, bank, or card transactions.
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
            <?php if ($demo_reference): ?>
                <br>Reference: <strong><?= htmlspecialchars($demo_reference) ?></strong>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Make a Demo Payment</h5>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Order ID (optional)</label>
                    <input type="number" min="1" name="order_id" class="form-control"
                           placeholder="Enter your existing order ID">
                    <div class="form-text">
                        Leave blank for a standalone demonstration record.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount (UGX)</label>
                    <input type="number" name="amount" min="1" step="1"
                           class="form-control" required placeholder="e.g. 50000">
                </div>

                <label class="form-label fw-semibold">Payment Method</label>
                <div class="row g-3 mb-3">
                    <?php foreach ($methods as $i => $method): ?>
                        <div class="col-md-6">
                            <label class="method-option">
                                <input type="radio" name="payment_method"
                                       value="<?= htmlspecialchars($method) ?>"
                                       <?= $i === 0 ? "checked" : "" ?>>
                                <strong><?= htmlspecialchars($method) ?></strong>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile Money Number</label>
                    <input type="tel" name="phone_number" class="form-control"
                           placeholder="e.g. 2567XXXXXXXX">
                    <div class="form-text">
                        Required for MTN Mobile Money or Airtel Money.
                        This demo does not charge the number.
                    </div>
                </div>

                <button type="submit" name="create_pending" value="1"
                        class="btn btn-outline-success">
                    Create Pending Demo Payment
                </button>

                <button type="submit" name="simulate_success" value="1"
                        class="btn btn-success">
                    Simulate Successful Demo Payment
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Payment History</h5>

            <?php if (!$payments): ?>
                <p class="text-muted mb-0">No payment records yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= htmlspecialchars($payment["transaction_ref"]) ?></td>
                                <td><?= htmlspecialchars($payment["payment_method"]) ?></td>
                                <td>UGX <?= number_format((float)$payment["amount"]) ?></td>
                                <td>
                                    <?php
                                    $status = strtolower($payment["status"]);
                                    $badge = $status === "successful"
                                        ? "bg-success"
                                        : ($status === "failed" ? "bg-danger"
                                        : ($status === "cancelled" ? "bg-secondary"
                                        : "bg-warning text-dark"));
                                    ?>
                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars(ucfirst($status)) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($payment["created_at"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>
