CREATE DATABASE IF NOT EXISTS stockwise_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE stockwise_db;

CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(120) NOT NULL,
 username VARCHAR(60) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL UNIQUE,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS suppliers (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 contact_person VARCHAR(120) DEFAULT '',
 phone VARCHAR(40) DEFAULT '',
 email VARCHAR(150) DEFAULT '',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 sku VARCHAR(80) NOT NULL UNIQUE,
 category_id INT NULL,
 supplier_id INT NULL,
 price DECIMAL(10,2) NOT NULL DEFAULT 0,
 quantity INT NOT NULL DEFAULT 0,
 reorder_level INT NOT NULL DEFAULT 5,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_product_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL,
 CONSTRAINT fk_product_supplier FOREIGN KEY(supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stock_transactions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_id INT NOT NULL,
 type ENUM('IN','OUT') NOT NULL,
 quantity INT NOT NULL,
 notes VARCHAR(255) DEFAULT '',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_transaction_product FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT IGNORE INTO users(full_name,username,password_hash)
VALUES ('System Administrator','admin','$2y$12$8cq9RfIbHL8hsEg5R3RBle04vB7SvxdCaJoxgqlorEAjXz0X/cwoS');
INSERT IGNORE INTO categories(name) VALUES ('Office Supplies'),('Electronics'),('General');
INSERT IGNORE INTO suppliers(name,contact_person,phone,email)
VALUES ('Sample Supplier','Juan Dela Cruz','09170000000','supplier@example.com');
INSERT IGNORE INTO products(name,sku,category_id,supplier_id,price,quantity,reorder_level)
VALUES ('Bond Paper A4','PAP-A4-001',1,1,220.00,35,10),
('USB Flash Drive 32GB','USB-32-001',2,1,250.00,8,10),
('Ballpen Blue','PEN-BLU-001',1,1,12.50,100,20);
