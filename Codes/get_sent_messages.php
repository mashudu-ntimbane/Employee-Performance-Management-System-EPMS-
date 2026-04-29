<?php
session_start();
include('NewDbConn.php');

header('Content-Type: application/json');

if (!isset($_SESSION['empID'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$empID = $_SESSION['empID'];

// Fetch all messages sent by the logged-in user
$stmt = $conn->prepare("SELECT m.id, m.message, m.timestamp, m.heading, m.replied, u.empLname, u.empFname AS receiver_name, f.file_path 
                        FROM messages m 
                        LEFT JOIN emplooyedetails u ON m.receiver_id = u.empID  
                        LEFT JOIN files f ON m.id = f.message_id 
                        WHERE m.sender_id = ? 
                        ORDER BY m.timestamp DESC");
$stmt->bind_param("i", $empID);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['messages' => $messages]);
