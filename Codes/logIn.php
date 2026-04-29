
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPMS</title>
    <link rel="stylesheet" href="LogIn.css">
    <link rel="icon" type="image/x-icon" href="EPMS1.JPG">

    <style>
      body{
        background:linear-gradient(19deg,silver,lightblue);
      }
    </style>
  
</head>
<body >

<div class="container">

  <!--<img src="Tirelo.JPG" class="rounded" alt="Tirelo" width="220" height="220"> -->
  <h1>Employee Performance Management System </h1>
  <h2> Action is the foundational key to all success. </h2>

</div> 

<div class="container1">
  <form action="logConn.php" method="post" class="form-section">
    <h3> Registered users: Login Credentials </h3>
    
    <div class="radio-group">
      <label><input type="radio" name="empPosition" value="HR"><strong>HR</strong></label>
      <label><input type="radio" name="empPosition" value="Manager"><strong>Manager</strong></label>
      <label><input type="radio" name="empPosition" value="Other staff" required><strong>Other staff</strong></label>
    </div>

    <div class="form-group">
      <label for="empID"><strong>Employee ID:</strong></label>
      <input type="text" id="empID" placeholder="Enter your employee ID" name="empID" required>
    </div>

    <div class="form-group">
      <label for="pwd"><strong>Password:</strong></label>
      <input type="password" id="pwd" placeholder="Enter your password" name="empPass" required>
    </div>

    <button type="submit">Clock-in</button>
  </form>

  <div class="new-user-section">
    <h3>New user:</h3>
    <form action="register.php">
      <button type="submit">Register</button>
    </form>
    <button type="button" onclick="location.href='company_page.html'">Home</button>
  </div>
</div> 

</body>
</html>