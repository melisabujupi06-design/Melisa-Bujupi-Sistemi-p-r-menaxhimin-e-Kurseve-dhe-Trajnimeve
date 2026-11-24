
<?php
session_start();
require_once 'config/db.php';

$success = "";
$error = "";

$selected_course_id = (int)($_GET["course_id"] ?? 0);

// Merr kurset aktive
$courses = $conn->query("
    SELECT course_id, title, price
    FROM courses
    WHERE status = 'open'
    ORDER BY title ASC
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $course_id = (int)($_POST["course_id"] ?? 0);

    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8 ||
        $course_id <= 0
    ) {
        $error = "Please complete all fields correctly. Password must contain at least 8 characters.";

    } else {

        try {
            $conn->begin_transaction();

            // Kontrollo nëse kursi ekziston dhe është i hapur
            $courseStmt = $conn->prepare("
                SELECT course_id
                FROM courses
                WHERE course_id = ? AND status = 'open'
                LIMIT 1
            ");

            $courseStmt->bind_param("i", $course_id);
            $courseStmt->execute();

            $courseResult = $courseStmt->get_result();

            if ($courseResult->num_rows === 0) {
                throw new Exception("The selected course is not available.");
            }

            $courseStmt->close();

            // Kontrollo nëse llogaria ekziston
            $userStmt = $conn->prepare("
                SELECT user_id, full_name, password_hash, role
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $userStmt->bind_param("s", $email);
            $userStmt->execute();

            $userResult = $userStmt->get_result();

            if ($user = $userResult->fetch_assoc()) {

                if (
                    $user["role"] !== "participant" ||
                    !password_verify($password, $user["password_hash"])
                ) {
                    throw new Exception("Email or password is incorrect.");
                }

                $user_id = (int)$user["user_id"];
                $student_name = $user["full_name"];

            } else {

                // Krijo llogari të re
                $password_hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $insertUser = $conn->prepare("
                    INSERT INTO users
                    (full_name, email, password_hash, role)
                    VALUES (?, ?, ?, 'participant')
                ");

                $insertUser->bind_param(
                    "sss",
                    $name,
                    $email,
                    $password_hash
                );

                $insertUser->execute();

                $user_id = (int)$conn->insert_id;
                $student_name = $name;

                $insertUser->close();
            }

            $userStmt->close();

            // Kontrollo regjistrimin e dyfishtë
            $checkEnrollment = $conn->prepare("
                SELECT enrollment_id
                FROM enrollments
                WHERE user_id = ? AND course_id = ?
                LIMIT 1
            ");

            $checkEnrollment->bind_param(
                "ii",
                $user_id,
                $course_id
            );

            $checkEnrollment->execute();

            $existingEnrollment = $checkEnrollment->get_result();

            if ($existingEnrollment->num_rows > 0) {
                throw new Exception(
                    "You are already registered for this course."
                );
            }

            $checkEnrollment->close();

            // Regjistro studentin në kurs
            $enrollStmt = $conn->prepare("
                INSERT INTO enrollments
                (user_id, course_id, status)
                VALUES (?, ?, 'registered')
            ");

            $enrollStmt->bind_param(
                "ii",
                $user_id,
                $course_id
            );

            $enrollStmt->execute();
            $enrollStmt->close();

            // Ruaj ndryshimet në databazë
            $conn->commit();

            // Kyç automatikisht studentin
            session_regenerate_id(true);

            $_SESSION["student_id"] = $user_id;
            $_SESSION["student_name"] = $student_name;

            // Dërgo studentin te dashboard
            header("Location: student_dashboard.php");
            exit;

        } catch (Throwable $e) {

            $conn->rollback();

            if ($e instanceof mysqli_sql_exception) {
                $error = "Registration could not be completed. Please try again.";
            } else {
                $error = $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration | SkillCRAFT</title>

    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/register.css">
</head>

<body>

<header class="register-header">
    <a href="index.php" class="register-logo">Skill<span>CRAFT.</span></a>
    <a href="index.php" class="back-link">Back to Home</a>
</header>

<main class="register-container">

    <section class="register-card">

        <p class="register-label">JOIN SKILLCRAFT</p>
        <h1>Course Registration</h1>
        <p class="register-description">
            Create your account and register for your preferred course.
        </p>

        <?php if ($success !== ""): ?>
            <div class="register-message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="register-message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="register-group">
                <label for="full_name">Full Name</label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?= htmlspecialchars($_POST["full_name"] ?? "") ?>"
                    required
                >
            </div>

            <div class="register-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                    required
                >
            </div>

            <div class="register-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    required
                >
                <small>At least 8 characters.</small>
            </div>

            <div class="register-group">
                <label for="course_id">Select Course</label>

                <select id="course_id" name="course_id" required>
                    <option value="">Choose a course</option>

                    <?php if ($courses): ?>
                        <?php while ($course = $courses->fetch_assoc()): ?>

                            <option
                                value="<?= (int)$course["course_id"] ?>"
                                <?= (
                                    (isset($_POST["course_id"]) &&
                                    (int)$_POST["course_id"] === (int)$course["course_id"])
                                    ||
                                    (!isset($_POST["course_id"]) &&
                                    $selected_course_id === (int)$course["course_id"])
                                ) ? "selected" : "" ?>
                            >
                                <?= htmlspecialchars($course["title"]) ?>
                                - €<?= number_format((float)$course["price"], 2) ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

                </select>
            </div>

            <button type="submit" class="register-button">
                Register Now
            </button>

        </form>

    </section>

</main>

</body>
</html>
