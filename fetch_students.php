<?php
include('config.php');

if (isset($_POST['sectionId'])) {
    $sectionId = $_POST['sectionId'];
    $result = mysqli_query($conn, "SELECT * FROM student WHERE section = $sectionId");

    echo '<option value="">Select Student</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['studentId'] . '">' . $row['name'] . ' - Roll No: ' . $row['rollNo'] . '</option>';
    }
}
mysqli_close($conn);
?>
