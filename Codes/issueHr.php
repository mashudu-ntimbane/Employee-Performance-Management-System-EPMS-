<?php
session_start();

// Include the database connection file
include('NewDbConn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issueCategory = $_POST['issueCategory'];
    $specificIssue = $_POST['specificIssue'];
    $role = $_POST['role'];
    $empID = $_SESSION['empID'];

    $query = "SELECT ed.empFname, ed.empLname, b.B_name, r.id, r.r_name 
              FROM emplooyedetails ed
              JOIN Rooms r ON ed.empID = r.empID
              JOIN Buildings b ON r.id = b.id
              WHERE ed.empID = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $empID);
        $stmt->execute();
        $stmt->bind_result($empFname, $empLname, $name, $id, $name);
        $stmt->fetch();
        $stmt->close();
    }

    $insertIssueQuery = "INSERT INTO Issues (empID, issue_category, specific_issue, role, status) 
                         VALUES (?, ?, ?, ?, 'Pending')";
    if ($stmt = $conn->prepare($insertIssueQuery)) {
        $stmt->bind_param('isss', $empID, $issueCategory, $specificIssue, $role);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

if (!isset($_SESSION['empID'])) {
    header('Location: logIn.php');
    exit();
}

$empID = $_SESSION['empID'];

// ── Table 1 query: current user's own reported issues ──
$stmtOwn = $conn->prepare("SELECT i.issueID, i.issue_category, i.specific_issue, i.status,
                             i.requested_date, i.attended_by, i.rating 
                            FROM issues i 
                            WHERE i.empID = ? 
                            ORDER BY i.requested_date DESC");
$stmtOwn->bind_param("i", $empID);
$stmtOwn->execute();
$userIssues = $stmtOwn->get_result();

// ── Fetch current user's full name ──
$stmtName = $conn->prepare("SELECT empFname FROM emplooyedetails WHERE empID = ?");
$stmtName->bind_param("i", $empID);
$stmtName->execute();
$nameResult = $stmtName->get_result();
if ($nameResult->num_rows > 0) {
    $nameRow  = $nameResult->fetch_assoc();
    $userName = $nameRow['empFname'];
} else {
    echo "Error: User not found."; exit();
}
$stmtName->close();

// ── Handle status update from role-based table ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['issueID'])) {
    $status   = $_POST['status'];
    $issueID  = $_POST['issueID'];
    $attendedBy = ($status == "Pending") ? NULL : $userName;

    $updateQuery = "UPDATE issues SET status = ?, attended_by = ?";
    if ($status == "Pending")     $updateQuery .= ", in_progress_time = NULL, fixed_time = NULL";
    elseif ($status == "In_Progress") $updateQuery .= ", in_progress_time = CURRENT_TIMESTAMP";
    elseif ($status == "fixed")   $updateQuery .= ", fixed_time = CURRENT_TIMESTAMP";
    $updateQuery .= " WHERE issueID = ?";

    $stmtUpd = $conn->prepare($updateQuery);
    $stmtUpd->bind_param("ssi", $status, $attendedBy, $issueID);
    if ($stmtUpd->execute()) {
        $_SESSION['status_update_success'] = "Issue status updated successfully.";
    } else {
        $_SESSION['status_update_error'] = "Error updating issue status.";
    }
    $stmtUpd->close();
}

// ── Fetch user's role ──
$stmtRole = $conn->prepare("SELECT empRole FROM emplooyedetails WHERE empID = ?");
$stmtRole->bind_param("i", $empID);
$stmtRole->execute();
$roleResult = $stmtRole->get_result();
if ($roleResult->num_rows > 0) {
    $roleRow  = $roleResult->fetch_assoc();
    $userRole = $roleRow['empRole'];
} else {
    echo "Error: User not found."; exit();
}
$stmtRole->close();

// ── Table 3 query: issues matching current user's role ──
$sql = "SELECT i.issueID, e.empFname, issue_category, specific_issue, status,
               i.attended_by, requested_date, r.r_name, b.b_name 
        FROM issues i 
        JOIN emplooyedetails e ON i.empID = e.empID 
        JOIN rooms r ON r.empID = e.empID 
        JOIN buildings b ON b.id = r.building_id 
        WHERE role = ?";
$stmtAssigned = $conn->prepare($sql);
$stmtAssigned->bind_param("s", $userRole);
$stmtAssigned->execute();
$result = $stmtAssigned->get_result();

function calculateTimeSpent($inProgressTime, $fixedTime) {
    if (!$inProgressTime || !$fixedTime) return "N/A";
    $start    = new DateTime($inProgressTime);
    $end      = new DateTime($fixedTime);
    $interval = $start->diff($end);
    $timeSpent = "";
    if ($interval->d > 0) $timeSpent .= $interval->d . " day"    . ($interval->d > 1 ? "s" : "") . " ";
    if ($interval->h > 0) $timeSpent .= $interval->h . " hour"   . ($interval->h > 1 ? "s" : "") . " ";
    if ($interval->i > 0) $timeSpent .= $interval->i . " minute" . ($interval->i > 1 ? "s" : "");
    return trim($timeSpent) ?: "Less than a minute";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href=".css">
      <link rel="icon" type="image/x-icon" href="EPMS1.JPG">
    <style>
        :root {
            --primary-deep: #0c1e3d;
            --primary-mid:  #1e3a5f;
            --accent-teal:  #0d9488;
            --accent-gold:  #f59e0b;
            --bg-card:      #ffffff;
            --text-main:    #1e293b;
            --text-muted:   #64748b;
            --nav-width:    230px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ── Header ── */
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
            box-shadow: 0 4px 20px rgba(12,30,61,0.25);
        }
        .header-item { flex: 1; text-align: center; min-width: 180px; }
        .header-item:first-child { font-size: 1.35rem; font-weight: 700; letter-spacing: .8px; color:#fff; text-align:left; }
        .header-message { flex:1.5; font-style:italic; color:#e0f2f1; font-weight:500; font-size:1.05rem; text-align:center; }
        .header-item:last-child { color:#e0f2f1; font-weight:600; font-size:.95rem; text-align:right; }

        /* ── Layout ── */
        .app-layout { display:flex; min-height:calc(100vh - 60px); }

        /* ── Side Nav ── */
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
            display:flex; align-items:center; gap:14px;
            padding:13px 18px; border-radius:14px;
            color:var(--text-main); text-decoration:none;
            font-weight:600; font-size:.95rem;
            transition:all .3s cubic-bezier(.4,0,.2,1);
            position:relative; overflow:hidden;
        }
        .nav-link-item i { font-size:1.15rem; width:26px; text-align:center; color:var(--text-muted); transition:all .3s ease; }
        .nav-link-item:hover { background:rgba(13,148,136,.08); color:var(--accent-teal); transform:translateX(4px); }
        .nav-link-item:hover i { color:var(--accent-teal); }
        .nav-link-item.active {
            background: linear-gradient(90deg,#1e3a5f,#0d9488);
            color:#fff; box-shadow:0 6px 20px rgba(13,148,136,.35); transform:translateX(6px);
        }
        .nav-link-item.active i { color:#fff; }
        .nav-link-item.active::before {
            content:''; position:absolute; left:0; top:50%; transform:translateY(-50%);
            width:4px; height:60%; background:var(--accent-gold); border-radius:0 4px 4px 0;
        }
        .nav-link-item.logout { margin-top:auto; color:#dc2626; }
        .nav-link-item.logout i { color:#dc2626; }
        .nav-link-item.logout:hover { background:rgba(220,38,38,.08); color:#dc2626; }
        .nav-link-item.logout:hover i { color:#dc2626; }

        /* ── Main Content ── */
        .main-content { flex:1; padding:28px 32px; background:transparent; }

        /* ── Dashboard Card ── */
        .dashboard-card {
            background:var(--bg-card); border:none; border-radius:18px;
            box-shadow:0 2px 12px rgba(12,30,61,.06);
            transition:all .3s ease; overflow:hidden;
        }
        .dashboard-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(12,30,61,.12); }
        .card-header-custom {
            background:transparent; border-bottom:1px solid rgba(0,0,0,.06);
            padding:18px 22px; font-weight:700; font-size:1rem;
            display:flex; align-items:center; gap:10px;
        }
        .card-body-custom { padding:22px; text-align:center; }

        .border-accent-teal   { border-left:4px solid #0d9488; }
        .border-accent-rose   { border-left:4px solid #e11d48; }
        .border-accent-slate  { border-left:4px solid #475569; }
        .border-accent-indigo { border-left:4px solid #4f46e5; }

        /* ── Gradient Buttons ── */
        .btn-gradient-teal {
            background:linear-gradient(90deg,#0d9488,#0f766e); border:none; color:#fff;
            border-radius:10px; padding:8px 24px; font-weight:600;
            box-shadow:0 4px 12px rgba(13,148,136,.25); transition:all .3s ease;
        }
        .btn-gradient-teal:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(13,148,136,.35); color:#fff; }

        .btn-gradient-gold {
            background:linear-gradient(90deg,#f59e0b,#d97706); border:none; color:#fff;
            border-radius:10px; padding:8px 24px; font-weight:600;
            box-shadow:0 4px 12px rgba(245,158,11,.25); transition:all .3s ease;
        }
        .btn-gradient-gold:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(245,158,11,.35); color:#fff; }

        .btn-gradient-slate {
            background:linear-gradient(90deg,#475569,#334155); border:none; color:#fff;
            border-radius:10px; padding:8px 24px; font-weight:600;
            box-shadow:0 4px 12px rgba(71,85,105,.25); transition:all .3s ease;
        }
        .btn-gradient-slate:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(71,85,105,.35); color:#fff; }

        .issue-form-card { width:100%; max-width:1200px; }
        .issue-form-card .form-select:focus,
        .issue-form-card .form-control:focus {
            border-color:var(--accent-teal);
            box-shadow:0 0 0 .2rem rgba(13,148,136,.25);
        }

        /* ── Unified Table Style ── */
        .issues-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.875rem; }
        .issues-table thead tr {
            background:linear-gradient(90deg,#0c1e3d 0%,#1e3a5f 60%,#0d9488 100%);
        }
        .issues-table thead th {
            color:#fff; font-weight:600; font-size:.75rem;
            text-transform:uppercase; letter-spacing:.7px;
            padding:13px 16px; border:none; white-space:nowrap;
        }
        .issues-table thead th:first-child { border-radius:12px 0 0 0; }
        .issues-table thead th:last-child  { border-radius:0 12px 0 0; }
        .issues-table tbody tr { transition:background .2s ease; }
        .issues-table tbody tr:hover { background:#f8fafc; }
        .issues-table tbody td {
            padding:12px 16px; color:var(--text-main);
            vertical-align:middle; border:none;
            border-bottom:1px solid #f1f5f9;
        }
        .issues-table tbody tr:last-child td { border-bottom:none; }

        /* ── Status Badges ── */
        .status-badge {
            display:inline-flex; align-items:center; gap:5px;
            padding:4px 12px; border-radius:20px;
            font-size:.75rem; font-weight:600; white-space:nowrap;
        }
        .status-pending  { background:#fef3c7; color:#92400e; }
        .status-progress { background:#dbeafe; color:#1e40af; }
        .status-fixed    { background:#d1fae5; color:#065f46; }
        .status-other    { background:#f1f5f9; color:#475569; }

        /* ── Star Rating ── */
        .star-rating { display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:2px; }
        .star-rating input { display:none; }
        .star-rating label {
            font-size:1.8rem; color:#d1d5db; cursor:pointer;
            transition:color .2s ease, transform .15s ease; line-height:1;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label { color:#f59e0b; }
        .star-rating label:hover { transform:scale(1.2); }

        /* ── Rate Button ── */
        .btn-rate {
            background:linear-gradient(90deg,#f59e0b,#d97706); border:none; color:#fff;
            border-radius:8px; padding:5px 14px; font-size:.78rem; font-weight:700;
            letter-spacing:.4px; cursor:pointer;
            box-shadow:0 3px 10px rgba(245,158,11,.3); transition:all .25s ease;
        }
        .btn-rate:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(245,158,11,.4); color:#fff; }

        /* ── Rating Modal ── */
        .rating-modal-header {
            background:linear-gradient(90deg,#0c1e3d,#0d9488);
            color:#fff; border-radius:12px 12px 0 0; padding:20px 24px;
        }
        .rating-modal-header .btn-close { filter:invert(1) opacity(.8); }

        /* ── Progress Bar ── */
        .prog-wrap { min-width:120px; }
        .prog-wrap .progress { height:22px; border-radius:30px; overflow:hidden; background:#e2e8f0; }
        .prog-wrap .progress-bar { font-size:.7rem; font-weight:700; letter-spacing:.5px; border-radius:30px; }

        /* ── Reporter Chip ── */
        .sender-chip {
            display:inline-flex; align-items:center; gap:6px;
            padding:4px 10px;
            background:linear-gradient(90deg,#ede9fe,#dbeafe);
            border-radius:20px; font-size:.78rem; font-weight:600; color:#3730a3;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .side-nav { width:70px; padding:16px 8px; }
            .nav-link-item span { display:none; }
            .nav-link-item { justify-content:center; padding:14px; }
            .nav-link-item i { font-size:1.3rem; width:auto; }
            .main-content { padding:18px; }
            .header-item:first-child { font-size:1.1rem; text-align:center; }
            .header-message { display:none; }
        }
    </style>
</head>
<body >

        
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
        <a href="issuesHr.php" class="nav-link-item active">
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
    <main class="main-content d-flex flex-column align-items-center gap-4">

        <!-- ══════════════════════════════════
             Report an Issue Form
             ══════════════════════════════════ -->
        <div class="dashboard-card border-accent-rose issue-form-card">
            <div class="card-header-custom">
                <i class="fas fa-exclamation-circle" style="color:#e11d48;"></i>
                <span>Report an Issue</span>
            </div>
            <div class="card-body-custom text-start">
                <form action="issueHr.php" method="POST" onsubmit="return handleFormSubmission()">
                    <div class="mb-3">
                        <label for="issueCategory" class="form-label fw-semibold">Issue Category</label>
                        <select id="issueCategory" name="issueCategory" class="form-select" onchange="updateSpecificIssues()" required>
                            <option value="">Select Category</option>
                            <option value="Hardware Issues">Hardware Issues</option>
                            <option value="Software Issues">Software Issues</option>
                            <option value="Network Connectivity">Network Connectivity</option>
                            <option value="Data Storage">Data Storage</option>
                            <option value="Security Breaches">Security Breaches</option>
                            <option value="Physical Infrastructure">Physical Infrastructure</option>
                            <option value="Furniture and fixtures">Furniture and fixtures</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="specificIssue" class="form-label fw-semibold">Specific Issue</label>
                        <select id="specificIssue" name="specificIssue" class="form-select" onchange="updateRole()" required>
                            <option value="">Select Specific Issue</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="role" class="form-label fw-semibold">Assigned Role</label>
                        <input type="text" id="role" name="role" class="form-control" readonly placeholder="Auto-populated based on issue selection">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-gradient-teal">
                            <i class="fas fa-paper-plane me-1"></i> Submit Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ══════════════════════════════════
             TABLE 1 — My Reported Issues
             (Rating modal added here)
             ══════════════════════════════════ -->
        <div class="dashboard-card border-accent-slate issue-form-card">
            <div class="card-header-custom">
                <i class="fas fa-clipboard-list" style="color:#475569;"></i>
                <span>My Reported Issues</span>
            </div>
            <div class="card-body-custom text-start">
                <div class="mb-3">
                    <button type="button" class="btn btn-gradient-slate btn-sm" onclick="toggleIssuesTable()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                        <span id="toggleText">Show Issues</span>
                    </button>
                </div>

                <div id="issuesTableContainer" style="display:none;">
                    <?php if ($userIssues->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="issues-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Specific Issue</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
                                    <th>Attended By</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $userIssues->fetch_assoc()):
                                    $status   = (!empty($row['status']))      ? $row['status']      : 'Pending';
                                    $attended = (!empty($row['attended_by'])) ? $row['attended_by'] : 'Pending';
                                    $badgeClass = match($status) {
                                        'fixed'       => 'status-fixed',
                                        'In_Progress' => 'status-progress',
                                        'Pending'     => 'status-pending',
                                        default       => 'status-other'
                                    };
                                    $badgeIcon = match($status) {
                                        'fixed'       => 'fa-check-circle',
                                        'In_Progress' => 'fa-spinner fa-spin',
                                        'Pending'     => 'fa-clock',
                                        default       => 'fa-circle'
                                    };
                                ?>
                                <tr>
                                    <td><span class="fw-bold" style="color:var(--text-muted);font-size:.8rem;">#<?php echo htmlspecialchars($row['issueID']); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['issue_category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['specific_issue']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $badgeClass; ?>">
                                            <i class="fas <?php echo $badgeIcon; ?>"></i>
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size:.82rem;color:var(--text-muted);">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php echo htmlspecialchars($row['requested_date']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($attended === 'Pending'): ?>
                                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                                        <?php else: ?>
                                            <span style="font-weight:600;">
                                                <i class="fas fa-user-check me-1" style="color:var(--accent-teal);"></i>
                                                <?php echo htmlspecialchars($attended); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- ── Rating Cell ── -->
                                    <td> 
                                        <?php if ($status === 'fixed' && is_null($row['rating'])): ?>
                                            <!-- Rate button triggers modal -->
                                            <button type="button" class="btn-rate"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#ratingModal<?php echo $row['issueID']; ?>">
                                                <i class="fas fa-star me-1"></i>Rate
                                            </button>

                                            <!-- Rating Modal -->
                                            <div class="modal fade" id="ratingModal<?php echo $row['issueID']; ?>"
                                                 tabindex="-1"
                                                 aria-labelledby="ratingModalLabel<?php echo $row['issueID']; ?>"
                                                 aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content"
                                                         style="border-radius:16px;overflow:hidden;border:none;
                                                                box-shadow:0 20px 60px rgba(12,30,61,.22);">
                                                        <!-- Modal Header -->
                                                        <div class="modal-header rating-modal-header">
                                                            <div>
                                                                <h5 class="modal-title mb-1"
                                                                    id="ratingModalLabel<?php echo $row['issueID']; ?>">
                                                                    <i class="fas fa-star me-2"></i>Rate the Service
                                                                </h5>
                                                                <small style="opacity:.8;font-size:.78rem;">
                                                                    Issue #<?php echo htmlspecialchars($row['issueID']); ?>
                                                                    &mdash; <?php echo htmlspecialchars($row['specific_issue']); ?>
                                                                </small>
                                                            </div>
                                                            <button type="button" class="btn-close rating-modal-header"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <!-- Modal Body -->
                                                        <div class="modal-body p-4">
                                                            <form action="submit_rating.php" method="post">
                                                                <input type="hidden" name="issueID" value="<?php echo $row['issueID']; ?>">

                                                                <!-- Descriptive Rating -->
                                                                <div class="mb-4">
                                                                    <label class="form-label fw-semibold" style="font-size:.9rem;">
                                                                        <i class="fas fa-comment-dots me-1" style="color:var(--accent-teal);"></i>
                                                                        How would you describe the service?
                                                                    </label>
                                                                    <select class="form-select" name="descriptiveRating" required>
                                                                        <option value="">Choose an option…</option>
                                                                        <option value="excellent">⭐ Excellent — Exceeded expectations</option>
                                                                        <option value="good">👍 Good — Met expectations</option>
                                                                        <option value="average">😐 Average — Could be better</option>
                                                                        <option value="poor">👎 Poor — Below expectations</option>
                                                                    </select>
                                                                </div>

                                                                <!-- Star Rating -->
                                                                <div class="mb-4">
                                                                    <label class="form-label fw-semibold" style="font-size:.9rem;">
                                                                        <i class="fas fa-star me-1" style="color:var(--accent-gold);"></i>
                                                                        Star Rating
                                                                    </label>
                                                                    <div class="star-rating">
                                                                        <input type="radio" id="star5_<?php echo $row['issueID']; ?>" name="graphicRating" value="5" required/>
                                                                        <label for="star5_<?php echo $row['issueID']; ?>" title="5 stars">&#9733;</label>
                                                                        <input type="radio" id="star4_<?php echo $row['issueID']; ?>" name="graphicRating" value="4"/>
                                                                        <label for="star4_<?php echo $row['issueID']; ?>" title="4 stars">&#9733;</label>
                                                                        <input type="radio" id="star3_<?php echo $row['issueID']; ?>" name="graphicRating" value="3"/>
                                                                        <label for="star3_<?php echo $row['issueID']; ?>" title="3 stars">&#9733;</label>
                                                                        <input type="radio" id="star2_<?php echo $row['issueID']; ?>" name="graphicRating" value="2"/>
                                                                        <label for="star2_<?php echo $row['issueID']; ?>" title="2 stars">&#9733;</label>
                                                                        <input type="radio" id="star1_<?php echo $row['issueID']; ?>" name="graphicRating" value="1"/>
                                                                        <label for="star1_<?php echo $row['issueID']; ?>" title="1 star">&#9733;</label>
                                                                    </div>
                                                                </div>

                                                                <div class="d-grid">
                                                                    <button type="submit" class="btn btn-gradient-gold">
                                                                        <i class="fas fa-paper-plane me-2"></i>Submit Rating
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /Rating Modal -->

                                        <?php elseif (!is_null($row['rating'])): ?>
                                            <span class="status-badge status-fixed">
                                                <i class="fas fa-star"></i>
                                                <?php echo htmlspecialchars($row['rating']); ?> / 5 stars
                                            </span>
                                        <?php else: ?>
                                            <!-- Not fixed yet — show Pending -->
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-hourglass-half"></i> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- /Rating Cell -->
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center mb-0" style="border-radius:12px;">
                            <i class="fas fa-info-circle me-2"></i>You have not reported any issues yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════
             TABLE 3 — Issues Assigned to My Role
             (Reporter = staff who submitted the issue requiring the current user's role)
             ══════════════════════════════════ -->
        <div class="dashboard-card border-accent-indigo issue-form-card">
            <div class="card-header-custom">
                <i class="fas fa-tools" style="color:#4f46e5;"></i>
                <span>Issues Assigned to My Role</span>
                <span class="ms-auto">
                    <span class="status-badge status-progress" style="font-size:.7rem;">
                        <i class="fas fa-user-tag"></i>
                        <?php echo htmlspecialchars($userRole); ?>
                    </span>
                </span>
            </div>
            <div class="card-body-custom text-start">

                <?php if (isset($_SESSION['status_update_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:10px;">
                        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['status_update_success']; unset($_SESSION['status_update_success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['status_update_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:10px;">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['status_update_error']; unset($_SESSION['status_update_error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="issues-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reporter</th>
                                <th>Category</th>
                                <th>Specific Issue</th>
                                <th>Room</th>
                                <th>Building</th>
                                <th>Update Status</th>
                                <th>Attended By</th>
                                <th>Date Sub.</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()):
                                $rowStatus   = (!empty($row['status']))      ? $row['status']      : 'Pending';
                                $rowAttended = ($rowStatus === 'Pending' || empty($row['attended_by'])) ? 'Pending' : $row['attended_by'];
                            ?>
                            <tr>
                                <td><span class="fw-bold" style="color:var(--text-muted);font-size:.8rem;">#<?php echo htmlspecialchars($row['issueID']); ?></span></td>

                                <!-- Reporter chip -->
                                <td>
                                    <span class="sender-chip">
                                        <i class="fas fa-user"></i>
                                        <?php echo htmlspecialchars($row['empFname']); ?>
                                    </span>
                                </td>

                                <td><?php echo htmlspecialchars($row['issue_category']); ?></td>
                                <td><?php echo htmlspecialchars($row['specific_issue']); ?></td>

                                <td>
                                    <span style="font-size:.82rem;">
                                        <i class="fas fa-door-open me-1" style="color:var(--accent-teal);"></i>
                                        <?php echo htmlspecialchars($row['r_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size:.82rem;">
                                        <i class="fas fa-building me-1" style="color:var(--primary-mid);"></i>
                                        <?php echo htmlspecialchars($row['b_name']); ?>
                                    </span>
                                </td>

                                <!-- Status dropdown -->
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="issueID" value="<?php echo htmlspecialchars($row['issueID']); ?>">
                                        <select name="status" class="form-select form-select-sm"
                                                style="min-width:130px;border-radius:8px;font-size:.82rem;font-weight:600;"
                                                onchange="this.form.submit()">
                                            <option value="Pending"     <?php echo $rowStatus === 'Pending'     ? 'selected' : ''; ?>>Pending</option>
                                            <option value="In_Progress" <?php echo $rowStatus === 'In_Progress' ? 'selected' : ''; ?>>In Progress</option>
                                            <option value="fixed"       <?php echo $rowStatus === 'fixed'       ? 'selected' : ''; ?>>Fixed</option>
                                        </select>
                                    </form>
                                </td>

                                <!-- Attended By -->
                                <td>
                                    <?php if ($rowAttended === 'Pending'): ?>
                                        <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                                    <?php else: ?>
                                        <span style="font-weight:600;font-size:.83rem;">
                                            <i class="fas fa-user-check me-1" style="color:var(--accent-teal);"></i>
                                            <?php echo htmlspecialchars($rowAttended); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Date -->
                                <td>
                                    <span style="font-size:.82rem;color:var(--text-muted);">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?php echo htmlspecialchars($row['requested_date']); ?>
                                    </span>
                                </td>

                                <!-- Progress visual -->
                                <td>
                                    <div class="prog-wrap">
                                        <?php if ($rowStatus === 'Pending'): ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="spinner-border spinner-border-sm text-warning"
                                                     role="status" style="width:16px;height:16px;"></div>
                                                <span style="font-size:.75rem;color:#92400e;font-weight:600;">Pending</span>
                                            </div>
                                        <?php elseif ($rowStatus === 'just started'): ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width:20%;">Just Started</div>
                                            </div>
                                        <?php elseif ($rowStatus === 'In_Progress'): ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width:60%;">In Progress</div>
                                            </div>
                                        <?php elseif ($rowStatus === 'about to finish'): ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width:85%;">Almost Done</div>
                                            </div>
                                        <?php elseif ($rowStatus === 'fixed'): ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width:100%;">✔ Fixed</div>
                                            </div>
                                        <?php else: ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-danger progress-bar-striped" style="width:100%;">Not Started</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info text-center mb-0" style="border-radius:12px;">
                        <i class="fas fa-clipboard-check me-2"></i>No issues have been assigned to your role at the moment.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div><!-- /app-layout -->

<script>
    function updateSpecificIssues() {
        var category = document.getElementById("issueCategory").value;
        var specificIssue = document.getElementById("specificIssue");
        var role = document.getElementById("role");

        var issues = {
            "Hardware Issues": [
                { issue:"Computers", role:"IT Technician" },
                { issue:"Servers",   role:"IT Technician" },
                { issue:"Printers",  role:"IT Technician" },
                { issue:"Scanners",  role:"IT Technician" }
            ],
            "Software Issues": [
                { issue:"Operating systems", role:"Software Developer/Engineer" },
                { issue:"Applications",      role:"Software Developer/Engineer" },
                { issue:"Software bugs",     role:"Software Developer/Engineer" }
            ],
            "Network Connectivity": [
                { issue:"Internet",                                         role:"Network Engineer" },
                { issue:"Intranet",                                         role:"Network Engineer" },
                { issue:"VPN issues",                                       role:"Network Engineer" },
                { issue:"Network equipment (routers, switches, firewalls)", role:"Network Engineer" }
            ],
            "Data Storage": [
                { issue:"Database errors", role:"Database Administrator" },
                { issue:"Backup failures", role:"Database Administrator" }
            ],
            "Security Breaches": [
                { issue:"Cyberattacks", role:"Security Specialist" },
                { issue:"Data loss",    role:"Security Specialist" }
            ],
            "Physical Infrastructure": [
                { issue:"Power outages",             role:"Electrician" },
                { issue:"Faulty wiring",             role:"Electrician" },
                { issue:"Outlets",                   role:"Electrician" },
                { issue:"Leaks",                     role:"Plumber" },
                { issue:"Clogs",                     role:"Plumber" },
                { issue:"Heating",                   role:"HVAC Technician" },
                { issue:"Ventilation",               role:"HVAC Technician" },
                { issue:"Air conditioning problems", role:"HVAC Technician" },
                { issue:"Alarm systems",             role:"Security Specialist" },
                { issue:"CCTV",                      role:"Security Specialist" },
                { issue:"Access control",            role:"Security Specialist" }
            ],
            "Furniture and fixtures": [
                { issue:"Repairs",      role:"Facilities Manager" },
                { issue:"Replacements", role:"Facilities Manager" }
            ]
        };

        specificIssue.innerHTML = "";
        role.value = "";

        if (issues[category]) {
            issues[category].forEach(function(item) {
                var option = document.createElement("option");
                option.value = item.issue;
                option.text  = item.issue;
                option.setAttribute("data-role", item.role);
                specificIssue.add(option);
            });
        }
    }

    function updateRole() {
        var specificIssue  = document.getElementById("specificIssue");
        var role           = document.getElementById("role");
        var selectedOption = specificIssue.options[specificIssue.selectedIndex];
        role.value = selectedOption.getAttribute("data-role");
    }

    function handleFormSubmission() {
        var category      = document.getElementById("issueCategory").value;
        var specificIssue = document.getElementById("specificIssue").value;
        var role          = document.getElementById("role").value;

        if (!category || !specificIssue || !role) {
            alert('Please select all issue details before submitting.');
            return false;
        }
        alert('Issue submitted successfully!');
        return true;
    }

    function toggleIssuesTable() {
        var container = document.getElementById("issuesTableContainer");
        var icon = document.getElementById("toggleIcon");
        var text = document.getElementById("toggleText");

        if (container.style.display === "none") {
            container.style.display = "block";
            icon.className = "fas fa-eye-slash";
            text.textContent = "Hide Issues";
        } else {
            container.style.display = "none";
            icon.className = "fas fa-eye";
            text.textContent = "Show Issues";
        }
    }
</script>

<?php
$stmtOwn->close();
$stmtAssigned->close();
$conn->close();
?>
</body>
</html>
