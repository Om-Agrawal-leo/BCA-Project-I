# Food Ordering System - Code Documentation

## 1. Project Overview

This document provides a detailed explanation of the Food Ordering System project, its architecture, features, and workflows. The system is a web-based application designed to facilitate online food ordering and management. It caters to three main user roles:

*   **Customers:** Can browse the menu, place orders, and track their order status.
*   **Admin:** Manages the entire system, including food items, categories, orders, and customers.
*   **Kitchen:** Views incoming orders and updates their status from "Pending" to "Ready" and "Served".

The application is built using PHP for the backend logic, MySQL for the database, and HTML, CSS, and JavaScript for the frontend.

## 2. Database Schema

The database is named `food_ordering` and consists of the following tables:

*   **`users`**: Stores information about the customers.
    *   `id`: Primary Key, Auto Increment
    *   `name`: Customer's full name
    *   `email`: Customer's email address (unique)
    *   `phone`: Customer's phone number
    *   `password`: Hashed password for authentication
    *   `created_at`: Timestamp of account creation

*   **`categories`**: Stores food categories (e.g., Pizza, Burgers, Pasta).
    *   `id`: Primary Key, Auto Increment
    *   `name`: Name of the category
    *   `description`: A brief description of the category
    *   `created_at`: Timestamp of category creation

*   **`foods`**: Stores details of the food items.
    *   `id`: Primary Key, Auto Increment
    *   `category_id`: Foreign Key referencing `categories(id)`
    *   `name`: Name of the food item
    *   `description`: A brief description of the food item
    *   `price`: Price of the food item
    *   `image`: Filename of the food item's image
    *   `available`: Boolean flag (1 for available, 0 for unavailable)
    *   `created_at`: Timestamp of food item creation

*   **`orders`**: Stores information about customer orders.
    *   `id`: Primary Key, Auto Increment
    *   `user_id`: Foreign Key referencing `users(id)`
    *   `total_amount`: The total cost of the order
    *   `status`: The current status of the order (e.g., Pending, Cooking, Ready, Served, Delivered, Cancelled)
    *   `notes`: Any special instructions from the customer
    *   `order_time`: Timestamp when the order was placed

*   **`order_items`**: Stores the individual items within each order.
    *   `id`: Primary Key, Auto Increment
    *   `order_id`: Foreign Key referencing `orders(id)`
    *   `food_id`: Foreign Key referencing `foods(id)`
    *   `quantity`: The quantity of the food item ordered
    *   `price`: The price of the food item at the time of order

## 3. Project Structure

The project is organized into the following directories:

*   **`/admin`**: Contains the PHP files for the admin panel.
*   **`/api`**: Contains the PHP files that act as API endpoints for AJAX requests.
*   **`/assets`**: Contains static files like CSS, JavaScript, images, and sounds.
*   **`/customer`**: Contains the PHP files for the customer-facing pages.
*   **`/database`**: Contains the SQL file for the database schema.
*   **`/includes`**: Contains common PHP files for configuration, functions, and session management.
*   **`/kitchen`**: Contains the PHP files for the kitchen dashboard.
*   **`/uploads`**: This directory is used to store images of food items.

## 4. Workflows

### 4.1. Customer Workflow

1.  **Registration/Login:** A new customer can create an account or log in with existing credentials.
2.  **Browse Menu:** The customer is directed to the dashboard where they can view all food items, filter by category, or search for specific items.
3.  **Add to Cart:** Customers can add desired food items to their shopping cart.
4.  **View Cart:** The customer can view their cart, update quantities, or remove items.
5.  **Checkout:** The customer proceeds to checkout, fills in their details, and places the order.
6.  **Track Order:** The customer can track the status of their order in real-time.

### 4.2. Admin Workflow

1.  **Login:** The admin logs into the admin panel.
2.  **Dashboard:** The admin dashboard displays key statistics like total sales, today's sales, total customers, and pending orders.
3.  **Manage Categories:** The admin can add, edit, and delete food categories.
4.  **Manage Food Items:** The admin can add, edit, and delete food items, including their name, description, price, and image. They can also mark items as available or unavailable.
5.  **Manage Orders:** The admin can view all orders, see their details, and update their status.
6.  **Manage Customers:** The admin can view a list of all registered customers.

### 4.3. Kitchen Workflow

1.  **View Orders:** The kitchen staff can view a real-time list of active orders (Pending, Received, Cooking, Preparing, Ready).
2.  **New Order Notification:** A sound notification is played whenever a new order is placed.
3.  **Update Status:** The kitchen staff can update the status of an order. For example, they can change the status from "Pending" to "Preparing" and then to "Ready".
4.  **Order Served:** Once the order is ready and picked up for delivery, it can be marked as "Served", which removes it from the active kitchen view.

## 5. Features in Detail

This section provides a breakdown of the key features and where to find their implementation in the code.

### 5.1. User Authentication

*   **Customer Registration:**
    *   File: `customer/register.php`
    *   Logic: Handles new user registration, password hashing, and insertion into the `users` table. (Lines: 10-30 approx)

*   **Customer Login:**
    *   File: `customer/login.php`
    *   Logic: Authenticates users by checking their email and password against the `users` table. (Lines: 10-25 approx)

*   **Session Management:**
    *   File: `includes/session.php` & `includes/config.php`
    *   Logic: Manages the user's session to keep them logged in. `is_logged_in()` function can be found in `includes/functions.php`.

### 5.2. Food and Category Management (Admin)

*   **Add/Edit/Delete Categories:**
    *   File: `admin/categories.php`
    *   Logic: Provides a form for adding and editing categories. The submission is handled by `api/add_category.php` and `api/delete_category.php`. (Lines: 100-150 for the form)

*   **Add/Edit/Delete Food Items:**
    *   File: `admin/foods.php`
    *   Logic: Provides a form for adding and editing food items. The submission is handled by `api/add_food.php` and `api/delete_food.php`. Image uploads are also handled here. (Lines: 120-200 for the form)

*   **Toggle Food Availability:**
    *   File: `admin/foods.php` (JavaScript) & `api/toggle_food_availability.php`
    *   Logic: A switch in the admin food list allows toggling the availability. An AJAX request is sent to the API endpoint to update the `available` status in the `foods` table. (JS in `admin/foods.php` Lines: 250-270 approx)

### 5.3. Customer Ordering

*   **Display Food Items:**
    *   File: `customer/dashboard.php`
    *   Logic: Fetches and displays food items from the `foods` table. Users can filter by category or use the search bar. (Lines: 250-350 approx)

*   **Add to Cart:**
    *   File: `customer/dashboard.php` (JavaScript) & `api/add_to_cart.php`
    *   Logic: The "Add to Cart" button triggers a JavaScript function that sends an AJAX request to `api/add_to_cart.php`. The cart data is stored in the `$_SESSION['cart']`. (JS in `dashboard.php` Lines: 400-420 approx)

*   **Shopping Cart Management:**
    *   File: `customer/cart.php` & `api/update_cart.php`, `api/remove_from_cart.php`
    *   Logic: Displays the items in the cart. Users can update quantities or remove items. Changes are sent via AJAX to the respective API endpoints.

*   **Place Order:**
    *   File: `customer/checkout.php` & `api/place_order.php`
    *   Logic: The checkout page collects customer information and sends it to `api/place_order.php` to create a new order in the `orders` and `order_items` tables.

*   **Order Tracking:**
    *   File: `customer/orders.php` & `customer/track.php`
    *   Logic: `orders.php` lists all past and current orders. `track.php` shows the detailed status of a specific order.

### 5.4. Kitchen Order Management

*   **Display Active Orders:**
    *   File: `kitchen/index.php`
    *   Logic: Fetches orders with status 'Pending', 'Received', 'Cooking', 'Preparing', or 'Ready' and displays them. (Lines: 5-20)

*   **Auto-Refresh and Sound Notification:**
    *   File: `kitchen/index.php` (JavaScript)
    *   Logic: A JavaScript interval fetches new orders from `kitchen/get_orders.php` every 5 seconds. If a new order is detected, a sound is played. (Lines: 200-250)

*   **Update Order Status:**
    *   File: `kitchen/index.php` (JavaScript) & `api/update_order_status.php`
    *   Logic: Buttons on each order card allow the kitchen staff to update the order's status. An AJAX request is sent to the API to update the status in the `orders` table. (JS in `kitchen/index.php` Lines: 280-310)

### 5.5. API Endpoints

The `/api` directory contains several PHP files that handle the backend logic for AJAX requests. These files are crucial for the dynamic functionality of the application. Key API files include:

*   `add_to_cart.php`: Adds an item to the session cart.
*   `place_order.php`: Saves the order to the database.
*   `update_order_status.php`: Updates the status of an order.
*   `get_foods.php`: Fetches food items for the customer dashboard.
*   `get_orders.php`: Fetches orders for the kitchen and admin panels.
