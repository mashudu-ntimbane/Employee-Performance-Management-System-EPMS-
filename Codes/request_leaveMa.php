<?php

// Start the session to access session variables
session_start();
// Include the database connection file
include('NewDbConn.php');

// Check if the user is logged in by verifying if the session variable 'empID' is set
if (!isset($_SESSION['empID'])) {
    // If not logged in, redirect to the login page
    header("Location: logIn.php");
    exit();
}

// Get the employee ID from the session
$empID = $_SESSION['empID'];

// Fetch leave requests for the logged-in employee from the database
$requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status IN ('rejected', 'approved', 'pending') AND e.empID ='$empID' ");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Leave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href=".css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <style>
      i {
            color: #007bff;
            margin-right: 10px;
        }

        .fas {
            margin-right: 10px;
        }

        body {
            font-family: 'Arial', sans-serif;
        }

        .nav-link.active {
            font-weight: bold;
            color: #007bff !important;
            border-bottom: 2px solid #007bff;
        }

        .nav-link:hover {
            color: #0056b3 !important;
        }

        .row {
            border-bottom: 2px solid #007bff;
        }
    </style>
</head>
<script>
        // JavaScript to ensure end date is not less than start date
        function validateDates() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (new Date(endDate) < new Date(startDate)) {
                alert('End date cannot be earlier than start date.');
                return false;
            }
            return true;
        }
    </script>
<body class="bg-light">
<div class="row">
  <div class="col-sm-3 pt-2 bg-light" style="text-align:center">EPMS <span style='font-size:20px;'>&#128202;</span></div>
  <div class="col-sm-6 pt-2 bg-light" style="text-align:center">Leave</div>
  <div class="col-sm-3 pt-2 bg-light" style="text-align:center"><?php
$mydate=getdate(date("U"));
echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
?></div>
<nav class="navbar navbar-expand-sm bg-light border justify-content-center ">
    <ul class="navbar-nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="manager.php">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="managerProfile.php">User profile</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="messagesMa.php">Messages</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="request_leaveMa.php">Leave</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="issuesMa.php">Issues</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="Track_issues.php">Track Workdone</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="send_feedback_Ma.php">Feedback</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="Page_after_logIn.html">Company page</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="logOut.php">Log out</a>
    </li>

  </ul>
</nav>
</div>
<!-- Display the leave requests and provide a form to submit new leave requests -->
<div class="container">
        <h3> <i class='fas fa-envelope-square' style='font-size:24px'> My Requests</i><br></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="background-color: #007bff; color: white;">Date Requested</th>
                    <th style="background-color: #007bff; color: white;">Start Date</th>
                    <th style="background-color: #007bff; color: white;">End Date</th>
                    <th style="background-color: #007bff; color: white;">Reason</th>
                    <th style="background-color: #007bff; color: white;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $requests->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= $row['start_date'] ?></td>
                    <td><?= $row['end_date'] ?></td>
                    <td><?= $row['reason'] ?></td>
                    <td><?= $row['status'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <i class='fas fa-comment-dots' style='font-size:24px'> Request Leave</i>
        <form action="submit_leave_requestMa.php" method="post" onsubmit="return validateDates()">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Request Leave</button>
        </form>
    </div>
</body>
</html>