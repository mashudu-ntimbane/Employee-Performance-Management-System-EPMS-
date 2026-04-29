
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
    <link rel="stylesheet" href="staff_profile.css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  

</head>
<body>
<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

  <a href="userProfile.php">User profile</a>
  <a href="annoucements.php">Annoucements</a>
  <a href="submitTask.php">Submit task</a>
  <a href="viewFeedback.php">Feeback</a>
  <a href="logIn.php">Log out</a>

</div>
<span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;EMPS</span>
<div>
<br>
<?php
$mydate=getdate(date("U"));
echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
?>
    <h1 style="font-size:43px"><i class="material-icons" style="font-size:30px">announcement</i>Annoucements</h1>

<textarea rows="4" cols="50" name="comment" form="usrform" placeholder="take note that your due date is approching make sure you submit in time!!"></textarea>
</div>

</body>
</html>
