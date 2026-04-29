<?php
// Start session
session_start();
include('NewDbConn.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get issue ID and new status from form submission
    $issueID = $_POST['issueID'];
    $status = $_POST['status'];

    // Update the issue status in the database
    $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE issueID = ?");
    $stmt->bind_param("si", $status, $issueID);

    if ($stmt->execute()) {
        // Redirect back to the issues page with a success message
        header("Location: issuesOS.php?status_updated=1");
    } else {
        // Redirect with an error message
        header("Location: issuesOS.php?status_updated=0");
    }

    $stmt->close();
    $conn->close();
}
?>