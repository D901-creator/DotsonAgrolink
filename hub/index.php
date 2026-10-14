<?php
require_once "../config/database.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agriculture Hub - Agro-Link Uganda</title>

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
                    <a class="nav-link active" href="index.php">
                        Agriculture Hub
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


<!-- HERO -->
<section class="bg-light py-5">

    <div class="container text-center">

        <h1 class="fw-bold text-success">
            <i class="fas fa-seedling"></i>
            Agriculture Hub
        </h1>

        <p class="lead text-muted">
            Agricultural information, opportunities and updates
            for Uganda.
        </p>

    </div>

</section>


<!-- INFORMATION CARDS -->
<div class="container py-5">

    <div class="row g-4">

        <!-- NEWS -->
        <div class="col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm">

                <div class="card-body text-center">

                    <i class="fas fa-newspaper fa-3x text-success mb-3"></i>

                    <h4>
                        Agriculture News
                    </h4>

                    <p class="text-muted">
                        Read agricultural news, developments and
                        important updates.
                    </p>

                    <a href="news.php"
                       class="btn btn-success">
                        View News
                    </a>

                </div>

            </div>

        </div>


        <!-- WEATHER -->
        <div class="col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm">

                <div class="card-body text-center">

                    <i class="fas fa-cloud-sun fa-3x text-primary mb-3"></i>

                    <h4>
                        Weather
                    </h4>

                    <p class="text-muted">
                        View weather information useful for
                        agricultural planning.
                    </p>

                    <a href="weather.php"
                       class="btn btn-primary">
                        View Weather
                    </a>

                </div>

            </div>

        </div>


        <!-- MARKET PRICES -->
        <div class="col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm">

                <div class="card-body text-center">

                    <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>

                    <h4>
                        Market Prices
                    </h4>

                    <p class="text-muted">
                        Check available agricultural market price
                        information.
                    </p>

                    <a href="../farmer/market_prices.php"
                       class="btn btn-warning">
                        View Prices
                    </a>

                </div>

            </div>

        </div>


        <!-- GOVERNMENT -->
        <div class="col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm">

                <div class="card-body text-center">

                    <i class="fas fa-landmark fa-3x text-success mb-3"></i>

                    <h4>
                        Government Updates
                    </h4>

                    <p class="text-muted">
                        Important agricultural government programs,
                        policies and updates.
                    </p>

                    <a href="government.php"
                       class="btn btn-success">
                        View Updates
                    </a>

                </div>

            </div>

        </div>


        <!-- SUPPORT PROGRAMS -->
        <div class="col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm">

                <div class="card-body text-center">

                    <i class="fas fa-hands-helping fa-3x text-info mb-3"></i>

                    <h4>
                        Support Programs
                    </h4>

                    <p class="text-muted">
                        Discover agricultural support and development
                        opportunities.
                    </p>

                    <a href="support.php"
                       class="btn btn-info text-white">
                        View Programs
                    </a>

                </div>

            </div>

        </div><!-- BUYER OPPORTUNITIES -->
<div class="col-md-6 col-lg-4">

    <div class="card h-100 shadow-sm">

        <div class="card-body text-center">

            <i class="fas fa-handshake fa-3x text-success mb-3"></i>

            <h4>
                Buyer Opportunities
            </h4>

            <p class="text-muted">
                Find buyers looking for agricultural
                produce on Agro-Link Uganda.
            </p>

            <a href="opportunities.php"
               class="btn btn-success">

                View Opportunities

            </a>

        </div>

    </div>

</div>


        <!-- ALERTS -->
        <div class="col-md-6 col-lg-4">

            <div class="card h-100 shadow-sm">

                <div class="card-body text-center">

                    <i class="fas fa-bell fa-3x text-danger mb-3"></i>

                    <h4>
                        Agricultural Alerts
                    </h4>

                    <p class="text-muted">
                        Important alerts and announcements for
                        farmers and agricultural stakeholders.
                    </p>

                    <a href="alerts.php"
                       class="btn btn-danger">
                        View Alerts
                    </a>

                </div>

            </div>

        </div>

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