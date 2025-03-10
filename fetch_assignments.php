<?php
include('config.php');

if (isset($_POST['sectionId'])) {
    $sectionId = $_POST['sectionId'];
    $result = mysqli_query($conn, "SELECT * FROM assignments WHERE section = $sectionId");

    echo '<option value="">Select Assignment</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['assignmentId'] . '">' . $row['name'] . ' - Due: ' . $row['lastDate'] . '</option>';
    }
}
mysqli_close($conn);
?>
