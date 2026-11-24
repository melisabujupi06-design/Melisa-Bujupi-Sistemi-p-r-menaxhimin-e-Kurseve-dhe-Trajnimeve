
<?php
session_start();
require_once "config/db.php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id FROM users
             WHERE email = ? AND role = 'participant'
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $update = $conn->prepare(
                "UPDATE users
                 SET password_hash = ?
                 WHERE user_id = ? AND role = 'participant'"
            );

            $update->bind_param(
                "si",
                $password_hash,
                $user["user_id"]
            );

            if ($update->execute() && $update->affected_rows === 1) {
                $message = "Your password has been changed successfully!";
                $success = true;
            } else {
                $message = "Could not update your password. Please try again.";
            }

            $update->close();
        } else {
            $message = "No student account was found with this email.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SkillCRAFT</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/register.css">
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <h1>SkillCRAFT</h1>
        <h2>Reset Password</h2>

        <p>Enter your registered email and choose a new password.</p>

        <?php if ($message !== ""): ?>
            <p style="color:<?= $success ? '#47734d' : '#b3261e' ?>;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">

            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your registered email"
                required
            >

            <label>New Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter new password"
                minlength="8"
                required
            >

            <label>Confirm New Password</label>
            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                minlength="8"
                required
            >

            <button type="submit">UPDATE PASSWORD</button>

        </form>
        <?php endif; ?>

        <p><a href="student_login.php">Back to Student Login</a></p>
    </div>
</div>

</body>
</html>

