-- Create database
CREATE DATABASE IF NOT EXISTS ezbooks;
USE ezbooks;

CREATE TABLE users(
    id int AUTO_INCREMENT PRIMARY KEY,
    username varchar(50) not null UNIQUE,
    phone_no varchar(10) NOT null,
    password varchar(255) not null,
    is_admin tinyint(1) DEFAULT 0
    );

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    edition VARCHAR(50),
    book_condition VARCHAR(50),
    description TEXT,
    MRP DECIMAL(10,2),
    image_path VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert admin user
INSERT INTO users (username, password, phone_no, is_admin) 
VALUES ('admin', 'admin', '1234567890', 1);

-- Insert a regular user
INSERT INTO users (username, password, phone_no, is_admin)
VALUES ('unnikkuttan', 'admin', '9876543210', 0);

-- Insert some test books with pending status
INSERT INTO books (user_id, title, author, edition, book_condition, description, MRP, price, image_path, status) VALUES
(2, 'Data Structures and Algorithms', 'Thomas H. Cormen', '3rd Edition', 'Good', 'Excellent condition, barely used', 999.00, 750.00, '../uploads/dsa.jpg', 'pending'),
(2, 'Introduction to Python', 'Mark Lutz', '2nd Edition', 'Like New', 'New book, no marks or folded pages', 599.00, 450.00, '../uploads/python.jpg', 'pending'),
(2, 'Web Development Basics', 'Jennifer Robbins', '4th Edition', 'Fair', 'Some highlighting but good condition', 799.00, 600.00, '../uploads/webdev.jpg', 'pending');