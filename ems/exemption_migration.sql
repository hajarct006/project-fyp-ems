-- ============================================================
-- Migration: Pengecualian Kuliah (Class Exemption) via
-- Exemption Memo + Online Survey
-- Run this once on your ems_db database (e.g. via phpMyAdmin
-- > SQL tab, or `mysql -u root ems_db < exemption_migration.sql`)
-- ============================================================

CREATE TABLE IF NOT EXISTS exemption_memos (
    memo_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS survey_questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    question_text TEXT NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS survey_answers (
    answer_id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    answer_text TEXT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES survey_questions(question_id) ON DELETE CASCADE
);

-- ============================================================
-- NOTE (update): students now just tick each survey question
-- (no typing). answer_text is simply stored as "Yes" once
-- ticked. Once every question for the event has been ticked,
-- the certificate + exemption memo unlock automatically.
-- ============================================================

-- ============================================================
-- Fix: some installs are missing the "education_level" column
-- on the users table (shown in the admin as "Programme" -
-- Diploma / Degree). Run this if you see an error like:
-- "Unknown column 'users.education_level' in 'field list'"
-- ============================================================

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS education_level VARCHAR(50) NULL AFTER ic_number;

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS department VARCHAR(50) NULL AFTER education_level;
