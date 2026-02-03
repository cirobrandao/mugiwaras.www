CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    original_name VARCHAR(255) NOT NULL,
    source_path VARCHAR(255) NOT NULL,
    target_path VARCHAR(255) NOT NULL,
    status ENUM('queued','done','failed') NOT NULL DEFAULT 'queued',
    job_id INT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    INDEX (user_id),
    INDEX (job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
