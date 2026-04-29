<?php
session_start();
include('NewDbConn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
  
    $issueCategory = $_POST['issue_category'];
    $specificIssue = $_POST['specific_issue'];
    $role = $_POST['role'];

    // Insert the new issue into the database
    $sql = "INSERT INTO issues (issue_category, specific_issue, role, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $sender, $issueCategory, $specificIssue, $role);

    if ($stmt->execute()) {
        // Redirect to a confirmation or issues page
        header("Location: issue.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
} else {
    // Handle invalid form submission
    echo "Invalid request.";
}
?>