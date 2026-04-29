<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New user</title>
    <link rel="stylesheet" href="register.css">
    <link rel="icon" type="image/x-icon" href="Tirelo.JPG">
  
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
  
<script>
    // Function to validate if the passwords match
    function validatepasswords() {
        var pass1 = document.validate.password.value; // Get the value of the password field
        var cpass2 = document.validate.confirmpassword.value; // Get the value of the confirm password field

        // Check if the passwords match
        if (pass1 == cpass2) {
            return true; // Passwords match
        } else if (cpass2 == '') {
            alert('Confirm your password'); // Alert if confirm password field is empty
            return false;
        } else {
            alert('Passwords must be the same, try again!'); // Alert if passwords do not match
            return false;
        }
    }

     // Function to toggle visibility of other staff options based on selection
     function toggleOtherStaffOptions() {
        var otherStaffOptions = document.getElementById('otherStaffOptions');
        var otherStaffRadio = document.querySelector('input[name="position"][value="Other staff"]');
        var selectedPosition = document.querySelector('input[name="position"]:checked').value;
        var otherPositionField = document.getElementById('other_position');
        var roleField = document.getElementById('role');

        if (selectedPosition === 'Manager' || selectedPosition === 'HR') {
            otherPositionField.value = selectedPosition;
            roleField.value = selectedPosition;
            otherStaffOptions.classList.add('hidden');
        } else if (otherStaffRadio.checked) {
            otherStaffOptions.classList.remove('hidden');
            roleField.value = ''; // Clear the role field
        } else {
            otherStaffOptions.classList.add('hidden');
            roleField.value = ''; // Clear the role field
        }
    }

    // Event listener to trigger the toggle function when a radio button is selected
    document.querySelectorAll('input[name="position"]').forEach(function(radio) {
        radio.addEventListener('change', toggleOtherStaffOptions);
    });
</script>

<div class="container">
  <img src="Tirelo.JPG" class="rounded" alt="Tirelo" width="230" height="220"> 
  <h1>CAPITAL BANK</h1>
  <h2>Welcome To Tirelo Capital EPMS</h2>
</div> 
 
<div class="container1">
  <form name="validate" method="POST" action="regConn.php" onsubmit="return validatepasswords()">
 
<div class="">
<strong>First name:</strong><input type="text" name="fname" placeholder="Enter your first name" pattern="[A-Za-z]+" title="Must be letters only" required><br>
<strong>Last name:</strong><input type="text" name="lname" placeholder="Enter your last name" pattern="[A-Za-z]+" title="Must be letters only" required><br>

<strong>Id Number:</strong><input type="text" id="Id_No" name="ID_number" placeholder="Enter your ID number" pattern="[0-9]{13}" title="Must contains numbers only(13 numbers)" required><br>
<strong>Select position:</strong> 
<br><input type="radio" name="position" value="HR" onclick="toggleOtherStaffOptions()">HR
  <input type="radio" name="position" value="Manager" onclick="toggleOtherStaffOptions()">Manager
  <input type="radio" name="position" value="Other staff" onclick="toggleOtherStaffOptions()" required>Other staff<br>

<div id="otherStaffOptions" class="hidden">
  <strong>Other staff options:</strong>
  <select name="other_position">
    <option value="IT Technician">IT Technician</option>
    <option value="Network Engineer">Network Engineer</option>
    <option value="Software Developer/Engineer">Software Developer/Engineer</option>
    <option value="Database Administrator">Database Administrator</option>
    <option value="Security Specialist">Security Specialist</option>
    <option value="Electrician">Electrician</option>
    <option value="Plumber">Plumber</option>
    <option value="HVAC Technician">HVAC Technician</option>
    <option value="Facilities Manager">Facilities Manager</option>
    <option value="Cleaning and Maintenance Staff">Cleaning and Maintenance Staff</option>
    <option value="ATM Technician">ATM Technician</option>
    <option value="Fraud Analyst">Fraud Analyst</option>
    <option value="Account Manager">Account Manager</option>
    <option value="Loan Officer">Loan Officer</option>
    <option value="Office Supply Manager">Office Supply Manager</option>
    <option value="Office Supply Manager">Security</option>
  </select><br>
</div>

  <div style="margin-top: 10px;"></div>
  <strong>Gender: 
    <br>
  </strong><input type="radio" name="gender" value="female">Female
  <input type="radio" name="gender" value="male" required>Male<br>

  <div style="margin-top: 10px;">
  <strong>Marital status:</strong><select id="MS" name="Marital_status" required>
    <option value="Single">Single</option>
    <option value="Divorced">Divorced</option>
    <option value="Married">Married</option>
    </select><br>
 <!-- Race:<input type="text" id="Race" name="Race" placeholder="Enter your race" pattern="[A-Za-z]+" title="Must be letters only" required><br>-->
  <strong>Race:</strong><select name="Race" required>
        <option value="">Select your race</option>
        <option value="African">African/Black</option>
        <option value="Coloured">Coloured</option>
        <option value="Indian/Asian">Indian/Asian</option>
        <option value="White">White</option>
        <option value="Other">Other</option>
      </select>
  <strong>Email Address:</strong><input type="email" id="email_Adress" name="email_Address" required placeholder="Enter your email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"><br>
  <strong>Phone Number:</strong><input type="tel" id="phone" name="phone" placeholder="e.g 0722328962" pattern="[0-9]{10}" title="Must be 10 numbers only" required><br>
  
    <strong>New password:</strong><input type="password" id="pwd" placeholder="Enter your new password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required><br>
    <strong>Confirm password:</strong><input type="password"  id="pwd_conf"  placeholder="Confirm your password" name="confirmpassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"><br>
     <input type="checkbox" name="Terms&Conditions" required>I agree to the terms and conditions<div class=""><a href="T&C.html">Terms and conditions</a></div><br>
    </div><br>
    <button type="submit" >Register</button>
  </form><br>
  <form><button type="submit" formaction="logIn.php">Log in</button></form>
    <button type="button" onclick="location.href='company_page.html'">Home</button>
</div> 

</body>
</html>
