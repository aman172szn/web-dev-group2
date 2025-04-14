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

-- Create function to calculate book price
DELIMITER //
CREATE FUNCTION calculate_book_price(mrp DECIMAL(10,2), book_condition ENUM('Brand New', 'Like New', 'Good', 'Fair'))
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE discount DECIMAL(4,2);
    
    CASE book_condition
        WHEN 'Brand New' THEN SET discount = 0.30;
        WHEN 'Like New' THEN SET discount = 0.30;
        WHEN 'Good' THEN SET discount = 0.35;
        WHEN 'Fair' THEN SET discount = 0.40;
        ELSE SET discount = 0.40;
    END CASE;
    
    RETURN ROUND(mrp - (mrp * discount), 2);
END //
DELIMITER ;

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    edition VARCHAR(50),
    book_condition ENUM('Brand New', 'Like New', 'Good', 'Fair') NOT NULL,
    description TEXT,
    MRP DECIMAL(10,2),
    price DECIMAL(10,2),
    image_path VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create trigger to automatically calculate price on insert
DELIMITER //
CREATE TRIGGER before_book_insert 
BEFORE INSERT ON books
FOR EACH ROW
BEGIN
    SET NEW.price = calculate_book_price(NEW.MRP, NEW.book_condition);
END //

-- Create trigger to automatically calculate price on update
CREATE TRIGGER before_book_update
BEFORE UPDATE ON books
FOR EACH ROW
BEGIN
    IF NEW.MRP != OLD.MRP OR NEW.book_condition != OLD.book_condition THEN
        SET NEW.price = calculate_book_price(NEW.MRP, NEW.book_condition);
    END IF;
END //
DELIMITER ;

INSERT INTO users (username, password, phone_no, is_admin) 
VALUES ('admin', 'admin', '1234567890', 1);

INSERT INTO users (username, password, phone_no, is_admin)
VALUES ('unnikkuttan', 'admin', '9876543210', 0);

INSERT INTO books (user_id, title, author, edition, book_condition, description, MRP, image_path, status) VALUES
(2, 'Data Structures and Algorithms', 'Thomas H. Cormen', '3rd Edition', 'Good', 'Excellent condition, barely used', 999.00, '../uploads/dsa.jpg', 'pending'),
(2, 'Introduction to Python', 'Mark Lutz', '2nd Edition', 'Like New', 'New book, no marks or folded pages', 599.00, '../uploads/python.jpg', 'pending'),
(2, 'Web Development Basics', 'Jennifer Robbins', '4th Edition', 'Fair', 'Some highlighting but good condition', 799.00, '../uploads/webdev.jpg', 'pending');