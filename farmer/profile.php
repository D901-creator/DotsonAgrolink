<?php
require_once "../config/database.php";

$farmer_id = 1;

$message = "";
$message_type = "success";

// Get farmer information
$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([$farmer_id]);
$farmer = $stmt->fetch(PDO::FETCH_ASSOC);
/*
|--------------------------------------------------------------------------
| Get farm information
|--------------------------------------------------------------------------
*/
$farm_stmt = $pdo->prepare("
    SELECT *
    FROM farm_information
    WHERE farmer_id = ?
    LIMIT 1
");

$farm_stmt->execute([$farmer_id]);

$farm = $farm_stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /*
     * We only update fields that we already know exist
     * from the users table.
     *
     * Username and phone are commonly used by the system.
     */

    $username = trim($_POST["username"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($username === "") {

        $message = "Username cannot be empty.";
        $message_type = "danger";

    } else {

        // Update username
        $update = $pdo->prepare("
            UPDATE users
            SET username = ?
            WHERE id = ?
        ");

        $update->execute([
            $username,
            $farmer_id
        ]);

        /*
         * Update phone only if the column exists in the current
         * user record.
         */
        if (array_key_exists("phone", $farmer)) {

            $phone_update = $pdo->prepare("
                UPDATE users
                SET phone = ?
                WHERE id = ?
            ");

            $phone_update->execute([
                $phone,
                $farmer_id
            ]);
        }

        $message = "Profile updated successfully.";

        // Reload farmer information
        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE id = ?
        ");

        $stmt->execute([$farmer_id]);

        $farmer = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Farmer Profile | Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 bg-success text-white min-vh-100 p-3">

            <h4 class="mb-4">

                <i class="fa-solid fa-seedling"></i>

                Agro-Link

            </h4>

            <hr>

            <a href="dashboard.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-house"></i>

                Dashboard

            </a>

            <a href="my_produce.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-wheat-awn"></i>

                My Produce

            </a>

            <a href="buyer_requests.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-list"></i>

                Buyer Requests

            </a>

            <a href="matches.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-link"></i>

                Matches

            </a>

            <a href="offers.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-handshake"></i>

                Offers

            </a>

            <a href="orders.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-cart-shopping"></i>

                Orders

            </a>

            <a href="delivery.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-truck"></i>

                Delivery

            </a>

            <a href="market_prices.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-chart-line"></i>

                Market Prices

            </a>

            <a href="sales_history.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-chart-column"></i>

                Sales History

            </a>

            <a href="notifications.php"
               class="text-white text-decoration-none d-block mb-3">

                <i class="fa-solid fa-bell"></i>

                Notifications

            </a>

            <a href="profile.php"
               class="text-white text-decoration-none d-block mb-3 fw-bold">

                <i class="fa-solid fa-user"></i>

                Profile

            </a>

            <hr>

            <a href="../index.php"
               class="text-white text-decoration-none d-block">

                <i class="fa-solid fa-right-from-bracket"></i>

                Back to Home

            </a>

        </div>


        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 p-4">

            <div class="mb-4">

                <h2>

                    <i class="fa-solid fa-user text-success"></i>

                    Farmer Profile

                </h2>

                <p class="text-muted">

                    View and update your Agro-Link account information.

                </p>

            </div>


            <?php if ($message !== ""): ?>

                <div class="alert alert-<?= htmlspecialchars($message_type) ?>">

                    <i class="fa-solid fa-circle-info"></i>

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>


            <div class="row">

                <!-- PROFILE CARD -->

                <div class="col-lg-4 mb-4">

                    <div class="card shadow-sm border-0">

                        <div class="card-body text-center py-4">

                            <div
                                class="bg-success text-white rounded-circle mx-auto mb-3
                                       d-flex align-items-center justify-content-center"
                                style="width:100px;height:100px;">

                                <i class="fa-solid fa-user fa-3x"></i>

                            </div>

                            <h4>

                                <?= htmlspecialchars(
                                    $farmer["username"] ?? "Farmer"
                                ) ?>

                            </h4>

                            <span class="badge bg-success">

                                Farmer

                            </span>

                        </div>

                    </div>

                </div>


                <!-- EDIT PROFILE -->

                <div class="col-lg-8">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h5 class="mb-4">

                                <i class="fa-solid fa-user-pen"></i>

                                Profile Information

                            </h5>


                            <form method="POST">


                                <!-- USERNAME -->

                                <div class="mb-3">

                                    <label class="form-label">

                                        Username

                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">

                                            <i class="fa-solid fa-user"></i>

                                        </span>

                                        <input
                                            type="text"
                                            name="username"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $farmer["username"] ?? ""
                                            ) ?>"
                                            required>

                                    </div>

                                </div>

<?php if ($farm): ?>

    <div class="card shadow-sm mt-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">

                <i class="fas fa-tractor"></i>

                Farm Information

            </h5>

        </div>

        <div class="card-body">

            <h4 class="text-success">

                <?= htmlspecialchars($farm['farm_name']) ?>

            </h4>


            <?php if (!empty($farm['location'])): ?>

                <p>
                    <i class="fas fa-location-dot text-success"></i>

                    <strong>Location:</strong>

                    <?= htmlspecialchars($farm['location']) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['district'])): ?>

                <p>
                    <i class="fas fa-map-marker-alt text-success"></i>

                    <strong>District:</strong>

                    <?= htmlspecialchars($farm['district']) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['village'])): ?>

                <p>

                    <i class="fas fa-home text-success"></i>

                    <strong>Village:</strong>

                    <?= htmlspecialchars($farm['village']) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['farm_size'])): ?>

                <p>

                    <i class="fas fa-ruler-combined text-success"></i>

                    <strong>Farm Size:</strong>

                    <?= htmlspecialchars($farm['farm_size']) ?>

                    <?= htmlspecialchars($farm['land_unit']) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['main_crops'])): ?>

                <p>

                    <i class="fas fa-seedling text-success"></i>

                    <strong>Main Crops:</strong>

                    <?= htmlspecialchars($farm['main_crops']) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['farming_type'])): ?>

                <p>

                    <i class="fas fa-leaf text-success"></i>

                    <strong>Farming Type:</strong>

                    <?= htmlspecialchars($farm['farming_type']) ?>

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['years_farming'])): ?>

                <p>

                    <i class="fas fa-calendar text-success"></i>

                    <strong>Years of Farming:</strong>

                    <?= htmlspecialchars($farm['years_farming']) ?>

                    years

                </p>

            <?php endif; ?>


            <?php if (!empty($farm['description'])): ?>

                <hr>

                <h6 class="fw-bold">

                    About the Farm

                </h6>

                <p class="text-muted">

                    <?= nl2br(
                        htmlspecialchars($farm['description'])
                    ) ?>

                </p>

            <?php endif; ?>

        </div>

    </div>

<?php else: ?>

    <div class="alert alert-info mt-4">

        <i class="fas fa-info-circle"></i>

        This farmer has not added farm information yet.

    </div>

<?php endif; ?>
                                <!-- PHONE -->

                                <?php if (array_key_exists("phone", $farmer)): ?>

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Phone Number

                                        </label>

                                        <div class="input-group">

                                            <span class="input-group-text">

                                                <i class="fa-solid fa-phone"></i>

                                            </span>

                                            <input
                                                type="text"
                                                name="phone"
                                                class="form-control"
                                                value="<?= htmlspecialchars(
                                                    $farmer["phone"] ?? ""
                                                ) ?>">

                                        </div>

                                    </div>

                                <?php endif; ?>


                                <!-- USER ID -->

                                <div class="mb-3">

                                    <label class="form-label">

                                        User ID

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        value="<?= htmlspecialchars(
                                            $farmer["id"] ?? ""
                                        ) ?>"
                                        readonly>

                                </div>


                                <!-- ROLE -->

                                <?php if (array_key_exists("role", $farmer)): ?>

                                    <div class="mb-3">

                                        <label class="form-label">

                                            Account Role

                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            value="<?= htmlspecialchars(
                                                $farmer["role"] ?? ""
                                            ) ?>"
                                            readonly>

                                    </div>

                                <?php endif; ?>


                                <button
                                    type="submit"
                                    class="btn btn-success">

                                    <i class="fa-solid fa-floppy-disk"></i>

                                    Save Changes

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>
