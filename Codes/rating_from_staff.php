<?php
session_start();
include('NewDbConn.php');



// Fetch all ratings
$query = "SELECT * FROM issue_ratings ORDER BY rating_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback from Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .star-rating {
            color: #f90;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Feedback from Staff</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Issue Category</th>
                    <th>Specific Issue</th>
                    <th>Attended By</th>
                    <th>Worked With</th>
                    <th>Descriptive Rating</th>
                    <th>Graphic Rating</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['issueID']); ?></td>
                        <td><?php echo htmlspecialchars($row['issue_category']); ?></td>
                        <td><?php echo htmlspecialchars($row['specific_issue']); ?></td>
                        <td><?php echo htmlspecialchars($row['attended_by']); ?></td>
                        <td><?php echo htmlspecialchars($row['staff_worked_with']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($row['descriptive_rating'])); ?></td>
                        <td>
                            <div class="star-rating">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $row['graphic_rating'] ? '★' : '☆';
                                }
                                ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['rating_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>