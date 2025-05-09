-- ساخت جداول
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  telegram_id BIGINT UNIQUE,
  username VARCHAR(100),
  first_name VARCHAR(100),
  gender ENUM('male','female','unknown') DEFAULT 'unknown',
  joined_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('truth','dare') NOT NULL,
  text TEXT NOT NULL,
  voice_file_id VARCHAR(255) DEFAULT NULL,
  status ENUM('approved','pending') DEFAULT 'approved',
  user_id INT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  key_name VARCHAR(100) UNIQUE,
  key_value TEXT
);
CREATE TABLE queue_1v1 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  want_gender ENUM('male','female','any') DEFAULT 'any',
  enqueued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE games_1v1 (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user1_id INT NOT NULL,
  user2_id INT NOT NULL,
  started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user1_id) REFERENCES users(id),
  FOREIGN KEY (user2_id) REFERENCES users(id)
);