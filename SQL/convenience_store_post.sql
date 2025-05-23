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
);

-- category table
CREATE TABLE categories (
    category_id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Product table
CREATE TABLE products (
    product_id INT PRIMARY KEY,
    category_id INT,
    name VARCHAR(50) NOT NULL,
    stock_level INT NOT NULL,
    price DECIMAL(10,5) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Account areas 

-- functions as a trigger to auto-generate customer_id
-- by taking the max value of customer_id and adding 
-- 1 to it while also placing C in the firsts char
-- to identify it as a Customer
-- Customer Accounts Table
CREATE TABLE customer_accounts (
    customer_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL
);

DELIMITER $$
CREATE TRIGGER before_insert_customer_accounts
BEFORE INSERT ON customer_accounts
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(customer_id, 2) AS UNSIGNED)), 100) + 1 INTO next_id FROM customer_accounts;
    SET NEW.customer_id = CONCAT('C', next_id);
END$$
DELIMITER ;

-- Example insert (do NOT include customer_id)
INSERT INTO customer_accounts (name, password, phone_number, email)
VALUES ('guest_account', 'password123', '0192222', 'guest@example.com');

-- same as above but for employee accounts
-- but the character E is used to identify it as an employee
-- and the employee_id is auto-generated by taking the max value of employee_id and adding 1 to it

-- Employee Accounts Table
CREATE TABLE employee_accounts (
    employee_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Cashier', 'Manager') NOT NULL,
    store_name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

DELIMITER $$
CREATE TRIGGER before_insert_employee_accounts
BEFORE INSERT ON employee_accounts
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(employee_id, 2) AS UNSIGNED)), 0) + 1 INTO next_id FROM employee_accounts;
    SET NEW.employee_id = CONCAT('E', next_id);
END$$
DELIMITER ;
-- Example insert (do NOT include employee_id)
INSERT INTO employee_accounts (name, role, store_name, password)
VALUES 
('Administrator', 'Admin', 'Main Store', 'admin');

-- this one is view for all accounts that is recorded in the systems
CREATE VIEW all_accounts AS
SELECT customer_id AS id, name, password, phone_number, email, NULL AS role FROM customer_accounts
UNION ALL
SELECT employee_id AS id, name, password, NULL AS phone_number, NULL AS email, role FROM employee_accounts;
-- for experiemntal purposes ill use this one as a login feature for the system

-- This table is for Tracking the sales made by each employee
CREATE TABLE inventory_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    employee_id VARCHAR(10),
    quantity_change INT NOT NULL,
    change_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (employee_id) REFERENCES employee_accounts(employee_id)
);
-- Here the table for the orders that are made by customers
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id VARCHAR(10),
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,5) NOT NULL,
    discount_id INT,
    payment_type ENUM('cash','e-wallet') NOT NULL,
    order_status ENUM('pending', 'completed', 'cancelled') NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customer_accounts(customer_id),
    FOREIGN KEY (discount_id) REFERENCES discounts(discount_id)
);

-- Here for RECORDS on items that comes an go in customers usage
CREATE TABLE order_item (
    order_id INT,
    product_id INT,
    total_ammount DECIMAL(10,5) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,5) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

