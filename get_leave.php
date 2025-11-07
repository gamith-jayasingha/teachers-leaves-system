<?php
session_start();
$conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_SESSION['teacher_id'];
    $leave_date = $_POST['leave_date'];
    $leave_type = $_POST['leave_type'];
    $reason = $_POST['reason'];

   
    $remaining_query = "SELECT 40 - COUNT(*) AS remaining 
                        FROM leaves WHERE teacher_id = $teacher_id AND YEAR(leave_date) = YEAR(CURDATE())";
    $remaining_result = $conn->query($remaining_query);
    $remaining = $remaining_result->fetch_assoc()['remaining'];

    if ($remaining > 0) {
        // Insert leave data if there are leaves left
        $query = "INSERT INTO leaves (teacher_id, leave_date, leave_type, reason) VALUES ('$teacher_id', '$leave_date', '$leave_type', '$reason')";
        if ($conn->query($query)) {
            $message = "<p style='color:green;'>Leave applied successfully!</p>";
        } else {
            $message = "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>No remaining leaves for this year!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Leave</title>
    <style>
        /* Reset default styles */
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
            animation: fadeIn 1s ease-in-out;
        }

        .container {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: slideUp 0.6s ease-out;
        }

        h1 {
            font-size: 2rem;
            color: #0056b3;
            margin-bottom: 20px;
        }

        label {
            font-size: 1rem;
            color: #333;
            margin-bottom: 10px;
            text-align: left;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #0056b3;
        }

        textarea {
            height: 80px;
            resize: vertical;
        }

        button {
            background-color: #0056b3;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #003d7a;
        }

        a {
            color: #0056b3;
            text-decoration: none;
            display: block;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            margin: 20px 0;
            font-size: 1.1rem;
            animation: fadeInMessage 1s ease-in-out;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            .container {
                padding: 15px;
            }

            input, select, textarea, button {
                font-size: 0.95rem;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }

            h1 {
                font-size: 1.25rem;
            }

            input, select, textarea, button {
                font-size: 0.9rem;
                padding: 8px;
            }
        }

        /* Keyframe animations */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes slideUp {
            0% { transform: translateY(50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeInMessage {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Apply for Leave</h1>
        <?php if (isset($message)) { echo "<div class='message'>$message</div>"; } ?>
        <form action="get_leave.php" method="POST">
            <label for="leave_date">Date</label>
            <input type="date" id="leave_date" name="leave_date" required>

            <label for="leave_type">Leave Type</label>
            <select id="leave_type" name="leave_type" required>
                <option value="Casual">Casual</option>
                <option value="Annual">Annual</option>
                <option value="Official">Official</option>
            </select>

            <label for="reason">Reason</label>
            <textarea id="reason" name="reason" placeholder="Enter your reason" required></textarea>

            <button type="submit">Apply Leave</button>
        </form>
        <a href="home.php">Back to Home</a>
    </div>
</body>
</html>
