<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Check login
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$farmer_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Get existing farm information
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT *
    FROM farm_information
    WHERE farmer_id = ?
    LIMIT 1
");

$stmt->execute([$farmer_id]);

$farm = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Save / Update farm information
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $farm_name = trim($_POST['farm_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $village = trim($_POST['village'] ?? '');
    $farm_size = trim($_POST['farm_size'] ?? '');
    $land_unit = trim($_POST['land_unit'] ?? '');
    $main_crops = trim($_POST['main_crops'] ?? '');
    $farming_type = trim($_POST['farming_type'] ?? '');
    $years_farming = trim($_POST['years_farming'] ?? '');
    $description = trim($_POST['description'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | Basic validation
    |--------------------------------------------------------------------------
    */
    if ($farm_name === '') {

        $error = "Farm name is required.";

    } else {

        if ($farm) {

            /*
            |--------------------------------------------------------------------------
            | Update existing farm
            |--------------------------------------------------------------------------
            */
            $update = $pdo->prepare("
                UPDATE farm_information
                SET
                    farm_name = ?,
                    location = ?,
                    district = ?,
                    village = ?,
                    farm_size = ?,
                    land_unit = ?,
                    main_crops = ?,
                    farming_type = ?,
                    years_farming = ?,
                    description = ?
                WHERE farmer_id = ?
            ");

            $update->execute([
                $farm_name,
                $location,
                $district,
                $village,
                $farm_size !== '' ? $farm_size : null,
                $land_unit,
                $main_crops,
                $farming_type,
                $years_farming !== '' ? $years_farming : null,
                $description,
                $farmer_id
            ]);

            $success = "Farm information updated successfully.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Create new farm information
            |--------------------------------------------------------------------------
            */
            $insert = $pdo->prepare("
                INSERT INTO farm_information
                (
                    farmer_id,
                    farm_name,
                    location,
                    district,
                    village,
                    farm_size,
                    land_unit,
                    main_crops,
                    farming_type,
                    years_farming,
                    description
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $insert->execute([
                $farmer_id,
                $farm_name,
                $location,
                $district,
                $village,
                $farm_size !== '' ? $farm_size : null,
                $land_unit,
                $main_crops,
                $farming_type,
                $years_farming !== '' ? $years_farming : null,
                $description
            ]);

            $success = "Farm information saved successfully.";
        }

        /*
        |--------------------------------------------------------------------------
        | Reload farm information
        |--------------------------------------------------------------------------
        */
        $stmt = $pdo->prepare("
            SELECT *
            FROM farm_information
            WHERE farmer_id = ?
            LIMIT 1
        ");

        $stmt->execute([$farmer_id]);

        $farm = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Farm Information - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold text-success">

                <i class="fas fa-tractor"></i>

                Farm Information

            </h2>

            <p class="text-muted mb-0">

                Manage information about your farm

            </p>

        </div>

        <a href="dashboard.php"
           class="btn btn-outline-success">

            <i class="fas fa-arrow-left"></i>

            Dashboard

        </a>

    </div>


    <!-- Success -->
    <?php if (isset($success)): ?>

        <div class="alert alert-success">

            <i class="fas fa-check-circle"></i>

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <!-- Error -->
    <?php if (isset($error)): ?>

        <div class="alert alert-danger">

            <i class="fas fa-exclamation-circle"></i>

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- Farm Form -->
    <div class="card shadow-sm">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">

                <i class="fas fa-seedling"></i>

                <?= $farm ? 'Update Farm Information' : 'Add Farm Information' ?>

            </h5>

        </div>


        <div class="card-body">

            <form method="POST">

                <!-- Farm Name -->
                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Farm Name

                    </label>

                    <input
                        type="text"
                        name="farm_name"
                        class="form-control"
                        value="<?= htmlspecialchars($farm['farm_name'] ?? '') ?>"
                        placeholder="Enter your farm name"
                        required
                    >

                </div>


                <div class="row">

                    <!-- Location -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            General Location

                        </label>

                        <input
                            type="text"
                            name="location"
                            class="form-control"
                            value="<?= htmlspecialchars($farm['location'] ?? '') ?>"
                            placeholder="e.g. Eastern Uganda"
                        >

                    </div>


                    <!-- District -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            District

                        </label>

                        <input
                            type="text"
                            name="district"
                            class="form-control"
                            value="<?= htmlspecialchars($farm['district'] ?? '') ?>"
                            placeholder="e.g. Jinja"
                        >

                    </div>

                </div>


                <div class="row">

                    <!-- Village -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Village / Community

                        </label>

                        <input
                            type="text"
                            name="village"
                            class="form-control"
                            value="<?= htmlspecialchars($farm['village'] ?? '') ?>"
                            placeholder="Enter village or community"
                        >

                    </div>


                    <!-- Farm Size -->
                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">

                            Farm Size

                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="farm_size"
                            class="form-control"
                            value="<?= htmlspecialchars($farm['farm_size'] ?? '') ?>"
                            placeholder="e.g. 5"
                        >

                    </div>


                    <!-- Land Unit -->
                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">

                            Unit

                        </label>

                        <select
                            name="land_unit"
                            class="form-select"
                        >

                            <option value="">Select unit</option>

                            <option value="Acres"
                                <?= (($farm['land_unit'] ?? '') === 'Acres') ? 'selected' : '' ?>>
                                Acres
                            </option>

                            <option value="Hectares"
                                <?= (($farm['land_unit'] ?? '') === 'Hectares') ? 'selected' : '' ?>>
                                Hectares
                            </option>

                        </select>

                    </div>

                </div>


                <!-- Main Crops -->
                <div class="mb-3">

                    <label class="form-label fw-bold">

                        Main Crops

                    </label>

                    <input
                        type="text"
                        name="main_crops"
                        class="form-control"
                        value="<?= htmlspecialchars($farm['main_crops'] ?? '') ?>"
                        placeholder="e.g. Maize, Beans, Coffee, Bananas"
                    >

                    <small class="text-muted">

                        Separate multiple crops with commas.

                    </small>

                </div>


                <div class="row">

                    <!-- Farming Type -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Farming Type

                        </label>

                        <select
                            name="farming_type"
                            class="form-select"
                        >

                            <option value="">Select farming type</option>

                            <option value="Subsistence"
                                <?= (($farm['farming_type'] ?? '') === 'Subsistence') ? 'selected' : '' ?>>
                                Subsistence
                            </option>

                            <option value="Commercial"
                                <?= (($farm['farming_type'] ?? '') === 'Commercial') ? 'selected' : '' ?>>
                                Commercial
                            </option>

                            <option value="Mixed"
                                <?= (($farm['farming_type'] ?? '') === 'Mixed') ? 'selected' : '' ?>>
                                Mixed
                            </option>

                            <option value="Organic"
                                <?= (($farm['farming_type'] ?? '') === 'Organic') ? 'selected' : '' ?>>
                                Organic
                            </option>

                        </select>

                    </div>


                    <!-- Years -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">

                            Years of Farming

                        </label>

                        <input
                            type="number"
                            name="years_farming"
                            class="form-control"
                            min="0"
                            value="<?= htmlspecialchars($farm['years_farming'] ?? '') ?>"
                            placeholder="e.g. 10"
                        >

                    </div>

                </div>


                <!-- Description -->
                <div class="mb-4">

                    <label class="form-label fw-bold">

                        Farm Description

                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="5"
                        placeholder="Describe your farm, farming activities, facilities and other useful information..."
                    ><?= htmlspecialchars($farm['description'] ?? '') ?></textarea>

                </div>


                <!-- Submit -->
                <button
                    type="submit"
                    class="btn btn-success"
                >

                    <i class="fas fa-save"></i>

                    <?= $farm ? 'Update Farm Information' : 'Save Farm Information' ?>

                </button>


                <a
                    href="dashboard.php"
                    class="btn btn-secondary"
                >

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>