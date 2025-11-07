<?php
session_start();
$conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch the selected month and year, or use the current month/year as default
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$days_in_month = date('t', strtotime($selected_month . '-01'));


$query = "SELECT leave_date, leave_type FROM leaves 
          WHERE teacher_id = $teacher_id AND leave_date LIKE '$selected_month%'";
$result = $conn->query($query);

$leaves = [];
while ($row = $result->fetch_assoc()) {
    $leaves[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Calendar</title>
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

        .container {
            background: #fff;
            width: 100%;
            max-width: 600px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 2rem;
            color: #0056b3;
            margin-bottom: 20px;
        }

        label {
            font-size: 1rem;
            margin-bottom: 10px;
            text-align: left;
        }

        input, button {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            background-color: #0056b3;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #003d7a;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            padding: 10px;
        }

        .calendar .day {
            padding: 10px;
            background: #f4f4f9;
            border: 1px solid #ddd;
            text-align: center;
            border-radius: 5px;
        }

        .calendar .leave-casual {
            background-color: #ffcccb;
        }

        .calendar .leave-annual {
            background-color: #add8e6;
        }

        .calendar .leave-official {
            background-color: #90ee90;
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

        /* Legend styling */
        .legend {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .legend div {
            margin: 0 10px;
            display: flex;
            align-items: center;
        }

        .legend div span {
            display: block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            .calendar {
                grid-template-columns: repeat(7, 1fr);
            }

            input, button {
                font-size: 0.9rem;
                padding: 10px;
            }

            .container {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .calendar {
                grid-template-columns: repeat(7, 1fr);
                gap: 3px;
            }

            .calendar .day {
                font-size: 0.9rem;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Teacher Calendar</h1>

        <!-- Month and Year Selection Form -->
        <form method="GET" action="">
            <label for="month">Select Month:</label>
            <input type="month" id="month" name="month" value="<?php echo $selected_month; ?>" required>
            <button type="submit">View</button>
        </form>

        <div class="calendar">
            <?php
            // Generate calendar days for the selected month
            for ($day = 1; $day <= $days_in_month; $day++) {
                $date = $selected_month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);

                // Check if this day has a leave
                $leave_class = '';
                foreach ($leaves as $leave) {
                    if ($leave['leave_date'] == $date) {
                        if ($leave['leave_type'] == 'Casual') {
                            $leave_class = 'leave-casual';
                        } elseif ($leave['leave_type'] == 'Annual') {
                            $leave_class = 'leave-annual';
                        } elseif ($leave['leave_type'] == 'Official') {
                            $leave_class = 'leave-official';
                        }
                        break;
                    }
                }

                echo "<div class='day $leave_class'>$day</div>";
            }
            ?>
        </div>

        <!-- Leave Category Legend -->
        <div class="legend">
            <div><span style="background-color: #ffcccb;"></span> Casual Leave</div>
            <div><span style="background-color: #add8e6;"></span> Annual Leave</div>
            <div><span style="background-color: #90ee90;"></span> Official Leave</div>
        </div>

        <a href="home.php">Back to Home</a>
    </div>
</body>
</html>
