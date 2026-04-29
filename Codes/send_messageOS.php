<?php
session_start();

// Include the database connection file
include('NewDbConn.php');

// Check if the user is logged in by verifying if the session variable 'empID' is set
if (!isset($_SESSION['empID'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: logIn.php");
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the sender ID from the session
    $sender_id = $_SESSION['empID'];
    // Retrieve the receiver ID, message, heading, and file from the POST request
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $heading = !empty($_POST['heading']) ? $_POST['heading'] : 'No Heading'; // Default to 'No Heading' if empty
    $file = $_FILES['file'];

    // Prepare an SQL statement to insert the message into the messages table with heading
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, heading, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $heading, $message);
    $stmt->execute();
    // Get the ID of the inserted message
    $message_id = $stmt->insert_id;

    // Check if a file was uploaded
    if ($file['size'] > 0) {
        // Set the file path to store the uploaded file
        $file_path = "Documents/" . basename($file["name"]);
        // Move the uploaded file to the specified file path
        move_uploaded_file($file["tmp_name"], $file_path);

        // Prepare an SQL statement to insert the file path into the files table
        $stmt = $conn->prepare("INSERT INTO files (message_id, file_path) VALUES (?, ?)");
        $stmt->bind_param("is", $message_id, $file_path);
        $stmt->execute();
    }

    // Close the statement
    $stmt->close();
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
  <link rel="stylesheet" href=".css">

  <title>Send message</title>
    <link rel="icon" type="image/x-icon" href="EPMS1.JPG">
    <style>
        :root {
            --primary-deep: #0c1e3d;
            --primary-mid: #1e3a5f;
            --accent-teal: #0d9488;
            --accent-gold: #f59e0b;
            --bg-main: #f0f4f8;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --nav-width: 230px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Header - rich gradient, no border */
        .top-header {
            background: linear-gradient(90deg, #0c1e3d 0%, #1e3a5f 40%, #0d9488 100%);
            color: #fff;
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            position: relative;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(12, 30, 61, 0.25);
        }

        .header-item {
            flex: 1;
            text-align: center;
            min-width: 180px;
        }

        .header-item:first-child {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.15);
            text-align: left;
        }

        .header-message {
            flex: 1.5;
            font-style: italic;
            color: #e0f2f1;
            font-weight: 500;
            font-size: 1.05rem;
            text-align: center;
        }

        .header-item:last-child {
            color: #e0f2f1;
            font-weight: 600;
            font-size: 0.95rem;
            text-align: right;
            letter-spacing: 0.5px;
        }

        /* App Layout - header touches nav seamlessly */
        .app-layout {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        /* Side Navigation Rail - same bg as content, no borders */
        .side-nav {
            width: var(--nav-width);
            background: transparent;
            padding: 24px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            position: sticky;
            top: 0;
            height: calc(100vh - 60px);
            overflow-y: auto;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 18px;
            border-radius: 14px;
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .nav-link-item i {
            font-size: 1.15rem;
            width: 26px;
            text-align: center;
            color: var(--text-muted);
            transition: all 0.3s ease;
        }

        .nav-link-item:hover {
            background: rgba(13, 148, 136, 0.08);
            color: var(--accent-teal);
            transform: translateX(4px);
        }

        .nav-link-item:hover i {
            color: var(--accent-teal);
        }

        /* Active indicator - beautiful pill matching header gradient */
        .nav-link-item.active {
            background: linear-gradient(90deg, #1e3a5f 0%, #0d9488 100%);
            color: #fff;
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.35);
            transform: translateX(6px);
        }

        .nav-link-item.active i {
            color: #fff;
        }

        .nav-link-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: var(--accent-gold);
            border-radius: 0 4px 4px 0;
        }

        /* Log out special styling */
        .nav-link-item.logout {
            margin-top: auto;
            color: #dc2626;
        }

        .nav-link-item.logout i {
            color: #dc2626;
        }

        .nav-link-item.logout:hover {
            background: rgba(220, 38, 38, 0.08);
            color: #dc2626;
        }

        .nav-link-item.logout:hover i {
            color: #dc2626;
        }

        /* Main Content Area - same background */
        .main-content {
            flex: 1;
            padding: 28px 32px;
            background: transparent;
        }

        /* Cards styling */
        .dashboard-card {
            background: var(--bg-card);
            border: none;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(12, 30, 61, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(12, 30, 61, 0.12);
        }

        .card-header-custom {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            padding: 18px 22px;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body-custom {
            padding: 22px;
        }

        /* Color accents for cards */
        .border-accent-teal { border-left: 4px solid #0d9488; }
        .border-accent-gold { border-left: 4px solid #f59e0b; }
        .border-accent-indigo { border-left: 4px solid #4f46e5; }
        .border-accent-rose { border-left: 4px solid #e11d48; }
        .border-accent-slate { border-left: 4px solid #475569; }

        /* Buttons */
        .btn-gradient-teal {
            background: linear-gradient(90deg, #0d9488 0%, #0f766e 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-teal:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.35);
            color: white;
        }

        .btn-gradient-gold {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
            color: white;
        }

        .btn-gradient-slate {
            background: linear-gradient(90deg, #475569 0%, #334155 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(71, 85, 105, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-slate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(71, 85, 105, 0.35);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .side-nav {
                width: 70px;
                padding: 16px 8px;
            }
            .nav-link-item span {
                display: none;
            }
            .nav-link-item {
                justify-content: center;
                padding: 14px;
            }
            .nav-link-item i {
                font-size: 1.3rem;
                width: auto;
            }
            .main-content {
                padding: 18px;
            }
            .header-item:first-child {
                font-size: 1.1rem;
                text-align: center;
            }
            .header-message {
                display: none;
            }
        }

        /* Send Message Page Specific Styles */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-deep);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            color: var(--accent-teal);
        }

        .compose-form {
            max-width: 700px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-label i {
            color: var(--accent-teal);
            margin-right: 6px;
            width: 18px;
            text-align: center;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            color: var(--text-main);
            background: #fff;
            transition: all 0.3s ease;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        }

        select.form-control-custom {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        textarea.form-control-custom {
            resize: vertical;
            min-height: 140px;
        }

        .file-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-input-wrapper:hover {
            border-color: var(--accent-teal);
            background: rgba(13, 148, 136, 0.04);
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
        }

        .file-input-icon {
            color: var(--accent-teal);
            font-size: 1.3rem;
        }

        .file-input-text {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .file-input-text strong {
            color: var(--accent-teal);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 8px;
            padding-top: 16px;
            border-top: 1px solid rgba(0,0,0,0.04);
        }

        .btn-send {
            background: linear-gradient(90deg, #0d9488 0%, #0f766e 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.35);
            color: white;
        }

        .btn-cancel {
            background: #f1f5f9;
            border: none;
            color: var(--text-muted);
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }
    </style>
</head>
<body>


<!-- Header -->
<div class="top-header">
    <div class="header-item">Employee Performance Management System</div>
    <div class="header-item header-message">
        <?php
        $messages = [
            "Have a good day!", "Keep going!", "You're doing great!",
            "Stay positive!", "Believe in yourself!", "You can do it!",
            "Never give up!", "Keep pushing forward!", "Stay strong!",
            "Keep up the good work!"
        ];
        $currentDate = date("Y-m-d");
        if (!isset($_SESSION['lastMessageDate']) || $_SESSION['lastMessageDate'] !== $currentDate) {
            $randomIndex = array_rand($messages);
            $_SESSION['lastMessage'] = $messages[$randomIndex];
            $_SESSION['lastMessageDate'] = $currentDate;
        }
        echo $_SESSION['lastMessage'];
        ?>
    </div>
    <div class="header-item">
        <?php $mydate = getdate(date("U")); echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]"; ?>
    </div>
</div>

<!-- App Layout with Side Nav -->
<div class="app-layout">
    <!-- Persistent Side Navigation Rail -->
    <aside class="side-nav">
        <a href="other_staff.php" class="nav-link-item ">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="other_staffProfile.php" class="nav-link-item">
            <i class="fas fa-user-circle"></i>
            <span>User Profile</span>
        </a>
        <a href="messagesOS.php" class="nav-link-item active">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <!--<a href="HRempl.php" class="nav-link-item">
            <i class="fas fa-users"></i>
            <span>Employees</span>
        </a>-->
        <a href="request_leaveOS.php" class="nav-link-item">
            <i class="fas fa-calendar-check"></i>
            <span>Leave</span>
        </a>
        <a href="feedback.php" class="nav-link-item">
            <i class="fas fa-comment"></i>
            <span>Feedback</span>
        </a>
        <a href="issuesOss.php" class="nav-link-item">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Issues</span>
        </a>
        <a href="Page.html" class="nav-link-item">
            <i class="fas fa-building"></i>
            <span>Company Page</span>
        </a>
        <a href="logOut.php" class="nav-link-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Log Out</span>
        </a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-paper-plane"></i>
                Compose Message
            </div>
            <a href="messagesOS.php" class="btn btn-gradient-slate">
                <i class="fas fa-arrow-left" style="margin-right: 6px;"></i>Back to Inbox
            </a>
        </div>

        <div class="dashboard-card border-accent-teal">
            <div class="card-header-custom">
                <i class="fas fa-edit" style="color: #0d9488;"></i>
                <span>New Message</span>
            </div>
            <div class="card-body-custom">
                <form method="POST" action="send_messageOS.php" enctype="multipart/form-data" class="compose-form">
                    <!-- Receiver selection dropdown -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>To
                        </label>
                        <select name="receiver_id" class="form-control-custom" required>
                            <option value="" disabled selected>Select recipient...</option>
                            <?php
                            // Retrieve the list of employees excluding the current user
                            $current_user_id = $_SESSION['empID'];
                            $result = $conn->query("SELECT empID, empFname, empLname, empRole FROM emplooyedetails WHERE empID != '$current_user_id' AND approved = 1 ORDER BY empFname");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['empID'] . "'>" . htmlspecialchars($row['empFname'] . ' ' . $row['empLname'] . ' — ' . $row['empRole']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Heading input -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-heading"></i>Subject
                        </label>
                        <input type="text" name="heading" class="form-control-custom" placeholder="Enter message subject..." required>
                    </div>

                    <!-- Message text area -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-comment-alt"></i>Message
                        </label>
                        <textarea name="message" class="form-control-custom" rows="6" placeholder="Type your message here..." required></textarea>
                    </div>

                    <!-- File upload input -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-paperclip"></i>Attachment
                        </label>
                        <div class="file-input-wrapper">
                            <i class="fas fa-cloud-upload-alt file-input-icon"></i>
                            <span class="file-input-text"><strong>Click to upload</strong> or drag and drop a file here</span>
                            <input type="file" name="file" id="fileInput" onchange="updateFileName(this)">
                        </div>
                        <div id="fileNameDisplay" style="margin-top: 8px; font-size: 0.85rem; color: var(--accent-teal); font-weight: 500;"></div>
                    </div>

                    <!-- Form actions -->
                    <div class="form-actions">
                        <a href="messagesHR.php" class="btn-cancel">
                            <i class="fas fa-times"></i>Cancel
                        </a>
                        <button type="submit" class="btn-send" onclick="return confirmSend()">
                            <i class="fas fa-paper-plane"></i>Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// Function to display selected file name
function updateFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    if (input.files && input.files[0]) {
        display.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 4px;"></i>Selected: ' + input.files[0].name;
    } else {
        display.textContent = '';
    }
}

// Function to confirm before sending
function confirmSend() {
    window.alert('Message sent successfully!');
    return true;
}
</script>

</body>
</html>

