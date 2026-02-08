CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    phone_country VARCHAR(8) NOT NULL,
    phone_has_whatsapp TINYINT(1) NOT NULL DEFAULT 1,
    birth_date VARCHAR(10) NOT NULL,
    avatar_path VARCHAR(255) NULL,
    observations TEXT NULL,
    password_hash VARCHAR(255) NOT NULL,
    access_tier ENUM('user','trial','assinante','restrito','vitalicio') NOT NULL DEFAULT 'user',
    role ENUM('user','admin','equipe','superadmin') NOT NULL DEFAULT 'user',
    support_agent TINYINT(1) NOT NULL DEFAULT 0,
    uploader_agent TINYINT(1) NOT NULL DEFAULT 0,
    moderator_agent TINYINT(1) NOT NULL DEFAULT 0,
    referral_code VARCHAR(32) NOT NULL UNIQUE,
    referrer_id INT NULL,
    credits INT NOT NULL DEFAULT 0,
    subscription_expires_at DATETIME NULL,
    ip_cadastro VARCHAR(45) NULL,
    ip_ultimo_acesso VARCHAR(45) NULL,
    ip_penultimo_acesso VARCHAR(45) NULL,
    data_registro DATETIME NOT NULL,
    data_ultimo_login DATETIME NULL,
    user_agent_ultimo_login VARCHAR(255) NULL,
    tentativas_login INT NOT NULL DEFAULT 0,
    lock_until DATETIME NULL,
    FOREIGN KEY (referrer_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (token_hash),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS support_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    status ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
    admin_note TEXT NULL,
    updated_at DATETIME NULL,
    attachment_path VARCHAR(255) NULL,
    attachment_name VARCHAR(255) NULL,
    public_token CHAR(32) NULL,
    whatsapp_opt_in TINYINT(1) NOT NULL DEFAULT 0,
    whatsapp_number VARCHAR(20) NULL,
    UNIQUE KEY public_token (public_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS email_blocklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(190) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS username_blocklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    bonus_credits INT NOT NULL DEFAULT 0,
    subscription_days INT NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS package_categories (
    package_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (package_id, category_id),
    INDEX (category_id),
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    months INT NOT NULL DEFAULT 1,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    proof_path VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (package_id) REFERENCES packages(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vouchers (
    code VARCHAR(40) PRIMARY KEY,
    package_id INT NOT NULL,
    days INT NOT NULL DEFAULT 0,
    max_uses INT NULL,
    uses INT NOT NULL DEFAULT 0,
    expires_at DATETIME NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS voucher_redemptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voucher_code VARCHAR(40) NOT NULL,
    user_id INT NOT NULL,
    redeemed_at DATETIME NOT NULL,
    UNIQUE KEY (voucher_code, user_id),
    INDEX (user_id),
    FOREIGN KEY (voucher_code) REFERENCES vouchers(code) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_type VARCHAR(50) NOT NULL,
    payload JSON NOT NULL,
    status ENUM('pending','running','done','failed') NOT NULL DEFAULT 'pending',
    error_message TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    started_at DATETIME NULL,
    finished_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(50) NOT NULL,
    user_id INT NULL,
    meta JSON NULL,
    created_at DATETIME NOT NULL,
    INDEX (event)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS login_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    logged_at DATETIME NOT NULL,
    INDEX (user_id),
    INDEX (logged_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS avatar_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NULL,
    file_path VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS libraries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL UNIQUE,
    banner_path VARCHAR(255) NULL,
    tag_color VARCHAR(20) NULL,
    display_orientation ENUM('vertical','horizontal') NOT NULL DEFAULT 'vertical',
    cbz_direction ENUM('rtl','ltr') NOT NULL DEFAULT 'rtl',
    cbz_mode ENUM('page','scroll') NOT NULL DEFAULT 'page',
    epub_mode ENUM('text','comic') NOT NULL DEFAULT 'text',
    hide_from_store TINYINT(1) NOT NULL DEFAULT 0,
    content_video TINYINT(1) NOT NULL DEFAULT 0,
    content_cbz TINYINT(1) NOT NULL DEFAULT 1,
    content_pdf TINYINT(1) NOT NULL DEFAULT 1,
    content_epub TINYINT(1) NOT NULL DEFAULT 0,
    content_download TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    requires_subscription TINYINT(1) NOT NULL DEFAULT 0,
    adult_only TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(190) NOT NULL,
    pin_order INT NOT NULL DEFAULT 0,
    adult_only TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (category_id, name),
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS content_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    library_id INT NULL,
    category_id INT NULL,
    series_id INT NULL,
    title VARCHAR(190) NOT NULL,
    cbz_path VARCHAR(255) NOT NULL,
    content_order INT NOT NULL DEFAULT 0,
    file_hash CHAR(64) NULL,
    file_size BIGINT NOT NULL DEFAULT 0,
    original_name VARCHAR(255) NULL,
    view_count INT NOT NULL DEFAULT 0,
    download_count INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (file_hash),
    FOREIGN KEY (library_id) REFERENCES libraries(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (series_id) REFERENCES series(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    INDEX (token_hash),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_blocklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason TEXT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS user_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (user_id, content_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_content_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_id INT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME NULL,
    last_page INT NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY (user_id, content_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_series_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    series_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY (user_id, series_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    category_id INT NULL,
    series_id INT NULL,
    original_name VARCHAR(255) NOT NULL,
    title VARCHAR(190) NULL,
    source_path VARCHAR(255) NOT NULL,
    target_path VARCHAR(255) NOT NULL,
    status ENUM('queued','pending','processing','done','completed','failed') NOT NULL DEFAULT 'queued',
    job_id INT NULL,
    file_size BIGINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    INDEX (user_id),
    INDEX (category_id),
    INDEX (series_id),
    INDEX (job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    show_sidebar TINYINT(1) NOT NULL DEFAULT 1,
    show_below_most_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    body TEXT NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    category_id INT NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NULL,
    INDEX (category_id),
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
