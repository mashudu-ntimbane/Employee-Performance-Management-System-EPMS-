<?php
// Start session
session_start();
include('NewDbConn.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the issue ID and employee selection from form submission
    $issueID = $_POST['issueID'];
    $workedWith = isset($_POST['worked_with']) ? $_POST['worked_with'] : 'no';

    if ($workedWith === 'yes' && isset($_POST['assigned_employee'])) {
        // Get the assigned employees from the form
        $assignedEmployees = $_POST['assigned_employee']; // This will be an array of employee IDs

        // Insert each assigned employee's first name into the worked_with table
        foreach ($assignedEmployees as $empID) {
            // Fetch the employee's first name (empFname) from the database
            $empStmt = $conn->prepare("SELECT empFname FROM emplooyedetails WHERE empID = ?");
            $empStmt->bind_param("i", $empID);
            $empStmt->execute();
            $empResult = $empStmt->get_result();

            if ($empResult->num_rows > 0) {
                $empRow = $empResult->fetch_assoc();
                $empFname = $empRow['empFname'];  // Store employee first name

                // Insert the empFname (employee's first name) into the worked_with table
                $stmt = $conn->prepare("INSERT INTO worked_with (issueID, staff_worked_with) VALUES (?, ?)");
                $stmt->bind_param("is", $issueID, $empFname);  // Using string bind type 's' for empFname
                $stmt->execute();
            }
        }

        // Fetch the names of the selected employees to display
        $workedWithNames = [];
        foreach ($assignedEmployees as $empID) {
            $empStmt = $conn->prepare("SELECT empFname FROM emplooyedetails WHERE empID = ?");
            $empStmt->bind_param("i", $empID);
            $empStmt->execute();
            $empResult = $empStmt->get_result();

            if ($empResult->num_rows > 0) {
                $empRow = $empResult->fetch_assoc();
                $workedWithNames[] = $empRow['empFname'];  // Store employee first name in array
            }
        }

        // Display the selected employees' names after saving
        echo "<script>alert('Worked with: " . implode(", ", $workedWithNames) . "');</script>";

        // Redirect back to the issues page with a success message
        header("Location: issuesOS.php?employee_assigned=1");
    } else {
        // Redirect back with a warning if no employee is selected
        header("Location: issuesOS.php?employee_assigned=0");
    }

    $stmt->close();
    $conn->close();
}
?>