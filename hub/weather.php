<?php
require_once "../config/database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Weather - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">

    <div class="container">

        <a class="navbar-brand fw-bold" href="../index.php">
            🌱 AGRO-LINK UGANDA
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Agriculture Hub
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="weather.php">
                        Weather
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../farmer/dashboard.php">
                        Farmer
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../buyer/dashboard.php">
                        Buyer
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- HEADER -->
<section class="bg-light py-5">

    <div class="container text-center">

        <h1 class="fw-bold text-success">

            <i class="fas fa-cloud-sun"></i>

            Agricultural Weather

        </h1>

        <p class="lead text-muted">

            Weather information to help farmers plan
            agricultural activities.

        </p>

    </div>

</section>


<!-- WEATHER CONTENT -->
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3>
            Weather Information
        </h3>

        <a href="index.php" class="btn btn-outline-success">

            <i class="fas fa-arrow-left"></i>

            Agriculture Hub

        </a>

    </div>


    <div class="row g-4">

        <!-- LOCATION -->
        <div class="col-md-6 col-lg-4">

            <div class="card shadow-sm h-100">

                <div class="card-body text-center">

                    <i class="fas fa-location-dot fa-3x text-danger mb-3"></i>

                    <h4>
                        Kampala
                    </h4>

                    <p class="text-muted">
                        Central Region, Uganda
                    </p>

                    <h2>
                        <i class="fas fa-cloud-sun"></i>
                        --
                    </h2>

                    <p>
                        Weather data will appear here.
                    </p>

                </div>

            </div>

        </div>


        <!-- FARMING ADVICE -->
        <div class="col-md-6 col-lg-8">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h4 class="text-success">

                        <i class="fas fa-seedling"></i>

                        Farming Planning

                    </h4>

                    <p class="text-muted">
                        Weather information can help farmers
                        plan important agricultural activities.
                    </p>


                    <div class="row g-3 mt-2">

                        <div class="col-md-4">

                            <div class="border rounded p-3 text-center">

                                <i class="fas fa-cloud-rain fa-2x text-primary"></i>

                                <h6 class="mt-2">
                                    Rainfall
                                </h6>

                                <small class="text-muted">
                                    Monitor rainfall conditions
                                    before planting.
                                </small>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded p-3 text-center">

                                <i class="fas fa-temperature-high fa-2x text-danger"></i>

                                <h6 class="mt-2">
                                    Temperature
                                </h6>

                                <small class="text-muted">
                                    Monitor temperature for
                                    crop planning.
                                </small>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded p-3 text-center">

                                <i class="fas fa-wind fa-2x text-info"></i>

                                <h6 class="mt-2">
                                    Wind
                                </h6>

                                <small class="text-muted">
                                    Monitor wind conditions
                                    for farm activities.
                                </small>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- NOTICE -->
    <div class="alert alert-info mt-4">

        <i class="fas fa-info-circle"></i>

        <strong>Weather service:</strong>

        Live weather information will be connected
        to Agro-Link Uganda in a later development step.

    </div>

</div>


<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-4">

    <div class="container">

        <h5>
            🌱 Agro-Link Uganda
        </h5>

        <p class="mb-0">
            Connecting Ugandan Farmers to Better Markets
        </p>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>