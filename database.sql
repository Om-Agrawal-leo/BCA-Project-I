-- ================================
-- Food Ordering System Database
-- ================================

-- Create Database
CREATE DATABASE IF NOT EXISTS food_ordering;
USE food_ordering;

-- ================================
-- Table: users
-- Stores customer information
-- ================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- Table: categories
-- Food categories (e.g., Pizza, Burgers)
-- ================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- Table: foods
-- Food items with details
-- ================================
CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ================================
-- Table: orders
-- Customer orders with status
-- ================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    notes TEXT,
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ================================
-- Table: order_items
-- Individual items in each order
-- ================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
);

-- ================================
-- Insert Sample Categories
-- ================================
INSERT INTO categories (name, description) VALUES
('Pizza', 'Delicious Italian pizzas with various toppings'),
('Burgers', 'Juicy burgers with fresh ingredients'),
('Pasta', 'Traditional Italian pasta dishes'),
('Beverages', 'Refreshing drinks and beverages'),
('Desserts', 'Sweet treats and desserts');

-- ================================
-- Insert Sample Food Items
-- ================================
INSERT INTO foods (category_id, name, description, price, available) VALUES
(1, 'Margherita Pizza', 'Classic pizza with tomato, mozzarella, and basil', 12.99, 1),
(1, 'Pepperoni Pizza', 'Pizza topped with pepperoni slices', 14.99, 1),
(1, 'Veggie Supreme Pizza', 'Loaded with fresh vegetables', 13.99, 1),
(2, 'Classic Burger', 'Beef patty with lettuce, tomato, and cheese', 8.99, 1),
(2, 'Chicken Burger', 'Grilled chicken breast with special sauce', 9.99, 1),
(2, 'Veggie Burger', 'Plant-based patty with fresh veggies', 8.49, 1),
(3, 'Spaghetti Carbonara', 'Creamy pasta with bacon and parmesan', 11.99, 1),
(3, 'Fettuccine Alfredo', 'Rich and creamy Alfredo sauce', 10.99, 1),
(3, 'Penne Arrabbiata', 'Spicy tomato sauce with garlic', 10.49, 1),
(4, 'Coca Cola', 'Classic cola drink', 2.99, 1),
(4, 'Fresh Orange Juice', 'Freshly squeezed orange juice', 3.99, 1),
(4, 'Iced Tea', 'Refreshing iced tea', 2.49, 1),
(5, 'Chocolate Cake', 'Rich chocolate layer cake', 5.99, 1),
(5, 'Tiramisu', 'Classic Italian dessert', 6.49, 1),
(5, 'Ice Cream Sundae', 'Vanilla ice cream with toppings', 4.99, 1);

-- ================================
-- Insert Sample Customer
-- Password: customer123 (plain text for testing)
-- ================================
INSERT INTO users (name, email, phone, password) VALUES
('John Doe', 'customer@test.com', '1234567890', 'customer123'),
('Jane Smith', 'jane@test.com', '0987654321', 'password123');

-- ================================
-- Insert Sample Orders
-- ================================
INSERT INTO orders (user_id, total_amount, status, notes) VALUES
(1, 27.98, 'Pending', 'Please make it extra spicy'),
(1, 15.98, 'Cooking', 'No onions please'),
(2, 34.47, 'Ready', 'Extra cheese');

-- ================================
-- Insert Sample Order Items
-- ================================
INSERT INTO order_items (order_id, food_id, quantity, price) VALUES
(1, 1, 2, 12.99),
(1, 10, 1, 2.99),
(2, 4, 1, 8.99),
(2, 11, 2, 3.99),
(3, 2, 2, 14.99),
(3, 13, 1, 5.99);

-- ================================
-- End of SQL File
-- ================================