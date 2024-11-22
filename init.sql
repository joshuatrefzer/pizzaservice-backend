CREATE DATABASE IF NOT EXISTS pizza_db;

CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'app_password';

GRANT ALL PRIVILEGES ON pizza_db.* TO 'app_user'@'%';

FLUSH PRIVILEGES;

USE pizza_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    street_nr VARCHAR(255) NOT NULL,
    postal_code VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS pizza (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    image_url VARCHAR(255) NULL
);

CREATE TABLE IF NOT EXISTS ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL
);

CREATE TABLE IF NOT EXISTS pizza_ingredients (
    pizza_id INT,
    ingredient_id INT,
    PRIMARY KEY (pizza_id, ingredient_id),
    FOREIGN KEY (pizza_id) REFERENCES pizza(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price INT NOT NULL,
    extra_wish VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    pizza_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_unit INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (pizza_id) REFERENCES pizza(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_item_extras (
    order_item_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_unit INT NOT NULL,
    PRIMARY KEY (order_item_id, ingredient_id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, firstname, lastname, street, street_nr , postal_code)
VALUES 
('demo_user', 'demo_password', 'John', 'Doe', 'Pizza Street' , '123' , '77723');

INSERT INTO pizza (name, description, price, image_url)
VALUES
('Margherita', 'Classic pizza with tomato and mozzarella', 800, 'images/margherita.jpg'),
('Pepperoni', 'Tomato, mozzarella, and pepperoni', 950, 'images/pepperoni.jpg'),
('Hawaiian', 'Tomato, mozzarella, ham, and pineapple', 1000, 'images/hawaiian.jpg'),
('Vegetarian', 'Tomato, mozzarella, and mixed vegetables', 900, 'images/vegetarian.jpg'),
('BBQ Chicken', 'BBQ sauce, mozzarella, chicken, and onions', 1100, 'images/bbq_chicken.jpg'),
('Meat Lovers', 'Tomato, mozzarella, sausage, bacon, and ham', 1200, 'images/meat_lovers.jpg'),
('Four Cheese', 'Tomato, mozzarella, parmesan, gorgonzola, and cheddar', 1000, 'images/four_cheese.jpg');

INSERT INTO ingredients (name, price)
VALUES
('Extra Cheese', 100),
('Pepperoni', 150),
('Mushrooms', 120),
('Onions', 80),
('Olives', 90),
('Bacon', 200),
('Ham', 180),
('Pineapple', 150),
('Jalapenos', 100),
('Chicken', 250);
