<?php
include('NewDbConn.php');
session_start();

// Check if the user is logged in and is a manager
if (!isset($_SESSION['empID']) || $_SESSION['empPosition'] != 'Manager') {
    header("Location: login.php");
    exit();
}

// Fetch all ratings (Feedback from Staff)
$query1 = "SELECT i.in_progress_time, i.fixed_time, r.rating_id, r.issueID, e.empFname as sender, r.descriptive_rating, r.graphic_rating, r.issue_category, r.specific_issue, r.attended_by, r.staff_worked_with, r.rating_date 
           FROM issue_ratings r 
           JOIN issues i ON i.issueID = r.issueID 
           JOIN emplooyedetails e ON i.empID = e.empID 
           ORDER BY r.rating_date DESC";
$result1 = $conn->query($query1);

// Fetch all rated issues pending manager feedback (Send Feedback to Staff)
$query = "SELECT r.rating_id, r.issueID, r.issue_category, r.specific_issue, r.attended_by, r.staff_worked_with, 
                 r.descriptive_rating, r.graphic_rating, r.rating_date, 
                 i.in_progress_time, i.fixed_time
          FROM issue_ratings r 
          JOIN issues i ON i.issueID = r.issueID 
          WHERE r.feedback_sent = 0
          ORDER BY r.rating_date DESC";
$result = $conn->query($query);

// Get counts for overview
$pendingFeedbackCount = $result->num_rows;
$totalReceivedCount = $result1->num_rows;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating_id = $_POST['rating_id'];
    $manager_rating = $_POST['manager_rating'];
    $time_assessment = $_POST['time_assessment'];
    
    // Update the issue_ratings table with manager's feedback
    $update_query = "UPDATE issue_ratings 
                     SET manager_rating = ?, time_assessment = ?, feedback_sent = 1 
                     WHERE rating_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $manager_rating, $time_assessment, $rating_id);
    $stmt->execute();
    
    // Redirect to prevent form resubmission
    header("Location: send_feedback_Ma.php");
    exit();
}

function calculateTimeSpent($inProgressTime, $fixedTime) {
    if (!$inProgressTime || !$fixedTime) {
        return "N/A";
    }
    $start = new DateTime($inProgressTime);
    $end = new DateTime($fixedTime);
    $interval = $start->diff($end);
    $timeSpent = "";
    if ($interval->d > 0) {
        $timeSpent .= $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ";
    }
    if ($interval->h > 0) {
        $timeSpent .= $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ";
    }
    if ($interval->i > 0) {
        $timeSpent .= $interval->i . " minute" . ($interval->i > 1 ? "s" : "");
    }
    return trim($timeSpent) ?: "Less than a minute";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href=".css">
    <link rel="icon" type="image/x-icon" href="EPMS1.JPG">
    <style>
        :root {
            --primary-deep: #0c1e3d;
            --primary-mid: #1e3a5f;
            --accent-teal: #0d9488;
            --accent-gold: #f59e0b;
            --accent-rose: #e11d48;
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

        /* Page Title */
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

        /* Cards */
        .dashboard-card {
            background: var(--bg-card);
            border: none;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(12, 30, 61, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
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

        .border-accent-teal { border-left: 4px solid #0d9488; }
        .border-accent-gold { border-left: 4px solid #f59e0b; }
        .border-accent-indigo { border-left: 4px solid #4f46e5; }
        .border-accent-rose { border-left: 4px solid #e11d48; }
        .border-accent-slate { border-left: 4px solid #475569; }

        /* Stats Card */
        .stats-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 28px 32px;
            margin-bottom: 28px;
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

        .overview-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 14px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .overview-item {
            text-align: center;
            flex: 1;
            min-width: 120px;
        }

        .overview-divider {
            width: 1px;
            height: 50px;
            background: #e2e8f0;
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

        .stat-icon.rose {
            background: rgba(225, 29, 72, 0.1);
            color: #e11d48;
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
        }

        /* Issues Table */
        .issues-table {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(12, 30, 61, 0.06);
            margin-bottom: 0;
        }

        .issues-table thead th {
            background: linear-gradient(90deg, #1e3a5f 0%, #334155 100%);
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 18px;
            border: none;
            white-space: nowrap;
        }

        .issues-table tbody td {
            padding: 14px 18px;
            font-size: 0.9rem;
            color: var(--text-main);
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .issues-table tbody tr:hover {
            background: #f0f9ff;
        }

        .issues-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Issue Badge */
        .issue-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(90deg, #475569 0%, #334155 100%);
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Star Rating */
        .star-rating {
            color: #f59e0b;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }

        /* Form Selects in Table */
        .feedback-form .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            padding: 8px 12px;
            min-width: 160px;
        }

        .feedback-form .form-select:focus {
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        /* Buttons */
        .btn-gradient-teal {
            background: linear-gradient(90deg, #0d9488 0%, #0f766e 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-teal:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.35);
            color: white;
        }

        .btn-gradient-rose {
            background: linear-gradient(90deg, #e11d48 0%, #be123c 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-rose:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(225, 29, 72, 0.35);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
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
            .overview-divider {
                display: none;
            }
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
        <a href="manager.php" class="nav-link-item">
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
        <a href="Track_issues.php" class="nav-link-item">
            <i class="fas fa-clipboard-check"></i>
            <span>Task tracking</span>
        </a>
        <a href="send_feedback_Ma.php" class="nav-link-item active">
            <i class="fas fa-paper-plane"></i>
            <span>Feedback</span>
        </a>
        <a href="Leaves.php" class="nav-link-item">
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
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-title">
            <i class="fas fa-paper-plane"></i>
            Feedback Management
        </div>
        <div class="page-subtitle">Review staff performance ratings and send your managerial feedback.</div>

        <!-- Feedback Overview Stats -->
        <div class="stats-card">
            <div class="stats-header">
                <i class="fas fa-chart-pie"></i>
                Feedback Overview
            </div>
            <div class="overview-box">
                <div class="overview-item">
                    <div class="stat-icon rose">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div class="stat-value"><?= $pendingFeedbackCount ?></div>
                    <div class="stat-label">Pending Feedback</div>
                </div>
                <div class="overview-divider"></div>
                <div class="overview-item">
                    <div class="stat-icon teal">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?= $totalReceivedCount - $pendingFeedbackCount ?></div>
                    <div class="stat-label">Feedback Sent</div>
                </div>
                <div class="overview-divider"></div>
                <div class="overview-item">
                    <div class="stat-icon gold">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="stat-value"><?= $totalReceivedCount ?></div>
                    <div class="stat-label">Total Received</div>
                </div>
            </div>
        </div>

        <!-- Send Feedback to Staff Section -->
        <div class="dashboard-card border-accent-rose mb-4">
            <div class="card-header-custom">
                <i class="fas fa-paper-plane" style="color: #e11d48"></i>
                <span>Send Feedback to Staff</span>
            </div>
            <div class="card-body-custom" style="text-align: left; padding: 0;">
                <div class="table-responsive">
                    <table class="table issues-table mb-0">
                        <thead>
                            <tr>
                                <th>Issue ID</th>
                                <th>Category</th>
                                <th>Specific Issue</th>
                                <th>Attended By</th>
                                <th>Staff Rating</th>
                                <th>Time Spent</th>
                                <th>Manager Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $hasPending = false;
                            while ($row = $result->fetch_assoc()): 
                                $hasPending = true;
                            ?>
                                <tr>
                                    <td><span class="issue-badge">#<?= htmlspecialchars($row['issueID']) ?></span></td>
                                    <td><?= htmlspecialchars($row['issue_category']) ?></td>
                                    <td><?= htmlspecialchars($row['specific_issue']) ?></td>
                                    <td><?= htmlspecialchars($row['attended_by']) ?></td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;"><?= ucfirst(htmlspecialchars($row['descriptive_rating'])) ?></div>
                                        <div class="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= $row['graphic_rating'] ? '★' : '☆' ?>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td><?= calculateTimeSpent($row['in_progress_time'], $row['fixed_time']) ?></td>
                                    <td>
                                        <form action="" method="post" class="feedback-form">
                                            <input type="hidden" name="rating_id" value="<?= $row['rating_id'] ?>">
                                            <select name="manager_rating" class="form-select mb-2" required>
                                                <option value="">Select feedback</option>
                                                <option value="good work">Good work</option>
                                                <option value="keep it up">Keep it up</option>
                                                <option value="improve next time">Improve next time</option>
                                            </select>
                                            <select name="time_assessment" class="form-select mb-2" required>
                                                <option value="">Assess time spent</option>
                                                <option value="early">Early</option>
                                                <option value="on time">On time</option>
                                                <option value="late">Late</option>
                                            </select>
                                            <button type="submit" class="btn btn-gradient-teal w-100">
                                                <i class="fas fa-paper-plane me-1"></i> Send
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (!$hasPending): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-check-circle" style="color: #0d9488;"></i>
                                            <h5>All Caught Up!</h5>
                                            <p style="margin-bottom: 0;">No pending feedback to send. Great job!</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Feedback from Staff Section -->
        <div class="dashboard-card border-accent-indigo">
            <div class="card-header-custom">
                <i class="fas fa-comments" style="color: #4f46e5"></i>
                <span>Feedback</span>
            </div>
            <div class="card-body-custom" style="text-align: left; padding: 0;">
                <div class="table-responsive">
                    <table class="table issues-table mb-0">
                        <thead>
                            <tr>
                                <th>Issue ID</th>
                                <th>Category</th>
                                <th>Specific Issue</th>
                                <th>Rated By</th>
                                <th>Attended By</th>
                                <th>Rating</th>
                                <th>Time Spent</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $hasReceived = false;
                            while ($row = $result1->fetch_assoc()): 
                                $hasReceived = true;
                            ?>
                                <tr>
                                    <td><span class="issue-badge">#<?= htmlspecialchars($row['issueID']) ?></span></td>
                                    <td><?= htmlspecialchars($row['issue_category']) ?></td>
                                    <td><?= htmlspecialchars($row['specific_issue']) ?></td>
                                    <td><?= htmlspecialchars($row['sender']) ?></td>
                                    <td><?= htmlspecialchars($row['attended_by']) ?></td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;"><?= ucfirst(htmlspecialchars($row['descriptive_rating'])) ?></div>
                                        <div class="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= $row['graphic_rating'] ? '★' : '☆' ?>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td><?= calculateTimeSpent($row['in_progress_time'], $row['fixed_time']) ?></td>
                                    <td><?= htmlspecialchars(date('M d, Y', strtotime($row['rating_date']))) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (!$hasReceived): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <h5>No Feedback Yet</h5>
                                            <p style="margin-bottom: 0;">Staff haven't submitted any ratings yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>

