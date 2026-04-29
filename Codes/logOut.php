<?php

session_start();

// Include the database connection file
include('NewDbConn.php');

// Check if the employee ID session variable is set, if not, redirect to the login page
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit(); // Ensure the script stops executing after redirection
}
$empID = $_SESSION['empID'];
$now = date('Y-m-d H:i:s');
$sql = "UPDATE clock_in_records SET clock_out_time ='$now' WHERE empID='$empID' AND clock_out_time IS NULL ORDER BY id DESC LIMIT 1";
$result_insert = $conn->query($sql);

session_destroy();
header("Location: logIn.php");
