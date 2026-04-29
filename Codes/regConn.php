<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
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

// Get the face encoding from the captured image
$face_image = $_POST['face_image'];
if (empty($face_image)) {
    echo "<script>alert('Face capture is required for registration.');</script>";
    echo "<script>window.location.href = 'register.php';</script>";
    exit();
}

// Save the face image temporarily
$temp_image = 'temp_faces/' . uniqid() . '.jpg';
$face_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $face_image));
file_put_contents($temp_image, $face_data);

// Execute Python script for face encoding
$command = "python facial_recognition_login.py register \"$empID\" \"$temp_image\"";
$output = [];
$return_var = 0;
exec($command, $output, $return_var);

if ($return_var !== 0) {
    unlink($temp_image); // Clean up temporary file
    echo "<script>alert('Face registration failed. Please try again.');</script>";
    echo "<script>window.location.href = 'register.php';</script>";
    exit();
}

// Check if the user is already registered
$checkUserQuery = "SELECT * FROM emplooyedetails WHERE empIDr = '$empID'";
$result = $conn->query($checkUserQuery);

if ($result->num_rows > 0) {
    unlink($temp_image); // Clean up temporary file
    echo "<script>alert('ID NUMBER already exists');</script>";
    echo "<script>window.location.href = 'register.php';</script>";
} else {
    // SQL query to insert the form data into the database
    $sql = "INSERT INTO emplooyedetails (empFname, empLname, empIdNumber, empRace, empMaritalStatus, empGender, empEmail, empPhoneNum, empPosition, empRole, empPass, empPassconfi)
            VALUES ('$empFname', '$empLname', '$empIdNumber', '$empRace', '$empMaritalStatus', '$empGender', '$empEmail', '$empPhoneNum', '$empPosition', '$empRole', '$password', '$confirmpassword')";

    // Execute the SQL query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        unlink($temp_image); // Clean up temporary file
        echo "<script>alert('Successfully registered and waiting for approval within the hour');</script>";
        echo "<script>window.location.href = 'logIn.php';</script>";
    } else {
        unlink($temp_image); // Clean up temporary file
        echo "Error Occurred: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>