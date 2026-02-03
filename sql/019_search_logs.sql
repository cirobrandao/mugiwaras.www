CREATE TABLE IF NOT EXISTS search_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    term VARCHAR(120) NOT NULL,
    results_count INT NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    INDEX (user_id),
    INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS series_search_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    series_id INT NOT NULL,
    user_id INT NULL,
    term VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX (series_id),
    INDEX (user_id),
    INDEX (created_at),
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;