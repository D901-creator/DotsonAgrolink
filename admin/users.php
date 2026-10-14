<?php
require_once "../config/database.php";

$search = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role'] ?? '');
if ($search !== '' && $role_filter !== '') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE (username LIKE ? OR phone LIKE ?)
        AND role = ?
        ORDER BY id DESC
    ");

    $search_value = "%{$search}%";

    $stmt->execute([
        $search_value,
        $search_value,
        $role_filter
    ]);

} elseif ($search !== '') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE username LIKE ?
           OR phone LIKE ?
        ORDER BY id DESC
    ");

    $search_value = "%{$search}%";

    $stmt->execute([
        $search_value,
        $search_value
    ]);

} elseif ($role_filter !== '') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE role = ?
        ORDER BY id DESC
    ");

    $stmt->execute([
        $role_filter
    ]);

} else {

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        ORDER BY id DESC
    ");

    $stmt->execute();
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Users - Agro-Link Uganda</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-success">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="dashboard.php">
            🌱 AGRO-LINK UGANDA
        </a>

        <span class="text-white">
            <i class="fas fa-user-shield"></i>
            Administrator
        </span>

    </div>

</nav>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h5 class="mb-4">
                <i class="fas fa-gauge"></i>
                Admin Panel
            </h5>

            <a href="dashboard.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-home me-2"></i>
                Dashboard

            </a>

            <a href="users.php"
               class="d-block text-warning text-decoration-none mb-3">

                <i class="fas fa-users me-2"></i>
                Users

            </a>

            <a href="#"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-seedling me-2"></i>
                Produce

            </a>

            <a href="#"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-cart-shopping me-2"></i>
                Orders

            </a>

            <a href="../hub/index.php"
               class="d-block text-white text-decoration-none mb-3">

                <i class="fas fa-newspaper me-2"></i>
                Agriculture Hub

            </a>

        </div>


        <!-- MAIN CONTENT -->

        <div class="col-md-9 col-lg-10 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold">
                        <i class="fas fa-users text-success"></i>
                        Manage Users
                    </h2>

                    <p class="text-muted">
                        View users registered on Agro-Link Uganda.
                    </p>

                </div>

                <a href="dashboard.php"
                   class="btn btn-success">

                    <i class="fas fa-arrow-left"></i>
                    Dashboard

                </a>

            </div>


            <div class="card shadow-sm">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">
                        <i class="fas fa-list"></i>
                        All Users
                    </h5>

                </div>

                <div class="card-body">

    <!-- USER SEARCH -->

    <form method="GET" class="mb-4">

    <div class="input-group">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search by username or phone..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        >

        <select name="role" class="form-select">

            <option value="">All Roles</option>

            <option value="farmer"
                <?= (($_GET['role'] ?? '') === 'farmer') ? 'selected' : '' ?>>
                Farmers
            </option>

            <option value="buyer"
                <?= (($_GET['role'] ?? '') === 'buyer') ? 'selected' : '' ?>>
                Buyers
            </option>

            <option value="admin"
                <?= (($_GET['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                Administrators
            </option>

        </select>

        <button type="submit" class="btn btn-success">

            <i class="fas fa-search"></i>

            Search

        </button>

        <a href="users.php" class="btn btn-secondary">

            <i class="fas fa-rotate-left"></i>

            Reset

        </a>

    </div>

</form>


    <?php if (count($users) > 0): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-success">

                                <tr>

                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Created</th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php foreach ($users as $user): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($user['id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($user['username'] ?? 'N/A') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($user['phone'] ?? 'N/A') ?>
                                    </td>

                                    <td>

                                        <?php
                                        $role = $user['role'] ?? 'N/A';

                                        if ($role === 'farmer') {
                                            echo '<span class="badge bg-success">Farmer</span>';
                                        } elseif ($role === 'buyer') {
                                            echo '<span class="badge bg-primary">Buyer</span>';
                                        } elseif ($role === 'admin') {
                                            echo '<span class="badge bg-danger">Admin</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">'
                                                . htmlspecialchars($role)
                                                . '</span>';
                                        }
                                        ?>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars($user['created_at'] ?? 'N/A') ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <?php else: ?>

                        <div class="alert alert-info">

                            <i class="fas fa-info-circle"></i>

                            No users have been registered yet.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>