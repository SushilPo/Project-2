CREATE DATABASE IF NOT EXISTS thriftwear CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'thriftwear'@'%' IDENTIFIED BY 'thriftwear_dev';
GRANT ALL PRIVILEGES ON thriftwear.* TO 'thriftwear'@'%';
FLUSH PRIVILEGES;

USE thriftwear;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    google_id VARCHAR(64) NULL UNIQUE,
    location VARCHAR(120) NOT NULL DEFAULT 'Australia',
    dark_mode TINYINT(1) NOT NULL DEFAULT 0,
    push_notifications TINYINT(1) NOT NULL DEFAULT 1,
    swap_alerts TINYINT(1) NOT NULL DEFAULT 1,
    location_enabled TINYINT(1) NOT NULL DEFAULT 1,
    profile_public TINYINT(1) NOT NULL DEFAULT 1,
    email_notifications TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS login_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    logged_in_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NULL,
    login_method ENUM('password','google') NOT NULL DEFAULT 'password',
    CONSTRAINT login_history_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX login_history_user_date (user_id, logged_in_at)
);

CREATE TABLE IF NOT EXISTS listings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category ENUM('Women','Men','Shoes','Bags') NOT NULL,
    size VARCHAR(30) NOT NULL,
    item_condition ENUM('Like new','Excellent','Good','Fair') NOT NULL,
    swap_available TINYINT(1) NOT NULL DEFAULT 0,
    image_url TEXT NOT NULL,
    status ENUM('approved','pending') NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT listings_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX listings_search (category, created_at)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    buyer_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    address VARCHAR(200) NOT NULL,
    city VARCHAR(100) NOT NULL,
    postcode VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT orders_buyer_fk FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS wishlists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    listing_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY wishlists_unique (user_id, listing_id),
    CONSTRAINT wishlists_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS swap_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    requester_id INT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT swap_requests_user_fk FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    sender_id INT UNSIGNED NOT NULL,
    body VARCHAR(1000) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT messages_user_fk FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);
