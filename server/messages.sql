CREATE DATABASE whatsapp_messaging;

USE whatsapp_messaging;

-- Table to store user information
CREATE TABLE users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50) NOT NULL UNIQUE,
	phone_number VARCHAR(15) NOT NULL UNIQUE,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table to store messages
CREATE TABLE messages (
	message_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT,
	message_text TEXT,
	sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	delivered_at TIMESTAMP,
	read_at TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES users(id)
);