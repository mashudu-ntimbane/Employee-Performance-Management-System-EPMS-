<?php

// Start the session to access session variables
session_start();
// Include the database connection file
include('NewDbConn.php');

// Check if the user is logged in by verifying if the session variable 'empID' is set
if (!isset($_SESSION['empID'])) {
    // If not logged in, redirect to the login page
    header("Location: logIn.php");
    exit();
}

// Get the employee ID from the session
$empID = $_SESSION['empID'];

// Fetch leave requests for the logged-in employee from the database
$requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status IN ('rejected', 'approved', 'pending') AND e.empID ='$empID' ");

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Leave</title>
    <!-- Include Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap JavaScript bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Font Awesome for icons -->
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <!-- Include custom CSS file -->
    <link rel="stylesheet" href=".css">
    <!-- Include favicon -->
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
            display: flex;
            flex-direction: column;
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
            flex: 1;
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

        /* Leave Page Specific Styles */
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

        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            flex: 1;
            min-height: 0;
        }

        @media (min-width: 992px) {
            .content-grid {
                grid-template-columns: 1.2fr 0.8fr;
                align-items: stretch;
            }
        }

        /* Table Styling */
        .leave-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .leave-table thead th {
            background: linear-gradient(90deg, #0d9488 0%, #0f766e 100%);
            color: #fff;
            padding: 14px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        .leave-table thead th:first-child {
            border-radius: 12px 0 0 0;
        }

        .leave-table thead th:last-child {
            border-radius: 0 12px 0 0;
        }

        .leave-table tbody td {
            padding: 14px 16px;
            font-size: 0.9rem;
            color: var(--text-main);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            background: #fff;
        }

        .leave-table tbody tr:hover td {
            background: #f8fafc;
        }

        .leave-table tbody tr:last-child td:first-child {
            border-radius: 0 0 0 12px;
        }

        .leave-table tbody tr:last-child td:last-child {
            border-radius: 0 0 12px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
        }

        .status-approved {
            background: rgba(13, 148, 136, 0.12);
            color: #0f766e;
        }

        .status-rejected {
            background: rgba(220, 38, 38, 0.12);
            color: #dc2626;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        /* Form Styling */
        .form-group-custom {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label-custom {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input-custom {
            width: 100%;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            color: var(--text-main);
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-input-custom:focus {
            outline: none;
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        }

        .form-input-custom.error {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        }

        textarea.form-input-custom {
            resize: vertical;
            min-height: 100px;
        }

        .form-hint {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .form-hint i {
            color: var(--accent-gold);
            margin-right: 4px;
        }

        .form-error {
            font-size: 0.85rem;
            color: #dc2626;
            margin-top: 6px;
            display: none;
            align-items: center;
            gap: 6px;
        }

        .form-error.visible {
            display: flex;
        }

        .form-error i {
            color: #dc2626;
            font-size: 0.9rem;
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
    </style>
</head>
<body class="bg-light">
 

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
        <a href="messagesOS.php" class="nav-link-item ">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <!--<a href="HRempl.php" class="nav-link-item">
            <i class="fas fa-users"></i>
            <span>Employees</span>
        </a>-->
        <a href="request_leaveOS.php" class="nav-link-item active">
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
        <!-- Page Header -->
        <div class="page-title">
            <i class="fas fa-calendar-check"></i>
            Leave Management
        </div>
        <div class="page-subtitle">View your leave history and submit new leave requests.</div>

        <div class="content-grid">
            <!-- Leave History Table -->
            <div class="dashboard-card border-accent-teal">
                <div class="card-header-custom">
                    <i class="fas fa-history" style="color: var(--accent-teal);"></i>
                    My Leave Requests
                </div>
                <div class="card-body-custom" style="text-align: left; padding: 0;">
                    <?php if ($requests->num_rows > 0): ?>
                    <table class="leave-table">
                        <thead>
                            <tr>
                                <th>Date Requested</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $requests->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['created_at'] ?></td>
                                <td><?= $row['start_date'] ?></td>
                                <td><?= $row['end_date'] ?></td>
                                <td><?= $row['reason'] ?></td>
                                <td>
                                    <span class="status-badge status-<?= $row['status'] ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <div>No leave requests found.</div>
                        <div style="font-size: 0.85rem; margin-top: 4px;">Submit your first request using the form.</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Request Leave Form -->
            <div class="dashboard-card border-accent-gold">
                <div class="card-header-custom">
                    <i class="fas fa-paper-plane" style="color: var(--accent-gold);"></i>
                    Request Leave
                </div>
                <div class="card-body-custom" style="text-align: left;">
                    <form id="leaveForm" action="submit_leave_requestMa.php" method="post" onsubmit="return validateForm()">
                        <div class="form-group-custom">
                            <label class="form-label-custom" for="start_date">
                                <i class="fas fa-calendar-day" style="color: var(--accent-teal); margin-right: 6px;"></i>Start Date
                            </label>
                            <input type="date" class="form-input-custom" id="start_date" name="start_date">
                            <div class="form-error" id="startDateError">
                                <i class="fas fa-circle-exclamation"></i> Please select a start date.
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-lightbulb"></i> Choose the first day of your leave.
                            </div>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom" for="end_date">
                                <i class="fas fa-calendar-day" style="color: var(--accent-teal); margin-right: 6px;"></i>End Date
                            </label>
                            <input type="date" class="form-input-custom" id="end_date" name="end_date">
                            <div class="form-error" id="endDateError">
                                <i class="fas fa-circle-exclamation"></i> Please select an end date.
                            </div>
                            <div class="form-error" id="dateRangeError">
                                <i class="fas fa-circle-exclamation"></i> End date cannot be before start date.
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-lightbulb"></i> Choose the last day of your leave.
                            </div>
                        </div>
                        <div class="form-group-custom">
                            <label class="form-label-custom" for="reason">
                                <i class="fas fa-comment-alt" style="color: var(--accent-teal); margin-right: 6px;"></i>Reason
                            </label>
                            <textarea class="form-input-custom" id="reason" name="reason" rows="3" placeholder="Briefly describe why you need leave..."></textarea>
                            <div class="form-error" id="reasonError">
                                <i class="fas fa-circle-exclamation"></i> Please provide a reason for your leave.
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-lightbulb"></i> A short reason helps with faster approval.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient-teal" style="width: 100%;">
                            <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>Submit Request
                        </button>
                    </form>
                </div>
        </div>
        </div>
    </main>
</div>

<script>
    function validateForm() {
        let isValid = true;
        
        // Get form elements
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const reason = document.getElementById('reason');
        
        // Get error elements
        const startDateError = document.getElementById('startDateError');
        const endDateError = document.getElementById('endDateError');
        const dateRangeError = document.getElementById('dateRangeError');
        const reasonError = document.getElementById('reasonError');
        
        // Reset errors
        startDate.classList.remove('error');
        endDate.classList.remove('error');
        reason.classList.remove('error');
        startDateError.classList.remove('visible');
        endDateError.classList.remove('visible');
        dateRangeError.classList.remove('visible');
        reasonError.classList.remove('visible');
        
        // Validate start date
        if (!startDate.value.trim()) {
            startDate.classList.add('error');
            startDateError.classList.add('visible');
            isValid = false;
        }
        
        // Validate end date
        if (!endDate.value.trim()) {
            endDate.classList.add('error');
            endDateError.classList.add('visible');
            isValid = false;
        }
        
        // Validate date range (start date must not be after end date)
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            
            if (start > end) {
                startDate.classList.add('error');
                endDate.classList.add('error');
                dateRangeError.classList.add('visible');
                isValid = false;
            }
        }
        
        // Validate reason
        if (!reason.value.trim()) {
            reason.classList.add('error');
            reasonError.classList.add('visible');
            isValid = false;
        }
        
        return isValid;
    }
    
    // Clear error styling on input
    document.getElementById('start_date').addEventListener('input', function() {
        this.classList.remove('error');
        document.getElementById('startDateError').classList.remove('visible');
        document.getElementById('dateRangeError').classList.remove('visible');
    });
    
    document.getElementById('end_date').addEventListener('input', function() {
        this.classList.remove('error');
        document.getElementById('endDateError').classList.remove('visible');
        document.getElementById('dateRangeError').classList.remove('visible');
    });
    
    document.getElementById('reason').addEventListener('input', function() {
        this.classList.remove('error');
        document.getElementById('reasonError').classList.remove('visible');
    });
</script>

</body>
</html>
