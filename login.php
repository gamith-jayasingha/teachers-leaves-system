<?php
session_start();
$conn = new mysqli("localhost", "u302884828_teacher", "Gamith123$$", "u302884828_teacher_manage");



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $pin = $_POST['pin'];

    $query = "SELECT * FROM teachers WHERE phone = '$phone' AND pin = '$pin'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $teacher = $result->fetch_assoc();
        $_SESSION['teacher_id'] = $teacher['id'];
        header("Location: home.php");
        exit();
    } else {
        $error_message = "Invalid phone number or PIN!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --error: #dc2626;
            --background: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #93a5cf 0%, #e4efe9 100%);
            padding: 1rem;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transform: translateY(0);
            opacity: 1;
            animation: slideIn 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            backdrop-filter: blur(10px);
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h1 {
            text-align: center;
            color: #1e293b;
            margin-bottom: 2rem;
            font-size: 2rem;
            font-weight: 700;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            position: relative;
        }

        input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--background);
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        label {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            transition: all 0.3s ease;
            background: var(--background);
            padding: 0 0.5rem;
        }

        input:focus ~ label,
        input:not(:placeholder-shown) ~ label {
            top: 0;
            font-size: 0.875rem;
            color: var(--primary);
        }

        button {
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
        }

        .error-message {
            color: var(--error);
            text-align: center;
            margin: 1rem 0;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            50% { transform: translateX(10px); }
            75% { transform: translateX(-5px); }
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748b;
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            position: relative;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .register-link a:hover::after {
            width: 100%;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem;
                border-radius: 1rem;
            }

            h1 {
                font-size: 1.75rem;
            }

            input, button {
                padding: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Teachers Login</h1>
        <form action="login.php" method="POST">
            <div class="input-group">
                <input type="text" id="phone" name="phone" placeholder=" " required>
                <label for="phone">Phone Number</label>
            </div>
            
            <div class="input-group">
                <input type="password" id="pin" name="pin" placeholder=" " required>
                <label for="pin">PIN</label>
            </div>

            <button type="submit">Sign In</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <p class="register-link">New teacher? <a href="register.php">Create account</a></p>
    </div>
</body>
</html>
