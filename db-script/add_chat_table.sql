-- Adds chat messages table for chatbot feature
CREATE TABLE IF NOT EXISTS tbl_chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(128) NULL,
    ip VARCHAR(45) NULL,
    role VARCHAR(16) NOT NULL,
    message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
