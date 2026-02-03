CREATE TABLE IF NOT EXISTS content_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    content_id INT NOT NULL,
    event ENUM('read_open','read_page','download') NOT NULL,
    page_num INT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    INDEX (user_id),
    INDEX (content_id),
    INDEX (event),
    FOREIGN KEY (content_id) REFERENCES content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
