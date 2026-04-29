<?php
    
    session_start();
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
  <link rel="stylesheet" href=".css">
  <link rel="icon" type="image/x-icon" href="Tirelo.JPG">

  <title>Track issues</title>
  <style>
    /* Custom styles for user's own issues table */
    .user-issues-table {
            border: 2px solid black;
            background-color:  #e3f2fd;
        }
        .user-issues-table th {
            background-color: #007bff;
            color: white;
            border: 1px solid black;
        }
        .user-issues-table td {
            border: 1px solid black;
        }
        .user-issues-table th {
        border-right: 1px solid black; /* Border between columns */
        padding: 10px;
        text-align: left;
        }

        .user-issues-table th:last-child {
        border-right: none;
        }

        .user-issues-table thead th {
        border-bottom: 2px solid #000;
        }

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
        .report-issue {
  border: 1px solid black;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin: 20px auto;
  width: 50%;
}

.report-issue {
  border: 1px solid black;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  margin: 20px auto;
  width: 50%;
}

.report-issue:hover {
  background-color: #f9f9f9;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.report-issue h2 {
  margin-top: 0;
}

.report-issue i {
  margin-bottom: 10px;
}

.report-issue button {
  margin-top: 10px;
}
    

        .star-rating {
    display: inline-block;
    direction: rtl;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    color: #bbb;
    font-size: 1.5em;
    padding: 0;
    cursor: pointer;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type="radio"]:checked ~ label {
    color: #f90;
}
.star-rating {
            color: #f90;
            font-size: 1.5em;
        }
        
.star-rating {
    display: inline-block;
    direction: rtl;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    color: #bbb;
    font-size: 1.5em;
    padding: 0;
    cursor: pointer;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type="radio"]:checked ~ label {
    color: #f90;
}


    
    
    </style>
</head>
<body class="bg-light">
<div class="row">
    <div class="col-sm-3 pt-2 bg-light" style="text-align:center">EPMS <span style='font-size:20px;'>&#128202;</span></div>
    <div class="col-sm-6 pt-2 bg-light" style="text-align:center">Manager profile</div>
    <div class="col-sm-3 pt-2 bg-light" style="text-align:center">
        <?php
        // Display the current date
        $mydate = getdate(date("U"));
        echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
        ?>
    </div>
</div>
<nav class="navbar navbar-expand-sm bg-light border justify-content-center ">
    <ul class="navbar-nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="manager.php">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link " href="managerProfile.php">User profile</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="messagesMa.php">Messages</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="request_leaveMa.php">Leave</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="issuesMa.php">Issues</a>
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
</nav><br>
<div class="report-issue bg-light text-center">
  <h2><i class="fa fa-warning" style="font-size:30px;color:red"></i> Report an Issue</h2>
  <button onclick="document.location='issueMa.php'" class="btn btn-secondary">Click here to report an issue</button>

</div>
        <div >
    
        
<?php
   
   // Include the database connection file
   include('NewDbConn.php');

   // Check if the user is logged in by verifying the 'empID' session variable
   if (!isset($_SESSION['empID'])) {
       // If not logged in, redirect to the login page
       header("Location: logIn.php");
       exit();
   }

   // Get the logged-in user's empID from the session
   $empID = $_SESSION['empID'];

   $requests = $conn->query("SELECT i.issueID,issue_category, specific_issue, status, requested_date,attended_by,staff_worked_with,rating,in_progress_time, fixed_time  FROM issues i JOIN emplooyedetails e ON i.empID = e.empID  WHERE e.empID = '$empID'");
   
// Fetch user's full name (empFname) from the database
$query = "SELECT empFname FROM emplooyedetails WHERE empID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $empID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
   $row = $result->fetch_assoc();
   $userName = $row['empFname'];  // Store the empFname for use in "Attended By"
} else {
   echo "Error: User not found.";
   exit();
}

// If a status update is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['issueID'])) {
   $status = $_POST['status'];
   $issueID = $_POST['issueID'];
   $attendedBy = ($status == "Pending") ? NULL : $userName;

   // Update the issue's status and set "Attended By" only if the status is not "Pending"
   $updateQuery = "UPDATE issues SET status = ?, attended_by = ?";

   if ($status == "Pending") {
       $updateQuery .= ", in_progress_time = NULL, fixed_time = NULL";
   } elseif ($status == "In_Progress") {
       $updateQuery .= ", in_progress_time = CURRENT_TIMESTAMP";
   } elseif ($status == "fixed") {
       $updateQuery .= ", fixed_time = CURRENT_TIMESTAMP";
   }

   $updateQuery .= " WHERE issueID = ?";

   $stmt = $conn->prepare($updateQuery);
   $stmt->bind_param("ssi", $status, $attendedBy, $issueID);
   $stmt->execute();

   if ($stmt->execute()) {
       $_SESSION['status_update_success'] = "Issue status updated successfully.";
   } else {
       $_SESSION['status_update_error'] = "Error updating issue status.";
   }

 
}
   // Fetch the user's role from the database using empID
   $query = "SELECT empRole, empFname FROM emplooyedetails WHERE empID = ?";
   $stmt = $conn->prepare($query);
   $stmt->bind_param("i", $empID);
   $stmt->execute();
   $result = $stmt->get_result();

   if ($result->num_rows > 0) {
       $row = $result->fetch_assoc();
       $userRole = $row['empRole'];
   } else {
       // If the user is not found in the database, handle the error
       echo "Error: User not found.";
       exit();
   }

   // Fetch issues from the database that correspond to the current user's role
   $sql = "SELECT i.issueID, e.empFname, issue_category, specific_issue, status,i.attended_by, requested_date, r.r_name, b.b_name 
           FROM issues i 
           JOIN emplooyedetails e ON i.empID = e.empID 
           JOIN rooms r ON r.empID = e.empID 
           JOIN buildings b ON b.id = r.building_id 
           WHERE role = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("s", $userRole);
   $stmt->execute();
   $result = $stmt->get_result();

   // Count pending issues
   $pendingCount = 0;

   // Function to calculate time spent
function calculateTimeSpent($inProgressTime, $fixedTime) {
   if (!$inProgressTime || !$fixedTime) {
       return "N/A";
   }

   $start = new DateTime($inProgressTime);
   $end = new DateTime($fixedTime);
   $interval = $start->diff($end);

   $days = $interval->d;
   $hours = $interval->h;
   $minutes = $interval->i;

   $timeSpent = "";
   if ($days > 0) {
       $timeSpent .= $days . " day" . ($days > 1 ? "s" : "") . " ";
   }
   if ($hours > 0) {
       $timeSpent .= $hours . " hour" . ($hours > 1 ? "s" : "") . " ";
   }
   if ($minutes > 0) {
       $timeSpent .= $minutes . " minute" . ($minutes > 1 ? "s" : "");
   }

   return trim($timeSpent) ?: "Less than a minute";
}
   ?>
   <div class="container">
   <i class='fas fa-exclamation-triangle' style='font-size:24px;color:black'> My Reported issues</i><br>
   <?php if ($requests->num_rows > 0): ?>
   <table class="table user-issues-table">
   <thead>
       <tr>
           <th>IssueID</th>
           <th>Issue Category</th>
           <th>Specific Issue</th>
           <th>Status</th>
           <th>Date</th>
           <th>Attended By</th>

           <th>Rating</th>
          
       </tr>
   </thead>
   <tbody>
   <?php while ($row = $requests->fetch_assoc()): ?>
       <tr>
           <td><?php echo htmlspecialchars($row['issueID']); ?></td>
           <td><?php echo htmlspecialchars($row['issue_category']); ?></td>
           <td><?php echo htmlspecialchars($row['specific_issue']); ?></td>
           <td><?php echo htmlspecialchars($row['status']); ?></td>
           <td><?php echo htmlspecialchars($row['requested_date']); ?></td>
           <td><?php echo htmlspecialchars($row['attended_by']); ?></td>
           
 
           <td>
               <?php if ($row['status'] == 'fixed' && is_null($row['rating'])): ?>
                   <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ratingModal<?php echo $row['issueID']; ?>">
                       Rate
                   </button>
                   
                   <!-- Rating Modal -->
                   <div class="modal fade" id="ratingModal<?php echo $row['issueID']; ?>" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
                       <div class="modal-dialog">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title" id="ratingModalLabel">Rate the Service</h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                               </div>
                               <div class="modal-body">
                                   <form action="submit_rating.php" method="post">
                                       <input type="hidden" name="issueID" value="<?php echo $row['issueID']; ?>">
                                       <div class="mb-3">
                                           <label for="descriptiveRating" class="form-label">Descriptive Rating:</label>
                                           <select class="form-select" id="descriptiveRating" name="descriptiveRating" required>
                                               <option value="">Choose...</option>
                                               <option value="excellent">Excellent</option>
                                               <option value="good">Good</option>
                                               <option value="average">Average</option>
                                               <option value="poor">Poor</option>
                                           </select>
                                       </div>
                                       <div class="mb-3">
                                           <label class="form-label">Graphic Rating:</label>
                                           <div class="star-rating">
                                               <input type="radio" id="star5_<?php echo $row['issueID']; ?>" name="graphicRating" value="5" required/>
                                               <label for="star5_<?php echo $row['issueID']; ?>">&#9733;</label>
                                               <input type="radio" id="star4_<?php echo $row['issueID']; ?>" name="graphicRating" value="4" />
                                               <label for="star4_<?php echo $row['issueID']; ?>">&#9733;</label>
                                               <input type="radio" id="star3_<?php echo $row['issueID']; ?>" name="graphicRating" value="3" />
                                               <label for="star3_<?php echo $row['issueID']; ?>">&#9733;</label>
                                               <input type="radio" id="star2_<?php echo $row['issueID']; ?>" name="graphicRating" value="2" />
                                               <label for="star2_<?php echo $row['issueID']; ?>">&#9733;</label>
                                               <input type="radio" id="star1_<?php echo $row['issueID']; ?>" name="graphicRating" value="1" />
                                               <label for="star1_<?php echo $row['issueID']; ?>">&#9733;</label>
                                           </div>
                                       </div>
                                       <button type="submit" class="btn btn-primary">Submit Rating</button>
                                   </form>
                               </div>
                           </div>
                       </div>
                   </div>
               <?php elseif (!is_null($row['rating'])): ?>
                   <span class="badge bg-success">Rated: <?php echo $row['rating']; ?> stars</span>
               <?php elseif ($row['status'] != 'fixed'): ?>
                   <span class="badge bg-secondary">Not yet fixed</span>
               <?php endif; ?>
             </td>
               </tr>
           <?php endwhile; ?>
           </tbody>
       </table>
   <?php else: ?>
       <div class="alert alert-info" role="alert">
           You have not reported any issues yet.
       </div>
   <?php endif; ?>

   </div><br>
   <div class="container">
   <i class='fas fa-exclamation-triangle' style='font-size:24px;color:red'> Reported Issues by other staff members</i><br>
   <?php if ($result->num_rows > 0): ?>
   <table class="table user-issues-table">
       <thead>
           <tr>
           <th>IssueID</th>
               <th>Sender</th>
               <th>Issue Category</th>
               <th>Specific Issue</th>
               <th>Room Name</th>
               <th>Building Name</th>
               <th>Status</th>
               <th>Attended By</th>
               <th>Date</th>
               <th>Progress</th>
              
           </tr>
       </thead>
       <tbody>
           <?php while ($row = $result->fetch_assoc()): ?>
               <tr>
               <td><?php echo htmlspecialchars($row['issueID']); ?></td>
                   <td><?= htmlspecialchars($row["empFname"]) ?></td>
                   <td><?= htmlspecialchars($row["issue_category"]) ?></td>
                   <td><?= htmlspecialchars($row["specific_issue"]) ?></td>
                   <td><?= htmlspecialchars($row["r_name"]) ?></td>
                   <td><?= htmlspecialchars($row["b_name"]) ?></td>
                   <td>
                       <form method="post" action="">
                           <input type="hidden" name="issueID" value="<?= htmlspecialchars($row["issueID"]) ?>">
                           <select name="status" class="form-select" onchange="this.form.submit()">
                               <option value="Pending" <?= $row["status"] == "Pending" ? "selected" : "" ?>>Pending</option>
                               <option value="In_Progress" <?= $row["status"] == "In_Progress" ? "selected" : "" ?>>In Progress</option>
                               <option value="fixed" <?= $row["status"] == "fixed" ? "selected" : "" ?>>Fixed</option>
                           </select>
                       </form>
                   </td>
                      <td><?= $row["status"] == "Pending" ? "NULL" : htmlspecialchars($row["attended_by"]) ?></td>
                   <td><?= htmlspecialchars($row["requested_date"]) ?></td>
                   <td>
                   <div class="progress">
                               <?php if ($row["status"] == "just started"): ?>
                                   <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 100%;">Just started</div>
                               <?php elseif ($row["status"] == "about to finish"): ?>
                                   <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 100%;">About to Finish</div>
                                   <?php elseif ($row["status"] == "In_Progress"): ?>
                                       <div class="progress-bar bg-seconadry progress-bar-striped progress-bar-animated" style="width: 100%";>In Progress</div>
                                   <?php elseif ($row['status'] == 'Pending'): ?>
                           <div class="spinner">
                               <div class="spinner-border spinner-border-sm text-dark" role="spinner"></div>
                           </div>
                               <?php elseif ($row["status"] == "fixed"): ?>
                                   <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 100%;">Fixed</div>
                               <?php else: ?>
                                   <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" style="width: 100%;">Not started</div>
                               <?php endif; ?>
                           </div>
                   </td>
                  
               </tr>
           <?php endwhile; ?>
       </tbody>
   </table>
   <?php else: ?>
       <div class="alert alert-info" role="alert">
           No issues have been reported by other staff members.
       </div>
   <?php endif; ?>
</div>

<?php
// Close connection
$stmt->close();
$conn->close();
?>

</body>
</html>

