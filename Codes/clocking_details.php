<?php

session_start();
//imcude database file
include('NewDbConn.php');

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit();
}

// Query to get clock-in details
$details_query = "SELECT c.empID,e.empFname,e.empLname,e.empRole, c.clock_in_time, c.clock_out_time FROM clock_in_records c join emplooyedetails e on c.empID=e.empID where e.approved = 1 ORDER BY c.clock_in_time DESC";
$details_result = $conn->query($details_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock-In Details</title>
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
    </style>
</head>
<body class="bg-light">
<div class="container-fluid">
<button onclick="document.location='HRempl.php'" class="btn btn-primary">Back</button><br><br>
<i class='fas fa-id-card' style='font-size:24px'>Clocking Details</i><br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th colspan="5" id="logins-header" style="color: #007bff;"><strong>Logins</strong></th>
            </tr>
            <tr>
            <th style="background-color: #007bff; color: white;">Employee ID</th>
            <th style="background-color: #007bff; color: white;">Employee Name</th>
            <th style="background-color: #007bff; color: white;">Employee Last Name</th>
            <th style="background-color: #007bff; color: white;">Role</th>
            <th style="background-color: #007bff; color: white;">Clock-In Time</th>
            <th style="background-color: #007bff; color: white;">Clock-Out Time</th>
               
            </tr>
        </thead>
        <tbody>
            <?php 
            $current_date = null;
            while ($row = $details_result->fetch_assoc()) {
                $date = date('Y-m-d', strtotime($row['clock_in_time']));
                if ($current_date != $date) {
                    if ($current_date !== null) {
                        echo '<tr><td colspan="6" style="color: #007bff;"><strong>Previous Logins</strong></td></tr>';
                    }
                    echo "<tr><td colspan='6' ><strong>Logins on $date</strong></td></tr>";
                    $current_date = $date;
                }
            ?>
            <tr>
                <td><?php echo $row['empID']; ?></td>
                <td><?php echo $row['empFname']; ?></td>
                <td><?php echo $row['empLname'];?></td>
                <td><?php echo $row['empRole']; ?></td>
                <td><?php echo $row['clock_in_time']; ?></td>
                <td><?php echo $row['clock_out_time']; ?></td>
              
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>