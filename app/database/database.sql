CREATE DATABASE IF NOT EXISTS complaint_system
    CHARACTER SET utf8;

USE complaint_system;

--1) Users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'tech', 'admin') NOT NULL DEFAULT 'customer'
);

--2) Customer Profiles
CREATE TABLE customer_profiles (
    user_id INT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    street_address VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(2) NOT NULL,
    zip VARCHAR(5) NOT NULL,
    phone VARCHAR(10) NOT NULL,
    CONSTRAINT fk_customer_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

--3) Employee Profiles
CREATE TABLE employee_profiles (
    user_id INT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_ext VARCHAR(10) DEFAULT NULL,
    level ENUM('tech', 'admin') NOT NULL,
    CONSTRAINT fk_employee_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

--4) Products/Services
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

--5) Complaint Types
CREATE TABLE complaint_types (
    complaint_type_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

-- 6) COMPLAINTS
CREATE TABLE complaints (
    complaint_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    tech_id INT NULL,
    product_id INT NOT NULL,
    complaint_type_id INT NOT NULL,

    status ENUM('open','assigned','in_progress','resolved') NOT NULL DEFAULT 'open',
    details TEXT NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,

    CONSTRAINT fk_complaints_customer
        FOREIGN KEY (customer_id) REFERENCES users(user_id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_complaints_tech
        FOREIGN KEY (tech_id) REFERENCES users(user_id)
        ON DELETE SET NULL,

    CONSTRAINT fk_complaints_product
        FOREIGN KEY (product_id) REFERENCES products(product_id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_complaints_type
        FOREIGN KEY (complaint_type_id) REFERENCES complaint_types(complaint_type_id)
        ON DELETE RESTRICT
);

-- Example seed data: 5 products/services
INSERT INTO products (name, description) VALUES
    ('Laptop', 'Personal Portable Computer'),
    ('Cell Phone', 'Personal Cellular Phone'),
    ('House Phone', 'Shared Static Phone'),
    ('Router/Modem', 'Internet and Networking Devices'),
    ('Desktop PC', 'Static Desktop PC');

-- Example seed data: 3 complaint types
INSERT INTO complaint_types (name, description) VALUES
    ('Product Defect', 'Product arrived broken or fails under normal use'),
    ('Warranty Claim', 'Customer needs help filing or using warranty'),
    ('Billing Issue', 'Incorrect charges, refunds, or invoice problems');

--Example seed data: 3 users
INSERT INTO users (email, password_hash, role) VALUES
    ('customer@example.com', 'customerPass', 'customer'),
    ('tech@example.com', 'techPass', 'tech'),
    ('admin@example.com', 'adminPass', 'admin');

--Example seed data: 1 customer profile
INSERT INTO customer_profiles(user_id, first_name, last_name, street_address, city, state, zip, phone) VALUES
    (1, 'Kyle', 'Bentley', '123 Street Road', 'Moyock', 'NC', '12345', '7571111111');


--Example seed data: 2 Employee profile
INSERT INTO employee_profiles(user_id, first_name, last_name, phone_ext, level) VALUES
    (2, 'Ulysses', 'Burden', '7572222222', 'tech'),
    (3, 'Bryson', 'Weaver', '7573333333', 'admin');

--Example seed data: 1 complaint
INSERT INTO complaints(customer_id, tech_id, product_id, complaint_type_id, status, details) VALUES
    (1, 2, 1, 1, 'assigned', 'My laptop will not turn on!');
