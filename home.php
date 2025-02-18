<?php
session_start();
$conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$teacher_id = intval($_SESSION['teacher_id']);

try {
    // Fetch teacher details from the 'teachers' table
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
        $total_leaves = $teacher['total_leaves'];
        $current_taken_leaves = $teacher['finish_leaves']; // Leaves already taken this year
        $profile_photo = $teacher['profile_photo']; // Profile photo URL or file path
    } else {
        throw new Exception("Teacher not found.");
    }

    // Fetch the number of leaves already taken for the current year from the 'leaves' table
    $stmt = $conn->prepare("SELECT COUNT(*) AS finish_leaves FROM leaves 
                            WHERE teacher_id = ? AND YEAR(leave_date) = YEAR(CURDATE())");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $leave_result = $stmt->get_result();
    $finish_leaves_this_year = $leave_result->fetch_assoc()['finish_leaves'] ?? 0;

    // Calculate remaining leaves after considering the taken leaves
    $remaining_leaves = max($total_leaves - ($current_taken_leaves + $finish_leaves_this_year), 0);
    $finish_leaves = $current_taken_leaves + $finish_leaves_this_year;

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       :root {
    --primary: #6366f1;
    --secondary: #4f46e5;
    --success: #22c55e;
    --error: #ef4444;
    --background: #f8fafc;
    --card-bg: #ffffff;
    --text: #1e293b;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

body {
    background: var(--background);
    color: var(--text);
    line-height: 1.6;
    min-height: 100vh;
    padding: 1rem;
}

.dashboard {
    max-width: 1200px;
    margin: 0 auto;
    animation: fadeIn 0.5s ease-out;
}

.header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 1.5rem;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
}

.header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
    transform: rotate(45deg);
}

.avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    border: 4px solid rgba(255,255,255,0.3);
    transition: transform 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.avatar:hover {
    transform: scale(1.05);
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar span {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    background: linear-gradient(45deg, var(--primary), var(--secondary));
}

.profile-info {
    text-align: center;
}

.profile-info h1 {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    letter-spacing: -0.5px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    text-align: center;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-text {
    flex: 1;
}

.stat-label {
    display: block;
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: rgba(99, 102, 241, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary);
    transition: background 0.3s ease;
}

.progress-container {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    animation: slideIn 0.5s ease-out;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.progress-percent {
    color: var(--primary);
    font-weight: 600;
}

.progress-bar-container {
    height: 12px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 6px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.nav-card {
    background: var(--card-bg);
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    text-decoration: none;
    color: var(--text);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
}

.nav-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    background: var(--primary);
    color: white;
}

.nav-card i {
    font-size: 1.8rem;
}

.warning-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(239,68,68,0.1);
    color: var(--error);
    border-radius: 2rem;
    animation: pulse 2s infinite;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .header {
        padding: 1.2rem;
        border-radius: 1rem;
    }

    .avatar {
        width: 80px;
        height: 80px;
    }

    .profile-info h1 {
        font-size: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .nav-grid {
        grid-template-columns: 1fr;
    }

    .nav-card {
        padding: 1.5rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .stat-value {
        font-size: 1.2rem;
    }

    .progress-percent {
        font-size: 1rem;
    }
}

        }
    </style>
</head>
<body>
    <!-- Keep the HTML body exactly the same as provided -->
    <div class="dashboard">
        <div class="header">
            <div class="avatar">
                <?php if ($profile_photo && file_exists($profile_photo)): ?>
                    <img src="<?= htmlspecialchars($profile_photo) ?>" alt="Profile Photo">
                <?php else: ?>
                    <span><?= strtoupper(substr($teacher['full_name'], 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1><?= htmlspecialchars($teacher['full_name']) ?></h1>
                <p>Employee ID: #<?= $teacher_id ?></p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-text">
                        <span class="stat-label">Total Leaves</span>
                        <span class="stat-value"><?= $total_leaves ?></span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-text">
                        <span class="stat-label">Leaves Taken</span>
                        <span class="stat-value"><?= $finish_leaves ?></span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-text">
                        <span class="stat-label">Available Leaves</span>
                        <span class="stat-value"><?= $remaining_leaves ?></span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-header">
                <span class="progress-label">Leave Utilization</span>
                <span class="progress-percent"><?= round(($finish_leaves / $total_leaves) * 100) ?>%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?= ($finish_leaves / $total_leaves) * 100 ?>%;"></div>
            </div>
        </div>

        <?php if($remaining_leaves < 5): ?>
            <div class="stat-card" style="margin-bottom: 1.5rem;">
                <div class="stat-content">
                    <div class="stat-text">
                        <span class="warning-badge">
                            <i class="fas fa-exclamation-circle"></i>
                            Low Leave Balance
                        </span>
                        <span class="stat-value" style="color: var(--error);">
                            <?= $remaining_leaves ?> Days Left
                        </span>
                    </div>
                    <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: var(--error);">
                        <i class="fas fa-hourglass-end"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="nav-grid">
            <a href="calendar.php" class="nav-card">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>
            <a href="get_leave.php" class="nav-card">
                <i class="fas fa-file-signature"></i>
                <span>Apply Leave</span>
            </a>
            <a href="monthly_leave.php" class="nav-card">
                <i class="fas fa-chart-pie"></i>
                <span>Analytics</span>
            </a>
            <a href="profile.php" class="nav-card">
                <i class="fas fa-user-cog"></i>
                <span>Profile</span>
            </a>
            <a href="logout.php" class="nav-card logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</body>
</html> 