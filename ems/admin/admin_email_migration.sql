-- Run this once in phpMyAdmin on your EMS database.
-- It changes admin login from IC Number to Email.

ALTER TABLE admins
    ADD COLUMN email VARCHAR(255) NULL AFTER ic_number;

-- After adding the column, put an email address into every existing admin row.
-- Example:
-- UPDATE admins SET email='admin@example.com' WHERE admin_id=1;

-- Optional: once every admin has a valid unique email, run:
-- ALTER TABLE admins MODIFY email VARCHAR(255) NOT NULL;
-- ALTER TABLE admins ADD UNIQUE KEY unique_admin_email (email);
