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
    status ENUM('pending', 'approved', 'rejected', 'sold') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    buyer_id INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processed', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (buyer_id) REFERENCES users(id)
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

-- Insert all users first
INSERT INTO users (username, password, phone_no, is_admin) 
VALUES ('admin', 'admin', '1234567890', 1);

-- Insert regular users
INSERT INTO users (username, password, phone_no, is_admin) VALUES
('unnikkuttan', 'admin', '9876543210', 0),
('user1', 'password123', '9876543210', 0),
('user2', 'password123', '8765432109', 0),
('user3', 'password123', '7654321098', 0),
('user4', 'password123', '6543210987', 0),
('user5', 'password123', '5432109876', 0);

-- Then insert books
INSERT INTO books (user_id, title, author, edition, book_condition, description, MRP, image_path, status) VALUES
(2, 'Data Structures and Algorithms', 'Thomas H. Cormen', '3rd Edition', 'Good', 'Excellent condition, barely used', 999.00, '../uploads/dsa.jpg', 'pending'),
(2, 'Introduction to Python', 'Mark Lutz', '2nd Edition', 'Like New', 'New book, no marks or folded pages', 599.00, '../uploads/python.jpg', 'pending'),
(2, 'Web Development Basics', 'Jennifer Robbins', '4th Edition', 'Fair', 'Some highlighting but good condition', 799.00, '../uploads/webdev.jpg', 'pending'),
(3, 'Clean Code', 'Robert C. Martin', '1st Edition', 'Brand New', 'Unopened, pristine condition', 1299.00, '../uploads/cleancode.jpg', 'approved'),
(3, 'Design Patterns', 'Erich Gamma', '1st Edition', 'Like New', 'Minimal wear, no markings', 899.00, '../uploads/designpatterns.jpg', 'approved'),
(4, 'JavaScript: The Good Parts', 'Douglas Crockford', '1st Edition', 'Good', 'Some highlighting, overall good condition', 699.00, '../uploads/javascript.jpg', 'approved'),
(4, 'Database Systems', 'Raghu Ramakrishnan', '3rd Edition', 'Fair', 'Well-used but complete', 1199.00, '../uploads/database.jpg', 'approved'),
(5, 'Operating Systems', 'Andrew Tanenbaum', '4th Edition', 'Good', 'Some notes in margins, good condition', 999.00, '../uploads/os.jpg', 'approved');

-- Finally insert orders
INSERT INTO orders (book_id, buyer_id, status) VALUES
(4, 3, 'completed'),  -- User1 bought Clean Code
(5, 4, 'pending'),    -- User2 bought Design Patterns
(6, 5, 'processed'),  -- User3 bought JavaScript: The Good Parts
(7, 3, 'completed'),  -- User1 bought Database Systems
(8, 4, 'pending');    -- User2 bought Operating Systems