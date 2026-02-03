ALTER TABLE support_messages
    ADD COLUMN status ENUM('open','closed') NOT NULL DEFAULT 'open',
    ADD COLUMN admin_note TEXT NULL,
    ADD COLUMN updated_at DATETIME NULL;
