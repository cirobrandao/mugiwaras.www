CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (category_id, name),
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE content_items
    ADD COLUMN category_id INT NULL AFTER library_id,
    ADD COLUMN series_id INT NULL AFTER category_id,
    ADD COLUMN file_hash CHAR(64) NULL AFTER cbz_path,
    ADD COLUMN file_size BIGINT NOT NULL DEFAULT 0 AFTER file_hash,
    ADD COLUMN original_name VARCHAR(255) NULL AFTER file_size,
    ADD UNIQUE KEY (file_hash),
    ADD CONSTRAINT fk_content_category FOREIGN KEY (category_id) REFERENCES categories(id),
    ADD CONSTRAINT fk_content_series FOREIGN KEY (series_id) REFERENCES series(id);

CREATE TABLE IF NOT EXISTS user_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (user_id, content_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;