# Online Bookstore - Phase 2 Implementation

## Overview
This is a complete e-commerce web application for a bookstore, implementing all Phase 2 requirements from the instructions.txt file.

## Files Created

### Database Functions
- **db.php** - Contains all database functions for customers, employees, products, cart, and orders

### Customer Functions (130 points)
1. **register.php** - Customer registration (10 points)
2. **cust_login.php** - Customer login (5 points)
3. **cust_main.php** - Customer dashboard with password change (15 points)
4. **products.php** - Product browsing by category (10 points)
5. **cart.php** - Shopping cart with all operations (75 points)
   - Display cart items (10 points)
   - Remove items (10 points)
   - Update quantities (10 points)
   - Add more items (10 points)
   - Checkout with stock verification (35 points)
6. **orders.php** - View order history and details (10 points)
7. **Interface usability** - Clean, intuitive design (10 points)

### Employee Functions (50 points)
1. **emp_login.php** - Employee login with forced password change (10 points)
2. **emp_main.php** - Employee dashboard with all operations (40 points)
   - Restock products
   - Change product prices
   - View stock history
   - View price history
   - Insert new products (optional)

### Main Entry Points
- **index.php** - Landing page with navigation to all areas

## Setup Instructions

### 1. Database Setup
Run the SQL files in the project1b/ directory in order:
```bash
mysql -u username -p database_name < project1b/createTable.sql
mysql -u username -p database_name < project1b/createPSM.sql
mysql -u username -p database_name < project1b/insertdata.sql
```

### 2. Configure Database Connection
Edit db.php and update the database connection path:
```php
$config = parse_ini_file('/path/to/your/db.ini');
```

Create a db.ini file with your database credentials:
```ini
dsn = "mysql:host=localhost;dbname=your_database_name"
username = "your_username"
password = "your_password"
```

### 3. Web Server Setup
Place all files in your web server's document root (e.g., /var/www/html/ or use XAMPP/WAMP/MAMP)

## Test Credentials

### Customers (from insertdata.sql)
- Username: matute, Password: 123456
- Username: valen99, Password: password
- Username: facu_boca, Password: test123

### Employees (from insertdata.sql)
- Username: valentin, Password: password (must change on first login)
- Username: jesse, Password: password (must change on first login)

## Testing the Application

### Customer Functions Testing
1. **Registration**: Visit register.php and create a new account
2. **Login**: Use cust_login.php to login as a customer
3. **Browse Products**: Select a category to view products
4. **Add to Cart**: Click "Add to Cart" on products (requires login)
5. **Manage Cart**: 
   - Update quantities
   - Add more items
   - Remove items
6. **Checkout**: Verify stock levels and complete purchase
7. **View Orders**: Check order history and details

### Employee Functions Testing
1. **Login**: Use emp_login.php (password change required on first login)
2. **Restock Products**: Update product stock quantities
3. **Change Prices**: Modify product prices
4. **View History**: Check stock and price change history
5. **Insert Products**: Add new products (optional)

## Features Implemented

### Security
- Password hashing with SHA-256
- SQL injection prevention using prepared statements
- Session management for authentication
- Forced password change for employees on first login

### User Experience
- Clean, modern UI with responsive design
- Clear navigation between functions
- Self-explanatory labels and buttons
- Error messages for validation
- Success notifications

### Business Logic
- Transaction-based checkout
- Stock verification before purchase
- Automatic cart creation on registration
- Order history tracking
- Product change history logging

## Grading Requirements Coverage

### Customer Functions (130 points)
✅ Registration (10 points)
✅ Login, logout, and password change (15 points)
✅ Product browsing (10 points)
✅ View orders (10 points)
✅ Shopping cart (75 points)
  - Display items (10 points)
  - Remove items (10 points)
  - Update quantities (10 points)
  - Add more items (10 points)
  - Checkout with stock verification (35 points)
✅ Interface usability (10 points)

### Employee Functions (50 points)
✅ Login with forced password change (10 points)
✅ Main page with all operations (40 points)
  - Restock products
  - Change product prices
  - Stock history
  - Price history
  - Insert new products (optional)

## Notes
- All forms use POST method for security
- Session management ensures only authenticated users access protected pages
- Database transactions ensure data integrity during checkout
- Images are expected to be in an 'images/' subdirectory
- The application follows the three-layer separation (database, business logic, presentation) as recommended
