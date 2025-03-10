<?php
include('config.php'); // Database connection

// Fetch Sections
$sections = [];
$result = mysqli_query($conn, "SELECT * FROM section");
while ($row = mysqli_fetch_assoc($result)) {
    $sections[] = $row;
}

$alertMessage = ""; // Initialize alert message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignmentId = $_POST['assignmentId'];
    $sectionId = $_POST['sectionId'];
    $studentId = $_POST['studentId'];

    // File upload handling
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];
        $fileName = basename($file['name']);
        $fileTmpPath = $file['tmp_name'];
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filePath = $uploadDir . time() . "_" . $fileName;

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $sql = "INSERT INTO uploadassignment (assignmentId, sectionId, studentId, file) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiis", $assignmentId, $sectionId, $studentId, $filePath);

            if (mysqli_stmt_execute($stmt)) {
                $alertMessage = '<div class="alert alert-success">Assignment uploaded successfully!</div>';
            } else {
                $alertMessage = '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            $alertMessage = '<div class="alert alert-danger">Failed to upload file.</div>';
        }
    } else {
        $alertMessage = '<div class="alert alert-danger">Please select a file to upload.</div>';
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include('include/navbar.php'); ?>

<div class="container mt-5">
    <h2>Upload Assignment</h2>

    <?= $alertMessage; ?>

    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="sectionId" class="form-label">Section</label>
            <select class="form-control" id="sectionId" name="sectionId" required>
                <option value="">Select Section</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?= $section['sectionId']; ?>">
                        <?= $section['department'] . " - " . $section['semester'] . " - " . $section['subject']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Please select a section.</div>
        </div>

        <div class="mb-3">
            <label for="studentId" class="form-label">Student</label>
            <select class="form-control" id="studentId" name="studentId" required>
                <option value="">Select Student</option>
            </select>
            <div class="invalid-feedback">Please select a student.</div>
        </div>

        <div class="mb-3">
            <label for="assignmentId" class="form-label">Assignment</label>
            <select class="form-control" id="assignmentId" name="assignmentId" required>
                <option value="">Select Assignment</option>
            </select>
            <div class="invalid-feedback">Please select an assignment.</div>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">Upload File</label>
            <input type="file" class="form-control" id="file" name="file" required>
            <div class="invalid-feedback">Please upload a valid file.</div>
        </div>

        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $("#sectionId").change(function() {
            let sectionId = $(this).val();
            if (sectionId) {
                // Fetch students
                $.ajax({
                    url: "fetch_students.php",
                    type: "POST",
                    data: {sectionId: sectionId},
                    success: function(response) {
                        $("#studentId").html(response);
                    }
                });

                // Fetch assignments
                $.ajax({
                    url: "fetch_assignments.php",
                    type: "POST",
                    data: {sectionId: sectionId},
                    success: function(response) {
                        $("#assignmentId").html(response);
                    }
                });
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
