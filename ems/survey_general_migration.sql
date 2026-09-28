-- ============================================================
-- Migration: General (reusable) survey questions with
-- multiple-choice answers + default "auto-shown" survey.
-- Run this once on your ems_db database, AFTER
-- exemption_migration.sql (e.g. via phpMyAdmin > SQL tab, or
-- `mysql -u root ems_db < survey_general_migration.sql`)
-- ============================================================

-- Allow a survey question to apply to ALL events by leaving
-- event_id empty (NULL). Existing per-event questions are
-- untouched.
ALTER TABLE survey_questions
    MODIFY event_id INT NULL;

-- Support two kinds of question:
--  - "tick"   : simple checkbox confirmation (existing behaviour)
--  - "choice" : multiple choice / rating scale (new)
-- "options" stores the answer choices for a "choice" question,
-- separated by the pipe character, e.g. "Tidak Memuaskan|Sederhana|Memuaskan"
ALTER TABLE survey_questions
    ADD COLUMN IF NOT EXISTS question_type ENUM('tick','choice') NOT NULL DEFAULT 'tick' AFTER question_text,
    ADD COLUMN IF NOT EXISTS options VARCHAR(500) NULL AFTER question_type,
    ADD COLUMN IF NOT EXISTS section_label VARCHAR(150) NULL AFTER options;

-- ============================================================
-- Seed the general, reusable survey (applies automatically to
-- every event, current and future, since event_id is NULL).
-- Content follows "Bahagian B - Penilaian Logistik & Pengurusan
-- Program" and "Bahagian C - Penilaian Impak & Pengisian Lawatan".
-- Admins can still add extra event-specific questions on top of
-- this from the Exemption page.
-- ============================================================

INSERT INTO survey_questions (event_id, question_text, question_type, options, section_label) VALUES
(NULL, 'Kecekapan urusetia dan kejelasan maklumat program', 'choice', 'Tidak Memuaskan|Sederhana|Memuaskan', 'Bahagian B - Penilaian Logistik & Pengurusan Program'),
(NULL, 'Kelancaran perjalanan dan ketetapan masa tentatif', 'choice', 'Tidak Memuaskan|Sederhana|Memuaskan', 'Bahagian B - Penilaian Logistik & Pengurusan Program'),
(NULL, 'Keselesaan ruang perbincangan', 'choice', 'Tidak Memuaskan|Sederhana|Memuaskan', 'Bahagian B - Penilaian Logistik & Pengurusan Program'),
(NULL, 'Penyediaan makanan dan minuman', 'choice', 'Tidak Memuaskan|Sederhana|Memuaskan', 'Bahagian B - Penilaian Logistik & Pengurusan Program'),
(NULL, 'Pengisian lawatan memenuhi objektif penanda aras', 'choice', 'Tidak Setuju|Setuju|Sangat Setuju', 'Bahagian C - Penilaian Impak & Pengisian Lawatan'),
(NULL, 'Perkongsian amalan terbaik (best practices) memberi nilai tambah kepada tugas saya', 'choice', 'Tidak Setuju|Setuju|Sangat Setuju', 'Bahagian C - Penilaian Impak & Pengisian Lawatan'),
(NULL, 'Secara keseluruhan, saya berpuas hati dengan penganjuran program ini.', 'choice', 'Tidak Setuju|Setuju|Sangat Setuju', 'Bahagian C - Penilaian Impak & Pengisian Lawatan');

-- NOTE: run this INSERT only once. If you re-run the migration
-- on a database that already has these 7 rows, remove/duplicate
-- rows manually or wrap the INSERT with a check first.
