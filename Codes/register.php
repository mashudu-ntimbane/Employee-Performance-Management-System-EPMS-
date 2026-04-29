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

        .face-capture-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        #video-container {
            margin: 10px 0;
            position: relative;
        }
        
        #video {
            border: 2px solid #ccc;
            border-radius: 5px;
            background-color: #000;
        }
        
        #capture-btn {
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        #capture-btn:hover {
            background-color: #45a049;
        }
        
        #capture-status {
            margin-top: 10px;
            font-weight: bold;
            color: #4CAF50;
        }

        .capture-guide {
            margin: 10px 0;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            color: #856404;
        }

        #retry-capture {
            background-color: #ffc107;
            color: #000;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: none;
        }

        #retry-capture:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
  
<script>
    // Function to validate if the passwords match
    function validatepasswords() {
        var pass1 = document.validate.password.value;
        var cpass2 = document.validate.confirmpassword.value;

        if (pass1 == cpass2) {
            return true;
        } else if (cpass2 == '') {
            alert('Confirm your password');
            return false;
        } else {
            alert('Passwords must be the same, try again!');
            return false;
        }
    }

    // Function to toggle visibility of other staff options
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
            roleField.value = '';
        } else {
            otherStaffOptions.classList.add('hidden');
            roleField.value = '';
        }
    }
</script>

<div class="container">
  <img src="Tirelo.JPG" class="rounded" alt="Tirelo" width="230" height="220"> 
  <h1>CAPITAL BANK</h1>
  <h2>Welcome To Tirelo Capital EPMS</h2>
</div> 
 
<div class="container1">
  <form name="validate" method="POST" action="regConn.php" onsubmit="return validateForm()">
 
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
            <select id="other_position" name="other_position" onchange="document.getElementById('role').value = this.value;">
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
                <option value="Security">Security</option>
            </select><br>
        </div>

        <div style="margin-top: 10px;"></div>
        <strong>Gender:</strong><br>
        <input type="radio" name="gender" value="female">Female
        <input type="radio" name="gender" value="male" required>Male<br>

        <div style="margin-top: 10px;">
            <strong>Marital status:</strong>
            <select id="MS" name="Marital_status" required>
                <option value="Single">Single</option>
                <option value="Divorced">Divorced</option>
                <option value="Married">Married</option>
            </select><br>

            <strong>Race:</strong>
            <select name="Race" required>
                <option value="">Select your race</option>
                <option value="African">African/Black</option>
                <option value="Coloured">Coloured</option>
                <option value="Indian/Asian">Indian/Asian</option>
                <option value="White">White</option>
                <option value="Other">Other</option>
            </select><br>

            <strong>Email Address:</strong><input type="email" id="email_Adress" name="email_Address" required placeholder="Enter your email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"><br>
            <strong>Phone Number:</strong><input type="tel" id="phone" name="phone" placeholder="e.g 0722328962" pattern="[0-9]{10}" title="Must be 10 numbers only" required><br>

            <!-- Face Capture Section -->
            <div class="face-capture-section">
                <strong>Face Registration:</strong>
                <div class="capture-guide">
                    <p>📸 Please ensure:</p>
                    <ul>
                        <li>Your face is well-lit</li>
                        <li>You're looking directly at the camera</li>
                        <li>Your face is centered in the frame</li>
                        <li>No face masks or sunglasses</li>
                    </ul>
                </div>
                <div id="video-container">
                    <video id="video" width="400" height="300" autoplay></video>
                    <canvas id="canvas" width="400" height="300" style="display: none;"></canvas>
                </div>
                <button type="button" id="capture-btn">Capture Face</button>
                <button type="button" id="retry-capture">Retry Capture</button>
                <div id="capture-status" style="display: none;">✓ Face captured successfully</div>
                <input type="hidden" name="face_image" id="face_image" required>
            </div>

            <strong>New password:</strong><input type="password" id="pwd" placeholder="Enter your new password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required><br>
            <strong>Confirm password:</strong><input type="password" id="pwd_conf" placeholder="Confirm your password" name="confirmpassword" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"><br>
            <input type="checkbox" name="Terms&Conditions" required>I agree to the terms and conditions<div class=""><a href="T&C.html">Terms and conditions</a></div><br>
        </div><br>
        <button type="submit">Register</button>
    </div>
  </form><br>
  <form><button type="submit" formaction="logIn.php">Log in</button></form>
  <button type="button" onclick="location.href='company_page.html'">Home</button>
</div> 

<script>
    // Face capture functionality
    let video = document.getElementById('video');
    let canvas = document.getElementById('canvas');
    let captureBtn = document.getElementById('capture-btn');
    let retryBtn = document.getElementById('retry-capture');
    let captureStatus = document.getElementById('capture-status');
    let faceImageInput = document.getElementById('face_image');
    let stream = null;

    // Access the webcam
    async function startVideo() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: {
                    width: { ideal: 400 },
                    height: { ideal: 300 },
                    facingMode: "user"
                }
            });
            video.srcObject = stream;
        } catch (err) {
            console.error("Error accessing webcam:", err);
            alert("Error accessing webcam. Please ensure your camera is connected and you have granted permission.");
        }
    }

    // Start video when page loads
    startVideo();

    // Capture photo
    captureBtn.addEventListener('click', function() {
        let context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert canvas to base64 image
        let imageData = canvas.toDataURL('image/jpeg');
        faceImageInput.value = imageData;
        
        // Show success status and retry button
        captureStatus.style.display = 'block';
        retryBtn.style.display = 'inline-block';
        captureBtn.style.display = 'none';
    });

    // Retry capture
    retryBtn.addEventListener('click', function() {
        faceImageInput.value = '';
        captureStatus.style.display = 'none';
        retryBtn.style.display = 'none';
        captureBtn.style.display = 'inline-block';
        startVideo(); // Restart video if it was stopped
    });

    // Form validation including face capture
    function validateForm() {
        if (!faceImageInput.value) {
            alert('Please capture your face before submitting');
            return false;
        }
        return validatepasswords();
    }

    // Event listeners for position radio buttons
    document.querySelectorAll('input[name="position"]').forEach(function(radio) {
        radio.addEventListener('change', toggleOtherStaffOptions);
    });
</script>

</body>
</html>