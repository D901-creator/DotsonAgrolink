<?php

session_start();

require_once __DIR__ . "/config/database.php";

$message = "";
$error = "";
$resetLink = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        /*
        ------------------------------------------------
        Always give the same general response.
        This helps avoid revealing whether an email
        exists in the system.
        ------------------------------------------------
        */

        $stmt = $pdo->prepare("
            SELECT id, fullname, email
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email
        ]);

        $user = $stmt->fetch();

        if ($user) {

            /*
            --------------------------------------------
            Remove previous unused reset tokens
            --------------------------------------------
            */

            $deleteOld = $pdo->prepare("
                DELETE FROM password_resets
                WHERE user_id = :user_id
            ");

            $deleteOld->execute([
                ":user_id" => $user["id"]
            ]);


            /*
            --------------------------------------------
            Generate secure random token
            --------------------------------------------
            */

            $token = bin2hex(random_bytes(32));

            $tokenHash = hash(
                "sha256",
                $token
            );


            /*
            --------------------------------------------
            Token expires after 30 minutes
            --------------------------------------------
            */

            $expiresAt = date(
                "Y-m-d H:i:s",
                time() + (30 * 60)
            );


            /*
            --------------------------------------------
            Save token
            --------------------------------------------
            */

            $insert = $pdo->prepare("
                INSERT INTO password_resets
                (
                    user_id,
                    token_hash,
                    expires_at
                )
                VALUES
                (
                    :user_id,
                    :token_hash,
                    :expires_at
                )
            ");

            $insert->execute([
                ":user_id" => $user["id"],
                ":token_hash" => $tokenHash,
                ":expires_at" => $expiresAt
            ]);


            /*
            --------------------------------------------
            LOCALHOST TEST LINK
            --------------------------------------------
            */

            $resetLink =
                "http://localhost/agrolink/reset-password.php?token=" .
                urlencode($token);

        }

        $message =
            "If an account exists for this email, a password reset link has been generated.";
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

    <title>Forgot Password | AGRO-LINK UGANDA</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    >

    <!-- Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            background: #ffffff;

            font-family: "Comic Neue", cursive;

            color: #1c2730;
        }

        .forgot-page {
            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 20px 10px;
        }

        .forgot-card {
            width: 90%;

            max-width: 550px;

            padding: 25px;

            border: 2px solid #13845f;

            border-radius: 20px;

            background: #ffffff;

            box-shadow:
                0 12px 35px rgba(0,0,0,0.08);
        }

        .brand {
            display: flex;

            align-items: center;

            gap: 15px;

            margin-bottom: 30px;
        }

        .brand-icon {
            width: 45px;
            height: 45px;

            border-radius: 50%;

            background: #ebf9f2;

            display: flex;

            justify-content: center;

            align-items: center;

            color: #13845f;

            font-size: 20px;
        }

        .brand h1 {
            margin: 0;

            font-size: 15px;

            color: #0f7453;
        }

        .brand span {
            display: block;

            font-size: 10px;

            letter-spacing: 4px;

            color: #13845f;
        }

        .page-title {
            font-size: 22px;

            margin-bottom: 5px;

            font-weight: 600;
        }

        .description {
            font-size: 0px;

            color: #788692;

            line-height: 1.5;

            margin-bottom: 28px;
        }

        .input-label {
            font-size: 15px;

            font-weight: 500;

            margin-bottom: 10px;

            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;

            left: 20px;

            top: 50%;

            transform: translateY(-50%);

            color: #72808d;

            font-size: 15px;
        }

        .email-input {
            width: 90%;

            height: 45px;

            border: 1.5px solid #d9e0e5;

            border-radius: 12px;

            padding: 0 12px 0 38px;

            font-family: inherit;

            font-size: 12px;

            outline: none;
        }

        .email-input:focus {
            border-color: #13845f;

            box-shadow: 0 0 0 3px rgba(19,132,95,.08);
        }

        .reset-btn {
            width: 90%;

            height: 45px;

            margin-top: 17px;

            border: none;

            border-radius: 12px;

            background: #13845f;

            color: white;

            font-family: inherit;

            font-size: 13px;

            font-weight: 500;

            cursor: pointer;
        }

        .reset-btn:hover {
            background: #0e6d4e;
        }

        .back-login {
            display: block;

            text-align: center;

            margin-top: 20px;

            color: #1683d8;

            text-decoration: none;

            font-size: 15px;

            font-weight: 500;
        }

        .demo-link {
            margin-top: 18px;

            padding: 16px;

            border-radius: 12px;

            background: #fff8df;

            border: 1px solid #efd57b;

            word-break: break-word;
        }

        .demo-link a {
            color: #1683d8;

            word-break: break-all;
        }

        @media (max-width: 600px) {

            .forgot-card {
                padding: 25px 20px;
            }

            .page-title {
                font-size: 34px;
            }

            .description {
                font-size: 18px;
            }
        }

    </style>

</head>

<body>

<div class="forgot-page">

    <div class="forgot-card">


        <!-- Brand -->

        <div class="brand">

            <div class="brand-icon">

                <i class="fa-solid fa-seedling"></i>

            </div>

            <div>

                <h1>AGRO-LINK</h1>

                <span>UGANDA</span>

            </div>

        </div>


        <!-- Heading -->

        <h2 class="page-title">
            Forgot password?
        </h2>


        <p class="description">

            Enter the email address associated with your
            AGRO-LINK account and we'll help you reset
            your password.

        </p>


        <!-- Error -->

        <?php if ($error): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- Success -->

        <?php if ($message): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($message) ?>

            </div>

        <?php endif; ?>


        <!-- Form -->

        <form method="POST">

            <label
                class="input-label"
                for="email"
            >
                Email Address
            </label>

            <div class="input-wrapper">

                <i class="fa-regular fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="email-input"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <button
                type="submit"
                class="reset-btn"
            >

                <i class="fa-solid fa-paper-plane me-2"></i>

                Send Reset Link

            </button>

        </form>


        <!-- LOCALHOST TEST LINK -->

        <?php if ($resetLink): ?>

            <div class="demo-link">

                <strong>
                    Local testing reset link:
                </strong>

                <br><br>

                <a href="<?= htmlspecialchars($resetLink) ?>">

                    <?= htmlspecialchars($resetLink) ?>

                </a>

                <br><br>

                <small>
                    This link is shown because you're testing on localhost.
                    In production, this should be sent by email.
                </small>

            </div>

        <?php endif; ?>


        <a
            href="login.php"
            class="back-login"
        >

            <i class="fa-solid fa-arrow-left me-2"></i>

            Back to Login

        </a>

    </div>

</div>

</body>

</html>