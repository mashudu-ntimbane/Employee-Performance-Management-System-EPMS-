<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workforce Issues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href=".css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <style>
        /* Custom styles for user's own issues table */
        .user-issues-table {
            border: 2px solid black;
            background-color: #e3f2fd;
        }
        .user-issues-table th {
            background-color: grey;
            color: white;
        }
        .user-issues-table td {
            border: 1px solid black;
        }
        .progress-bar {
            height: 20px;
            font-size: 14px;
            color: white;
        }
    </style>
</head>
<body>

<div class="row">
  <div class="col-sm-2 pt-2 bg-light text-center">EPMS <span style='font-size:20px;'>&#128202;</span></div>
  <div class="col-sm-8 pt-2 bg-light text-center">Issues</div>
  <div class="col-sm-2 pt-2 bg-light text-center">
    <?php
    // Display the current date
    $mydate = getdate(date("U"));
    echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
    ?>
  </div>
</div>

<nav class="navbar navbar-expand-sm bg-light border justify-content-center">
    <ul class="navbar-nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="other_staff.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="other_staffProfile.php">User profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="messagesOS.php">Messages</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="request_leave.php">Leave</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="issuesOS.php">Issues</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">Feedback</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">Help</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logOut.php">Log out</a>
        </li>
    </ul>
</nav>

<div class="bg-light">
    <?php
    session_start();
    include('NewDbConn.php');
// Check if the employee ID session variable is set, if not, redirect to the login page
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit(); // Ensure the script stops executing after redirection
}
    // Fetch issues and their statuses
    $empID = $_SESSION['empID'];
    $requests = $conn->query("SELECT issue_category, specific_issue, status, requested_date FROM issues i JOIN emplooyedetails e ON i.empID = e.empID WHERE e.empID = '$empID'");

    $query = "SELECT empRole, empFname FROM emplooyedetails WHERE empID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $empID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userRole = $row['empRole'];
    }

    // Fetch issues assigned to other staff
    $sql = "SELECT i.issueID, e.empFname, issue_category, specific_issue, status, requested_date 
            FROM issues i 
            JOIN emplooyedetails e ON i.empID = e.empID 
            WHERE role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userRole);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    <div class="container-fluid">
        <i class='fas fa-exclamation-triangle' style='font-size:24px;color:red'>Reported Issues by other staff members</i><br>
        <table class="table user-issues-table">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Issue Category</th>
                    <th>Specific Issue</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Progress</th>
                    <th>Worked with</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["empFname"]) ?></td>
                        <td><?= htmlspecialchars($row["issue_category"]) ?></td>
                        <td><?= htmlspecialchars($row["specific_issue"]) ?></td>
                        <td>
                            <form method="post" action="main_taskUp.php">
                                <input type="hidden" name="issueID" value="<?= htmlspecialchars($row["issueID"]) ?>">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="not started" <?= $row["status"] == "not started" ? "selected" : "" ?>>Not started</option>
                                    <option value="just started" <?= $row["status"] == "just started" ? "selected" : "" ?>>Just Started</option>
                                    <option value="about to finish" <?= $row["status"] == "about to finish" ? "selected" : "" ?>>About to Finish</option>
                                    <option value="fixed" <?= $row["status"] == "fixed" ? "selected" : "" ?>>Fixed</option>

                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($row["requested_date"]) ?></td>
                        <td>
                            <div class="progress">
                                <?php if ($row["status"] == "just started"): ?>
                                    <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 30%;"></div>
                                <?php elseif ($row["status"] == "about to finish"): ?>
                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 80%;"></div>
                                <?php elseif ($row["status"] == "fixed"): ?>
                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 100%;">Fixed</div>
                                <?php else: ?>
                                    <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" style="width: 15%;"></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
    <?php if ($row["status"] == "fixed"): ?>
        <form method="post" action="main_taskAssi.php" id="workedWithForm">
            <input type="hidden" name="issueID" value="<?= htmlspecialchars($row["issueID"]) ?>">
            <input type="checkbox" name="worked_with" id="workedWithCheckbox" value="yes"> Worked with someone?

            <!-- Employee selection dropdown, hidden initially -->
            <div id="employeeSelection" style="display:none;">
                <label>Select Employee(s) Worked With:</label>
                <select name="assigned_employee[]" class="form-select" multiple>
                    <?php
                    // Fetch the list of employees from the database
                    $employees = $conn->query("SELECT empID, empFname FROM emplooyedetails");
                    while ($employee = $employees->fetch_assoc()): ?>
                        <option value="<?= $employee['empID'] ?>"><?= $employee['empFname'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>

        <script>
            // JavaScript to toggle employee selection dropdown
            document.getElementById('workedWithCheckbox').addEventListener('change', function() {
                var employeeSelection = document.getElementById('employeeSelection');
                if (this.checked) {
                    employeeSelection.style.display = 'block';
                } else {
                    employeeSelection.style.display = 'none';
                }
            });
        </script>
    <?php endif; ?>
</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
// Close connection
$stmt->close();
$conn->close();
?>
</body>
</html>