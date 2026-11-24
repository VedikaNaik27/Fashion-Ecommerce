================================================================================
AURA — LUXURY FASHION BRAND E-COMMERCE WEBSITE
Academic Mini-Project Demonstration
================================================================================

1. PROJECT OVERVIEW
--------------------------------------------------------------------------------
AURA is a modern, minimalist luxury fashion e-commerce platform built for an
academic mini-project demonstration. It delivers a premium aesthetic using an
elegant color palette (onyx black, warm sand, ivory, and champagne gold),
clean typography, and smooth micro-interactions.

The platform is built with a pure, standard stack without third-party frameworks:
  - Frontend: HTML5, CSS3 (pure custom CSS), Vanilla JavaScript
  - Backend: PHP (8.0+ recommended)
  - Database: MySQL (InnoDB, UTF-8 utf8mb4)
  - Local Server Environment: XAMPP (Apache + MySQL)
  - Database Management: phpMyAdmin


2. SYSTEM REQUIREMENTS
--------------------------------------------------------------------------------
  - XAMPP for Windows (with Apache and MySQL installed)
  - PHP version 8.0 or higher
  - Modern web browser (Chrome, Edge, Firefox, Safari)


3. STEP-BY-STEP XAMPP SETUP INSTRUCTIONS
--------------------------------------------------------------------------------
Step 1: Move / Copy the Project Folder
  Copy the entire "fashion" folder into your XAMPP web root directory:
  Target path:
  C:\xampp\htdocs\fashion

Step 2: Start Apache and MySQL
  Open the XAMPP Control Panel and click "Start" next to both Apache and MySQL.
  Both modules should turn green.

Step 3: Import the Database in phpMyAdmin
  1. Open your browser and navigate to:
     http://localhost/phpmyadmin/
  2. Click on the "Import" tab in the top navigation bar.
  3. Under "File to import", click "Choose File" and select:
     C:\xampp\htdocs\fashion\database.sql
  4. Scroll to the bottom and click "Import" (or "Go").
  5. The script automatically creates the database "aura_db", builds all 6
     tables (users, products, wishlist, cart, orders, order_items), and seeds
     16 luxury fashion products, demo wishlists, cart items, and sample orders.

Step 4: Launch the Website
  Open your web browser and navigate to:
  http://localhost/fashion/


4. PRE-CONFIGURED DEMO ACCOUNT CREDENTIALS
--------------------------------------------------------------------------------
You can immediately sign in with the pre-seeded demonstration member account:
  - Email:    demo@aurafashion.com
  - Password: password123

Alternatively, you can test registration by creating a new account on:
  http://localhost/fashion/register.php


5. COMPREHENSIVE TESTING CHECKLIST
--------------------------------------------------------------------------------
Verify each of the following workflows during project evaluation:

[✓] 1. User Registration:
       Go to register.php. Register a new name, email, and password (min 8 chars).
       Verifies duplicate email detection, hashes password using password_hash(),
       and logs the user in immediately.

[✓] 2. User Login & Logout:
       Go to login.php. Enter credentials. Verifies password_verify(), session
       regeneration, and redirects to intended page. Logout cleans up session.

[✓] 3. Homepage & Editorial Navigation:
       View index.php. Check hero banner, brand value propositions, 4 category cards
       (Women, Men, Kids, Accessories), featured products, and new arrivals.

[✓] 4. Product Catalog, Searching & Filtering:
       Go to products.php.
       - Filter by category pills (Women, Men, Kids, Accessories).
       - Filter by price range (Under ₹1,000, ₹1,000-₹2,500, Above ₹2,500).
       - Search for keywords (e.g., "linen", "blazer", "silk", "wool").
       - Sort by Newest, Price Low to High, Price High to Low, or Highest Rated.

[✓] 5. Product Details View:
       Click on any product card or visit product_details.php?id=1.
       Review product specifications, size selector pills (XS, S, M, L, XL),
       quantity counter (- / +), and related product recommendations.

[✓] 6. Wishlist Management:
       Tap the heart icon (♡) on any product card or details page.
       Visit wishlist.php to review saved garments. Test "Move to Shopping Bag"
       or "Remove from Wishlist".

[✓] 7. Shopping Bag & Live Calculations:
       Visit cart.php. Change quantities, check live line totals and subtotal.
       Notice complimentary shipping milestone indicator (Free over ₹1,999).
       Apply test promotional coupon code "AURA10" for an instant 10% discount.

[✓] 8. Checkout & Order Placement:
       Proceed from cart to checkout.php. Fill in delivery destination,
       choose simulated payment option (Cash on Delivery / UPI / Card),
       and submit. Database transaction creates order and order items.

[✓] 9. Order Confirmation Receipt:
       After placing an order, review order_confirmation.php with generated
       order ID, customer shipping address, date stamp, and item breakdown.

[✓] 10. Order History:
       Navigate to orders.php. Review all past orders placed by the logged-in
       user, including order status badges and individual product thumbnails.

[✓] 11. AI Stylist Interactive Studio (Demo):
       Navigate to stylist.php. Choose occasion (Office, Gala, Brunch, Travel),
       wardrobe department, and palette. Generates a curated 3-piece capsule
       directly from the MySQL database with stylist notes and one-click bag add.

[✓] 12. Virtual Try-On Studio (Demo):
       Navigate to tryon.php. Switch between studio mannequins (female/male)
       or upload a custom photo from device. Select any catalog garment and adjust
       scale, vertical alignment, and fabric opacity in real-time.

[✓] 13. Mobile Responsiveness:
       Resize browser to mobile/tablet width or inspect device modes (375px,
       768px, 1024px). Test the mobile hamburger slide-out navigation drawer.


6. TRANSPARENT DEMONSTRATION DISCLOSURES
--------------------------------------------------------------------------------
In accordance with academic integrity guidelines:
  1. Checkout Simulation:
     Checkout records the order in the database and clears the cart, but does
     NOT connect to a commercial payment gateway or debit real credit cards.
  2. AI Stylist Demo:
     The recommendation engine uses rule-based catalog matching based on item
     categories, style tags, and occasion algorithms. No commercial AI API
     is billed.
  3. Virtual Try-On Demo:
     The virtual fitting room is an interactive client-side visual preview tool
     allowing photo uploads, mannequin switches, scale, and opacity adjustments.
     Photorealistic 3D cloth physics and AR are not claimed.


7. PROJECT STRUCTURE & MODIFIED FILES
--------------------------------------------------------------------------------
fashion/
│
├── config/
│   └── db.php                  Centralized PDO MySQL connection & output buffer
│
├── includes/
│   ├── header.php              Sticky navigation bar, live counters, mobile drawer
│   ├── footer.php              4-column luxury footer & demo disclaimers
│   └── functions.php           Security helpers, CSRF, flash messages, escaping
│
├── assets/
│   ├── style.css               Pure CSS3 luxury styling (responsive, zero frameworks)
│   ├── app.js                  Mobile nav, toasts, promo code, Try-On engine
│   ├── placeholder.svg         Fallback SVG graphic
│   └── images/                 16 custom luxury SVG fashion product illustrations,
│                               category covers, hero art, and try-on models
│
├── index.php                   Homepage with hero, value props, featured, new arrivals
├── products.php                Dynamic catalog with search, category filters, sorting
├── product_details.php         Full product details view, size pills, related items
├── cart.php                    Shopping bag with quantity controls & discount code
├── cart_action.php             Cart mutations with CSRF validation
├── wishlist.php                Saved items portal with move-to-cart actions
├── wishlist_action.php         Wishlist mutations with CSRF validation
├── checkout.php                Checkout address form & transaction order placement
├── order_confirmation.php      Detailed order confirmation receipt
├── orders.php                  Itemized past order history
├── login.php                   Secure login with password_verify & session regen
├── register.php                Registration with password_hash & duplicate prevention
├── logout.php                  Safe session termination
├── stylist.php                 Interactive AI Stylist recommendation studio (Demo)
├── tryon.php                   Virtual Try-On room with photo preview & sliders (Demo)
├── database.sql                MySQL schema with 6 tables and 16 seeded products
└── README.txt                  Execution guide and documentation
================================================================================
