-- AURA Fashion Brand E-Commerce Database Schema
-- Compatible with MySQL 5.7+ / MySQL 8.0+ / MariaDB via XAMPP & phpMyAdmin

CREATE DATABASE IF NOT EXISTS aura_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aura_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(30) NULL,
  address TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Products Table
CREATE TABLE IF NOT EXISTS products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  category VARCHAR(50) NOT NULL,
  image VARCHAR(500) DEFAULT 'assets/placeholder.svg',
  rating DECIMAL(2,1) DEFAULT 4.5,
  is_featured TINYINT(1) DEFAULT 0,
  is_new TINYINT(1) DEFAULT 0,
  stock INT DEFAULT 50,
  tag VARCHAR(50) DEFAULT 'Popular',
  style_tag VARCHAR(120) DEFAULT 'Casual, Minimal',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
  wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_wishlist (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Cart Table
CREATE TABLE IF NOT EXISTS cart (
  cart_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  size VARCHAR(10) DEFAULT 'M',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cart (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Orders Table
CREATE TABLE IF NOT EXISTS orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(190) NULL,
  phone VARCHAR(30) NOT NULL,
  shipping_address TEXT NOT NULL,
  city VARCHAR(100) NULL,
  postal_code VARCHAR(20) NULL,
  payment_method VARCHAR(100) DEFAULT 'Cash on Delivery (Demo)',
  order_status VARCHAR(50) DEFAULT 'Confirmed',
  total_amount DECIMAL(10,2) NOT NULL,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
  order_item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Demo Account (password: password123)
INSERT INTO users (user_id, username, email, password, phone, address) VALUES
(1, 'Aura Member', 'demo@aurafashion.com', '$2y$10$vPqb0q1DctbyDvjritURxufSbxJBpMX3Zg4BkVspTKJFXlwBuyTJW', '+91 98765 43210', 'Flat 402, Royale Crest, MG Road, Bangalore 560001')
ON DUPLICATE KEY UPDATE username=VALUES(username);

-- Seed Comprehensive Curated Product Catalog
INSERT INTO products (product_id, name, description, price, category, image, rating, is_featured, is_new, stock, tag, style_tag) VALUES
(1, 'Linen Everyday Shirt', 'Tailored from breathable European flax linen. Features a relaxed camp collar, mother-of-pearl buttons, and a clean chest pocket for warm-weather sophistication.', 1499.00, 'Men', 'assets/images/p1_linen_shirt.svg', 4.6, 1, 0, 42, 'Best Seller', 'Casual, Summer, Minimal'),
(2, 'Classic Tailored Blazer', 'An iconic single-breasted blazer structured in lightweight virgin wool blend. Styled with notched lapels, horn buttons, and functional flap pockets.', 3299.00, 'Women', 'assets/images/p2_tailored_blazer.svg', 4.8, 1, 0, 28, 'Signature', 'Formal, Office, Chic'),
(3, 'Essential Cotton Tee', 'Spun from 240 GSM combed organic cotton. Features a durable ribbed collar, relaxed shoulder drop, and velvety soft hand-feel designed for daily layering.', 799.00, 'Men', 'assets/images/p3_cotton_tee.svg', 4.4, 0, 1, 95, 'Wardrobe Basic', 'Casual, Streetwear, Everyday'),
(4, 'Flowy Linen Midi Dress', 'Effortlessly feminine silhouette woven in pure washed linen. Cut with delicate shoulder straps, an empire waist tie, and a breezy tiered midi hemline.', 2199.00, 'Women', 'assets/images/p4_midi_dress.svg', 4.7, 1, 0, 34, 'Bestseller', 'Brunch, Vacation, Elegant'),
(5, 'Kids Weekend Set', 'A coordinated two-piece tee and shorts ensemble made from GOTS-certified organic cotton. Elasticated drawstring waistband for playful daily comfort.', 1199.00, 'Kids', 'assets/images/p5_kids_set.svg', 4.5, 0, 1, 50, 'Eco Cotton', 'Cozy, Play, Casual'),
(6, 'Minimal Crossbody Bag', 'Architectural camera-bag silhouette handcrafted in vegetable-tanned grain leather. Finished with brushed gold hardware, adjustable strap, and card slot.', 1699.00, 'Accessories', 'assets/images/p6_crossbody_bag.svg', 4.3, 1, 0, 60, 'Trending', 'Everyday, Travel, Minimal'),
(7, 'Silk Satin Slip Dress', 'Bias-cut 19-momme mulberry silk dress that flows along the body. Delivers an understated champagne sheen with adjustable spaghetti straps for evening soirees.', 2899.00, 'Women', 'assets/images/p7_slip_dress.svg', 4.9, 1, 1, 20, 'Luxury Atelier', 'Party, Evening, Cocktail'),
(8, 'Pleated Wool Trousers', 'Precision tailored in fine olive-charcoal Italian wool blend. Features sharp front pleats, tapered legs, and side adjusters for a polished modern profile.', 2499.00, 'Men', 'assets/images/p8_wool_trousers.svg', 4.7, 0, 0, 36, 'Italian Fit', 'Formal, Office, Smart Casual'),
(9, 'Cashmere Knit Turtleneck', 'Pure Mongolian cashmere spun into an opulent ribbed turtleneck. Provides weightless warmth, ribbed trims, and timeless luxury during cooler seasons.', 3599.00, 'Women', 'assets/images/p9_cashmere_knit.svg', 4.8, 1, 1, 22, 'Pure Cashmere', 'Winter, Office, Cozy Chic'),
(10, 'Minimalist Trench Coat', 'Double-breasted trench crafted from weatherproof compact cotton gabardine. Detailed with storm flaps, raglan sleeves, and a belted waist.', 4499.00, 'Men', 'assets/images/p10_trench_coat.svg', 4.9, 1, 1, 15, 'Iconic Outerwear', 'Classic, Travel, Streetwear'),
(11, '18K Gold Plated Chunky Hoops', 'Hand-finished hollow tubular hoops electroplated in durable 18K yellow gold. Hypoallergenic, feather-light, and water-resistant for everyday glamour.', 999.00, 'Accessories', 'assets/images/p11_gold_hoops.svg', 4.6, 0, 1, 80, 'Waterproof', 'Party, Daily Luxury, Statement'),
(12, 'Handcrafted Chelsea Boots', 'Full-grain calfskin leather boots with elasticated side gussets and pull tabs. Built on a Goodyear welted rubber commando sole for grip and longevity.', 3999.00, 'Accessories', 'assets/images/p12_chelsea_boots.svg', 4.8, 0, 0, 24, 'Handmade', 'Smart Casual, Winter, Classic'),
(13, 'Embroidered Holiday Romper', 'Delicate vintage-inspired baby romper in brushed organic cotton. Decorated with heirloom floral embroidery and natural coconut shell buttons.', 1399.00, 'Kids', 'assets/images/p13_kids_romper.svg', 4.7, 0, 1, 40, 'Soft Touch', 'Festive, Weekend, Gifting'),
(14, 'Pure Mulberry Silk Scarf', 'Lustrous square silk twill scarf with hand-rolled edges. Screen-printed in minimalist architectural geometry using non-toxic mineral pigments.', 1299.00, 'Accessories', 'assets/images/p14_silk_scarf.svg', 4.5, 0, 0, 55, '100% Silk', 'Formal, Elegant, Accent'),
(15, 'Crisp Oxford Overshirt', 'Substantial 100% cotton oxford cloth overshirt with utility twin chest pockets. Wear closed as a structured shirt or open as a light jacket.', 1899.00, 'Men', 'assets/images/p15_oxford_shirt.svg', 4.6, 0, 1, 45, 'Heavy Cotton', 'Work, Layering, Minimal'),
(16, 'High-Waisted Wide Leg Trousers', 'Breezy fluid trousers with a high-rise waist and flattering wide leg cut. Features tortoiseshell statement buckle and discreet side seam pockets.', 2299.00, 'Women', 'assets/images/p16_wide_leg_pants.svg', 4.7, 0, 0, 30, 'Flowy Drape', 'Office, Chic, Summer')
ON DUPLICATE KEY UPDATE
  name=VALUES(name),
  description=VALUES(description),
  price=VALUES(price),
  category=VALUES(category),
  image=VALUES(image),
  rating=VALUES(rating),
  is_featured=VALUES(is_featured),
  is_new=VALUES(is_new),
  stock=VALUES(stock),
  tag=VALUES(tag),
  style_tag=VALUES(style_tag);

-- Seed Sample Wishlist and Cart for Demo User
INSERT INTO wishlist (user_id, product_id) VALUES
(1, 2),
(1, 6)
ON DUPLICATE KEY UPDATE created_at=CURRENT_TIMESTAMP;

INSERT INTO cart (user_id, product_id, quantity, size) VALUES
(1, 1, 1, 'L'),
(1, 11, 1, 'M')
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity);

-- Seed a completed demonstration order with items for demo user
INSERT INTO orders (order_id, user_id, customer_name, customer_email, phone, shipping_address, city, postal_code, payment_method, order_status, total_amount, order_date) VALUES
(1001, 1, 'Aura Member', 'demo@aurafashion.com', '+91 98765 43210', 'Flat 402, Royale Crest, MG Road', 'Bangalore', '560001', 'Cash on Delivery (Demo)', 'Delivered', 4798.00, DATE_SUB(NOW(), INTERVAL 3 DAY))
ON DUPLICATE KEY UPDATE total_amount=VALUES(total_amount);

INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES
(1001, 1, 1, 1499.00),
(1001, 2, 1, 3299.00)
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity);
