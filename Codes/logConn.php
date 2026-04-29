<?php

session_start();

// Include the database connection file
include('NewDbConn.php');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve posted form data
    $empID = $_POST['empID'];
    $empPass = $_POST['empPass'];
    $empPosition = $_POST['empPosition'];
    
    // SQL query to check if the employee ID exists in the database
    $sql_check_id = "SELECT * FROM emplooyeDetails WHERE empID = '$empID'";
    $result_check_id = $conn->query($sql_check_id);

  
    // If the employee ID is not found in the database
    if ($result_check_id->num_rows == 0) {
        echo "<script>alert('Employee ID not found');</script>";
        echo "<script>window.location.href = 'logIn.php';</script>";
    } else {
        // Fetch the row corresponding to the employee ID
        $row = $result_check_id->fetch_assoc();
        
        // Verify the password
        if ($row['empPass'] != $empPass) {
            echo "<script>alert('Incorrect password');</script>";
            echo "<script>window.location.href = 'logIn.php';</script>";
        }
        // Verify the position
        elseif ($row['empPosition'] != $empPosition) {
            echo "<script>alert('Incorrect employee position selected');</script>";
            echo "<script>window.location.href = 'logIn.php';</script>";
        }
        // Check if the account is approved
        elseif ($row['approved'] != 1) {
            echo "<script>alert('Account waiting for approval');</script>";
            echo "<script>window.location.href = 'logIn.php';</script>";
        }
        // All checks passed
        else {
            // Set session variables
            $_SESSION['empID'] = $empID;
            $_SESSION['empPosition'] = $empPosition;

            // Redirect to the appropriate page based on employee position
            if ($empPosition === 'Other staff') {
                header('Location: other_staff.php');
                exit();
            } elseif ($empPosition === 'HR') {
                header('Location: HR.php');
                exit();
            } elseif ($empPosition === 'Manager') {
                header('Location: manager.php');
                exit();
            }
        }
    }
      //SQL query insert employee clocking details
      $now = date('Y-m-d H:i:s');
      $sql_insert = "INSERT INTO clock_in_records (empID,clock_in_time) values('$empID', '$now')";
    $result_insert = $conn->query($sql_insert);
   
}
session_destroy();