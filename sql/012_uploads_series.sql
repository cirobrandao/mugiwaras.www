ALTER TABLE uploads
    ADD COLUMN category_id INT NULL AFTER user_id,
    ADD COLUMN series_id INT NULL AFTER category_id,
    ADD COLUMN title VARCHAR(190) NULL AFTER original_name,
    ADD INDEX (category_id),
    ADD INDEX (series_id);