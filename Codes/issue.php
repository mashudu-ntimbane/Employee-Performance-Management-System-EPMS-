<?php
session_start();

// Include the database connection file
include('NewDbConn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted form data
    $issueCategory = $_POST['issueCategory'];
    $specificIssue = $_POST['specificIssue'];
    $role = $_POST['role'];
    
    // Assuming empID is stored in the session after user login
    $empID = $_SESSION['empID'];

    // Fetch user details based on empID
    $query = "SELECT ed.empFname, ed.empLname, b.B_name, r.id, r.r_name 
              FROM emplooyedetails ed
              JOIN Rooms r ON ed.empID = r.empID
              JOIN Buildings b ON r.id = b.id
              WHERE ed.empID = ?";
              
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $empID);
        $stmt->execute();
        $stmt->bind_result($empFname, $empLname, $name, $id, $name);
        $stmt->fetch();
        $stmt->close();
    }

    // Insert the issue into the Issues table
    $insertIssueQuery = "INSERT INTO Issues (empID, issue_category, specific_issue, role, status) 
                         VALUES (?, ?, ?, ?, 'Pending')";

    if ($stmt = $conn->prepare($insertIssueQuery)) {
        $stmt->bind_param('isss', $empID, $issueCategory, $specificIssue, $role);
        $stmt->execute();
        $stmt->close();
       
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href=".css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <style>
         body {
            background-color: #add8e6; /* Light blue background */
        }
        .centered-container {
            width: 50%;
            padding: 20px;
            margin-top: 50px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: white;
            transition: background-color 0.3s;
        }

        .centered-container:hover {
            background-color: #e3f2fd; /* Blue hover color */
        }

        .btn-custom {
            background-color: #0d6efd; /* Bootstrap primary blue */
            color: white;
        }

        .btn-custom:hover {
            background-color: #0b5ed7; /* Darker blue on hover */
        }
    </style>
</head>
<body >

<div class="d-flex justify-content-center">

    <div class="centered-container">
        <div class="code">    <button onclick="document.location='issuesOS.php'" class="btn btn-primary">Back</button></div>
        <br>

   <i class='fas fa-exclamation-circle' style='font-size:24px;color:black; text-align:center;'> Report an Issue</i><br><br>
        <form action="issue.php" method="POST" onsubmit="return handleFormSubmission()">
            <div class="mb-3">
                <label for="issueCategory" class="form-label">Issue Category:</label>
                <select id="issueCategory" name="issueCategory" class="form-select" onchange="updateSpecificIssues()" required>
                    <option value="">Select Category</option>
                    <option value="Hardware Issues">Hardware Issues</option>
                    <option value="Software Issues">Software Issues</option>
                    <option value="Network Connectivity">Network Connectivity</option>
                    <option value="Data Storage">Data Storage</option>
                    <option value="Security Breaches">Security Breaches</option>
                    <option value="Physical Infrastructure">Physical Infrastructure</option>
                    <option value="Furniture and fixtures">Furniture and fixtures</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="specificIssue" class="form-label">Specific Issue:</label>
                <select id="specificIssue" name="specificIssue" class="form-select" onchange="updateRole()" required>
                    <option value="">Select Specific Issue</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role:</label>
                <input type="text" id="role" name="role" class="form-control" readonly>
            </div>
            <button type="submit" class="btn btn-custom">Submit Issue</button>
        </form>
    </div>
</div>

<script>
    // Function to update specific issues based on selected category
    function updateSpecificIssues() {
        var category = document.getElementById("issueCategory").value;
        var specificIssue = document.getElementById("specificIssue");
        var role = document.getElementById("role");

        var issues = {
            "Hardware Issues": [
                { issue: "Computers", role: "IT Technician" },
                { issue: "Servers", role: "IT Technician" },
                { issue: "Printers", role: "IT Technician" },
                { issue: "Scanners", role: "IT Technician" }
            ],
            "Software Issues": [
                { issue: "Operating systems", role: "Software Developer/Engineer" },
                { issue: "Applications", role: "Software Developer/Engineer" },
                { issue: "Software bugs", role: "Software Developer/Engineer" }
            ],
            "Network Connectivity": [
                { issue: "Internet", role: "Network Engineer" },
                { issue: "Intranet", role: "Network Engineer" },
                { issue: "VPN issues", role: "Network Engineer" },
                { issue: "Network equipment (routers, switches, firewalls)", role: "Network Engineer" }
            ],
            "Data Storage": [
                { issue: "Database errors", role: "Database Administrator" },
                { issue: "Backup failures", role: "Database Administrator" }
            ],
            "Security Breaches": [
                { issue: "Cyberattacks", role: "Security Specialist" },
                { issue: "Data loss", role: "Security Specialist" }
            ],
            "Physical Infrastructure": [
                { issue: "Power outages", role: "Electrician" },
                { issue: "Faulty wiring", role: "Electrician" },
                { issue: "Outlets", role: "Electrician" },
                { issue: "Leaks", role: "Plumber" },
                { issue: "Clogs", role: "Plumber" },
                { issue: "Heating", role: "HVAC Technician" },
                { issue: "Ventilation", role: "HVAC Technician" },
                { issue: "Air conditioning problems", role: "HVAC Technician" },
                { issue: "Alarm systems", role: "Security Specialist" },
                { issue: "CCTV", role: "Security Specialist" },
                { issue: "Access control", role: "Security Specialist" }
            ],
            "Furniture and fixtures": [
                { issue: "Repairs", role: "Facilities Manager" },
                { issue: "Replacements", role: "Facilities Manager" }
            ]
        };

        specificIssue.innerHTML = "";
        role.value = "";

        issues[category].forEach(function(item) {
            var option = document.createElement("option");
            option.value = item.issue;
            option.text = item.issue;
            option.setAttribute("data-role", item.role);
            specificIssue.add(option);
        });
    }

    // Function to update the role based on selected specific issue
    function updateRole() {
        var specificIssue = document.getElementById("specificIssue");
        var role = document.getElementById("role");
        var selectedOption = specificIssue.options[specificIssue.selectedIndex];
        role.value = selectedOption.getAttribute("data-role");
    }

    // Function to handle form submission
    function handleFormSubmission() {
        alert('Issue submitted successfully!');
        // Here you could add code to handle session termination if necessary
        return true; // This allows the form to be submitted
    }
</script>

</body>
</html>