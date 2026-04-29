<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href=".css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
  
</head>
<body >

<div class="row">
  <div class="col-sm-2 pt-2 bg-light" style="text-align:center">EPMS <i class='fas fa-chart-line' style='font-size:15px'></i></div>
  <div class="col-sm-8 pt-2 bg-light" style="text-align:center">Messages</div>
  <div class="col-sm-2 pt-2 bg-light" style="text-align:center"><?php
$mydate=getdate(date("U"));
echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
?></div>
<nav class="navbar navbar-expand-sm bg-light border justify-content-center ">
    <ul class="navbar-nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link " href="">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="profile.php">User profile</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="messages.php">Messages</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Employees</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="request_leave.php">Leave</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Help</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Company page</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="logOut.php">Log out</a>
    </li>

  </ul>
</nav>
<div class="container-fluid p-5 bg-light border" >
<button class="btn btn-secondary" onclick="document.location='send_message.php'">New message</button><br>

<?php
session_start();
include('NewDbConn.php');

if (!isset($_SESSION['empID'])) {
    header("Location: logIn.php");
    exit();
}

$empID = $_SESSION['empID'];

$stmt = $conn->prepare("SELECT m.id, m.message, m.timestamp, u.empFname AS sender, f.file_path FROM messages m JOIN emplooyedetails u ON m.sender_id = u.empID  JOIN files f ON m.id = f.message_id WHERE m.receiver_id = ?  ORDER BY m.timestamp DESC");

$stmt->bind_param("i", $empID);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    echo "Messages received:";
while ($row = $result->fetch_assoc()) {
    echo "<div class='border' style='text-align:center'>";
    echo "<p><strong>From:</strong> " . $row['sender'] . "</p>";
    echo "<p><strong>Message:</strong> " . $row['message'] . "</p>";
    echo "<p><strong>Time:</strong> " . $row['timestamp'] . "</p>";
    if ($row['file_path']) {
        echo "<p><strong>File:</strong> <a href='" . $row['file_path'] . "'>Open file</a></p>";
    }
    echo "</div>";
}

} else {
      echo "<br>";
      echo "No messages";

}

?>
</div>
</body>
</html>