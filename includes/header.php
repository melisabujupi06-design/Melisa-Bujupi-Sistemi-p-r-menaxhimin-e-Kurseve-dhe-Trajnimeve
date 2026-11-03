
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistemi për Menaxhimin e Kurseve dhe Trajnimeve</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="header">
    <div class="container navbar">
        <h1>EduTrain</h1>

        <nav>
            <a href="index.php">Ballina</a>
            <a href="index.php#kurset">Kurset</a>
            <a href="index.php#rreth-nesh">Rreth nesh</a>
            <a href="contact.php">Kontakti</a>
            <a href="admin/login.php">Admin</a>
        </nav>
    </div>
</header>

<main>
