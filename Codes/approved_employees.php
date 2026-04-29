<?php
session_start();
include('NewDbConn.php');

$result = $conn->query("SELECT empID, empFname, empLname, empGender, empPosition, empRole, empEmail, empPhoneNum FROM emplooyedetails WHERE approved = 1 ORDER BY empID DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fafc; padding: 20px; }
        h4 { color: #0d9488; font-weight: 700; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
        .table th { background: linear-gradient(90deg, #0d9488, #0f766e); color: #fff; font-weight: 600; }
        .table td, .table th { vertical-align: middle; font-size: 0.9rem; }
        .badge-male { background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-female { background: #fce7f3; color: #9d174d; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-other { background: #f3f4f6; color: #4b5563; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>
    <h4><i class="fas fa-user-check"></i> Approved Employees</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Position</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['empID']; ?></td>
                    <td><?php echo htmlspecialchars($row['empFname']); ?></td>
                    <td><?php echo htmlspecialchars($row['empLname']); ?></td>
                    <td>
                        <?php
                        $g = strtolower(trim($row['empGender']));
                        if ($g == 'male') echo '<span class="badge-male"><i class="fas fa-mars"></i> Male</span>';
                        elseif ($g == 'female') echo '<span class="badge-female"><i class="fas fa-venus"></i> Female</span>';
                        else echo '<span class="badge-other"><i class="fas fa-user"></i> ' . htmlspecialchars($row['empGender']) . '</span>';
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['empPosition']); ?></td>
                    <td><?php echo htmlspecialchars($row['empRole']); ?></td>
                    <td><?php echo htmlspecialchars($row['empEmail']); ?></td>
                    <td><?php echo htmlspecialchars($row['empPhoneNum']); ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8" class="text-center text-muted">No approved employees found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>

