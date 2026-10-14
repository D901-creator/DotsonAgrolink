
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/database.php";

$error = "";
$success = "";

// Require a logged-in user.
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$farmer_id = (int) $_SESSION["user_id"];

// Escape output safely.
function e($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Keep form values when validation fails.
$produce_name = "";
$quantity = "";
$unit = "";
$quality_grade = "";
$expected_price = "";
$location = "";
$available_date = "";
$expected_harvest_date = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $produce_name = trim($_POST["produce_name"] ?? "");
    $quantity = trim($_POST["quantity"] ?? "");
    $unit = trim($_POST["unit"] ?? "");
    $quality_grade = trim($_POST["quality_grade"] ?? "");
    $expected_price = trim($_POST["expected_price"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $available_date = trim($_POST["available_date"] ?? "");
    $expected_harvest_date = trim(
        $_POST["expected_harvest_date"] ?? ""
    );
    $description = trim($_POST["description"] ?? "");

    $allowed_units = ["kg", "bags", "tons", "pieces", "litres"];
    $allowed_grades = ["Excellent", "Good", "Average"];

    // Validate required fields.
    if (
        $produce_name === "" ||
        $quantity === "" ||
        $unit === "" ||
        $expected_price === "" ||
        $location === ""
    ) {
        $error = "Please fill in all required fields.";

    } elseif (!is_numeric($quantity) || (float)$quantity <= 0) {
        $error = "Quantity must be a number greater than zero.";

    } elseif (
        !is_numeric($expected_price) ||
        (float)$expected_price < 0
    ) {
        $error = "Enter a valid expected price.";

    } elseif (!in_array($unit, $allowed_units, true)) {
        $error = "Please select a valid unit.";

    } elseif (
        $quality_grade !== "" &&
        !in_array($quality_grade, $allowed_grades, true)
    ) {
        $error = "Please select a valid quality grade.";

    } else {
        // Validate optional dates.
        $date_pattern = '/^\d{4}-\d{2}-\d{2}$/';

        foreach (
            [
                "Available date" => $available_date,
                "Expected harvest date" => $expected_harvest_date
            ] as $label => $date
        ) {
            if ($date !== "") {
                $parsed_date = DateTime::createFromFormat(
                    "!Y-m-d",
                    $date
                );

                if (
                    !preg_match($date_pattern, $date) ||
                    !$parsed_date ||
                    $parsed_date->format("Y-m-d") !== $date
                ) {
                    $error = $label . " is invalid.";
                    break;
                }
            }
        }

        if ($error === "") {
            try {
                /*
                 * The SQL has 10 placeholders.
                 * The execute array below also supplies 10 values.
                 *
                 * These columns are based on your original file.
                 */
                $stmt = $pdo->prepare("
                    INSERT INTO produce (
                        farmer_id,
                        produce_name,
                        quantity,
                        unit,
                        quality_grade,
                        expected_price,
                        location,
                        available_date,
                        expected_harvest_date,
                        description,
                        status,
                        availability
                    )
                    VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                        'active',
                        'available'
                    )
                ");

                $stmt->execute([
                    $farmer_id,
                    $produce_name,
                    (float)$quantity,
                    $unit,
                    $quality_grade !== ""
                        ? $quality_grade
                        : null,
                    (float)$expected_price,
                    $location,
                    $available_date !== ""
                        ? $available_date
                        : null,
                    $expected_harvest_date !== ""
                        ? $expected_harvest_date
                        : null,
                    $description !== ""
                        ? $description
                        : null
                ]);

                $_SESSION["produce_success"] =
                    "Produce added successfully!";

                header("Location: my_produce.php?success=added");
                exit;

            } catch (PDOException $ex) {
                error_log(
                    "Agro-Link add produce error: " . $ex->getMessage()
                );

                $error = "The produce could not be saved. "
                    . "Please check that the produce table contains "
                    . "the required database columns.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Produce | Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            background: #f4f7f5;
            font-family: Arial, sans-serif;
        }

        .form-card {
            max-width: 850px;
            margin: 40px auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .page-title {
            color: #198754;
            font-weight: 700;
        }

        .required {
            color: red;
        }

        @media (max-width: 576px) {
            .form-card {
                margin: 15px auto;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="form-card">

        <div class="d-flex flex-wrap justify-content-between
                    align-items-center gap-3 mb-4">

            <div>
                <h2 class="page-title">
                    <i class="fa-solid fa-seedling"></i>
                    Add Produce
                </h2>

                <p class="text-muted mb-0">
                    Add agricultural produce you want to sell.
                </p>
            </div>

            <a href="my_produce.php" class="btn btn-outline-success">
                <i class="fa-solid fa-arrow-left"></i>
                My Produce
            </a>
        </div>

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="add_produce.php">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Produce Name <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="produce_name"
                        class="form-control"
                        placeholder="e.g. Maize"
                        value="<?= e($produce_name) ?>"
                        maxlength="150"
                        required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        Quantity <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        class="form-control"
                        min="0.01"
                        step="0.01"
                        placeholder="e.g. 100"
                        value="<?= e($quantity) ?>"
                        required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        Unit <span class="required">*</span>
                    </label>

                    <select name="unit" class="form-select" required>
                        <option value="">Select unit</option>

                        <option value="kg"
                            <?= $unit === "kg" ? "selected" : "" ?>>
                            Kilograms (kg)
                        </option>

                        <option value="bags"
                            <?= $unit === "bags" ? "selected" : "" ?>>
                            Bags
                        </option>

                        <option value="tons"
                            <?= $unit === "tons" ? "selected" : "" ?>>
                            Tons
                        </option>

                        <option value="pieces"
                            <?= $unit === "pieces" ? "selected" : "" ?>>
                            Pieces
                        </option>

                        <option value="litres"
                            <?= $unit === "litres" ? "selected" : "" ?>>
                            Litres
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Quality Grade</label>

                    <select name="quality_grade" class="form-select">
                        <option value="">Select quality</option>

                        <option value="Excellent"
                            <?= $quality_grade === "Excellent"
                                ? "selected" : "" ?>>
                            Excellent
                        </option>

                        <option value="Good"
                            <?= $quality_grade === "Good"
                                ? "selected" : "" ?>>
                            Good
                        </option>

                        <option value="Average"
                            <?= $quality_grade === "Average"
                                ? "selected" : "" ?>>
                            Average
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Expected Price (UGX)
                        <span class="required">*</span>
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">UGX</span>

                        <input
                            type="number"
                            name="expected_price"
                            class="form-control"
                            min="0"
                            step="0.01"
                            placeholder="e.g. 1500"
                            value="<?= e($expected_price) ?>"
                            required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Location <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="location"
                        class="form-control"
                        placeholder="e.g. Mukono"
                        value="<?= e($location) ?>"
                        maxlength="200"
                        required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Available Date</label>

                    <input
                        type="date"
                        name="available_date"
                        class="form-control"
                        value="<?= e($available_date) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fa-solid fa-calendar-check"></i>
                        Expected Harvest Date
                    </label>

                    <input
                        type="date"
                        name="expected_harvest_date"
                        id="expected_harvest_date"
                        class="form-control"
                        value="<?= e($expected_harvest_date) ?>">

                    <small class="text-muted">
                        Enter when you expect the produce to be ready.
                    </small>
                </div>

                <div class="col-12 mb-4">
                    <label class="form-label">Description</label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4"
                        placeholder="Describe your produce..."><?= e($description) ?></textarea>
                </div>

            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save Produce
                </button>

                <a href="my_produce.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
