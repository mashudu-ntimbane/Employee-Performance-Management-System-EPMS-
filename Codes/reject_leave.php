<?php
// Include the database connection file to establish a connection to the database
include('NewDbConn.php');

// Retrieve the leave request ID from the URL parameters
$id = $_GET['id'];

// Create an SQL query to update the status of the leave request to 'rejected'
$sql = "UPDATE leave_requests SET status='rejected' WHERE id='$id'";

// Execute the SQL query and check if it was successful
if ($conn->query($sql) === TRUE) {
    // If the query was successful, print a message indicating the leave request was rejected
    echo "Leave request rejected.";
    // Exit the script to ensure no further code is executed
    exit();
} else {
    // If there was an error executing the query, print an error message with details
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>