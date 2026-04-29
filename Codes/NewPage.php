<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://kit.fontawesome.com/2c88d5aa9c.js' crossorigin='anonymous'></script>
    <link rel="stylesheet" href=".css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
  
</head>
<body >

<div class="row">
  <div class="col-sm-2 pt-2 border" style="text-align:center">EPMS</div>
  <div class="col-sm-8 pt-2 border" style="text-align:center"> Welcome!! to our company have a good day ahead</div>
  <div class="col-sm-2 pt-2 border" style="text-align:center"><?php
$mydate=getdate(date("U"));
echo "$mydate[weekday], $mydate[month] $mydate[mday], $mydate[year]";
?></div>
<nav class="navbar navbar-expand-sm bg-light border justify-content-center ">
    <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" href="">Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">User profile</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Messages</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Tasks</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Feedback</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Help</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Company page</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="">Log out</a>
    </li>

  </ul>
</nav>

</body>
</html>