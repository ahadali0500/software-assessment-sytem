<?php
include('config.php'); // Database connection

// Fetch sections from the database before handling form submission
$sections = [];
$result = mysqli_query($conn, "SELECT * FROM section");
while ($row = mysqli_fetch_assoc($result)) {
    $sections[] = $row;
}

$alertMessage = ""; // Initialize alert message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $rollNo = trim($_POST['rollNo']);
    $section = trim($_POST['section']);

    // Basic validation
    if (empty($name) || empty($rollNo) || empty($section)) {
        $alertMessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            All fields are required.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                         </div>';
    } else {
        // Insert data into students table
        $sql = "INSERT INTO student (name, rollNo, section) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $name, $rollNo, $section);

        if (mysqli_stmt_execute($stmt)) {
            $alertMessage = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                Student registered successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                             </div>';
        } else {
            $alertMessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error: ' . mysqli_error($conn) . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                             </div>';
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('include/navbar.php'); ?>

<div class="container mt-5">
    <h2>Student Registration</h2>

    <!-- Display Bootstrap Alert Message -->
    <?= $alertMessage; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Student Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
            <div class="invalid-feedback">Please enter a valid name.</div>
        </div>

        <div class="mb-3">
            <label for="rollNo" class="form-label">Roll Number</label>
            <input type="text" class="form-control" id="rollNo" name="rollNo" required>
            <div class="invalid-feedback">Roll number is required.</div>
        </div>

        <div class="mb-3">
            <label for="section" class="form-label">Section</label>
            <select class="form-control" id="section" name="section" required>
                <option value="">Select Section</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?= $section['sectionId']; ?>">
                        <?= $section['department'] . " - " . $section['semester'] . " - " . $section['subject']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a section.</div>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<script>
    // Bootstrap Form Validation
    (function () {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
