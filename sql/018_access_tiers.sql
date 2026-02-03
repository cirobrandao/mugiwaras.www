ALTER TABLE users
    MODIFY COLUMN access_tier ENUM('user','trial','assinante','restrito','vitalicio') NOT NULL DEFAULT 'user';