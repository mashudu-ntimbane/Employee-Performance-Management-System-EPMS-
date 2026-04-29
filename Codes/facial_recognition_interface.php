<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

function runPythonScript($action, $name = '') {
    $command = escapeshellcmd("python facial_recognition_login.py $action $name");
    error_log("Executing command: $command");
    $output = shell_exec($command);
    error_log("Python script output: $output");
    return $output;
}

function verifyWithDatabase($name) {
    $conn = new mysqli('localhost', 'Mashudu', '', 'Practice');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['id'];
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'register') {
            $name = $_POST['name'];
            error_log("Attempting to register user: $name");
            $result = runPythonScript('register', $name);
            echo $result;
        } elseif ($action == 'login') {
            error_log("Attempting login");
            $result = runPythonScript('login');
            if (strpos($result, 'Welcome') !== false) {
                $name = trim(str_replace('Welcome,', '', $result));
                $user_id = verifyWithDatabase($name);
                if ($user_id) {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_id'] = $user_id;
                    echo "Login successful. Welcome, $name (User ID: $user_id)";
                    error_log("Login successful for user: $name (ID: $user_id)");
                } else {
                    echo "Login failed. User not found in database.";
                    error_log("Login failed. User not found in database: $name");
                }
            } else {
                echo "Login failed. User not recognized.";
                error_log("Login failed. Python script output: $result");
            }
        }
    }
} else {
    // Display the HTML form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Facial Recognition Login</title>
    </head>
    <body>
        <h2>Register</h2>
        <form method="post">
            <input type="hidden" name="action" value="register">
            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="submit" value="Register">
        </form>

        <h2>Login</h2>
        <form method="post">
            <input type="hidden" name="action" value="login">
            <input type="submit" value="Login with Face">
        </form>
    </body>
    </html>
    <?php
}
?>