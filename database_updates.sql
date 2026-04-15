-- Database Updates for Work-Study Management System
-- Run these commands in phpMyAdmin SQL tab

-- 1. Add supervisor_id to jobs table (with foreign key)
ALTER TABLE jobs ADD COLUMN supervisor_id INT DEFAULT NULL;
ALTER TABLE jobs ADD FOREIGN KEY (supervisor_id) REFERENCES supervisors(id) ON DELETE SET NULL;

-- 2. Add updated_at timestamp to job_applications
ALTER TABLE job_applications ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 3. Add rejection_reason to job_applications
ALTER TABLE job_applications ADD COLUMN rejection_reason TEXT DEFAULT NULL;

-- 4. Add job_id to work_logs table (to track which job the hours are for)
ALTER TABLE work_logs ADD COLUMN job_id INT DEFAULT NULL;
ALTER TABLE work_logs ADD FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL;

-- 5. Add updated_at timestamp to work_logs
ALTER TABLE work_logs ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 6. Add rejection_reason to work_logs
ALTER TABLE work_logs ADD COLUMN rejection_reason TEXT DEFAULT NULL;

-- NOTE: After running these commands, update the jobs table data:
-- UPDATE jobs SET supervisor_id = 1 WHERE supervisor = 'Dr Adewale';
-- UPDATE jobs SET supervisor_id = 2 WHERE supervisor = 'Mr Akin';
-- (adjust based on your supervisor IDs)
