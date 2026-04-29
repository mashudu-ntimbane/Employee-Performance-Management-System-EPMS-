<?php

session_start();

// Include the database connection file
include('NewDbConn.php');

// Check if the employee ID session variable is set, if not, redirect to the login page
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit(); // Ensure the script stops executing after redirection
}

// Retrieve the employee ID from the session
$empID = $_SESSION['empID'];

// Handle profile photo upload
$uploadDir = 'uploads/';
$profilePhoto = null;
foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
    $checkFile = $uploadDir . 'profile_' . $empID . '.' . $ext;
    if (file_exists($checkFile)) {
        $profilePhoto = $checkFile . '?' . filemtime($checkFile);
        break;
    }
}

$uploadMessage = '';

// Check if the request method is POST, indicating the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle photo upload
    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] == 0) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        // Remove old photo
        foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
            $oldFile = $uploadDir . 'profile_' . $empID . '.' . $ext;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        $fileExt = strtolower(pathinfo($_FILES['profilePhoto']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExt, $allowedTypes)) {
            $fileName = 'profile_' . $empID . '.' . $fileExt;
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $targetFile)) {
                $profilePhoto = $targetFile . '?' . time();
                $uploadMessage = "Photo uploaded successfully!";
            } else {
                $uploadMessage = "Error uploading photo.";
            }
        } else {
            $uploadMessage = "Invalid file type. Only JPG, PNG, and GIF allowed.";
        }
    }

    // Only update profile details when the profile form fields are submitted
    if (isset($_POST['newFname'])) {
        // Retrieve the updated profile details from the form submission
        $newFname = $_POST['newFname'];
        $newLname = $_POST['newLname'];
        $newEmail = $_POST['newEmail'];
        $newPhoneNum = $_POST['newPhoneNum'];
        $newMaritalStatus = $_POST['newMaritalStatus'];

        // Construct the SQL update query to update the employee details in the database
        $sql = "UPDATE emplooyeDetails SET 
                    empFname = '$newFname',
                    empLname = '$newLname',
                    empEmail = '$newEmail',
                    empPhoneNum = '$newPhoneNum',
                    empMaritalStatus = '$newMaritalStatus'
                WHERE empID = '$empID'";

        // Execute the SQL query and check if it was successful
        if ($conn->query($sql) === TRUE) {
            // Update the session variables with the new details
            $_SESSION['empFname'] = $newFname;
            $_SESSION['empLname'] = $newLname;
        } else {
            // Output an error message if the update failed
            echo "Error updating profile: " . $conn->error;
        }
    }
}

// Construct the SQL select query to retrieve the employee details from the database
$sql = "SELECT * FROM emplooyeDetails WHERE empID = '$empID'";
$result = $conn->query($sql);

// Check if the query returned any rows
if ($result->num_rows > 0) {
    // Fetch the row as an associative array
    $row = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Font Awesome for icons -->
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <!-- Link to an external CSS file -->
    <link rel="stylesheet" href=".css">
    <!-- Link to a favicon -->
     <link rel="icon" type="image/x-icon" href="EPMS1.JPG">
    <script>
        // JavaScript function to enable editing of a field
        function enableEdit(fieldId, inputId) {
            document.getElementById(fieldId).style.display = 'none';
            document.getElementById(inputId).style.display = 'inline';
            document.getElementById(inputId).removeAttribute("readonly");
            document.getElementById(inputId).focus();
        }

        // JavaScript function to submit the form
        function saveProfile() {
            document.getElementById("profileForm").submit();
        }
    </script>
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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;

            z-index: 2000;
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
            
        }

        
        /* Side Navigation Rail - same bg as content, no borders */
        .side-nav {
            width: var(--nav-width);
            background: transparent;
            padding: 24px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            
            position: fixed;
            top: var(--header-height);
            left: 0;

            height: calc(100vh - var(--header-height));

            overflow-y: auto;

            z-index: 1500;
            margin-top: 80px;
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
            margin-left: var(--nav-width);
            margin-top: var(--header-height);

   
            min-height: 100vh;
            margin-top: 80px;
            overflow-x: hidden;
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
            text-align: center;
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

        .btn-gradient-indigo {
            background: linear-gradient(90deg, #4f46e5 0%, #4338ca 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-indigo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
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
        /* Profile Page Specific Styles */
        .profile-header {
            background: linear-gradient(135deg, #0c1e3d 0%, #1e3a5f 50%, #0d9488 100%);
            border-radius: 18px;
            padding: 40px 30px;
            color: #fff;
            text-align: center;
            margin-bottom: 28px;
            box-shadow: 0 8px 32px rgba(12, 30, 61, 0.25);
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 3rem;
            border: 3px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .photo-upload-form {
            position: relative;
            display: inline-block;
            margin-top: -24px;
            margin-bottom: 8px;
            z-index: 10;
        }

        .photo-upload-btn {
            background: var(--accent-teal);
            color: #fff;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .photo-upload-btn:hover {
            transform: scale(1.1);
            background: #0f766e;
        }

        .upload-message {
            font-size: 0.85rem;
            margin-top: 8px;
            color: #fbbf24;
            font-weight: 500;
        }

        .profile-name {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .profile-role {
            font-size: 0.95rem;
            opacity: 0.85;
            font-weight: 500;
        }

        .profile-role-badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        .info-field {
            background: #f8fafc;
            border-radius: 14px;
            padding: 18px 20px;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            position: relative;
        }

        .info-field:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .info-field.editable:hover {
            border-color: rgba(13, 148, 136, 0.3);
        }

        .field-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .field-label i {
            font-size: 0.9rem;
            color: var(--accent-teal);
        }

        .field-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .field-value input {
            flex: 1;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
            transition: all 0.3s ease;
            background: #fff;
        }

        .field-value input:focus {
            outline: none;
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        }

        .edit-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(13, 148, 136, 0.1);
            color: var(--accent-teal);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .edit-btn:hover {
            background: var(--accent-teal);
            color: #fff;
            transform: scale(1.1);
        }

        .readonly-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px dashed #cbd5e1;
        }

        .readonly-section .field-value {
            color: var(--text-muted);
            font-weight: 500;
        }

        .section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--accent-gold);
        }

        .action-bar {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        .btn-gradient-teal, .btn-gradient-slate {
            min-width: 140px;
            padding: 10px 28px;
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
        <a href="HR.php" class="nav-link-item ">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="HRProfile.php" class="nav-link-item active">
            <i class="fas fa-user-circle"></i>
            <span>User Profile</span>
        </a>
        <a href="messagesHR.php" class="nav-link-item">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <a href="HRempl.php" class="nav-link-item">
            <i class="fas fa-users"></i>
            <span>Employees</span>
        </a>
        <a href="request_leaveHR.php" class="nav-link-item">
            <i class="fas fa-calendar-check"></i>
            <span>Leave</span>
        </a>
        <a href="issueHr.php" class="nav-link-item">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Issues</span>
        </a>
        <a href="Page_after_logIn.html" class="nav-link-item">
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
        <!-- Profile Header Banner -->
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if ($profilePhoto) : ?>
                    <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Photo">
                <?php else : ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <form class="photo-upload-form" method="POST" enctype="multipart/form-data">
                <label for="profilePhotoInput" class="photo-upload-btn" title="Change Photo">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="profilePhotoInput" name="profilePhoto" accept="image/jpeg,image/png,image/gif" style="display: none;" onchange="this.form.submit()">
            </form>
            <?php if ($uploadMessage) : ?>
                <div class="upload-message"><?php echo htmlspecialchars($uploadMessage); ?></div>
            <?php endif; ?>
            <div class="profile-name"><?php echo $row['empFname'] . ' ' . $row['empLname']; ?></div>
            <div class="profile-role"><?php echo $row['empRole']; ?></div>
            <span class="profile-role-badge">HR Department</span>
        </div>

        <!-- Profile Card -->
        <div class="dashboard-card border-accent-teal">
            <div class="card-header-custom">
                <i class="fas fa-address-card" style="color: var(--accent-teal);"></i>
                General Information
            </div>
            <div class="card-body-custom" style="text-align: left;">
                <form id="profileForm" method="POST" action="HRProfile.php">
                    <!-- Editable Fields Grid -->
                    <div class="section-title">
                        <i class="fas fa-pen-to-square"></i>
                        Editable Details
                    </div>
                    <div class="info-grid">
                        <!-- First Name -->
                        <div class="info-field editable">
                            <div class="field-label">
                                <i class="fas fa-user"></i> First Name
                            </div>
                            <div class="field-value">
                                <span id="fnameField"><?php echo $row['empFname']; ?></span>
                                <input type="text" id="fnameInput" name="newFname" value="<?php echo $row['empFname']; ?>" readonly style="display:none;">
                                <button type="button" class="edit-btn" onclick="enableEdit('fnameField', 'fnameInput')" title="Edit First Name">
                                    <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="info-field editable">
                            <div class="field-label">
                                <i class="fas fa-user"></i> Last Name
                            </div>
                            <div class="field-value">
                                <span id="lnameField"><?php echo $row['empLname']; ?></span>
                                <input type="text" id="lnameInput" name="newLname" value="<?php echo $row['empLname']; ?>" readonly style="display:none;">
                                <button type="button" class="edit-btn" onclick="enableEdit('lnameField', 'lnameInput')" title="Edit Last Name">
                                    <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="info-field editable">
                            <div class="field-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </div>
                            <div class="field-value">
                                <span id="emailField"><?php echo $row['empEmail']; ?></span>
                                <input type="email" id="emailInput" name="newEmail" value="<?php echo $row['empEmail']; ?>" readonly style="display:none;">
                                <button type="button" class="edit-btn" onclick="enableEdit('emailField', 'emailInput')" title="Edit Email">
                                    <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="info-field editable">
                            <div class="field-label">
                                <i class="fas fa-phone"></i> Phone Number
                            </div>
                            <div class="field-value">
                                <span id="phoneNumField"><?php echo $row['empPhoneNum']; ?></span>
                                <input type="text" id="phoneNumInput" name="newPhoneNum" value="<?php echo $row['empPhoneNum']; ?>" readonly style="display:none;">
                                <button type="button" class="edit-btn" onclick="enableEdit('phoneNumField', 'phoneNumInput')" title="Edit Phone">
                                    <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Marital Status -->
                        <div class="info-field editable">
                            <div class="field-label">
                                <i class="fas fa-heart"></i> Marital Status
                            </div>
                            <div class="field-value">
                                <span id="maritalStatusField"><?php echo $row['empMaritalStatus']; ?></span>
                                <input type="text" id="maritalStatusInput" name="newMaritalStatus" value="<?php echo $row['empMaritalStatus']; ?>" readonly style="display:none;">
                                <button type="button" class="edit-btn" onclick="enableEdit('maritalStatusField', 'maritalStatusInput')" title="Edit Marital Status">
                                    <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Read-Only Fields -->
                    <div class="readonly-section">
                        <div class="section-title">
                            <i class="fas fa-lock"></i>
                            System Information (Read-Only)
                        </div>
                        <div class="info-grid">
                            <div class="info-field">
                                <div class="field-label">
                                    <i class="fas fa-id-badge"></i> Employee ID
                                </div>
                                <div class="field-value"><?php echo $row['empID']; ?></div>
                            </div>
                            <div class="info-field">
                                <div class="field-label">
                                    <i class="fas fa-id-card"></i> ID Number
                                </div>
                                <div class="field-value"><?php echo $row['empIdNumber']; ?></div>
                            </div>
                            <div class="info-field">
                                <div class="field-label">
                                    <i class="fas fa-briefcase"></i> Role
                                </div>
                                <div class="field-value"><?php echo $row['empRole']; ?></div>
                            </div>
                            <div class="info-field">
                                <div class="field-label">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </div>
                                <div class="field-value"><?php echo $row['empGender']; ?></div>
                            </div>
                            <div class="info-field">
                                <div class="field-label">
                                    <i class="fas fa-globe"></i> Race
                                </div>
                                <div class="field-value"><?php echo $row['empRace']; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-bar">
                        <button class="btn btn-gradient-teal" type="button" onclick="saveProfile()">
                            <i class="fas fa-save" style="margin-right: 8px;"></i>Save Changes
                        </button>
                        <button class="btn btn-gradient-slate" type="button" onclick="location.reload()">
                            <i class="fas fa-rotate-left" style="margin-right: 8px;"></i>Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
