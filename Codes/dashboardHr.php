<?php

// Include the database connection file
include('NewDbConn.php');

// Fetch all pending leave requests from the database, joining with employee details to get employee names
$pending_requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status = 'pending'");
// Fetch all pending new users from the database, joining with employee details to get employee names
$pending_users = $conn->query("SELECT e.*, e.empFname FROM emplooyedetails e where = e.empID WHERE e.approved = 0 ");

// Fetch the count of new and pending leave requests for alerts
$new_leave_count = $pending_requests->num_rows;
$new_user_count = $pending_users->num_rows;

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap JavaScript bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Font Awesome for icons -->
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <!-- Include favicon -->
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <title>Leaves</title>
</head>
<body class="bg-light">
    <!-- Header displaying the company name and the current date -->
    <div class="row">
        <div class="col-sm-3 pt-2 bg-light" style="text-align:center">EPMS <span style='font-size:20px;'>&#128202;</span></div>
        <div class="col-sm-6 pt-2 bg-light" style="text-align:center">Leaves</div>
        <div class="col-sm-3 pt-2 bg-light" style="text-align:center">
            <?php
            $mydate = getdate(date("U"));
            echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
            ?>
        </div>
    </div>
    <!-- Navigation bar with links -->
    <nav class="navbar navbar-expand-sm bg-light border justify-content-center">
        <ul class="navbar-nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="HR.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="HRProfile.php">User profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messagesHR.php">Messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="HRempl.php">Employees</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="">Help</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="">Company page</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logOut.php">Log out</a>
            </li>
        </ul>
    </nav>
    
    <!-- Alerts for new/pending leave requests -->
    <div class="container mt-3">
        <?php if ($new_leave_count > 0): ?>
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-circle"></i> You have <strong><?= $new_leave_count; ?></strong> new/pending leave requests! 
                <a href="#pending-requests" class="alert-link">View Details</a>
            </div>
        <?php else: ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> No pending leave requests.
            </div>
        <?php endif; ?>
    </div>

   
    </div>
</body>
</html>