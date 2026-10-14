<?php

session_start();

require_once __DIR__ . "/config/database.php";

$error = "";
$success = "";

$token = trim($_GET["token"] ?? $_POST["token"] ?? "");

$validReset = false;
$resetRecord = null;


/*
=================================================
CHECK TOKEN
=================================================
*/

if ($token !== "") {

    $tokenHash = hash(
        "sha256",
        $token
    );

    $stmt = $pdo->prepare("
        SELECT
            pr.id,
            pr.user_id,
            pr.expires_at,
            u.email,
            u.fullname
        FROM password_resets pr
        INNER JOIN users u
            ON u.id = pr.user_id
        WHERE pr.token_hash = :token_hash
          AND pr.used = 0
          AND pr.expires_at > NOW()
        LIMIT 1
    ");

    $stmt->execute([
        ":token_hash" => $tokenHash
    ]);

    $resetRecord = $stmt->fetch();

    if ($resetRecord) {

        $validReset = true;

    } else {

        $error =
            "This password reset link is invalid or has expired.";
    }
}


/*
=================================================
RESET PASSWORD
=================================================
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && $validReset
) {

    $password =
        $_POST["password"] ?? "";

    $confirmPassword =
        $_POST["confirm_password"] ?? "";


    /*
    ---------------------------------------------
    Validate password
    ---------------------------------------------
    */

    if (strlen($password) < 8) {

        $error =
            "Password must contain at least 8 characters.";

    } elseif ($password !== $confirmPassword) {

        $error =
            "Passwords do not match.";

    } else {

        $hashedPassword =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );


        try {

            $pdo->beginTransaction();


            /*
            -----------------------------------------
            Update user password
            -----------------------------------------
            */

            $updateUser = $pdo->prepare("
                UPDATE users
                SET password = :password
                WHERE id = :user_id
            ");

            $updateUser->execute([
                ":password" => $hashedPassword,
                ":user_id" => $resetRecord["user_id"]
            ]);


            /*
            -----------------------------------------
            Mark token as used
            -----------------------------------------
            */

            $updateToken = $pdo->prepare("
                UPDATE password_resets
                SET used = 1
                WHERE id = :id
            ");

            $updateToken->execute([
                ":id" => $resetRecord["id"]
            ]);


            /*
            -----------------------------------------
            Remove any other reset tokens
            -----------------------------------------
            */

            $deleteOtherTokens = $pdo->prepare("
                DELETE FROM password_resets
                WHERE user_id = :user_id
                  AND id != :id
            ");

            $deleteOtherTokens->execute([
                ":user_id" => $resetRecord["user_id"],
                ":id" => $resetRecord["id"]
            ]);


            $pdo->commit();

            $validReset = false;

            $success =
                "Your password has been changed successfully.";

        } catch (PDOException $e) {

            $pdo->rollBack();

            $error =
                "Unable to change your password. Please try again.";

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

    <title>Reset Password | AGRO-LINK UGANDA</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    >

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

            color: #17212b;
        }

        .reset-page {
            min-height: 90vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 20px 20px;
        }

        .reset-card {
            width: 90%;

            max-width: 550px;

            padding: 20px;

            border: 2px solid #13845f;

            border-radius: 20px;

            background: white;

            box-shadow:
                0 12px 35px rgba(0,0,0,.08);
        }

        .brand {
            display: flex;

            align-items: center;

            gap: 15px;

            margin-bottom: 30px;
        }

        .brand-icon {
            width: 52px;
            height: 52px;

            border-radius: 50%;

            background: #88fac1;

            display: flex;

            align-items: center;
            justify-content: center;

            color: #13845f;

            font-size: 20px;
        }

        .brand h1 {
            margin: 0;

            font-size: 18px;

            color: #0f7453;
        }

        .brand span {
            display: block;

            font-size: 10px;

            letter-spacing: 4px;

            color: #13845f;
        }

        .title {
            font-size: 18px;

            margin-bottom: 8px;

            font-weight: 600;
        }

        .subtitle {
            color: #788692;

            font-size: 15px;

            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .label {
            display: block;

            font-size: 16px;

            font-weight: 500;

            margin-bottom: 7px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap > i.main-icon {
            position: absolute;

            left: 20px;

            top: 50%;

            transform: translateY(-50%);

            color: #71808e;

            font-size: 15px;
        }

        .password-input {
            width: 100%;

            height: 43px;

            border: 1.5px solid #d9e0e5;

            border-radius: 12px;

            padding: 0 35px;

            font-family: inherit;

            font-size: 14px;

            outline: none;
        }

        .password-input:focus {
            border-color: #13845f;

            box-shadow:
                0 0 0 3px rgba(19,132,95,.08);
        }

        .toggle {
            position: absolute;

            right: 17px;

            top: 50%;

            transform: translateY(-50%);

            background: none;

            border: none;

            color: #1f2933;

            font-size: 20px;

            cursor: pointer;
        }

        .change-btn {
            width: 100%;

            height: 45px;

            margin-top: 8px;

            border: none;

            border-radius: 12px;

            background: #13845f;

            color: white;

            font-family: inherit;

            font-size: 17px;

            font-weight: 500;
        }

        .change-btn:hover {
            background: #0e6d4e;
        }

        .back-login {
            display: block;

            text-align: center;

            margin-top: 20px;

            text-decoration: none;

            color: #1683d8;

            font-size: 14px;

            font-weight: 700;
        }

        @media (max-width: 600px) {

            .reset-card {
                padding: 25px 20px;
            }

            .title {
                font-size: 34px;
            }

        }

    </style>

</head>

<body>

<div class="reset-page">

    <div class="reset-card">

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


        <?php if ($success): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($success) ?>

            </div>

            <a
                href="login.php"
                class="back-login"
            >

                Go to Login

            </a>

        <?php elseif (!$validReset): ?>

            <div class="alert alert-danger">

                <?= htmlspecialchars($error) ?>

            </div>

            <a
                href="forgot-password.php"
                class="back-login"
            >

                Request a new reset link

            </a>

        <?php else: ?>


            <h2 class="title">
                Reset password
            </h2>

            <p class="subtitle">

                Create a new password for your
                AGRO-LINK account.

            </p>


            <?php if ($error): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form method="POST">

                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($token) ?>"
                >


                <!-- Password -->

                <div class="form-group">

                    <label
                        for="password"
                        class="label"
                    >
                        New Password
                    </label>

                    <div class="input-wrap">

                        <i
                            class="fa-solid fa-lock main-icon"
                        ></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="password-input"
                            placeholder="Enter new password"
                            minlength="8"
                            required
                        >

                        <button
                            type="button"
                            class="toggle"
                            data-target="password"
                        >
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>

                    </div>

                </div>


                <!-- Confirm Password -->

                <div class="form-group">

                    <label
                        for="confirm_password"
                        class="label"
                    >
                        Confirm Password
                    </label>

                    <div class="input-wrap">

                        <i
                            class="fa-solid fa-lock main-icon"
                        ></i>

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="password-input"
                            placeholder="Confirm new password"
                            minlength="8"
                            required
                        >

                        <button
                            type="button"
                            class="toggle"
                            data-target="confirm_password"
                        >
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="change-btn"
                >

                    <i class="fa-solid fa-key me-2"></i>

                    Change Password

                </button>

            </form>

        <?php endif; ?>

    </div>

</div>


<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const toggles =
            document.querySelectorAll(".toggle");

        toggles.forEach(function (toggle) {

            toggle.addEventListener(
                "click",
                function () {

                    const targetId =
                        this.dataset.target;

                    const input =
                        document.getElementById(targetId);

                    const icon =
                        this.querySelector("i");

                    if (input.type === "password") {

                        input.type = "text";

                        icon.classList.remove(
                            "fa-eye-slash"
                        );

                        icon.classList.add(
                            "fa-eye"
                        );

                    } else {

                        input.type = "password";

                        icon.classList.remove(
                            "fa-eye"
                        );

                        icon.classList.add(
                            "fa-eye-slash"
                        );

                    }

                }
            );

        });

    }
);

</script>

</body>
</html>