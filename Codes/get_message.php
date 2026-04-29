<?php
session_start();
include('NewDbConn.php');

header('Content-Type: application/json');

if (!isset($_SESSION['empID'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No message ID provided']);
    exit();
}

$messageId = intval($_GET['id']);
$empID = $_SESSION['empID'];

// Mark message as read
$updateStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ? AND receiver_id = ?");
$updateStmt->bind_param("ii", $messageId, $empID);
$updateStmt->execute();
$updateStmt->close();

// Fetch message details
$stmt = $conn->prepare("SELECT m.id, m.message, m.timestamp, m.heading, u.empLname, u.empFname AS sender, f.file_path 
                        FROM messages m 
                        LEFT JOIN emplooyedetails u ON m.sender_id = u.empID  
                        LEFT JOIN files f ON m.id = f.message_id 
                        WHERE m.id = ? AND m.receiver_id = ?");
$stmt->bind_param("ii", $messageId, $empID);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();
$stmt->close();

if (!$message) {
    http_response_code(404);
    echo json_encode(['error' => 'Message not found']);
    exit();
}

// Fetch replies
$replyStmt = $conn->prepare("SELECT r.reply_message, r.timestamp, u.empFname, u.empLname 
                             FROM replies r 
                             JOIN emplooyedetails u ON r.sender_id = u.empID 
                             WHERE r.message_id = ? 
                             ORDER BY r.timestamp ASC");
$replyStmt->bind_param("i", $messageId);
$replyStmt->execute();
$replyResult = $replyStmt->get_result();
$replies = [];
while ($row = $replyResult->fetch_assoc()) {
    $replies[] = $row;
}
$replyStmt->close();
$conn->close();

echo json_encode([
    'message' => $message,
    'replies' => $replies
]);

