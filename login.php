<?php

session_start();

require_once __DIR__ . "/config/database.php";

$errors = [];
$loginValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $loginValue = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    // -------------------------
    // VALIDATION
    // -------------------------

    if ($loginValue === "") {
        $errors[] = "Username or email is required.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    // -------------------------
    // LOGIN
    // -------------------------

    if (empty($errors)) {

        $sql = "
            SELECT
                id,
                fullname,
                username,
                email,
                password,
                role,
                profile_picture
            FROM users
            WHERE username = :login
               OR email = :login
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":login" => $loginValue
        ]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {

            // Prevent session fixation
            session_regenerate_id(true);

            // Store user information in session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fullname"] = $user["fullname"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["profile_picture"] = $user["profile_picture"];

            // -------------------------
            // ROLE REDIRECTION
            // -------------------------

            if ($user["role"] === "farmer") {

                header("Location: farmer/dashboard.php");
                exit;

            } elseif ($user["role"] === "buyer") {

                header("Location: buyer/dashboard.php");
                exit;

            } else {

                $errors[] = "Invalid account role.";

            }

        } else {

            $errors[] = "Incorrect username/email or password.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Log In | AGRO-LINK UGANDA</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap"
        rel="stylesheet"
    >

    <!-- Login CSS -->
    <link
        rel="stylesheet"
        href="login.css"
    >

</head>

<body>

    <!-- Close button -->
    <button
        type="button"
        class="close-btn"
        id="closeButton"
        aria-label="Close"
    >
        <i class="fa-solid fa-xmark"></i>
    </button>


    <!-- Login page -->

    <main class="login-page">

        <div class="login-card">


            <!-- LOGO -->

            <div class="brand-area">

                <div class="brand-icon">
                    <i class="fa-solid fa-seedling"></i>
                </div>

                <div class="brand-text">

                    <h1>AGRO-LINK</h1>

                    <span>UGANDA</span>

                </div>

            </div>


            <!-- Tagline -->

            <div class="tagline">
                Connecting Farmers to Real Markets
            </div>


            <!-- Heading -->

            <h2 class="login-title">
                Log in
            </h2>


            <div class="new-user">

                New user?

                <a href="register.php">
                    Register Now
                </a>

            </div>


            <!-- ERROR -->

            <?php if (!empty($errors)): ?>

                <div class="alert alert-danger login-alert">

                    <i class="fa-solid fa-circle-exclamation me-2"></i>

                    <?php foreach ($errors as $error): ?>

                        <div>
                            <?= htmlspecialchars($error) ?>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>


            <!-- GOOGLE BUTTON -->

            <button
                type="button"
                class="google-btn"
                id="googleLogin"
            >

                <span class="google-icon">
                    G
                </span>

                <span>
                    Continue with Google
                </span>

            </button>


            <!-- OR -->

            <div class="or-divider">

                <span></span>

                <b>or</b>

                <span></span>

            </div>


            <!-- LOGIN FORM -->

            <form
                method="POST"
                action="login.php"
                id="loginForm"
                novalidate
            >


                <!-- Username / Email -->

                <div class="form-group">

                    <label class="input-label">
                        Username or Email
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-regular fa-user input-icon"></i>

                        <input
                            type="text"
                            name="login"
                            id="login"
                            class="login-input"
                            placeholder="Username or Email"
                            value="<?= htmlspecialchars($loginValue) ?>"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>


                <!-- Password -->

                <div class="form-group">

                    <label class="input-label">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-lock input-icon"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="login-input password-field"
                            placeholder="Enter Password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Show password"
                        >
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>

                    </div>

                </div>


                <!-- Forgot password -->

                <div class="forgot-area">

                    <a href="forgot-password.php">
                        Forgot password?
                    </a>

                </div>


                <!-- Login button -->

                <button
                    type="submit"
                    class="login-btn"
                >

                    <i class="fa-solid fa-right-to-bracket me-2"></i>

                    Log In

                </button>


                <!-- Policy -->

                <div class="policy-text">

                    By logging in, you agree to our

                    <a href="#">
                        Privacy Policy
                    </a>

                    and

                    <a href="#">
                        Cookie Policy
                    </a>.

                </div>

            </form>

        </div>

    </main>


    

    <script src="login.js"></script>

</body>

</html>