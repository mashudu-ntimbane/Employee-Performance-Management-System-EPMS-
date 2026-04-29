<?php
include('NewDbConn.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['empID'])) {
    header("Location: login.php");
    exit();
}

$empID = $_SESSION['empID'];

// Fetch the employee's name from the database
$empNameQuery = "SELECT empFname FROM emplooyedetails WHERE empID = ?";
$stmt = $conn->prepare($empNameQuery);
$stmt->bind_param("i", $empID);
$stmt->execute();
$empNameResult = $stmt->get_result();
$empName = $empNameResult->fetch_assoc()['empFname'];
$stmt->close();

// Fetch feedback for the logged-in user
$query = "SELECT r.rating_id, r.issueID, r.issue_category, r.specific_issue, 
                 r.descriptive_rating, r.graphic_rating, r.rating_date,
                 r.manager_rating, r.time_assessment,
                 i.in_progress_time, i.fixed_time
          FROM issue_ratings r 
          JOIN issues i ON i.issueID = r.issueID 
          WHERE (i.attended_by = ? OR FIND_IN_SET(?, i.staff_worked_with) > 0)
            AND r.feedback_sent = 1
          ORDER BY r.rating_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $empName, $empName);
$stmt->execute();
$result = $stmt->get_result();

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

        .btn-gradient-rose {
            background: linear-gradient(90deg, #e11d48 0%, #be123c 100%);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-rose:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(225, 29, 72, 0.35);
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

        /* Star Rating */
        .star-rating {
            color: #f59e0b;
            font-size: 1.15rem;
            letter-spacing: 2px;
            text-shadow: 0 1px 2px rgba(245, 158, 11, 0.2);
        }

        /* Page Header */
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
        <!--New css-->
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
        <a href="other_staff.php" class="nav-link-item ">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="other_staffProfile.php" class="nav-link-item">
            <i class="fas fa-user-circle"></i>
            <span>User Profile</span>
        </a>
        <a href="messagesOS.php" class="nav-link-item">
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
        <a href="feedback.php" class="nav-link-item active">
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
                <i class="fas fa-comment-dots"></i>
                Your Feedback, <?php echo htmlspecialchars($empName); ?>
            </div>
        </div> -->
        <div class="dashboard-card border-accent-teal">
            <div class="card-header-custom">
                <i class="fas fa-star" style="color: var(--accent-gold);"></i>
                <span>Performance Ratings Received</span>
            </div>
            <div class="card-body-custom" style="text-align: left; padding: 0;">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
            <table class="table issues-table">
                <thead>
                    <tr>
                        <th>Issue ID</th>
                        <th>Category</th>
                        <th>Specific Issue</th>
                        <th>Staff Rating</th>
                        <th>Time Spent</th>
                        <th>Manager Feedback</th>
                        <th>Time Assessment</th>
                        <th>Date Rated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['issueID']); ?></td>
                            <td><?php echo htmlspecialchars($row['issue_category']); ?></td>
                            <td><?php echo htmlspecialchars($row['specific_issue']); ?></td>
                            <td>
                                <div><?php echo ucfirst(htmlspecialchars($row['descriptive_rating'])); ?></div>
                                <div class="star-rating">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $row['graphic_rating'] ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td><?php echo calculateTimeSpent($row['in_progress_time'], $row['fixed_time']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($row['manager_rating'])); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($row['time_assessment'])); ?></td>
                            <td><?php echo htmlspecialchars($row['rating_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-comment-slash"></i>
                <h5>No Feedback Yet</h5>
                <p>You haven't received any performance feedback. Complete tasks to receive ratings from your manager.</p>
            </div>
        <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<?php
$conn->close();
?>
