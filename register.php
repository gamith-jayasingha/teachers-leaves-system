<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize inputs
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $pin = $conn->real_escape_string($_POST['pin']);
    $school = $conn->real_escape_string($_POST['school']);
    $district = $conn->real_escape_string($_POST['district']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $finish_leaves = intval($_POST['finish_leaves']);
    $registration_year = date("Y");

     // File upload handling (optional)
    $uploadOk = 1;
    if ($_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image file
        $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
        if($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["profile_photo"]["size"] > 5000000) {
            echo "File is too large (max 5MB).";
            $uploadOk = 0;
        }

        if(!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk) {
            if (!move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                echo "Error uploading file.";
                $uploadOk = 0;
            }
        }
    } elseif ($_FILES['profile_photo']['error'] != UPLOAD_ERR_NO_FILE) {
        echo "File upload error: " . $_FILES['profile_photo']['error'];
        $uploadOk = 0;
    }

    if ($uploadOk) {
        $stmt = $conn->prepare("INSERT INTO teachers (profile_photo, full_name, pin, school, district, phone, finish_leaves, total_leaves, registration_year) VALUES (?, ?, ?, ?, ?, ?, ?, 40, ?)");
        $stmt->bind_param("ssssssis", $target_file, $full_name, $pin, $school, $district, $phone, $finish_leaves, $registration_year);
        
        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Teacher Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a90e2;
            --primary-dark: #357abd;
            --background: #f5f7fb;
            --text: #2d3436;
            --border: #e0e6f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            background: var(--background);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            transform: translateY(0);
            opacity: 1;
            animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes slideUp {
            from { transform: translateY(40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        h1 {
            color: var(--text);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            transition: color 0.3s ease;
        }

        input, .file-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border);
            border-radius: 0.8rem;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        input:focus, .file-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.2);
            transform: scale(1.02);
        }

        input:focus + i {
            color: var(--primary-dark);
        }

        .file-input {
            padding: 0;
            height: 0;
            opacity: 0;
            position: absolute;
        }

        .file-label {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 80px;
            height: 80px;
            border: 2px dashed var(--primary);
            border-radius: 50%;
            margin: 0 auto 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(74, 144, 226, 0.1);
        }

        .file-label:hover {
            transform: scale(1.05);
            background: rgba(74, 144, 226, 0.15);
        }

        button {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.8rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 1rem;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(74, 144, 226, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text);
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                border-radius: 1rem;
            }

            input {
                font-size: 0.9rem;
                padding: 0.8rem 0.8rem 0.8rem 2.5rem;
            }

            .form-group i {
                left: 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <div class="container">
        <h1>📝 Teacher Registration</h1>
        <form action="register.php" method="POST" enctype="multipart/form-data" onsubmit="showLoading()">
            <div class="form-group">
                <input type="file" class="file-input" id="profile_photo" name="profile_photo" accept="image/">
                <label for="profile_photo" class="file-label">
                    <i class="fas fa-camera fa-2x"></i>
                </label>
            </div>

            
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" placeholder="Full Name" id="full_name" name="full_name" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" placeholder="PIN (4 Digits)" id="pin" name="pin" required minlength="4">
            </div>

            <div class="form-group">
                <i class="fas fa-school"></i>
                <input type="text" placeholder="School" id="school" name="school" required>
            </div>

            <div class="form-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" placeholder="District" id="district" name="district" required>
            </div>

            <div class="form-group">
                <i class="fas fa-phone"></i>
                <input type="tel" placeholder="Phone Number (10 digits)" id="phone" name="phone" pattern="[0-9]{10}" required>
            </div>

            <div class="form-group">
                <i class="fas fa-leaf"></i>
                <input type="number" placeholder="Leaves Already Taken" id="finish_leaves" name="finish_leaves" min="0" value="">
            </div>
            <!-- Keep other form groups same as original -->

            <button type="submit">Register Now <i class="fas fa-arrow-right"></i></button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loading').style.display = 'flex';
        }

        // Input elevation effect
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'translateY(-2px)';
            });
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = 'translateY(0)';
            });
        });

        // File upload preview
        const fileInput = document.getElementById('profile_photo');
        const fileLabel = document.querySelector('.file-label');
        
        fileInput.addEventListener('change', function(e) {
            if (this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    fileLabel.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>