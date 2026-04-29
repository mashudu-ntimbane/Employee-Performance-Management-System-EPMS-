<?php
session_start();
include('NewDbConn.php');

$query = "SELECT c.empID, e.empFname, e.empLname, e.empRole, c.clock_in_time, c.clock_out_time 
          FROM clock_in_records c 
          JOIN emplooyedetails e ON c.empID = e.empID 
          WHERE DATE(c.clock_in_time) = CURDATE() AND c.clock_out_time IS NOT NULL 
          ORDER BY c.clock_out_time DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clocked Out Today</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; padding: 20px; }
        h4 { color: #475569; font-weight: 700; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
        .table th { background: linear-gradient(90deg, #475569, #334155); color: #fff; font-weight: 600; }
        .table td, .table th { vertical-align: middle; font-size: 0.9rem; }
        .status-out { background: #e2e8f0; color: #334155; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>
    <h4><i class="fas fa-sign-out-alt"></i> Clocked Out Today — <?php echo date('Y-m-d'); ?></h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Clock-In Time</th>
                    <th>Clock-Out Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['empID']; ?></td>
                    <td><?php echo htmlspecialchars($row['empFname']); ?></td>
                    <td><?php echo htmlspecialchars($row['empLname']); ?></td>
                    <td><?php echo htmlspecialchars($row['empRole']); ?></td>
                    <td><?php echo $row['clock_in_time']; ?></td>
                    <td><?php echo $row['clock_out_time']; ?></td>
                    <td><span class="status-out"><i class="fas fa-check" style="font-size:0.6rem;vertical-align:middle;margin-right:4px;"></i> Clocked Out</span></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center text-muted">No employees clocked out today.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>

