ALTER TABLE support_messages
    MODIFY COLUMN status ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
    ADD COLUMN attachment_path VARCHAR(255) NULL,
    ADD COLUMN attachment_name VARCHAR(255) NULL,
    ADD COLUMN public_token CHAR(32) NULL,
    ADD COLUMN whatsapp_opt_in TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN whatsapp_number VARCHAR(20) NULL,
    ADD UNIQUE KEY public_token (public_token);

CREATE TABLE IF NOT EXISTS support_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    support_id INT NOT NULL,
    user_id INT NULL,
    admin_id INT NULL,
    message TEXT NOT NULL,
    attachment_path VARCHAR(255) NULL,
    attachment_name VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    INDEX (support_id),
    INDEX (user_id),
    INDEX (admin_id),
    FOREIGN KEY (support_id) REFERENCES support_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
