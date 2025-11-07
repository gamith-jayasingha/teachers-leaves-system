<?php
session_start();
$conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];


$year = date('Y');
$month = '';
$date = '';
$daily_reasons = [];

// Handle form submissions for filters
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['year'])) {
        $year = $_POST['year'];
    }
    if (isset($_POST['month'])) {
        $month = $_POST['month'];
    }
    if (isset($_POST['date'])) {
        $date = $_POST['date'];
    }
}

// Query for annual and monthly summaries
$query = "SELECT DATE_FORMAT(leave_date, '%Y-%m') AS month, leave_type, COUNT(*) AS leave_count 
          FROM leaves 
          WHERE teacher_id = $teacher_id AND YEAR(leave_date) = '$year'";

if (!empty($month)) {
    $query .= " AND DATE_FORMAT(leave_date, '%Y-%m') = '$month'";
}

$query .= " GROUP BY month, leave_type";
$result = $conn->query($query);

$monthly_leaves = [];
while ($row = $result->fetch_assoc()) {
    $monthly_leaves[$row['month']][$row['leave_type']] = $row['leave_count'];
}

// Query for daily leave reasons
if (!empty($date)) {
    $daily_query = "SELECT leave_date, leave_type, reason 
                    FROM leaves 
                    WHERE teacher_id = $teacher_id AND leave_date = '$date'";
    $daily_result = $conn->query($daily_query);
    while ($row = $daily_result->fetch_assoc()) {
        $daily_reasons[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Summary</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            transition: background-color 0.3s ease;
        }

        h1, h2 {
            font-size: 2rem;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
            animation: fadeIn 1s ease-out;
        }

        .container {
            background: #fff;
            width: 100%;
            max-width: 800px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.5s ease-out;
        }

        .form-container form {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
        }

        label {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #555;
            width: 100%;
        }

        input[type="number"], input[type="month"], input[type="date"] {
            padding: 12px;
            width: calc(33.33% - 10px);
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
            animation: fadeIn 2s ease-out;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            font-size: 1rem;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        a {
            display: block;
            text-align: center;
            color: #3498db;
            margin-top: 20px;
            font-size: 1.1rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #2980b9;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            input[type="number"], input[type="month"], input[type="date"] {
                width: 100%;
            }

            .form-container form {
                flex-direction: column;
                align-items: flex-start;
            }

            button {
                font-size: 1rem;
            }

            table, th, td {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Leave Summary</h1>

        <!-- Filter Form -->
        <div class="form-container">
            <form method="POST">
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" value="<?php echo $year; ?>" min="2000" max="<?php echo date('Y'); ?>">

                <label for="month">Month:</label>
                <input type="month" id="month" name="month" value="<?php echo $month; ?>">

                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?php echo $date; ?>">

                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Annual/Monthly Summary -->
        <?php if (!empty($monthly_leaves)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Casual Leave</th>
                        <th>Annual Leave</th>
                        <th>Official Leave</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly_leaves as $month => $leave_types): ?>
                        <tr>
                            <td><?php echo $month; ?></td>
                            <td><?php echo $leave_types['Casual'] ?? 0; ?></td>
                            <td><?php echo $leave_types['Annual'] ?? 0; ?></td>
                            <td><?php echo $leave_types['Official'] ?? 0; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No leave data found for the selected year or month.</p>
        <?php endif; ?>

        <!-- Daily Leave Details -->
        <?php if (!empty($daily_reasons)): ?>
            <h2>Daily Leave Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Leave Type</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily_reasons as $reason): ?>
                        <tr>
                            <td><?php echo $reason['leave_date']; ?></td>
                            <td><?php echo $reason['leave_type']; ?></td>
                            <td><?php echo $reason['reason']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (!empty($date)): ?>
            <p>No leave records found for the selected date.</p>
        <?php endif; ?>

        <a href="home.php">Back to Home</a>
    </div>
</body>
</html>
