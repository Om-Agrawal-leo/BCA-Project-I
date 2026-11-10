# Food Ordering System Documentation

This document provides a detailed overview of the Food Ordering System project, including its features, technologies used, database schema, and file structure.

## 1. Introduction

The Food Ordering System is a web-based application designed to facilitate the process of ordering food online. It provides a platform for customers to browse through a menu of available food items, place orders, and track their order status. The system also includes an admin panel for managing food items, categories, orders, and customers.

## 2. Features

### Customer-facing Features:
- User registration and login
- Browse food items by category
- Add items to the shopping cart
- Place and track orders
- View order history
- Manage user profile

### Admin Features:
- Manage food categories
- Manage food items (add, edit, delete, toggle availability)
- View and manage orders
- View and manage customers
- Generate sales reports

### Kitchen Staff Features:
- View incoming orders in real-time
- Update order status

## 3. Technologies Used

- **Frontend:** HTML, CSS, JavaScript (jQuery)
- **Backend:** PHP
- **Database:** MySQL
- **Web Server:** Apache (via XAMPP)

## 4. Database Schema

The database schema is defined in the `database/food_ordering.sql` file. The main tables include:
- `users`
- `categories`
- `foods`
- `orders`
- `order_items`
- `cart`

(A more detailed schema with relationships can be generated from the `.sql` file).

## 5. API Endpoints

The system uses a set of PHP scripts in the `/api` directory to handle asynchronous requests from the frontend. Key endpoints include:

- `api/add_to_cart.php`: Adds a food item to the user's cart.
- `api/place_order.php`: Places a new order.
- `api/get_orders.php`: Retrieves a list of orders.
- `api/get_foods.php`: Retrieves a list of food items.
- `api/update_order_status.php`: Updates the status of an order.
- ...and more.

## 6. File Structure

The project is organized into the following main directories:

- `/admin`: Contains the PHP files for the admin panel.
- `/api`: Contains the PHP files for the backend API.
- `/assets`: Holds static assets like CSS, JavaScript, images, and sounds.
- `/customer`: Contains PHP files for the customer-facing pages.
- `/database`: Contains the SQL file for the database schema.
- `/includes`: Contains common PHP files like database configuration, functions, and session management.
- `/kitchen`: Contains the interface for the kitchen staff.
- `/uploads`: Directory for uploaded food images.

Each directory is further broken down into specific files for different functionalities, as seen in the initial file listing.
