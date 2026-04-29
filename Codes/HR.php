<?php
session_start();
// Include the database connection file
include('NewDbConn.php');

// Check if the user is logged in by verifying the 'empID' session variable
if (!isset($_SESSION['empID'])) {
  // If not logged in, redirect to the login page
  header('Location:logIn.php');
  exit();
}

// Get the logged-in user's empID from the session
$empID = $_SESSION['empID'];

// Fetch all pending leave requests from the database, joining with employee details to get employee names
$pending_requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status = 'pending'");
// Fetch all users that that are not approved
$pending_users = $conn->query("SELECT count(*),empFname FROM emplooyedetails WHERE approved = 0 Group by empFname ");
// Fetch the count of new and pending leave requests for alerts
$new_leave_count = $pending_requests->num_rows;
// Fetch the count of new users for alerts
$new_user_count = $pending_users->num_rows;
// Fetch leave requests for the logged-in employee from the database
$requests = $conn->query("SELECT lr.*, e.empFname FROM leave_requests lr JOIN emplooyedetails e ON lr.empID = e.empID WHERE lr.status IN ('rejected', 'approved', 'pending') AND e.empID ='$empID' ORDER BY lr.created_at DESC");

// Check the most recent leave request status if there are any requests
$recentStatus = null;
if ($requests->num_rows > 0) {
    $recentRequest = $requests->fetch_assoc();
    $recentStatus = $recentRequest['status'];
}
// Prepare an SQL statement to select messages for the logged-in user
$stmt = $conn->prepare("SELECT m.id, m.message, m.timestamp, m.is_read,m.heading, u.empLname, u.empFname AS sender, f.file_path 
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
// Check if there are any unread messages
$unreadCount = 0;
while ($row = $result->fetch_assoc()) {
if (!$row['is_read']) {
$unreadCount++;
}
}

$query = "SELECT empRole FROM emplooyedetails WHERE empID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $empID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userRole = $row['empRole'];
} else {
    // If the user is not found in the database, handle the error
    echo "Error: User not found.";
    exit();
}

// Fetch the most recent issue for the current user
$sql = "SELECT i.issueID, i.issue_category, i.specific_issue, i.status, i.requested_date 
        FROM issues i 
        WHERE i.empID = ? 
        ORDER BY i.requested_date DESC 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empID);
$stmt->execute();
$recentIssueResult = $stmt->get_result();
$recentIssue = null;
if ($recentIssueResult->num_rows > 0) {
    $recentIssue = $recentIssueResult->fetch_assoc();
}

// Count pending issues from the database that correspond to the current user's role
$sql = "SELECT COUNT(*) AS pending_count FROM issues i 
        JOIN emplooyedetails e ON i.empID = e.empID 
        WHERE role = ? AND status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userRole);
$stmt->execute();
$result = $stmt->get_result();
$pendingCount = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pendingCount = $row['pending_count'];
}

// Fetch pending issue details for the modal
$sql = "SELECT i.issueID, e.empFname, i.issue_category, i.specific_issue, i.requested_date, r.r_name, b.b_name 
        FROM issues i 
        JOIN emplooyedetails e ON i.empID = e.empID 
        JOIN rooms r ON r.empID = e.empID 
        JOIN buildings b ON b.id = r.building_id 
        WHERE i.role = ? AND i.status = 'Pending' 
        ORDER BY i.requested_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userRole);
$stmt->execute();
$pendingIssuesResult = $stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
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

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(12, 30, 61, 0.3);
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(90deg, #0c1e3d 0%, #1e3a5f 40%, #0d9488 100%);
            color: #fff;
            border-bottom: none;
            padding: 20px 28px;
        }

        .modal-header .modal-title {
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 28px;
            background: #f8fafc;
        }

        .modal-footer {
            border-top: 1px solid rgba(0,0,0,0.06);
            padding: 18px 28px;
            background: #fff;
        }

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
        <a href="HR.php" class="nav-link-item active">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="HRProfile.php" class="nav-link-item">
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
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            
            <!-- Leave Requests Card 
            <div class="col">
                <div class="dashboard-card h-100 <?= $new_leave_count > 0 ? 'border-accent-gold' : 'border-accent-teal' ?>">
                    <div class="card-header-custom">
                        <i class="fas fa-calendar-alt" style="color: <?= $new_leave_count > 0 ? '#f59e0b' : '#0d9488' ?>"></i>
                        <span>Leave Requests</span>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($new_leave_count > 0): ?>
                            <h5 class="card-title" style="color: #d97706; font-weight: 700;"><?= $new_leave_count; ?> Pending</h5>
                            <p class="card-text text-muted mb-3">Pending leave requests awaiting your action.</p>
                            <a href="HRLeaves.php" class="btn btn-gradient-gold">View Details</a>
                        <?php else: ?>
                            <h5 class="card-title" style="color: #0d9488; font-weight: 700;"><i class="fas fa-check-circle"></i> All Clear</h5>
                            <p class="card-text text-muted mb-3">No pending leave requests.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div> -->

            <!-- New Users Card -->
            <div class="col">
                <div class="dashboard-card h-100 <?= $new_user_count > 0 ? 'border-accent-indigo' : 'border-accent-teal' ?>">
                    <div class="card-header-custom">
                        <i class="fas fa-user-plus" style="color: <?= $new_user_count > 0 ? '#4f46e5' : '#0d9488' ?>"></i>
                        <span>New Users</span>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($new_user_count > 0): ?>
                            <h5 class="card-title" style="color: #4f46e5; font-weight: 700;"><?= $new_user_count; ?> New</h5>
                            <p class="card-text text-muted mb-3">New users registered awaiting approval.</p>
                            <a href="HRapprove.php" class="btn btn-gradient-indigo">View Users</a>
                        <?php else: ?>
                            <h5 class="card-title" style="color: #0d9488; font-weight: 700;"><i class="fas fa-check-circle"></i> Up to Date</h5>
                            <p class="card-text text-muted mb-3">No new users to approve.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Leave Status Card -->
            <div class="col">
                <div class="dashboard-card h-100 border-accent-indigo">
                    <div class="card-header-custom">
                        <i class="fas fa-history" style="color: #4f46e5"></i>
                        <span>My Leave Status</span>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($recentStatus): ?>
                            <h5 class="card-title" style="color: #4f46e5; font-weight: 700;"><?= ucfirst($recentStatus) ?></h5>
                            <p class="card-text text-muted mb-3">Your most recent leave request status.</p>
                            <a href="request_leaveHR.php" class="btn btn-gradient-indigo">My Requests</a>
                        <?php else: ?>
                            <h5 class="card-title text-muted" style="font-weight: 700;">No Requests</h5>
                            <p class="card-text text-muted mb-3">You haven't submitted any leave requests yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Messages Card -->
            <div class="col">
                <div class="dashboard-card h-100 <?= $unreadCount > 0 ? 'border-accent-gold' : 'border-accent-teal' ?>">
                    <div class="card-header-custom">
                        <i class="fas fa-envelope" style="color: <?= $unreadCount > 0 ? '#f59e0b' : '#0d9488' ?>"></i>
                        <span>Messages</span>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($unreadCount > 0): ?>
                            <h5 class="card-title" style="color: #d97706; font-weight: 700;"><?= $unreadCount; ?> Unread</h5>
                            <p class="card-text text-muted mb-3">You have unread messages in your inbox.</p>
                            <a href="messagesHR.php" class="btn btn-gradient-gold">Read Messages</a>
                        <?php else: ?>
                            <h5 class="card-title" style="color: #0d9488; font-weight: 700;"><i class="fas fa-check-circle"></i> Inbox Clear</h5>
                            <p class="card-text text-muted mb-3">No unread messages.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Issues Card -->
            <div class="col">
                <div class="dashboard-card h-100 <?= $recentIssue ? 'border-accent-slate' : 'border-accent-teal' ?>">
                    <div class="card-header-custom">
                        <i class="fas fa-bell" style="color: <?= $recentIssue ? '#475569' : '#0d9488' ?>"></i>
                        <span>Issues</span>
                    </div>
                    <div class="card-body-custom">
                        <?php if ($recentIssue): ?>
                            <h5 class="card-title" style="color: #475569; font-weight: 700;"><?= htmlspecialchars($recentIssue['issue_category']) ?></h5>
                            <p class="card-text text-muted mb-3"><?= htmlspecialchars($recentIssue['specific_issue']) ?></p>
                            <span class="badge <?= $recentIssue['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-success' ?> mb-2"><?= $recentIssue['status'] ?></span><br>
                            <a href="issueHr.php" class="btn btn-gradient-slate mt-2">View Issues</a>
                        <?php else: ?>
                            <h5 class="card-title" style="color: #0d9488; font-weight: 700;"><i class="fas fa-check-circle"></i> No Issues Report</h5>
                            <p class="card-text text-muted mb-3">You haven't reported any issues yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>
