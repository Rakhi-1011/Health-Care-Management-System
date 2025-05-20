<?php
session_start();
include 'connect.php';

// Get fixed category from URL
$category = isset($_GET['category']) ? $_GET['category'] : 'Nutrition Tips';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$pregnancy = isset($_GET['pregnancy']) ? $_GET['pregnancy'] : '';
$age_input = isset($_GET['age']) ? intval($_GET['age']) : null;

// Table name based on category
$table = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($category)) . "_tips";

$where = [];
if ($gender) $where[] = "gender = '" . $conn->real_escape_string($gender) . "'";
if ($gender === "Female" && $pregnancy) $where[] = "pregnancy_status = '" . $conn->real_escape_string($pregnancy) . "'";
if ($age_input) {
    $where[] = "$age_input BETWEEN CAST(SUBSTRING_INDEX(age_range, '-', 1) AS UNSIGNED) AND CAST(SUBSTRING_INDEX(age_range, '-', -1) AS UNSIGNED)";
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT * FROM `$table` $where_sql ORDER BY created_at DESC";
$tips = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($category); ?> | Health Heaven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-green { background-color: rgb(41, 159, 68); color: white; }
        .tip-card { margin-bottom: 24px; }
        .doctor-credit { font-size: 0.95em; color: #228c3c; font-weight: 500; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Health Heaven</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="firstpage.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="contractuspage1.html">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="custom-green py-4 text-center">
        <h1><?php echo htmlspecialchars($category); ?></h1>
        <p class="lead mb-0">Find tips tailored for your age and gender!</p>
    </section>

    <div class="container mt-4">
        <form method="get" class="row g-3 mb-4 justify-content-center">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            <div class="col-md-3">
                <label class="form-label">Enter Your Age</label>
                <input type="number" class="form-control" name="age" min="1" max="120" value="<?php echo $age_input ? $age_input : ''; ?>" placeholder="e.g. 25">
            </div>
            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender" id="gender" onchange="togglePregnancy(this.value); this.form.submit();">
                    <option value="">All</option>
                    <option value="Male" <?= $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="col-md-3" id="pregnancyDiv" style="<?= ($gender == 'Female') ? '' : 'display:none;' ?>">
                <label class="form-label">Pregnancy Status</label>
                <select class="form-select" name="pregnancy" id="pregnancy" onchange="this.form.submit();">
                    <option value="">All</option>
                    <option value="Pregnant" <?= $pregnancy == 'Pregnant' ? 'selected' : '' ?>>Pregnant</option>
                    <option value="Not Pregnant" <?= $pregnancy == 'Not Pregnant' ? 'selected' : '' ?>>Not Pregnant</option>
                </select>
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-success w-100">Search</button>
            </div>
        </form>

        <?php if ($tips && $tips->num_rows > 0): ?>
            <?php while ($row = $tips->fetch_assoc()): ?>
                <div class="card tip-card">
                    <div class="card-body">
                        <h5 class="card-title">For ages: <?= htmlspecialchars($row['age_range']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($row['tip'])) ?></p>
                        <div class="doctor-credit">Published by Doctor <?= htmlspecialchars($row['doctor_name']) ?></div>
                        <div class="text-muted small mt-1"><?= htmlspecialchars($row['created_at']) ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No tips found for this selection.</div>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2025 Health Heaven. All rights reserved.</p>
    </footer>
    <script>
        function togglePregnancy(gender) {
            document.getElementById('pregnancyDiv').style.display = (gender === 'Female') ? '' : 'none';
        }
        window.onload = function() {
            togglePregnancy(document.getElementById('gender').value);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>