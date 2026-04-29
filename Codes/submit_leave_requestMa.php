<?php

// Start the session to use session variables
session_start();
// Include the file that connects to the database
include('NewDbConn.php');

// Check if the user is logged in by verifying if the session variable 'empID' is set
if (!isset($_SESSION['empID'])) {
    // If not logged in, redirect to the login page
    header("Location: logIn.php");
    exit();
}

// Get the employee ID from the session
$empID = $_SESSION['empID'];

// Get the leave request details from the POST request
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$reason = $_POST['reason'];

// Create an SQL query to insert the leave request details into the database
$sql = "INSERT INTO leave_requests (empID, start_date, end_date, reason) VALUES ('$empID', '$start_date', '$end_date', '$reason')";

// Execute the SQL query and check if it was successful
if ($conn->query($sql) === TRUE) {
    // If successful, print a message saying the leave request was submitted successfully
    echo "Leave request submitted successfully.";
    exit();
} else {
    // If there was an error with the query, print an error message with details
    echo "Error occured ";
}

// Close the database connection
$conn->close();
?>