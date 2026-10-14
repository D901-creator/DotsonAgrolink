<?php

session_start();

require_once __DIR__ . "/config/database.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "";

    /* =========================
       VALIDATION
    ========================= */

    if ($fullname === "") {
        $errors[] = "Full name is required.";
    }

    if ($username === "") {
        $errors[] = "Username is required.";
    } 

    if ($address === "") {
        $errors[] = "Address is required.";
    }
    if ($phone === "") {
    $errors[] = "Phone number is required.";
} elseif (!preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
    $errors[] = "Please enter a valid phone number.";
}

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must contain at least 8 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!in_array($role, ["farmer", "buyer"], true)) {
        $errors[] = "Please select whether you are a farmer or buyer.";
    }

    /* =========================
       PROFILE IMAGE
    ========================= */

    $profilePictureName = null;

    if (
        isset($_FILES["profile_picture"]) &&
        $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "There was a problem uploading the profile picture.";
        } else {

            $file = $_FILES["profile_picture"];

            if ($file["size"] > 5 * 1024 * 1024) {
                $errors[] = "Profile picture cannot exceed 5MB.";
            }

            $allowedMimeTypes = [
                "image/jpeg",
                "image/png"
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimeTypes, true)) {
                $errors[] = "Only JPG and PNG profile pictures are allowed.";
            }

            if (empty($errors)) {

                $uploadDirectory = __DIR__ . "/uploads/profile/";

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0755, true);
                }

                $extension = strtolower(
                    pathinfo($file["name"], PATHINFO_EXTENSION)
                );

                $profilePictureName =
                    uniqid("profile_", true) . "." . $extension;

                $destination =
                    $uploadDirectory . $profilePictureName;

                if (!move_uploaded_file(
                    $file["tmp_name"],
                    $destination
                )) {
                    $errors[] = "Failed to save profile picture.";
                    $profilePictureName = null;
                }
            }
        }
    }

    /* =========================
       DATABASE CHECK
    ========================= */

    if (empty($errors)) {

        $checkUsername = $pdo->prepare(
            "SELECT id FROM users WHERE username = ? LIMIT 1"
        );

        $checkUsername->execute([$username]);

        if ($checkUsername->fetch()) {
            $errors[] = "Username is already taken.";
        }

        $checkEmail = $pdo->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $checkEmail->execute([$email]);

        if ($checkEmail->fetch()) {
            $errors[] = "Email is already registered.";
        }
    }

    /* =========================
       INSERT USER
    ========================= */

    if (empty($errors)) {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {

            $sql = "
    INSERT INTO users
    (
        fullname,
        username,
        address,
        phone,
        email,
        password,
        role,
        profile_picture
    )
    VALUES
    (
        :fullname,
        :username,
        :address,
        :phone,
        :email,
        :password,
        :role,
        :profile_picture
    )
";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
    ":fullname" => $fullname,
    ":username" => $username,
    ":address" => $address,
    ":phone" => $phone,
    ":email" => $email,
    ":password" => $hashedPassword,
    ":role" => $role,
    ":profile_picture" => $profilePictureName
]);

            $success = "Account created successfully. You can now log in.";

            $_POST = [];

        } catch (PDOException $e) {

            if ($profilePictureName) {

                $uploadedFile =
                    __DIR__ . "/uploads/profile/" . $profilePictureName;

                if (file_exists($uploadedFile)) {
                    unlink($uploadedFile);
                }
            }

            $errors[] = "Registration failed. Please try again.";
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

    <title>Register | AGRO-LINK UGANDA</title>

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

    <!-- Custom CSS -->
    <link
        rel="stylesheet" href="register.css">

</head>

<body>

<div class="register-page">

    <div class="register-container">

        <!-- Close -->
        <button
            type="button"
            class="close-btn"
            onclick="window.location.href='index.php'"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <!-- Logo -->
        <div class="logo-area">

            <div class="logo-icon">
                <i class="fa-solid fa-seedling"></i>
            </div>

            <div class="logo-text">
                <h2>AGRO-LINK</h2>
                <span>UGANDA</span>
            </div>

        </div>


        <!-- Heading -->
        <h1 class="page-title">
            Create account
        </h1>

        <div class="login-text">
            Already have an account?
            <a href="login.php">Log in</a>
        </div>


        <!-- Errors -->

        <?php if (!empty($errors)): ?>

            <div class="alert alert-danger">

                <ul class="mb-0">

                    <?php foreach ($errors as $error): ?>

                        <li>
                            <?= htmlspecialchars($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- Success -->

        <?php if ($success): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($success) ?>

                <br>

                <a href="login.php">
                    Go to Login
                </a>

            </div>

        <?php endif; ?>


        <!-- Registration form -->

        <form
            id="registerForm"
            method="POST"
            enctype="multipart/form-data"
            novalidate
        >


            <!-- Full Name -->

            <div class="form-group">

                <div class="input-box">

                    <i class="fa-regular fa-user main-icon"></i>
<input type="text"name="fullname" class="form-control-custom" placeholder="Full Name" value="<?= htmlspecialchars($_POST["fullname"] ?? "") ?>"
                        required >

                </div>

            </div>


            <!-- Username -->

            <div class="form-group">

                <div class="input-box">

                    <i class="fa-solid fa-user main-icon"></i>

                    <input
                        type="text"
                        name="username"
                        class="form-control-custom"
                        placeholder="Username"
                        value="<?= htmlspecialchars($_POST["username"] ?? "") ?>"
                        required
                    >

                </div>

            </div>


            <!-- Address -->

            <div class="form-group">

                <div class="input-box">

                    <i class="fa-solid fa-location-dot main-icon"></i>

                    <input
                        type="text"
                        name="address"
                        class="form-control-custom"
                        placeholder="Address"
                        value="<?= htmlspecialchars($_POST["address"] ?? "") ?>"
                        required
                    >

                </div>

            </div>
            <!-- Phone -->

<div class="form-group">

    <div class="input-box">

        <i class="fa-solid fa-phone main-icon"></i>

        <input
            type="tel"
            name="phone"
            id="phone"
            class="form-control-custom"
            placeholder="Phone Number"
            value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>"
            required
        >

    </div>

</div>


            <!-- Email -->

            <div class="form-group">

                <div class="input-box">

                    <i class="fa-regular fa-envelope main-icon"></i>

                    <input
                        type="email"
                        name="email"
                        class="form-control-custom"
                        placeholder="Email"
                        value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                        required
                    >

                </div>

            </div>


            <!-- Password -->

            <div class="form-group">

                <div class="input-box">

                    <i class="fa-solid fa-lock main-icon"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control-custom"
                        placeholder="Password"
                        required
                    >

                    <button
                        type="button"
                        id="togglePassword"
                        class="password-toggle"
                    >
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>

                </div>

            </div>


            <!-- Confirm Password -->

            <div class="form-group">

                <div class="input-box">

                    <i class="fa-solid fa-lock main-icon"></i>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-control-custom"
                        placeholder="Confirm Password"
                        required
                    >

                    <button
                        type="button"
                        id="toggleConfirmPassword"
                        class="password-toggle"
                    >
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>

                </div>

            </div>


            <!-- Register As -->

            <div class="section-title">
                Register as
            </div>

            <div class="role-container">


                <!-- Farmer -->

                <div class="role-option">

                    <input
                        type="radio"
                        name="role"
                        id="farmer"
                        value="farmer"
                        <?= ($_POST["role"] ?? "") === "farmer" ? "checked" : "" ?>
                    >

                    <label for="farmer" class="role-label">

                        <i class="fa-solid fa-seedling role-icon"></i>

                        <span class="role-name">
                            Farmer
                        </span>

                        <span class="role-check">
                            <i class="fa-solid fa-check"></i>
                        </span>

                    </label>

                </div>


                <!-- Buyer -->

                <div class="role-option">

                    <input
                        type="radio"
                        name="role"
                        id="buyer"
                        value="buyer"
                        <?= ($_POST["role"] ?? "") === "buyer" ? "checked" : "" ?>
                    >

                    <label
                        for="buyer"
                        class="role-label"
                    >

                        <i class="fa-solid fa-shop role-icon"></i>

                        <span class="role-name">
                            Buyer
                        </span>

                        <span class="role-check">
                            <i class="fa-solid fa-check"></i>
                        </span>

                    </label>

                </div>

            </div>


            <!-- Profile picture -->

            <div class="profile-section">

                <div class="section-title">
                    Profile Picture
                </div>

                <div class="profile-upload">

                    <img
                        id="profilePreview"
                        src="https://via.placeholder.com/125x125.png?text=Profile"
                        alt="Profile Preview"
                        class="profile-preview"
                    >

                    <div class="upload-area">

                        <label
                            for="profile_picture"
                            class="file-label"
                        >
                            <i class="fa-solid fa-camera"></i>

                            <span>
                                Choose photo
                            </span>
                        </label>

                        <input
                            type="file"
                            name="profile_picture"
                            id="profile_picture"
                            accept="image/jpeg,image/png"
                        >

                        

                    </div>

                </div>

            </div>


            <!-- Register button -->

            <button
                type="submit"
                class="register-btn"
            >

                <i class="fa-solid fa-user-plus me-2"></i>

                Create Account

            </button>


            <!-- Policy -->

            <div class="policy">

                By creating this account, you agree to our

                <a href="#">
                    Privacy Policy
                </a>

                &

                <a href="#">
                    Cookie Policy
                </a>.

            </div>

        </form>

    </div>

</div>


<!-- JavaScript -->

<script src="register.js"></script>

</body>
</html>