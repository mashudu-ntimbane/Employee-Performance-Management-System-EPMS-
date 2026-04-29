<?php
    
    session_start();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="icon" type="image/x-icon" href="EPMS1.JPG">
  
</head>
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

            overflow-x: hidden;
            margin-top: 80px;
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

        /* Messages Page Specific Styles */
        .message-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message-item {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 18px 22px;
            box-shadow: 0 2px 8px rgba(12, 30, 61, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border-left: 4px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .message-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(12, 30, 61, 0.1);
        }

        .message-item.unread {
            border-left-color: var(--accent-teal);
            background: linear-gradient(90deg, rgba(13, 148, 136, 0.04) 0%, #ffffff 100%);
        }

        .message-item.unread .message-sender-name,
        .message-item.unread .message-heading-text {
            font-weight: 700;
        }

        .message-content {
            flex: 1;
            min-width: 0;
        }

        .message-sender-name {
            font-size: 1rem;
            color: var(--primary-mid);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-heading-text {
            font-size: 0.95rem;
            color: var(--text-main);
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
        }

        .message-timestamp {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .message-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .badge-replied {
            background: rgba(13, 148, 136, 0.1);
            color: var(--accent-teal);
        }

        .badge-unread {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .message-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .message-actions a {
            color: var(--text-muted);
            transition: all 0.2s ease;
            padding: 6px;
            border-radius: 8px;
        }

        .message-actions a:hover {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.08);
            transform: scale(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            color: var(--text-main);
            font-weight: 600;
            margin-bottom: 8px;
        }

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
        /* Modal Styles */
        .message-modal .modal-content {
            border: none;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(12, 30, 61, 0.25);
            overflow: hidden;
        }

        .message-modal .modal-header {
            background: linear-gradient(90deg, #0c1e3d 0%, #1e3a5f 40%, #0d9488 100%);
            color: #fff;
            border-bottom: none;
            padding: 18px 24px;
        }

        .message-modal .modal-title {
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message-modal .btn-close {
            filter: invert(1);
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .message-modal .btn-close:hover {
            opacity: 1;
        }

        .message-modal .modal-body {
            padding: 28px;
            background: #f1f5f9;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-sender-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(12, 30, 61, 0.04);
        }

        .modal-sender-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .modal-sender-info {
            flex: 1;
        }

        .modal-sender-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-deep);
            margin-bottom: 4px;
        }

        .modal-message-date {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-message-heading {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-mid);
            margin-bottom: 16px;
            padding: 0 4px;
        }

        .modal-message-body {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(12, 30, 61, 0.04);
            font-size: 1rem;
            line-height: 1.7;
            color: var(--text-main);
            white-space: pre-wrap;
        }

        .modal-file-attachment {
            margin-bottom: 20px;
        }

        .modal-file-attachment a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 20px;
            color: var(--accent-teal);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .modal-file-attachment a:hover {
            background: rgba(13, 148, 136, 0.05);
            border-color: var(--accent-teal);
            transform: translateY(-1px);
        }

        .modal-replies-section {
            margin-bottom: 20px;
        }

        .modal-replies-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary-deep);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-reply-item {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(12, 30, 61, 0.04);
            border-left: 3px solid var(--accent-teal);
        }

        .modal-reply-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .modal-reply-author {
            font-weight: 700;
            color: var(--primary-mid);
            font-size: 0.95rem;
        }

        .modal-reply-time {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .modal-reply-text {
            color: var(--text-main);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .modal-reply-form {
            background: #fff;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(12, 30, 61, 0.04);
        }

        .modal-reply-form label {
            font-weight: 700;
            color: var(--primary-deep);
            margin-bottom: 10px;
            display: block;
        }

        .modal-reply-form textarea {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            width: 100%;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .modal-reply-form textarea:focus {
            outline: none;
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        .modal-reply-form button {
            margin-top: 14px;
        }

        .modal-empty-replies {
            text-align: center;
            padding: 24px;
            color: var(--text-muted);
            font-style: italic;
        }

        .modal-dialog {
            max-width: 800px;
        }

        @media (max-width: 768px) {
            .message-modal .modal-dialog {
                max-width: 95%;
                margin: 10px auto;
            }
            .message-modal .modal-body {
                padding: 18px;
                max-height: 85vh;
            }
            .modal-sender-card {
                padding: 14px 16px;
            }
            .modal-message-body {
                padding: 16px;
            }
        }
    </style>

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
        <a href="HRProfile.php" class="nav-link-item">
            <i class="fas fa-user-circle"></i>
            <span>User Profile</span>
        </a>
        <a href="messagesHR.php" class="nav-link-item active">
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
        <div class="page-header">
            <div class="page-title">
                <i class="fas fa-envelope"></i>
                Messages
            </div>
            <button class="btn btn-gradient-teal" onclick="document.location='send_messageHR.php'">
                <i class="fas fa-plus" style="margin-right: 6px;"></i>New Message
            </button>
        </div>

        <?php
        // Include the database connection file
        include('NewDbConn.php');

        // Check if the user is logged in by verifying if the session variable 'empID' is set
        if (!isset($_SESSION['empID'])) {
            // Redirect to the login page if the user is not logged in
            header("Location: logIn.php");
            exit();
        }

        // Retrieve the employee ID from the session
        $empID = $_SESSION['empID'];

        // Fetch messages for the logged-in user
        $stmt = $conn->prepare("SELECT m.id, m.message, m.timestamp, m.is_read, m.heading, m.replied, u.empLname, u.empFname AS sender, f.file_path 
        FROM messages m 
        LEFT JOIN emplooyedetails u ON m.sender_id = u.empID  
        LEFT JOIN files f ON m.id = f.message_id 
        WHERE m.receiver_id = ? 
        ORDER BY m.timestamp DESC");

        // Bind the employee ID parameter to the SQL statement
        $stmt->bind_param("i", $empID);

        // Execute the SQL statement
        $stmt->execute();

        // Get the result of the executed query
        $result = $stmt->get_result();

        // Check if there are any messages for the user
        if($result->num_rows > 0) {
            $unreadCount = 0;
            $totalCount = $result->num_rows;
            
            // Count unread messages
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                if (!$row['is_read']) {
                    $unreadCount++;
                }
            }
            
            echo "<div class='dashboard-card'>";
            echo "<div class='card-header-custom'>";
            echo "<i class='fas fa-inbox' style='color: #0d9488;'></i>";
            echo "<span>Messages Received</span>";
            if ($unreadCount > 0) {
                echo "<span class='message-badge badge-unread' style='margin-left: auto;'><i class='fas fa-circle' style='font-size: 6px;'></i> $unreadCount New</span>";
            }
            echo "</div>";
            echo "<div class='card-body-custom' style='padding: 22px; text-align: left;'>";
            echo "<div class='message-list'>";
            
            // Reset result pointer and fetch messages again for display
            $result->data_seek(0);
            
            // Fetch and display each message and its associated details
            while ($row = $result->fetch_assoc()) {
                $messageClass = $row['is_read'] ? '' : 'unread';
                $messageTime = strtotime($row['timestamp']);
                $currentTime = time();
                $timeDifference = $currentTime - $messageTime;

                // Calculate and display the formatted date
                $formattedDate = "";
                if ($timeDifference < 86400) { // Less than 24 hours
                    $formattedDate = "Today, " . date('g:i A', $messageTime);
                } elseif ($timeDifference < 172800) { // Less than 48 hours
                    $formattedDate = "Yesterday, " . date('g:i A', $messageTime);
                } else {
                    $formattedDate = date('M j, Y', $messageTime);
                }

                echo "<div class='message-item $messageClass' data-message-id='" . $row['id'] . "' onclick='openMessageModal(" . $row['id'] . ")'>";
                
                echo "<div class='message-content'>";
                echo "<div class='message-sender-name'>";
                echo "<i class='fas fa-user-circle' style='color: #0d9488; font-size: 1.1rem;'></i>";
                echo htmlspecialchars($row['sender'] . ' ' . $row['empLname']);
                if (!$row['is_read']) {
                    echo "<span class='message-badge badge-unread'><i class='fas fa-circle' style='font-size: 6px;'></i> New</span>";
                }
                echo "</div>";
                echo "<div class='message-heading-text'>" . htmlspecialchars($row['heading']) . "</div>";
                echo "</div>";
                
                echo "<div class='message-meta'>";
                echo "<div class='message-timestamp'><i class='far fa-clock' style='margin-right: 4px; color: #94a3b8;'></i>$formattedDate</div>";
                echo "<div class='message-actions'>";
                if ($row['replied']) {
                    echo "<span class='message-badge badge-replied'><i class='fas fa-reply'></i> Replied</span>";
                }
                echo "<a href='delete_message.php?id=" . $row['id'] . "' onclick='event.stopPropagation();' title='Delete message'><i class='fas fa-trash'></i></a>";
                echo "</div>";
                echo "</div>";
                
                echo "</div>";
            }
            
            echo "</div>"; // Close message-list
            echo "</div>"; // Close card-body-custom
            echo "</div>"; // Close dashboard-card
        } else {
            // Display a message if there are no messages for the user
            echo "<div class='dashboard-card'>";
            echo "<div class='empty-state'>";
            echo "<i class='fas fa-envelope-open'></i>";
            echo "<h5>No Messages Yet</h5>";
            echo "<p>Your inbox is empty. Start a conversation by sending a new message.</p>";
            echo "</div>";
            echo "</div>";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
        ?>

        <!-- Message Read Modal -->
        <div class="modal fade message-modal" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Message Details</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="messageModalBody">
                        <!-- Content loaded dynamically -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-teal" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const modalBody = document.getElementById('messageModalBody');

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function openMessageModal(messageId) {
        // Show loading state
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border" style="color: var(--accent-teal);" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading message...</p>
            </div>
        `;
        messageModal.show();

        // Fetch message data
        fetch('get_message.php?id=' + encodeURIComponent(messageId))
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    modalBody.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                    return;
                }

                const msg = data.message;
                const replies = data.replies;

                let html = '';

                // Sender card
                html += `
                    <div class="modal-sender-card">
                        <div class="modal-sender-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="modal-sender-info">
                            <div class="modal-sender-name">${escapeHtml(msg.sender)} ${escapeHtml(msg.empLname)}</div>
                            <div class="modal-message-date">
                                <i class="far fa-clock"></i> ${formatDate(msg.timestamp)}
                            </div>
                        </div>
                    </div>
                `;

                // Heading
                html += `<div class="modal-message-heading">${escapeHtml(msg.heading)}</div>`;

                // Message body
                html += `<div class="modal-message-body">${escapeHtml(msg.message)}</div>`;

                // File attachment
                if (msg.file_path) {
                    html += `
                        <div class="modal-file-attachment">
                            <a href="${escapeHtml(msg.file_path)}" target="_blank">
                                <i class="fas fa-file-alt"></i> Open Attachment
                            </a>
                        </div>
                    `;
                }

                // Replies section
                html += `
                    <div class="modal-replies-section">
                        <div class="modal-replies-title">
                            <i class="fas fa-reply"></i> Conversation
                        </div>
                `;

                if (replies.length > 0) {
                    replies.forEach(reply => {
                        html += `
                            <div class="modal-reply-item">
                                <div class="modal-reply-header">
                                    <span class="modal-reply-author">${escapeHtml(reply.empFname)} ${escapeHtml(reply.empLname)}</span>
                                    <span class="modal-reply-time">${formatDate(reply.timestamp)}</span>
                                </div>
                                <div class="modal-reply-text">${escapeHtml(reply.reply_message)}</div>
                            </div>
                        `;
                    });
                } else {
                    html += `<div class="modal-empty-replies">No replies yet. Be the first to respond!</div>`;
                }

                html += `</div>`; // end replies section

                // Reply form
                html += `
                    <form class="modal-reply-form" action="replyHR.php" method="POST">
                        <label for="replyMessage"><i class="fas fa-comment-dots" style="color: var(--accent-teal);"></i> Your Reply</label>
                        <textarea id="replyMessage" name="replyMessage" placeholder="Type your response here..." required></textarea>
                        <input type="hidden" name="messageId" value="${messageId}">
                        <button type="submit" class="btn btn-gradient-teal">
                            <i class="fas fa-paper-plane" style="margin-right: 6px;"></i>Send Reply
                        </button>
                    </form>
                `;

                modalBody.innerHTML = html;

                // Scroll to top of modal
                modalBody.scrollTop = 0;
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = `<div class="alert alert-danger">Failed to load message. Please try again.</div>`;
            });

        // Visually mark the message as read in the list
        const messageEl = document.querySelector('.message-item[data-message-id="' + messageId + '"]');
        if (messageEl) {
            messageEl.classList.remove('unread');
            const newBadge = messageEl.querySelector('.badge-unread');
            if (newBadge) newBadge.remove();
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Auto-open modal if URL has ?id= parameter (e.g. after reply submission)
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const messageId = urlParams.get('id');
        if (messageId) {
            openMessageModal(messageId);
            // Clean up URL without reloading
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>

</body>
</html>

