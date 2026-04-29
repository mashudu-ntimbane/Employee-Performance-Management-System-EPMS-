<?php

session_start();

// Include the database connection file
include('NewDbConn.php');

// Check if the request method is POST and if the 'approve' button was clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    // Retrieve the employee ID from the POST request
    $empID = $_POST['empID'];

    // Prepare an SQL statement to update the employee's approval status in the database
    $stmt = $conn->prepare("UPDATE emplooyedetails SET approved = 1 WHERE empID = ?");
    $stmt->bind_param("i", $empID);

    // Execute the SQL statement and check if it was successful
    if ($stmt->execute()) {
        // Display an alert message if the user was approved successfully
        echo "<script>window.alert('User approved!')</script>";
    }
    // Close the prepared statement
    $stmt->close();
}

// Retrieve the list of employees who have not yet been approved
$result = $conn->query("SELECT empID, empFname, empPosition, empLname, approved FROM emplooyedetails WHERE approved = 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Font Awesome for icons -->
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <!-- Set favicon for the page -->
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <title>Approve</title>
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
        .approve-button {
          background-color: #007bff;
          color: white;
          border: none;
          padding: 7px 20px;
          cursor: pointer;
          border-radius: 5px;
        }
        .approve-button:hover{
          background-color: #0056b3;
        }
            .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            margin-bottom: 20px;
            border-radius: 0 0 12px 12px;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .header-item {
            flex: 1;
            text-align: center;
            min-width: 150px;
            font-size: 1rem;
            color: #495057;
        }

        .header-item:first-child {
            font-size: 1.3rem;
            color: #1a237e;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header-item:last-child {
            color: #6c757d;
            font-weight: 500;
        }

        .header-message {
            flex: 2;
            font-style: italic;
            color: #00695c;
            font-weight: 500;
        }

    </style>
</head>
<body>

<h2><i class='fas fa-users' style='font-size:24px'> New employees</i></h2>
<!-- Table to display the list of employees pending approval -->
<table class="table table-bordered">
<tr>
  <th style="background-color: #007bff; color: white;">empID</th>
  <th style="background-color: #007bff; color: white;">First name</th>
  <th style="background-color: #007bff; color: white;">Last name</th>
  <th style="background-color: #007bff; color: white;">Position</th>
  <th style="background-color: #007bff; color: white;">Action</th>
</tr>
<?php

// Check if there are any employees to approve
if ($result->num_rows > 0) {
    // Loop through each employee and display their details in the table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['empID']."</td>";
        echo "<td>".$row['empFname']."</td>";
        echo "<td>".$row['empLname']."</td>";
        echo "<td>".$row['empPosition']."</td>";
        echo "<td>
              <form method='post' action=''>
                <input type='hidden' name='empID' value='".$row['empID']."'>
               <input type='submit' name='approve' value='Approve' class='approve-button'>
              </form>
              </td>";
        echo "</tr>";
    }
} else {
    // Display a message if there are no employees to approve
    echo "<tr><td colspan='5'>No users to approve</td></tr>";
}
?>
</table>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>