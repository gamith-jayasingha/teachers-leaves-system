<?php
// Simulate the number of days remaining
$daysRemaining = 2;

// Check if payment has been made (this would typically come from a database)
$paymentMade = false;

// Handle payment submission (if needed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
    // Simulate payment processing
    $paymentMade = true;
    // Redirect to the payment link
    header("Location: https://paylink.geniebiz.lk/EqDRJa584a");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .payment-button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .payment-button:hover {
            background-color: #218838;
        }

        .payment-button:active {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$paymentMade && $daysRemaining > 0): ?>
            <h1>Payment Reminder</h1>
            <p>You have <?php echo $daysRemaining; ?> days remaining to make payment for your servers.</p>
            <p>If payment is not made, your website will be taken down.</p>
            <form method="POST" action="">
                <button type="submit" name="make_payment" class="payment-button">Make Payment Now</button>
            </form>
        <?php elseif ($paymentMade): ?>
            <h1>Payment Successful</h1>
            <p>Thank you for your payment. Your servers will remain active.</p>
        <?php else: ?>
            <h1>Website Down</h1>
            <p>Your website has been taken down due to non-payment.</p>
        <?php endif; ?>
    </div>
</body>
</html> 