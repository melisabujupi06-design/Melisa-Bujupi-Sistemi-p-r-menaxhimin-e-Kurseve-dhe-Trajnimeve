
<?php
require_once 'config/db.php';

$success = "";
$error = "";

$name = "";
$email = "";
$subject = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["sender_name"] ?? "");
    $email = trim($_POST["sender_email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name === "" || $email === "" || $subject === "" || $message === "") {
        $error = "Please complete all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO contact_messages
            (sender_name, sender_email, subject, message)
            VALUES (?, ?, ?, ?)
        ");

        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            if ($stmt->execute()) {
                $success = "Thank you! Your message has been sent successfully.";

                $name = "";
                $email = "";
                $subject = "";
                $message = "";
            } else {
                $error = "Something went wrong. Please try again.";
            }

            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us | SkillCRAFT</title>

<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/contact.css">
</head>

<body>

<header>
    <div class="container">
        <nav>
            <a href="index.php" class="logo">Skill<span>CRAFT.</span></a>

            <div class="nav-links">
                <a href="index.php">HOME</a>
                <a href="index.php#courses">COURSES</a>
                <a href="index.php#about">ABOUT US</a>
                <a href="contact.php">CONTACT</a>
                <a href="admin/login.php" class="admin-link">ADMIN</a>
            </div>
        </nav>
    </div>
</header>

<section class="contact-section">

    <div class="container">

        <div class="contact-heading">
            <div class="section-label">WE WOULD LOVE TO HEAR FROM YOU</div>
            <h1 class="section-title">Get in touch.</h1>
            <p style="color:#887b6e;">
                Have a question? Send us a message and we will get back to you.
            </p>
        </div>

        <div class="contact-layout">

            <div class="contact-info">
                <h2>Let's start a conversation.</h2>

                <p>
                    Whether you have questions about our courses,
                    training opportunities or the SkillCRAFT platform,
                    feel free to contact us.
                </p>

                <br>

                <p>
                    <strong>SkillCRAFT</strong><br>
                    Learn today. Lead tomorrow.
                </p>
            </div>

            <div class="contact-form">

                <?php if ($success !== ""): ?>
                    <div class="form-message form-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error !== ""): ?>
                    <div class="form-message form-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="contact.php">

                    <label for="sender_name">Your Name</label>
                    <input
                        type="text"
                        id="sender_name"
                        name="sender_name"
                        value="<?= htmlspecialchars($name) ?>"
                        placeholder="Enter your name"
                        required
                    >

                    <label for="sender_email">Your Email</label>
                    <input
                        type="email"
                        id="sender_email"
                        name="sender_email"
                        value="<?= htmlspecialchars($email) ?>"
                        placeholder="Enter your email"
                        required
                    >

                    <label for="subject">Subject</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        value="<?= htmlspecialchars($subject) ?>"
                        placeholder="Message subject"
                        required
                    >

                    <label for="message">Your Message</label>
                    <textarea
                        id="message"
                        name="message"
                        placeholder="Write your message here..."
                        required
                    ><?= htmlspecialchars($message) ?></textarea>

                    <button type="submit" class="primary-btn">
                        SEND MESSAGE →
                    </button>

                </form>

            </div>

        </div>
    </div>

</section>

<footer>
    <div class="container footer-content">
        <a href="index.php" class="logo">Skill<span>CRAFT.</span></a>
        <p>© 2026 SkillCRAFT. All rights reserved.</p>
        <p>Learn today. Lead tomorrow.</p>
    </div>
</footer>

</body>
</html>
