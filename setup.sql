-- ============================================================
-- EduNova – Database Setup
-- Run this in phpMyAdmin > SQL tab (or via mysql CLI)
-- ============================================================

CREATE DATABASE IF NOT EXISTS edunova;
USE edunova;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS applications (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    program     VARCHAR(100) NOT NULL,
    degree      VARCHAR(50)  NOT NULL,
    dob         DATE         NOT NULL,
    phone       VARCHAR(20)  NOT NULL,
    address     TEXT         NOT NULL,
    prev_school VARCHAR(150) NOT NULL,
    prev_grade  VARCHAR(20)  NOT NULL,
    statement   TEXT         NOT NULL,
    status      ENUM('pending','reviewed','accepted','rejected') DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ── Admin credentials (set in admin_login.php) ──────────────
-- Default email   : admin@edunova.com
-- Default password: edunova2025
-- Change these in admin_login.php before going live!
