<?php
session_start();
include('NewDbConn.php');

// Check if the user is logged in
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit();
}

if (isset($_POST['replyMessage'], $_POST['messageId'])) {
    $replyMessage = $_POST['replyMessage'];
    $messageId = $_POST['messageId'];
    $senderId = $_SESSION['empID']; // The ID of the logged-in user sending the reply

    // Fetch the original message's details including heading and sender
    $originalMessageStmt = $conn->prepare("SELECT sender_id, heading FROM messages WHERE id = ?");
    $originalMessageStmt->bind_param("i", $messageId);
    $originalMessageStmt->execute();
    $originalMessageStmt->bind_result($originalSenderId, $originalHeading);
    $originalMessageStmt->fetch();
    $originalMessageStmt->close();

    // Insert the reply into the replies table
    $stmt = $conn->prepare("INSERT INTO replies (message_id, sender_id, reply_message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $messageId, $senderId, $replyMessage);

    if ($stmt->execute()) {
        // Mark the original message as replied
        $updateMessageStmt = $conn->prepare("UPDATE messages SET replied = 1 WHERE id = ?");
        $updateMessageStmt->bind_param("i", $messageId);
        $updateMessageStmt->execute();
        $updateMessageStmt->close();

        // Insert the reply as a new message for the original sender, keeping the original heading
        $replyHeading = "RE: " . $originalHeading;  // Prefix the heading with "RE:"
        $insertReplyStmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_read, heading) 
                                           VALUES (?, ?, ?, 0, ?)");
        $insertReplyStmt->bind_param("iiss", $senderId, $originalSenderId, $replyMessage, $replyHeading);
        $insertReplyStmt->execute();
        $insertReplyStmt->close();

        // Redirect back to the original message page
        header("Location: messagesOS.php?id=" . $messageId);
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
