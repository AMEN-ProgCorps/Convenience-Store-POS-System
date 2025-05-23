-- Insert sample categories
INSERT INTO categories (category_id, name)
VALUES 
(1, 'Beverages'),
(2, 'Food & Snacks'),
(3, 'Personal Care'),
(4, 'Household Items'),
(5, 'Groceries'),
(6, 'Other Essentials');


-- Insert sample products
INSERT INTO products (product_id, category_id, name, stock_level, price)
VALUES 
(1, 1, 'Bottled Water', 100, 20.00), -- Price in PHP
(2, 2, 'Potato Chips', 100, 50.00),
(3, 3, 'Soap', 100, 35.00),
(4, 4, 'Dishwashing Liquid', 100, 150.00),
(5, 5, 'Rice', 100, 40.00),
(6, 6, 'Condoms', 100, 60.00);

INSERT INTO discounts (discount_id, valid_from, valid_till, description, discount_percentage)
VALUES 
(1, '2023-10-01', '2023-10-31', 'kiss muna', 10.00),
(2, '2023-11-01', '2023-11-30', 'November Discount', 15.00),
(3, '2023-12-01', '2023-12-31', 'December Discount', 20.00),
(4, '2025-01-01', '2025-01-31', 'New Year Discount', 25.00),
(5, '2025-02-01', '2025-02-29', 'Valentine Discount', 30.00),
(6, '2025-03-01', '2025-03-31', 'March Madness Discount', 35.00),
(7, '2025-04-01', '2025-04-30', 'April Discount', 40.00),
(8, '2025-05-01', '2025-05-31', 'May Discount', 45.00),
(9, '2025-06-01', '2025-06-30', 'June Discount', 50.00),
(10, '2025-07-01', '2025-07-31', 'July Discount', 55.00),
(11, '2025-08-01', '2025-08-31', 'August Discount', 60.00),
(12, '2025-09-01', '2025-09-30', 'September Discount', 65.00);