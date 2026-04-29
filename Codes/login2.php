<?php
session_start();
require_once 'db_config.php'; // Make sure this file exists with your database credentials

// Set up logging
ini_set('log_errors', 1);
ini_set('error_log', 'login_errors.log');

function createConnection() {
    global $servername, $username, $password, $dbname;
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return null;
    }
    return $conn;
}

function processPasswordLogin($conn, $idNumber, $password) {
    $stmt = $conn->prepare("SELECT empIdNumber, empFname, empLname, empRole, empPass FROM emplooyedetails WHERE empIdNumber = ?");
    $stmt->bind_param("s", $idNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['empPass']) { // In production, use password_verify()
            // Log successful login
            logLoginAttempt($conn, $idNumber, 'password', 'success', 1.0);
            
            // Set session variables
            $_SESSION['user_id'] = $user['empIdNumber'];
            $_SESSION['user_name'] = $user['empFname'] . ' ' . $user['empLname'];
            $_SESSION['user_role'] = $user['empRole'];
            
            return true;
        }
    }
    
    // Log failed login attempt
    logLoginAttempt($conn, $idNumber, 'password', 'failed', 0.0);
    return false;
}

function processFaceLogin($conn, $faceImage) {
    // Save the captured image temporarily
    $tempImage = 'temp_faces/' . uniqid() . '.jpg';
    $faceData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $faceImage));
    file_put_contents($tempImage, $faceData);
    
    // Execute face recognition script
    $command = "python3 script.py login \"$tempImage\"";
    $output = [];
    exec($command, $output, $return_var);
    
    // Clean up temporary file
    unlink($tempImage);
    
    if ($return_var === 0 && !empty($output)) {
        // Parse the Python script output
        // Expected format: "user_id:confidence_score"
        list($userId, $confidence) = explode(':', $output[0]);
        
        if ($confidence >= 0.6) { // Adjust threshold as needed
            // Fetch user details
            $stmt = $conn->prepare("SELECT empIdNumber, empFname, empLname, empRole FROM emplooyedetails WHERE empIdNumber = ?");
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Log successful login
                logLoginAttempt($conn, $userId, 'face', 'success', $confidence);
                
                // Set session variables
                $_SESSION['user_id'] = $user['empIdNumber'];
                $_SESSION['user_name'] = $user['empFname'] . ' ' . $user['empLname'];
                $_SESSION['user_role'] = $user['empRole'];
                
                return true;
            }
        }
    }
    
    // Log failed login attempt
    logLoginAttempt($conn, null, 'face', 'failed', 0.0);
    return false;
}

function logLoginAttempt($conn, $empId, $method, $status, $confidence) {
    $stmt = $conn->prepare("INSERT INTO face_recognition_logs (emp_id, action, status, confidence_score, details) VALUES (?, ?, ?, ?, ?)");
    $details = "Login attempt via $method";
    $action = 'login';
    $stmt->bind_param("sssds", $empId, $action, $status, $confidence, $details);
    $stmt->execute();
}

// Main login processing
$conn = createConnection();
if (!$conn) {
    die("Connection failed");
}

$loginMethod = $_POST['login_method'];
$loginSuccess = false;

if ($loginMethod === 'password') {
    $idNumber = $_POST['ID_number'];
    $password = $_POST['password'];
    $loginSuccess = processPasswordLogin($conn, $idNumber, $password);
} elseif ($loginMethod === 'face') {
    $faceImage = $_POST['face_image'];
    $loginSuccess = processFaceLogin($conn, $faceImage);
}

$conn->close();

if ($loginSuccess) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php?error=1");
    exit();
}
?>