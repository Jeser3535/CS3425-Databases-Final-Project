Based on the bookstore project, here are potential questions you might be asked:
Architecture & Design
- How is the three-layer separation implemented in your project?
- What's the purpose of db.php and what functions does it contain?
- How did you organize the code structure?
Database & Transactions
- How do database transactions ensure data integrity during checkout?
- What tables are in your database and how are they related?
- How do you handle concurrent orders with limited stock?
- What happens if stock becomes unavailable during checkout?
Security
- How does your application prevent SQL injection attacks?
- Why are you using SHA-256 for password hashing? Why not bcrypt/Argon2?
- How do you manage user sessions and prevent unauthorized access?
- Why are employees forced to change passwords on first login?
Customer Functions
- Walk through the shopping cart checkout process
- How do you verify stock levels before purchase?
- What happens when a customer tries to buy more items than available?
- How is automatic cart creation handled during registration?
- How do customers view their order history and details?
Employee Functions
- What operations can employees perform on the main dashboard?
- How do you track stock and price change history?
- What's the process for restocking products?
- How do you handle price changes and log them?
Implementation Challenges
- What was the most difficult feature to implement and why?
- How did you ensure the cart operations work correctly?
- What validation do you perform on user inputs?
- How do you handle error scenarios (database failures, insufficient stock)?
Business Logic
- Explain the transaction flow when a customer completes a purchase
- How does stock verification work before checkout?
- What's the relationship between customers, carts, products, and orders?