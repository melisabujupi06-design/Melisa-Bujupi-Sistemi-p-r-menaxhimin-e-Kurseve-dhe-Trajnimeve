
<?php
require_once 'config/db.php';

$courses = $conn->query("
    SELECT course_id, title, description, start_date, price, status
    FROM courses
    WHERE status = 'open'
    ORDER BY start_date ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SkillCRAFT | Learn Something New</title>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="assets/style.css">

</head>
<body>

<header>
    <div class="container">
        <nav>
            <a href="index.php" class="logo">Skill<span>CRAFT.</span></a>
            <div class="nav-links">
    <a href="#home">HOME</a>
    <a href="#courses">COURSES</a>
    <a href="about.php">ABOUT US</a>
    <a href="contact.php">CONTACT</a>
    <a href="student_login.php">STUDENT LOGIN</a>
    <a href="admin/login.php" class="admin-link">ADMIN</a>
</div>

        </nav>
    </div>
</header>

<main>

<section class="hero" id="home">
    <div class="container hero-content">

        <div>
            <div class="eyebrow">LEARN • GROW • ACHIEVE</div>

            <h1>
                Your journey<br>
                to a <em>better future</em><br>
                starts here.
            </h1>

            <p class="hero-description">
                Discover new skills, explore inspiring courses and
                take the next step towards your personal and
                professional growth with SkillCRAFT.
            </p>

            <a href="#courses" class="primary-btn">EXPLORE COURSES →</a>
        </div>

        <div class="hero-visual">
            <div class="visual-main">
                <div class="visual-text">
                    <div class="big-number">01</div>
                    <p>START LEARNING TODAY</p>
                </div>
            </div>

            <div class="floating-card">
                <strong>Learn with purpose.</strong>
                <p>Build skills that shape your future.</p>
            </div>
        </div>

    </div>
</section>

<section class="intro">
    <div class="container">
        <div class="section-label">WELCOME TO SKILLCRAFT</div>
        <h2 class="section-title">Knowledge opens new doors.</h2>
        <p>
            We believe that learning is a continuous journey.
            Our platform brings courses and training opportunities
            together to help you develop your knowledge and skills.
        </p>
    </div>
</section>

<section class="courses" id="courses">
    <div class="container">

        <div class="section-heading">
            <div>
                <div class="section-label">EXPLORE & DISCOVER</div>
                <h2 class="section-title">Featured courses</h2>
            </div>

            <p class="course-heading-text">
                Find the right course for your next step.
            </p>
        </div>

        <div class="course-grid">

        <?php if ($courses && $courses->num_rows > 0): ?>

            <?php while ($course = $courses->fetch_assoc()): ?>
                <article class="course-card">

                    <div class="course-top">✦</div>

                    <div class="course-info">
                        <h3><?= htmlspecialchars($course['title']) ?></h3>

                        <p><?= htmlspecialchars($course['description']) ?></p>

                       <div class="course-meta">
    <span><?= htmlspecialchars($course['start_date']) ?></span>

    <strong>
        €<?= htmlspecialchars($course['price']) ?>
    </strong>
</div>

<a href="register.php?course_id=<?= (int)$course['course_id'] ?>"
   class="course-register-btn">
    REGISTER NOW →
</a>
                    </div>

                </article>
            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty-message">
                No courses are currently available. Please check back soon.
            </div>

        <?php endif; ?>

        </div>
    </div>
</section>

<section class="about" id="about">
    <div class="container about-content">

        <div class="about-box">
            SkillCRAFT
        </div>

        <div class="about-text">
            <div class="section-label">OUR PHILOSOPHY</div>
            <h2 class="section-title">Learning that moves you forward.</h2>

            <p>
                SkillCRAFT is designed to make course and training
                management simple and accessible. Our goal is to
                connect learners with opportunities that support
                their development.
            </p>

            <a href="contact.php" class="primary-btn">GET IN TOUCH →</a>
        </div>

    </div>
</section>

</main>

<footer>
    <div class="container footer-content">
        <a href="index.php" class="logo">Skill<span>CRAFT.</span></a>

        <p>© 2026 SkillCRAFT. All rights reserved.</p>
        <p>Learn today. Lead tomorrow.</p>
    </div>
</footer>

</body>
</html>
