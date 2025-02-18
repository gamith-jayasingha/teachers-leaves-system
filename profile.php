<?php
session_start();
$conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher profile
$query = "SELECT * FROM teachers WHERE id = $teacher_id";
$result = $conn->query($query);
$teacher = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $school = $_POST['school'];
    $district = $_POST['district'];
    $phone = $_POST['phone'];

    // Update teacher profile
    $update_query = "UPDATE teachers SET full_name='$full_name', school='$school', district='$district', phone='$phone' WHERE id = $teacher_id";
    if ($conn->query($update_query)) {
        echo "<p style='color:green;'>Profile updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        /* Reset some default browser styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8fbfc, #eaf8ff);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #0056b3;
        }

        .container {
            background: #fff;
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        label {
            font-size: 1rem;
            color: #333;
            display: block;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        button {
            background-color: #0056b3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #003d7a;
        }

        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Profile</h1>
        <form action="profile.php" method="POST">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $teacher['full_name']; ?>" required>

            <label for="school">School</label>
            <input type="text" id="school" name="school" value="<?php echo $teacher['school']; ?>" required>

            <label for="district">District</label>
            <input type="text" id="district" name="district" value="<?php echo $teacher['district']; ?>" required>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo $teacher['phone']; ?>" required>

            <button type="submit">Update Profile</button>
        </form>
        <a href="home.php">Back to Home</a>
    </div>
</body>
</html>
