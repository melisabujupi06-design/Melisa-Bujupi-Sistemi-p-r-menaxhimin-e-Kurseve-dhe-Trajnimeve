
<?php
session_start();
require_once '../config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare(
            "SELECT user_id, full_name, email, password_hash
             FROM users
             WHERE email = ? AND role = 'admin'
             LIMIT 1"
        );

        if ($stmt) {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($admin = $result->fetch_assoc()) {

                if (password_verify($password, $admin["password_hash"])) {

                    session_regenerate_id(true);

                    $_SESSION["admin_id"] = $admin["user_id"];
                    $_SESSION["admin_name"] = $admin["full_name"];
                    $_SESSION["admin_email"] = $admin["email"];

                    header("Location: dashboard.php");
                    exit;

                } else {
                    $error = "Incorrect email or password.";
                }

            } else {
                $error = "Incorrect email or password.";
            }

            $stmt->close();

        } else {
            $error = "A database error occurred. Please try again.";
        }

    } else {
        $error = "Please enter your email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | SkillCRAFT</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>
        body {
            background: #f5f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .login-container {
            width: 100%;
            max-width: 430px;
            background: #fffdf9;
            padding: 45px;
            border: 1px solid #e6ddd1;
        }

        .login-logo {
            display: block;
            text-align: center;
            font-size: 30px;
            font-weight: 700;
            color: #30251e;
            text-decoration: none;
            margin-bottom: 30px;
        }

        .login-logo span {
            color: #a77b55;
        }

        .login-container h1 {
            font-family: 'Playfair Display', serif;
            color: #604632;
            font-size: 30px;
            text-align: center;
            margin-bottom: 10px;
        }

        .login-description {
            text-align: center;
            color: #887b6e;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .login-container label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #30251e;
        }

        .login-container input {
            width: 100%;
            padding: 14px;
            margin-bottom: 22px;
            border: 1px solid #e6ddd1;
            background: #f5f0e8;
            font-size: 14px;
            outline: none;
        }

        .login-container input:focus {
            border-color: #a77b55;
        }

        .login-container button {
            width: 100%;
            padding: 15px;
            background: #604632;
            color: #fffdf9;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        .login-container button:hover {
            background: #30251e;
        }

        .login-error {
            background: #f8e8e5;
            color: #8b3025;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #887b6e;
            font-size: 13px;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="login-container">

    <a href="../index.php" class="login-logo">
        Skill<span>CRAFT.</span>
    </a>

    <h1>Welcome Back</h1>

    <p class="login-description">
        Sign in to your administrator account.
    </p>

    <?php if ($error !== ""): ?>
        <div class="login-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <label for="email">Email Address</label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">SIGN IN</button>

    </form>

    <a href="../index.php" class="back-home">
        ← Back to Home
    </a>

</div>

</body>
</html>
