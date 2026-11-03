
CREATE DATABASE IF NOT EXISTS kurs_trajnime_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE kurs_trajnime_db;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'participant') DEFAULT 'participant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE trainers (
    trainer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    specialization VARCHAR(120) NOT NULL,
    phone VARCHAR(30)
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    trainer_id INT NOT NULL,
    title VARCHAR(160) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    capacity INT NOT NULL DEFAULT 20,
    status ENUM('planned','open','completed','cancelled') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id)
);

CREATE TABLE enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('registered','approved','completed','cancelled') DEFAULT 'registered',
    UNIQUE (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

CREATE TABLE contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(120) NOT NULL,
    sender_email VARCHAR(150) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (category_name, description) VALUES
('Programim', 'Kurse te programimit'),
('Dizajn', 'Kurse te dizajnit'),
('Marketing', 'Kurse te marketingut digjital');

INSERT INTO trainers (full_name, email, specialization, phone) VALUES
('Arben Krasniqi', 'arben@example.com', 'PHP dhe MySQL', '044111222'),
('Elira Gashi', 'elira@example.com', 'UI/UX Design', '044333444'),
('Dren Berisha', 'dren@example.com', 'Digital Marketing', '044555666');

INSERT INTO courses
(category_id, trainer_id, title, description, start_date, end_date, price, capacity, status)
VALUES
(1, 1, 'Bazat e PHP dhe MySQL',
 'Hyrje ne zhvillimin e aplikacioneve web.',
 '2026-11-10', '2026-12-10', 80.00, 25, 'open'),
(2, 2, 'Hyrje ne UI/UX Design',
 'Parimet baze te dizajnit te nderfaqeve.',
 '2026-11-15', '2026-12-15', 65.00, 20, 'open');
