<?php
session_start();

// Include the database connection file
include('NewDbConn.php');

// Fetch all pending leave requests from the database, joining with employee details to get employee names
$pending_requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status = 'pending'");
// Fetch all approved leave requests
$approved_requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status = 'approved'");
// Fetch all rejected leave requests
$rejected_requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status = 'rejected'");
// Fetch all leave requests ordered by the most recently updated
$recent_requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID ORDER BY lr.updated_at DESC");

// Count statistics
$pending_count = $pending_requests->num_rows;
$approved_count = $approved_requests->num_rows;
$rejected_count = $rejected_requests->num_rows;

// Calculate currently on leave (approved and today is between start_date and end_date)
$today = date("Y-m-d");
$on_leave_result = $conn->query("SELECT COUNT(*) as count FROM leave_requests WHERE status = 'approved' AND start_date <= '$today' AND end_date >= '$today'");
$on_leave_count = $on_leave_result->fetch_assoc()['count'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap JavaScript bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Font Awesome for icons -->
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <!-- Include favicon -->
    <link rel="icon" type="image/x-icon" href="EPMS1.JPG">
    <title>Leave</title>
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

        /* Employee Page Specific Styles */
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            color: var(--accent-teal);
            font-size: 1.3rem;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 28px;
            padding-left: 36px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 992px) {
            .action-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .action-grid {
                grid-template-columns: 1fr;
            }
        }

        .action-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 32px 28px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }

        .action-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(12, 30, 61, 0.12);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 18px 18px 0 0;
        }

        .action-card.accent-indigo::before { background: linear-gradient(90deg, #4f46e5, #4338ca); }
        .action-card.accent-gold::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .action-card.accent-teal::before { background: linear-gradient(90deg, #0d9488, #0f766e); }

        .action-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
        }

        .action-card.accent-indigo .action-icon {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }

        .action-card.accent-gold .action-icon {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .action-card.accent-teal .action-icon {
            background: rgba(13, 148, 136, 0.1);
            color: #0d9488;
        }

        .action-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .action-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        .action-card.accent-indigo .action-btn {
            background: linear-gradient(90deg, #4f46e5 0%, #4338ca 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .action-card.accent-indigo .action-btn:hover {
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
        }

        .action-card.accent-gold .action-btn {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        }

        .action-card.accent-gold .action-btn:hover {
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
        }

        .action-card.accent-teal .action-btn {
            background: linear-gradient(90deg, #0d9488 0%, #0f766e 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
        }

        .action-card.accent-teal .action-btn:hover {
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.35);
        }

        /* Statistics Card Styles */
        .stats-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 28px 32px;
            margin-top: 28px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 2px 12px rgba(12, 30, 61, 0.06);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(12, 30, 61, 0.12);
        }

        .stats-card::before {
            content: '';
            display: block;
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, #4f46e5, #0d9488, #f59e0b, #475569);
            border-radius: 18px 18px 0 0;
            margin: -28px -32px 24px -32px;
            width: calc(100% + 64px);
        }

        .stats-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .stats-header i {
            color: var(--accent-teal);
            font-size: 1.3rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 14px;
            padding: 22px 18px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(12, 30, 61, 0.08);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 1.3rem;
        }

        .stat-icon.teal {
            background: rgba(13, 148, 136, 0.1);
            color: #0d9488;
        }

        .stat-icon.gold {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .stat-icon.indigo {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
        }

        .stat-icon.slate {
            background: rgba(71, 85, 105, 0.1);
            color: #475569;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-breakdown {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .stat-breakdown span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stat-breakdown i {
            font-size: 0.75rem;
        }

        /* Table Styles */
        .leave-table thead th {
            background: linear-gradient(90deg, #1e3a5f 0%, #0d9488 100%);
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 14px 16px;
            border: none;
        }

        .leave-table tbody td {
            padding: 14px 16px;
            font-size: 0.9rem;
            color: var(--text-main);
            vertical-align: middle;
        }

        .leave-table tbody tr {
            transition: background 0.2s ease;
        }

        .leave-table tbody tr:hover {
            background: rgba(13, 148, 136, 0.04);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .status-badge i {
            font-size: 0.7rem;
        }

        .status-upcoming {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
        }

        .status-ongoing {
            background: rgba(13, 148, 136, 0.12);
            color: #0f766e;
        }

        .status-finished {
            background: rgba(71, 85, 105, 0.12);
            color: #475569;
        }

        .mb-4 { margin-bottom: 1.5rem !important; }
        .mb-5 { margin-bottom: 3rem !important; }

        /* Reject button style */
        .btn-reject {
            background: linear-gradient(90deg, #e11d48 0%, #be123c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 5px 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
            color: white;
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
        <a href="manager.php" class="nav-link-item ">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="managerProfile.php" class="nav-link-item">
            <i class="fas fa-user-circle"></i>
            <span>User Profile</span>
        </a>
        <a href="messagesMa.php" class="nav-link-item">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <a href="employees.php" class="nav-link-item">
            <i class="fas fa-users"></i>
            <span>Employees</span>
        </a>
        <a href="Track_issues.php" class="nav-link-item ">
            <i class="fas fa-clipboard-check"></i>
            <span>Task tracking</span>
        </a>
        <a href="send_feedback_Ma.php" class="nav-link-item">
            <i class="fas fa-paper-plane"></i>
            <span>Feedback</span>
        </a>
        <a href="Leaves.php" class="nav-link-item active">
            <i class="fas fa-calendar-check"></i>
            <span>Leave</span>
        </a>
        <a href="issueMa.php" class="nav-link-item">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Issues</span>
        </a>
        <a href="page1.html" class="nav-link-item">
            <i class="fas fa-building"></i>
            <span>Company Page</span>
        </a>
        <a href="logOut.php" class="nav-link-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Log Out</span>
        </a>
    </aside>

 <!-- Main Content -->
    <div class="main-content">
        <!-- Page Title -->
        <div class="page-title">
            <i class="fas fa-calendar-check"></i>
            Leave Management
        </div>
        <div class="page-subtitle">Overview of all employee leave requests and their current status.</div>

        <!-- Statistics Cards -->
        <div class="stats-card mb-5">
            <div class="stats-header">
                <i class="fas fa-chart-pie"></i>
                Leave Overview
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon teal"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?= $pending_count ?></div>
                    <div class="stat-label">Pending Requests</div></div>
                <div class="stat-item">
                    <div class="stat-icon indigo"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?= $approved_count ?></div>
                    <div class="stat-label">Approved Leaves</div></div>
                <div class="stat-item">
                    <div class="stat-icon slate"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-value"><?= $rejected_count ?></div>
                    <div class="stat-label">Rejected Leaves</div></div>
                <div class="stat-item">
                    <div class="stat-icon gold"><i class="fas fa-plane-departure"></i></div>
                    <div class="stat-value"><?= $on_leave_count ?></div>
                    <div class="stat-label">Currently On Leave</div>
            </div>
        </div><br>          

        <!-- Pending Leave Requests -->
        <div class="dashboard-card border-accent-teal mb-5">
            <div class="card-header-custom">
                <i class="fas fa-clock" style="color: #0d9488;"></i>
                Pending Leave Requests
            </div>
            <div class="p-4">
                <table class="table table-hover leave-table mb-0">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pending_requests->num_rows > 0): ?>
                            <?php while ($row = $pending_requests->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['empID']; ?></td>
                                <td><?= $row['empFname'] ?></td>
                                <td><?= $row['start_date'] ?></td>
                                <td><?= $row['end_date'] ?></td>
                                <td><?= $row['reason'] ?></td>
                                <td class="text-center">
                                    <a href="approve_leave.php?id=<?= $row['id'] ?>" class="btn btn-gradient-teal btn-sm me-2">Approve</a>
                                    <a href="reject_leave.php?id=<?= $row['id'] ?>" class="btn-reject btn-sm">Reject</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No pending leave requests.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approved Leave Requests -->
        <div class="dashboard-card border-accent-indigo mb-5">
            <div class="card-header-custom">
                <i class="fas fa-user-check" style="color: #4f46e5;"></i>
                Approved Leave Requests
            </div>
            <div class="p-4">
                <table class="table table-hover leave-table mb-0">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($approved_requests->num_rows > 0): ?>
                            <?php while ($row = $approved_requests->fetch_assoc()): ?>
                                <?php
                                    $start = strtotime($row['start_date']);
                                    $end = strtotime($row['end_date']);
                                    $now = strtotime($today);

                                    if ($now < $start) {
                                        $leave_status = 'Upcoming';
                                        $status_class = 'status-upcoming';
                                        $status_icon = 'fa-hourglass-start';
                                    } elseif ($now >= $start && $now <= $end) {
                                        $leave_status = 'On Leave';
                                        $status_class = 'status-ongoing';
                                        $status_icon = 'fa-plane';
                                    } else {
                                        $leave_status = 'Finished';
                                        $status_class = 'status-finished';
                                        $status_icon = 'fa-check-double';
                                    }
                                ?>
                                <tr>
                                    <td><?= $row['empID'] ?></td>
                                    <td><?= $row['empFname'] ?></td>
                                    <td><?= $row['start_date'] ?></td>
                                    <td><?= $row['end_date'] ?></td>
                                    <td><?= $row['reason'] ?></td>
                                    <td class="text-center">
                                        <span class="status-badge <?= $status_class ?>">
                                            <i class="fas <?= $status_icon ?>"></i> <?= $leave_status ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No approved leave requests.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Rejected Leave Requests -->
        <div class="dashboard-card border-accent-rose mb-5">
            <div class="card-header-custom">
                <i class="fas fa-user-alt-slash" style="color: #e11d48;"></i>
                Rejected Leave Requests
            </div>
            <div class="p-4">
                <table class="table table-hover leave-table mb-0">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rejected_requests->num_rows > 0): ?>
                            <?php while ($row = $rejected_requests->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['empID'] ?></td>
                                <td><?= $row['empFname'] ?></td>
                                <td><?= $row['start_date'] ?></td>
                                <td><?= $row['end_date'] ?></td>
                                <td><?= $row['reason'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No rejected leave requests.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
    </div>
        </div>
        </div>
</body>
</html>
