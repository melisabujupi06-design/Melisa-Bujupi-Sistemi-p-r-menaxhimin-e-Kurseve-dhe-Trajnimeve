
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once '../config/db.php';

$error = "";
$success = "";

// ADD COURSE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_course"])) {

    $category_id = (int)($_POST["category_id"] ?? 0);
    $trainer_id = (int)($_POST["trainer_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $price = (float)($_POST["price"] ?? 0);
    $capacity = (int)($_POST["capacity"] ?? 0);
    $status = $_POST["status"] ?? "open";

    if (
        $category_id <= 0 || $trainer_id <= 0 ||
        $title === "" || $description === "" ||
        $start_date === "" || $end_date === "" ||
        $price < 0 || $capacity <= 0 ||
        !in_array($status, ["open", "closed"], true)
    ) {
        $error = "Please complete all fields correctly.";
    } elseif ($end_date < $start_date) {
        $error = "End date cannot be before start date.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO courses
            (category_id, trainer_id, title, description,
             start_date, end_date, price, capacity, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iissssdis",
            $category_id,
            $trainer_id,
            $title,
            $description,
            $start_date,
            $end_date,
            $price,
            $capacity,
            $status
        );

        if ($stmt->execute()) {
            $success = "Course added successfully!";
        } else {
            $error = "Could not add course. Check the selected category and trainer.";
        }

        $stmt->close();
    }
}

// DELETE COURSE
if (isset($_GET["delete"])) {
    $course_id = (int)$_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $success = "Course deleted successfully!";
    } else {
        $error = "Course could not be deleted. It may be connected to other records.";
    }

    $stmt->close();
}

// LOAD COURSES
$courses = $conn->query("
    SELECT course_id, title, description, start_date,
           end_date, price, capacity, status
    FROM courses
    ORDER BY course_id DESC
");

// LOAD CATEGORIES
$categories = $conn->query("
    SELECT category_id, category_name
    FROM categories
    ORDER BY category_name
");

// LOAD TRAINERS
$trainers = $conn->query("
    SELECT trainer_id, full_name
    FROM trainers
    ORDER BY full_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses | SkillCRAFT</title>

    <link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/admin-courses.css">

</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">
        <a href="../index.php" class="sidebar-logo">
            Skill<span>CRAFT.</span>
        </a>

        <h4>ADMIN MENU</h4>

        <a href="dashboard.php" class="menu-link">Dashboard</a>
        <a href="courses.php" class="menu-link active">Manage Courses</a>
        <a href="enrollments.php" class="menu-link">Enrollments</a>
        <a href="messages.php" class="menu-link">Contact Messages</a>

        <div class="logout-link">
            <a href="logout.php" class="menu-link">Log Out</a>
        </div>
    </aside>

    <main class="main-content">

        <div class="page-heading">
            <h1>Manage Courses</h1>
            <p>Add and manage the courses available on SkillCRAFT.</p>
        </div>

        <?php if ($success !== ""): ?>
            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="course-form">

            <h2>Add New Course</h2>

            <form method="POST" action="courses.php">

                <div class="form-grid">

                    <div class="form-group">
                        <label>Course Title</label>
                        <input type="text" name="title" required>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">Select category</option>
                            <?php if ($categories): ?>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat["category_id"] ?>">
                                        <?= htmlspecialchars($cat["category_name"]) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Trainer</label>
                        <select name="trainer_id" required>
                            <option value="">Select trainer</option>
                            <?php if ($trainers): ?>
                                <?php while ($trainer = $trainers->fetch_assoc()): ?>
                                    <option value="<?= $trainer["trainer_id"] ?>">
                                        <?= htmlspecialchars($trainer["full_name"]) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price (€)</label>
                        <input type="number" name="price" min="0" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" required>
                    </div>

                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" required>
                    </div>

                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" name="capacity" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" required></textarea>
                    </div>

                    <div class="form-group full-width">
                        <button type="submit" name="add_course" class="primary-btn">
                            ADD COURSE
                        </button>
                    </div>

                </div>

            </form>

        </div>

        <div class="table-container">

            <h2>All Courses</h2>

            <div class="table-scroll">

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Price</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($courses && $courses->num_rows > 0): ?>

                            <?php while ($course = $courses->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $course["course_id"] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($course["title"]) ?></strong><br>
                                        <?= htmlspecialchars($course["description"]) ?>
                                    </td>
                                    <td><?= htmlspecialchars($course["start_date"]) ?></td>
                                    <td><?= htmlspecialchars($course["end_date"]) ?></td>
                                    <td>€<?= number_format((float)$course["price"], 2) ?></td>
                                    <td><?= $course["capacity"] ?></td>
                                    <td>
                                        <span class="status <?= $course["status"] === "closed" ? "closed" : "" ?>">
                                            <?= htmlspecialchars(ucfirst($course["status"])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a
                                            class="delete-btn"
                                            href="courses.php?delete=<?= $course["course_id"] ?>"
                                            onclick="return confirm('Are you sure you want to delete this course?');"
                                        >Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="8">No courses found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>
