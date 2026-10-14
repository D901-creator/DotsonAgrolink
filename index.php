<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agro-Link Uganda</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- Our CSS -->
    <link rel="stylesheet" href="style.css">

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">

    <div class="container">

        <!-- LOGO / BRAND -->
        <a class="navbar-brand fw-bold" href="index.php">

            🌱 Agro-Link Uganda

        </a>


        <!-- NAVIGATION -->
        <div class="d-flex align-items-center gap-2">

            <!-- HOME -->
            <a href="index.php"
               class="nav-link text-white">

                <i class="fas fa-home"></i>
                Home

            </a>


            <!-- ABOUT -->
            <a href="#about"
               class="nav-link text-white">

                About

            </a>


            <!-- FEATURES -->
            <a href="#features"
               class="nav-link text-white">

                Features

            </a>


            <!-- REGISTER -->
            <a href="register.php"
               class="btn btn-light btn-sm ms-2">

                <i class="fas fa-user-plus"></i>
                Register

            </a>


            <!-- LOGIN -->
            <a href="login.php"
               class="btn btn-outline-light btn-sm">

                <i class="fas fa-sign-in-alt"></i>
                Login

            </a>

        </div>

    </div>

</nav>


<!-- HERO SECTION -->
<section class="hero-section">

    <div class="container">

        <div class="row align-items-center min-vh-75">

            <div class="col-lg-7">

                <span class="badge bg-light text-success mb-3">
                    🇺🇬 Agriculture Technology Platform
                </span>

                <h1 class="display-4 fw-bold">
                    Connecting Ugandan Farmers to Better Markets
                </h1>

                <p class="lead mt-3">
                    Agro-Link Uganda connects farmers with buyers while providing
                    access to market prices, agricultural information,
                    opportunities, alerts and support.
                </p>

                <div class="mt-4">

                    <a href="farmer/dashboard.php"
                       class="btn btn-light btn-lg me-2 mb-2">
                        <i class="fa-solid fa-seedling"></i>
                        I'm a Farmer
                    </a>

                    <a href="buyer/dashboard.php"
                       class="btn btn-outline-light btn-lg me-2 mb-2">
                        <i class="fa-solid fa-cart-shopping"></i>
                        I'm a Buyer
                    </a>

                    <a href="hub/index.php"
                       class="btn btn-warning btn-lg mb-2">
                        <i class="fa-solid fa-globe"></i>
                        Agriculture Hub
                    </a>

                    <a href="admin/dashboard.php" class="btn btn-dark"
                        class="btn btn-light btn-lg me-2 mb-2">
                    <i class="fas fa-user-shield"></i>
                        Admin </a>
                
                </div>
                

            </div>

            <div class="col-lg-5 text-center mt-5 mt-lg-0">

                <div class="hero-card">

                    <i class="fa-solid fa-tractor hero-icon"></i>

                    <h3 class="mt-3">
                        FARMERS → MARKETS
                    </h3>

                    <p>
                        Produce → Matching → Offers → Orders → Delivery
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- FEATURES -->
<section id="features" class="py-5">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="fw-bold">
                What Agro-Link Uganda Provides
            </h2>

            <p class="text-muted">
                One platform connecting agriculture stakeholders.
            </p>

        </div>


        <div class="row g-4">

            <!-- Farmers -->
            <div class="col-md-6 col-lg-3">

                <div class="card feature-card h-100 text-center">

                    <div class="card-body">

                        <i class="fa-solid fa-seedling feature-icon"></i>

                        <h5 class="fw-bold mt-3">
                            Farmers
                        </h5>

                        <p>
                            List produce, receive buyer requests,
                            manage offers and track orders.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Buyers -->
            <div class="col-md-6 col-lg-3">

                <div class="card feature-card h-100 text-center">

                    <div class="card-body">

                        <i class="fa-solid fa-store feature-icon"></i>

                        <h5 class="fw-bold mt-3">
                            Buyers
                        </h5>

                        <p>
                            Find agricultural produce,
                            submit requests and connect with farmers.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Market -->
            <div class="col-md-6 col-lg-3">

                <div class="card feature-card h-100 text-center">

                    <div class="card-body">

                        <i class="fa-solid fa-chart-line feature-icon"></i>

                        <h5 class="fw-bold mt-3">
                            Market Prices
                        </h5>

                        <p>
                            Access agricultural market price
                            information and opportunities.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Agriculture Hub -->
            <div class="col-md-6 col-lg-3">

                <div class="card feature-card h-100 text-center">

                    <div class="card-body">

                        <i class="fa-solid fa-circle-info feature-icon"></i>

                        <h5 class="fw-bold mt-3">
                            Agriculture Hub
                        </h5>

                        <p>
                            Access news, weather, government updates,
                            support programs and alerts.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ABOUT -->
<section id="about" class="about-section py-5">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <h2 class="fw-bold">
                    About Agro-Link Uganda
                </h2>

                <p>
                    Agro-Link Uganda is a digital agriculture platform
                    designed to connect farmers and buyers and improve
                    access to agricultural markets.
                </p>

                <p>
                    The platform also provides agricultural information,
                    market prices, weather information, government updates,
                    support programs and agricultural alerts.
                </p>

            </div>

            <div class="col-lg-6">

                <div class="info-box">

                    <h4>
                        <i class="fa-solid fa-link"></i>
                        The Agro-Link Connection
                    </h4>

                    <p class="mb-0">
                        FARMERS
                        <strong>→</strong>
                        AGRO-LINK
                        <strong>→</strong>
                        BUYERS
                        <strong>→</strong>
                        ORDERS
                        <strong>→</strong>
                        DELIVERY
                        <strong>→</strong>
                        TRANSACTION
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- FOOTER -->
<footer class="bg-dark text-white py-4">

    <div class="container text-center">

        <h5>🌱 Agro-Link Uganda</h5>

        <p class="mb-1">
            Connecting Ugandan Farmers to Better Markets
        </p>

        <small>
            &copy; <?php echo date("Y"); ?> Agro-Link Uganda.
            All Rights Reserved.
        </small>

    </div>

</footer>


<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<!-- Our JS -->
<script src="script.js"></script>

</body>
</html>