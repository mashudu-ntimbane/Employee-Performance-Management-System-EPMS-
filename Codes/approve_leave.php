<?php
// Include the file that establishes a connection to the database
include('NewDbConn.php');

// Get the leave request ID from the URL parameters
$id = $_GET['id'];

// Create an SQL query to update the status of the leave request to 'approved' for the specified ID
$sql = "UPDATE leave_requests SET status='approved' WHERE id='$id'";

// Execute the SQL query and check if it was successful
if ($conn->query($sql) === TRUE) {
    // If the query was successful, print a message saying the leave request was approved
    echo "Leave request approved.";
    // Exit the script to stop further execution
    exit();
} else {
    // If there was an error with the query, print an error message with details
    echo "Error occured ";
}

// Close the database connection
$conn->close();
?>
