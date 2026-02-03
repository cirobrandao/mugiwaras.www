ALTER TABLE user_content_status
    ADD COLUMN last_page INT NOT NULL DEFAULT 0 AFTER read_at;
