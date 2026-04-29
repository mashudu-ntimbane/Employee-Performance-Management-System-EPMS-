<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
  
    
    <style>
        /* Set background color of the entire page */
        body {
            background-color: #add8e6; /* Light blue background */
        }
        
        /* Fix and enhance border styling */
        .b1 {
            border-bottom: 2px solid black;
            margin-bottom: 20px; /* Added space for better visual */
        }

        /* Center and perfect the div container */
        .centered-container {
            width: 50%;
            background-color: #ffffff;
            border: 2px solid #000; /* Made border solid black */
            border-radius: 10px;
            padding: 30px;
            margin: 50px auto; /* Centered horizontally */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Added slight shadow */
            transition: background-color 0.3s;
        }

        .centered-container:hover {
            background-color: #e3f2fd; /* Blue hover color */
        }
    
       .b1 {
  border-bottom: 2px solid black;

}
        .centered-container {
            width: 50%;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 50px;
            transition: background-color 0.3s;
        }
        .centered-container:hover {
            background-color: #e3f2fd; /* Blue hover color */
        }
        .container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>

<?php
// Start a new session or resume the existing session
session_start();

// Include the database connection file
include('NewDbConn.php');

// Check if the user is logged in
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit();
}

// Get the message ID from the URL
$messageId = $_GET['id'];

// Update the message to mark it as read
$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
$stmt->bind_param("i", $messageId);
$stmt->execute();

// Prepare an SQL statement to select the message details
$stmt = $conn->prepare("SELECT m.id, m.message, m.timestamp, u.empLname, u.empFname AS sender, f.file_path 
                        FROM messages m 
                        LEFT JOIN emplooyedetails u ON m.sender_id = u.empID  
                        LEFT JOIN files f ON m.id = f.message_id 
                        WHERE m.id = ?");

// Bind the message ID parameter to the SQL statement
$stmt->bind_param("i", $messageId);

// Execute the SQL statement
$stmt->execute();

// Get the result of the executed query
$result = $stmt->get_result();
?>

<div class="container-wrapper">
    <div class="container centered-container">
        <?php if ($result->num_rows > 0): 
            $row = $result->fetch_assoc(); ?>
            <h2><i class="fas fa-envelope"></i> Message Details</h2>
            <p><strong>From:</strong> <?php echo $row['sender'] . ', ' . $row['empLname']; ?></p>
            <p><strong>Message:</strong> <?php echo $row['message']; ?></p>

            <?php if ($row['file_path']): ?>
                <p><strong><i class="fas fa-file"></i> File:</strong> <a href="<?php echo $row['file_path']; ?>" target="_blank">Open file</a></p>
            <?php endif; ?>

            <p><strong>Sent:</strong> <?php echo date('Y-m-d H:i:s', strtotime($row['timestamp'])); ?></p>

            <!-- Reply form -->
            <form action="replyMA.php" method="POST">
                <div class="mb-3">
                    <label for="replyMessage" class="form-label">Reply:</label>
                    <textarea class="form-control" id="replyMessage" name="replyMessage" rows="4" required></textarea>
                </div>
                <input type="hidden" name="messageId" value="<?php echo $messageId; ?>">
                <button type="submit" class="btn btn-primary">Send Reply</button>
            </form>

        <?php else: ?>
            <h2>Message Not Found</h2>
        <?php endif; ?>
    </div>
</div>


    <div class="container centered-container">
        <?php
// Fetch replies for the message
$replyStmt = $conn->prepare("SELECT r.reply_message, r.timestamp, u.empFname, u.empLname 
                             FROM replies r 
                             JOIN emplooyedetails u ON r.sender_id = u.empID 
                             WHERE r.message_id = ? 
                             ORDER BY r.timestamp ASC");
$replyStmt->bind_param("i", $messageId);
$replyStmt->execute();
$replyResult = $replyStmt->get_result();
?>

<!-- Display replies -->
<?php if ($replyResult->num_rows > 0): ?>
    <h3><i class="fas fa-reply"></i> Replies:</h3>
    <ul class="list-group mb-3">
        <?php while ($replyRow = $replyResult->fetch_assoc()): ?>
            <li class="list-group-item">
                <strong><?php echo $replyRow['empFname'] . ' ' . $replyRow['empLname']; ?></strong> 
                <small class="text-muted"><?php echo date('Y-m-d H:i:s', strtotime($replyRow['timestamp'])); ?></small>
                <p><?php echo $replyRow['reply_message']; ?></p>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No replies.</p>
<?php endif; ?>

<?php
// Close the reply statement
$replyStmt->close();
?>
    </div>
<?php 
        // Close the statement and connection
        $stmt->close();
        $conn->close();
        ?>
</body>
</html>