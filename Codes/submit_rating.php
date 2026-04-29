<?php
session_start();
include('NewDbConn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issueID = $_POST['issueID'];
    $descriptiveRating = $_POST['descriptiveRating'];
    $graphicRating = $_POST['graphicRating'];

    // Check if the issue has already been rated
    $checkStmt = $conn->prepare("SELECT rating FROM issues WHERE issueID = ?");
    $checkStmt->bind_param("i", $issueID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $issueData = $checkResult->fetch_assoc();

    if (!is_null($issueData['rating'])) {
        $_SESSION['rating_error'] = "This issue has already been rated.";
        header("Location: issuesOS.php");
        exit();
    }

    // Fetch issue details
    $stmt = $conn->prepare("SELECT issue_category, specific_issue, attended_by, staff_worked_with FROM issues WHERE issueID = ?");
    $stmt->bind_param("i", $issueID);
    $stmt->execute();
    $result = $stmt->get_result();
    $issueDetails = $result->fetch_assoc();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert rating into issue_ratings table
        $insertStmt = $conn->prepare("INSERT INTO issue_ratings (issueID, descriptive_rating, graphic_rating, issue_category, specific_issue, attended_by, staff_worked_with) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("isissss", $issueID, $descriptiveRating, $graphicRating, $issueDetails['issue_category'], $issueDetails['specific_issue'], $issueDetails['attended_by'], $issueDetails['staff_worked_with']);
        $insertStmt->execute();

        // Update the issues table to mark that this issue has been rated
        $updateStmt = $conn->prepare("UPDATE issues SET rating = ? WHERE issueID = ?");
        $updateStmt->bind_param("ii", $graphicRating, $issueID);
        $updateStmt->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['rating_success'] = "Rating submitted successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['rating_error'] = "Error submitting rating. Please try again.";
    }

    header("Location: issuesOS.php");
    exit();
}
