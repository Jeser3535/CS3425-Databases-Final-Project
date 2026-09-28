-- delete statements
SET SQL_SAFE_UPDATES = 0;
DROP TRIGGER IF EXISTS before_product_delete;
DELETE FROM ProductHistory;
DELETE FROM OrderItems;
DELETE FROM CartItems;
DELETE FROM Orders;
DELETE FROM Carts;
DELETE FROM Products;
DELETE FROM Categories;
DELETE FROM Customers;
DELETE FROM Employees;

-- 3. INSERT SAMPLE DATA

-- Employees (FIXED: Added TRUE to the end of these two lines)
CALL create_employee(100, 'valentin', 'valentin@bookshop.com', '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08', TRUE);
CALL create_employee(101, 'jesse', 'jesse@bookshop.com', '1b4f0e9851971998e732078544c96b36c3d01cedf7caa332359d6f1d83567014', TRUE);

-- Categories (4 total - Name is still VARCHAR, so these remain strings)
CALL insert_category('Latin American Literature', 'Classic and contemporary works from Latin American authors');
CALL insert_category('Science Fiction', 'Futuristic concepts and advanced science');
CALL insert_category('Fantasy', 'Magic, mythology, and supernatural phenomena');
CALL insert_category('Mystery', 'Crime, detective fiction, and suspenseful plots');

-- Products (10 total - Changed 'P10' to 10)
CALL insert_product(10, 'Ficciones', 'Latin American Literature', 'Short stories by Jorge Luis Borges', 15.50, 10, 25, 'ficciones.jpg', 'Active');
CALL insert_product(11, 'Dune', 'Science Fiction', 'Epic science fiction novel by Frank Herbert', 20.00, 15, 50, 'dune.jpg', 'Active');
CALL insert_product(12, 'El Aleph', 'Latin American Literature', 'Short stories by Jorge Luis Borges', 14.00, 5, 0, 'aleph.jpg', 'Discontinued');
CALL insert_product(13, 'The Hobbit', 'Fantasy', 'Fantasy novel by J.R.R. Tolkien', 18.00, 20, 40, 'hobbit.jpg', 'Active');
CALL insert_product(14, '1984', 'Science Fiction', 'Dystopian social science fiction novel by George Orwell', 16.00, 12, 30, '1984.jpg', 'Active');
CALL insert_product(15, 'The City and the Dogs', 'Latin American Literature', 'Novel by Mario Vargas Llosa', 17.50, 8, 15, 'citydogs.jpg', 'Active');
CALL insert_product(16, 'The Name of the Wind', 'Fantasy', 'Heroic fantasy novel by Patrick Rothfuss', 22.00, 15, 45, 'notw.jpg', 'Active');
CALL insert_product(17, 'The Adventures of Sherlock Holmes', 'Mystery', 'Collection of twelve short stories by Arthur Conan Doyle', 12.50, 20, 60, 'sherlock.jpg', 'Active');
CALL insert_product(18, 'Murder on the Orient Express', 'Mystery', 'Detective novel by Agatha Christie', 14.99, 10, 20, 'orientexpress.jpg', 'Active');
CALL insert_product(19, 'Foundation', 'Science Fiction', 'First novel in Isaac Asimovs Foundation series', 19.00, 10, 35, 'foundation.jpg', 'Active');

-- Customers (Already correct integers)
INSERT INTO Customers (CustomerID, Username, FirstName, LastName, Email, PasswordHash, ShippingAddress) VALUES
(1, 'matute', 'Mateo', 'Rossi', 'mateo.rossi@email.com.ar', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'Av. Corrientes 1234, CABA, Argentina'),
(2, 'valen99', 'Valentina', 'Gimenez', 'valen.gimenez@email.com.ar', 'b3a8e0e1f9ab1bfe3a36f231f676f78bb30a519d2b21e6c530c0eee8ebb4a5d0', 'Calle Falsa 123, Córdoba, Argentina'),
(3, 'facu_boca', 'Facundo', 'Perez', 'facu.perez@email.com.ar', 'c1dfd96eea8cc2b62785275bca38ac261256e2781536ddb4815b09efd18105c2', 'San Martín 456, Rosario, Argentina');

-- Carts (Changed 'CART1' to 1)
INSERT INTO Carts (CartID, CustomerID) VALUES
(1, 1),
(2, 2),
(3, 3);

-- CartItems (Changed string IDs to matching integers)
INSERT INTO CartItems (CartID, ProductID, Quantity) VALUES
(1, 10, 2),
(1, 11, 1),
(2, 13, 1);

-- Orders (Changed 'ORD1001' to 1001)
INSERT INTO Orders (OrderID, CustomerID, OrderDate, Status, TotalDollars) VALUES
(1001, 1, '2023-10-01 10:30:00', 'Delivered', 51.00),
(1002, 2, '2023-10-05 14:15:00', 'Shipped', 18.00);

-- OrderItems (Changed string IDs to matching integers)
INSERT INTO OrderItems (OrderID, ProductID, Quantity, PriceAtOrder) VALUES
(1001, 10, 2, 15.50),
(1001, 11, 1, 20.00),
(1002, 13, 1, 18.00);

-- ProductHistory (Changed all string IDs to matching integers. Note: WhoID is now an INT)
INSERT INTO ProductHistory (ProductID, Action, WhoID, Timestamp, Details, OrderID) VALUES
(10, 'INSERT', 100, '2023-09-01 09:00:00', 'Initial stock added. Price: 15.50, Stock: 27', NULL),
(10, 'UPDATE', 1, '2023-10-01 10:30:00', 'Stock change: 27 -> 25', 1001),
(11, 'UPDATE', 1, '2023-10-01 10:30:00', 'Stock change: 51 -> 50', 1001),
(13, 'UPDATE', 2, '2023-10-05 14:15:00', 'Stock change: 41 -> 40', 1002),
(12, 'DELETE', 101, '2023-10-10 11:45:00', 'Marked Discontinued. Stock changed to 0', NULL),
(13, 'UPDATE', 100, '2023-10-12 08:30:00', 'Price change: 18.00 -> 19.50', NULL);

-- check the checkout procedure, by making variables for the results, and displaying it
SET @final_order_number = '';
SET @missing_product_id = '';
CALL checkout(1, @final_order_number, @missing_product_id);
SELECT @final_order_number AS 'Order Confirmation', @missing_product_id AS 'Stock Error';

SET SQL_SAFE_UPDATES = 1;

SELECT * FROM Employees;
SELECT * FROM Customers;
SELECT * FROM Categories;
SELECT * FROM Products;
SELECT * FROM Orders;
SELECT * FROM OrderItems;
SELECT * FROM Carts;
SELECT * FROM CartItems;
SELECT * FROM ProductHistory;