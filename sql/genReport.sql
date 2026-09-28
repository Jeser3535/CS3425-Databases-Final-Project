-- a) List the historic prices for a given product (e.g., P10).


-- SELECT ProductID, Timestamp, Details 
-- FROM ProductHistory 
-- WHERE ProductID = 'P10' AND Details LIKE '%Price%';

SELECT ProductID, `Timestamp`, Details
FROM ProductHistory
WHERE ProductID = 'P10'
  AND Details LIKE '%Price%'
ORDER BY `Timestamp`;

-- b) List the highest and lowest price within a given period for all products.

SELECT 
    oi.ProductID, 
    MAX(oi.PriceAtOrder) AS HighestPrice, 
    MIN(oi.PriceAtOrder) AS LowestPrice
FROM OrderItems oi
JOIN Orders o ON oi.OrderID = o.OrderID
WHERE o.OrderDate 
-- >= '2023-01-01'
  -- AND o.OrderDate <  '2024-01-01' 
BETWEEN '2023-01-01 00:00:00' AND '2023-12-31 23:59:59'
GROUP BY oi.ProductID;

-- c) List how many quantities sold for each product within a specified time frame.
SELECT 
    oi.ProductID, 
    SUM(oi.Quantity) AS TotalSold
FROM OrderItems oi
JOIN Orders o ON oi.OrderID = o.OrderID
WHERE o.OrderDate >= '2023-01-01'
  AND o.OrderDate <  '2024-01-01'
-- BETWEEN '2023-01-01 00:00:00' AND '2023-12-31 23:59:59'
GROUP BY oi.ProductID;
-- d) List products below the restocking threshold and the quantity needed to reach the threshold.

-- SELECT 
--     Name, 
--     Threshold, 
--     Stock, 
--     (Threshold - Stock) AS QuantityNeeded
-- FROM Products
-- WHERE Stock < Threshold;

SELECT 
    ProductID,
    Name, 
    Threshold, 
    Stock, 
    (Threshold - Stock) AS QuantityNeeded
FROM Products
WHERE Stock < Threshold
ORDER BY QuantityNeeded DESC;

