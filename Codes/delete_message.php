<?php
session_start();
include('NewDbConn.php');

// Check if the user is logged in
if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit();
}

// Check if the message ID is set
if (isset($_GET['id'])) {
    $messageId = $_GET['id'];

    // Delete the message
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $messageId);

    if ($stmt->execute()) {
        header("Location: messagesOS.php");
        exit();
    } else {
        echo "Error deleting message: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
