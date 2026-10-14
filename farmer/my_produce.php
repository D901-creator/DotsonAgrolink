
<?php
session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Agro-Link Uganda - My Produce
|--------------------------------------------------------------------------
| This page:
| 1. Displays the farmer's produce.
| 2. Processes the Add Produce form.
| 3. Uploads an optional produce image.
| 4. Saves the produce to MySQL.
|--------------------------------------------------------------------------
*/

// Use the logged-in user's ID.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$farmer_id = (int) $_SESSION['user_id'];

$success_message = "";
$error_message = "";

// Upload directory.
$upload_directory = __DIR__ . "/../uploads/produce/";
$upload_relative_path = "../uploads/produce/";

// Create upload directory if it does not exist.
if (!is_dir($upload_directory)) {
    if (!mkdir($upload_directory, 0755, true) && !is_dir($upload_directory)) {
        $error_message = "Unable to create the image upload directory.";
    }
}

// Process the form.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_produce"])) {
    $produce_name = trim($_POST["produce_name"] ?? "");
    $quantity = filter_var($_POST["quantity"] ?? "", FILTER_VALIDATE_FLOAT);
    $unit = trim($_POST["unit"] ?? "");
    $expected_price = filter_var($_POST["expected_price"] ?? "", FILTER_VALIDATE_FLOAT);
    $location = trim($_POST["location"] ?? "");
    $availability = trim($_POST["availability"] ?? "available");
    $description = trim($_POST["description"] ?? "");

    $allowed_units = ["kg", "bags", "tonnes", "litres", "pieces"];
    $allowed_availability = ["available", "pending"];

    if ($produce_name === "" || $location === "") {
        $error_message = "Please enter the produce name and location.";
    } elseif ($quantity === false || $quantity <= 0) {
        $error_message = "Please enter a valid quantity greater than zero.";
    } elseif ($expected_price === false || $expected_price < 0) {
        $error_message = "Please enter a valid expected price.";
    } elseif (!in_array($unit, $allowed_units, true)) {
        $error_message = "Please select a valid unit.";
    } elseif (!in_array($availability, $allowed_availability, true)) {
        $error_message = "Please select a valid availability status.";
    } else {
        $image_path = null;
        $uploaded_file_path = null;

        try {
            // Validate and upload an optional image.
            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {
                if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                    throw new RuntimeException("The image upload failed.");
                }

                if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
                    throw new RuntimeException("The image must be 5 MB or smaller.");
                }

                $temporary_file = $_FILES["image"]["tmp_name"];

                if (!is_uploaded_file($temporary_file)) {
                    throw new RuntimeException("Invalid uploaded image.");
                }

                $image_info = getimagesize($temporary_file);

                if ($image_info === false) {
                    throw new RuntimeException("Please upload a valid image.");
                }

                $mime_type = $image_info["mime"] ?? "";

                $allowed_images = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed_images[$mime_type])) {
                    throw new RuntimeException(
                        "Only JPG, PNG, and WebP images are allowed."
                    );
                }

                $filename = bin2hex(random_bytes(16))
                    . "." . $allowed_images[$mime_type];

                $destination = $upload_directory . $filename;

                if (!move_uploaded_file($temporary_file, $destination)) {
                    throw new RuntimeException("Unable to save the uploaded image.");
                }

                $image_path = $upload_relative_path . $filename;
                $uploaded_file_path = $destination;
            }

            /*
             * IMPORTANT:
             * This INSERT assumes your produce table contains an "image"
             * column. If your database uses a different image column name,
             * the query must be adjusted to match it.
             */
            $stmt = $pdo->prepare("
                INSERT INTO produce
                    (
                        farmer_id,
                        produce_name,
                        quantity,
                        unit,
                        expected_price,
                        location,
                        availability,
                        description,
                        image,
                        created_at
                    )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $farmer_id,
                $produce_name,
                $quantity,
                $unit,
                $expected_price,
                $location,
                $availability,
                $description,
                $image_path
            ]);

            // Prevent the form from being submitted twice on refresh.
            $_SESSION["produce_success"] = "Produce added successfully!";
            header("Location: my_produce.php");
            exit;

        } catch (Throwable $e) {
            // Remove an uploaded file if the database insert fails.
            if ($uploaded_file_path && file_exists($uploaded_file_path)) {
                unlink($uploaded_file_path);
            }

            error_log("Agro-Link add produce error: " . $e->getMessage());

            $error_message = "Unable to save your produce. Please check that "
                . "your database columns match this form. Details: "
                . $e->getMessage();
        }
    }
}

// Show success message after redirect.
if (isset($_SESSION["produce_success"])) {
    $success_message = $_SESSION["produce_success"];
    unset($_SESSION["produce_success"]);
}

// Fetch the current farmer's produce.
$stmt = $pdo->prepare("
    SELECT *
    FROM produce
    WHERE farmer_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$farmer_id]);
$produce = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h($value): string
{
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Produce | Agro-Link Uganda</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f7f5;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: #176b3a;
            color: #fff;
            padding: 25px 15px;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 11px 13px;
            margin-bottom: 5px;
            border-radius: 6px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #23894e;
        }

        .brand {
            font-size: 21px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .main-content {
            padding: 28px;
        }

        .content-card {
            background: #fff;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        }

        .btn-agro {
            background: #198754;
            color: #fff;
        }

        .btn-agro:hover {
            background: #146c43;
            color: #fff;
        }

        .table th {
            white-space: nowrap;
        }

        .produce-image {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 7px;
        }

        @media (max-width: 767px) {
            .sidebar {
                min-height: auto;
            }

            .main-content {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <aside class="col-md-3 col-lg-2 sidebar">
            <div class="brand">
                <i class="fa-solid fa-leaf"></i> Agro-Link Uganda
            </div>

            <a href="dashboard.php">
                <i class="fa-solid fa-gauge me-2"></i> Dashboard
            </a>

            <a href="my_produce.php" class="active">
                <i class="fa-solid fa-seedling me-2"></i> My Produce
            </a>

            <a href="buyer_requests.php">
                <i class="fa-solid fa-clipboard-list me-2"></i> Buyer Requests
            </a>

            <a href="matches.php">
                <i class="fa-solid fa-handshake me-2"></i> Matches
            </a>

            <a href="../logout.php">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
            </a>
        </aside>

        <main class="col-md-9 col-lg-10 main-content">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="mb-1">My Produce</h2>
                    <p class="text-muted mb-0">
                        Add, manage and view your agricultural produce.
                    </p>
                </div>

                <button class="btn btn-agro" data-bs-toggle="modal"
                        data-bs-target="#addProduceModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Produce
                </button>
            </div>

            <?php if ($success_message !== ""): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= h($success_message) ?>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_message !== ""): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= h($error_message) ?>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="content-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Produce</th>
                            <th>Quantity</th>
                            <th>Expected Price</th>
                            <th>Location</th>
                            <th>Harvest Date</th>
                            <th>Availability</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($produce)): ?>
                            <?php foreach ($produce as $item): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($item["image"])): ?>
                                            <img class="produce-image"
                                                 src="<?= h($item["image"]) ?>"
                                                 alt="<?= h($item["produce_name"]) ?>">
                                        <?php else: ?>
                                            <i class="fa-solid fa-seedling fa-2x text-success"></i>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <strong><?= h($item["produce_name"]) ?></strong>
                                        <div class="small text-muted">
                                            <?= h($item["description"] ?? "") ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?= h($item["quantity"]) ?>
                                        <?= h($item["unit"]) ?>
                                    </td>

                                    <td>
                                        UGX <?= number_format((float) $item["expected_price"]) ?>
                                    </td>

                                    <td><?= h($item["location"]) ?></td>

                                    <td>
                                        <?= h($item["expected_harvest_date"] ?? "Not specified") ?>
                                    </td>

                                    <td>
                                        <?php
                                        $status = strtolower($item["availability"] ?? "pending");
                                        $badge_class = $status === "available"
                                            ? "bg-success"
                                            : ($status === "sold" ? "bg-secondary" : "bg-warning text-dark");
                                        ?>
                                        <span class="badge <?= h($badge_class) ?>">
                                            <?= h(ucfirst($status)) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fa-solid fa-seedling fa-3x text-success mb-3"></i>
                                    <h5>No produce listed yet</h5>
                                    <p class="text-muted">
                                        Click Add Produce to list your first product.
                                    </p>
                                    
<a href="add_produce.php" class="btn btn-success">
    <i class="fa-solid fa-plus me-1"></i>
    Add Produce
</a>

                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Add Produce Modal -->
<div class="modal fade" id="addProduceModal" tabindex="-1"
     aria-labelledby="addProduceModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form method="POST" enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title" id="addProduceModalLabel">
                        <i class="fa-solid fa-seedling text-success me-2"></i>
                        Add New Produce
                    </h5>

                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Produce Name *</label>
                            <input type="text" name="produce_name"
                                   class="form-control" required
                                   placeholder="e.g. Maize">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Produce Image</label>
                            <input type="file" name="image"
                                   class="form-control"
                                   accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted">Maximum size: 5 MB</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Quantity *</label>
                            <input type="number" name="quantity"
                                   class="form-control" min="0.01"
                                   step="0.01" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Unit *</label>
                            <select name="unit" class="form-select" required>
                                <option value="">Select unit</option>
                                <option value="kg">Kilograms (kg)</option>
                                <option value="bags">Bags</option>
                                <option value="tonnes">Tonnes</option>
                                <option value="litres">Litres</option>
                                <option value="pieces">Pieces</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Expected Price (UGX) *</label>
                            <input type="number" name="expected_price"
                                   class="form-control" min="0"
                                   step="1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location"
                                   class="form-control" required
                                   placeholder="District or village">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Availability *</label>
                            <select name="availability" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="pending">Coming soon / Pending</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"
                                      rows="3"
                                      placeholder="Describe your produce, quality or packaging"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>

                    <button type="submit" name="add_produce"
                            value="1" class="btn btn-agro">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Save Produce
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
