/*
Created by AMEN-ProgCorps Development Team
This code is Open Source meaning anyone has
access and free modify its Code Structure

- MacCloudGZ(2025)
txtfile created on 13/04/2025 15:43 UTC
*/

-- Database Creation 
CREATE DATABASE convenience_store_post;

USE convenience_store_post;

-- Discounts table 
CREATE TABLE discounts (
    discount_id INT PRIMARY KEY,
    valid_from DATE NOT NULL,
    valid_till DATE NOT NULL,
    description VARCHAR(250) NOT NULL,
    discount_percentage DECIMAL(5,2) NOT NULL
)

-- category table
CREATE TABLE categories (
    category_id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

--Product table
CREATE TABLE products (
    product_id INT PRIMARY KEY,
    category_id INT,
    name VARCHAR(50) NOT NULL,
    stock_level INT NOT NULL,
    price DECIMAL(10,5) NOT NULL,
    
    FOREIGN KEY (category_id) REFERENCES categories(category_id);
)


-- below is for Experimental Purposes 

-- Users Table Creation
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    verification BOOLEAN NOT NULL DEFAULT 0,
    dateregistered DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Insert Guest Data
INSERT INTO users (username, password, email, verification, dateregistered)
VALUES ('guest', '', 'guest@example.com', 0, CURDATE());

--Users Verification Table
CREATE TABLE verification_token (
    id INT PRIMARY KEY,
    verification_token VARCHAR(255) NOT NULL,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE account_ip (
    id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
);
