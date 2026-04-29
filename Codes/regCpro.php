<?php
// Database connection parameters
$servername = "localhost";
$username = "Mashudu";
$password = "";
$dbname = "practice";

// Create a new connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data from POST request
$empFname = $_POST['fname'];
$empLname = $_POST['lname'];
$empIdNumber = $_POST['ID_number'];
$empRace = $_POST['Race'];
$empMaritalStatus = $_POST['Marital_status'];
$empGender = $_POST['gender'];
$empEmail = $_POST['email_Address'];
$empPhoneNum = $_POST['phone'];
$empPosition = $_POST['position'];
$empRole = $_POST['empRole'];
$password = $_POST['password'];
$confirmpassword = $_POST['confirmpassword'];

// Check if the user is already registered
$checkUserQuery = "SELECT * FROM emplooyedetails WHERE empIdNumber = '$empIdNumber'";
$result = $conn->query($checkUserQuery);

if ($result->num_rows > 0) {
    // If a user with the same empIdNumber is found, display an error message
    echo "<script>alert('ID NUMBER already exist');</script>";
    // Redirect back to the registration page
    echo "<script>window.location.href = 'register.php';</script>";
} else {
    // SQL query to insert the form data into the database
    $sql = "INSERT INTO emplooyedetails (empFname, empLname, empIdNumber, empRace, empMaritalStatus, empGender, empEmail, empPhoneNum, empPosition, empRole, empPass, empPassconfi)
            VALUES ('$empFname', '$empLname', '$empIdNumber', '$empRace', '$empMaritalStatus', '$empGender', '$empEmail', '$empPhoneNum', '$empPosition', '$empRole', '$password', '$confirmpassword')";

    // Execute the SQL query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        // Display a success message
        echo "<script>alert('Successfully registered and waiting for approval within the hour');</script>";
        // Redirect to the login page after the alert
        echo "<script>window.location.href = 'logIn.php';</script>";
    } else {
        // Display an error message if the query failed
        echo "Error Occurred: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>