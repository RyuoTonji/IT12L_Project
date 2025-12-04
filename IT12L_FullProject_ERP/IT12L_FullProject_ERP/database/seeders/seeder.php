<?php
// database/seeder.php - Sample data seeder

require_once __DIR__ . '/../config/database.php';

try {
    echo "Starting database seeding...\n\n";

    // ========================================================================
    // 1. SEED BRANCHES
    // ========================================================================
    echo "Seeding branches...\n";
    $branches = [
        ['name' => 'BBQ Lagao Branch', 'address' => 'Lagao, Davao City', 'phone' => '(02) 1234-5678'],
        ['name' => 'BBQ Ulas Branch', 'address' => 'Ulas, Davao City', 'phone' => '(02) 8765-4321']
    ];

    $stmt = $db->prepare("INSERT INTO branches (name, address, phone) VALUES (?, ?, ?)");
    foreach ($branches as $branch) {
        $stmt->execute([$branch['name'], $branch['address'], $branch['phone']]);
    }
    echo "✓ Branches seeded successfully\n\n";

    // ========================================================================
    // 2. SEED CATEGORIES
    // ========================================================================
    echo "Seeding categories...\n";
    $categories = [
        'Rice Meals',
        'Barbecue',
        'Pares',
        'Beverages',
        'Sides',
        'Desserts'
    ];

    $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    foreach ($categories as $category) {
        $stmt->execute([$category]);
    }
    echo "✓ Categories seeded successfully\n\n";

    // ========================================================================
    // 3. SEED PRODUCTS
    // ========================================================================
    echo "Seeding products...\n";
    
    // Products for both branches (branch_id: 1 and 2)
    $products = [
        // Rice Meals (category_id: 1)
        [1, 1, 'Chicken BBQ with Rice', 99.00, null, 1],
        [1, 1, 'Pork BBQ with Rice', 99.00, null, 1],
        [1, 1, 'Beef Tapa with Rice', 120.00, null, 1],
        [1, 1, 'Sisig with Rice', 110.00, null, 1],
        
        [2, 1, 'Chicken BBQ with Rice', 99.00, null, 1],
        [2, 1, 'Pork BBQ with Rice', 99.00, null, 1],
        [2, 1, 'Beef Tapa with Rice', 120.00, null, 1],
        [2, 1, 'Sisig with Rice', 110.00, null, 1],
        
        // Barbecue (category_id: 2)
        [1, 2, 'Pork Isaw (Grilled pork intestines)', 65.00, null, 1],
        [1, 2, 'Adidas (Grilled chicken feet)', 120.00, null, 1],
        [1, 2, 'Chicken Isaw (Grilled chicken intestines)', 150.00, null, 1],
        
        [2, 2, 'Pork Isaw (Grilled pork intestines)', 65.00, null, 1],
        [2, 2, 'Adidas (Grilled chicken feet)', 120.00, null, 1],
        [2, 2, 'Chicken Isaw (Grilled chicken intestines)', 150.00, null, 1],
        
        // Pares (category_id: 3)
        [1, 3, 'Pares Classic', 85.00, null, 1],
        [1, 3, 'Beef Pares (House Special)', 95.00, null, 1],
        [1, 3, 'Pares Overload', 110.00, null, 1],
        
        [2, 3, 'Pares Classic', 85.00, null, 1],
        [2, 3, 'Beef Pares (House Special)', 95.00, null, 1],
        [2, 3, 'Pares Overload', 110.00, null, 1],
        
        // Beverages (category_id: 4)
        [1, 6, 'Iced Tea', 35.00, null, 1],
        [1, 6, 'Coke', 40.00, null, 1],
        [1, 6, 'Sprite', 40.00, null, 1],
        [1, 6, 'Bottled Water', 25.00, null, 1],
        
        [2, 6, 'Iced Tea', 35.00, null, 1],
        [2, 6, 'Coke', 40.00, null, 1],
        [2, 6, 'Sprite', 40.00, null, 1],
        [2, 6, 'Bottled Water', 25.00, null, 1],
        
        // Sides (category_id: 5)
        [1, 5, 'French Fries', 50.00, null, 1],
        [1, 5, 'Onion Rings', 60.00, null, 1],
        [1, 5, 'Coleslaw', 40.00, null, 1],
        
        [2, 5, 'French Fries', 50.00, null, 1],
        [2, 5, 'Onion Rings', 60.00, null, 1],
        [2, 5, 'Coleslaw', 40.00, null, 1],
               
        // Desserts (category_id: 6)
        [1, 7, 'Halo-Halo', 80.00, null, 1],
        [1, 7, 'Ice Cream Sundae', 70.00, null, 1],
        [1, 7, 'Leche Flan', 60.00, null, 1],
        
        [2, 7, 'Halo-Halo', 80.00, null, 1],
        [2, 7, 'Ice Cream Sundae', 70.00, null, 1],
        [2, 7, 'Leche Flan', 60.00, null, 1]
    ];

    $stmt = $db->prepare("INSERT INTO products (branch_id, category_id, name, price, image, is_available) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($products as $product) {
        $stmt->execute($product);
    }
    echo "✓ Products seeded successfully\n\n";

    // ========================================================================
    // 4. SEED SAMPLE USERS
    // ========================================================================
    echo "Seeding sample users...\n";
    $users = [
        [
            'name' => 'Admin User',
            'email' => 'admin@foodorder.com',
            'password' => password_hash('admin12345', PASSWORD_BCRYPT),
            'phone' => '09171234567'
        ],
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'phone' => '09187654321'
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'phone' => '09199876543'
        ]
    ];

    $stmt = $db->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute([$user['name'], $user['email'], $user['password'], $user['phone']]);
    }
    echo "✓ Users seeded successfully\n\n";

    // ========================================================================
    // 5. SEED SAMPLE ORDERS
    // ========================================================================
    echo "Seeding sample orders...\n";
    
    // Order 1 - John's order
    $stmt = $db->prepare("INSERT INTO orders (user_id, branch_id, total_amount, status, address, customer_name, customer_phone) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        2, // John's user_id
        1, // SM North branch
        299.00,
        'delivered',
        '123 Main St, Quezon City',
        'John Doe',
        '09187654321'
    ]);
    $orderId1 = $db->lastInsertId();

    // Order 1 items
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId1, 'Chicken BBQ with Rice', 1, 99.00]);
    $stmt->execute([$orderId1, 'Fried Chicken (2pcs)', 1, 120.00]);
    $stmt->execute([$orderId1, 'Iced Tea', 2, 35.00]);
    $stmt->execute([$orderId1, 'Halo-Halo', 1, 80.00]);

    // Order 2 - Jane's order
    $stmt = $db->prepare("INSERT INTO orders (user_id, branch_id, total_amount, status, address, customer_name, customer_phone) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        3, // Jane's user_id
        2, // Robinsons branch
        425.00,
        'confirmed',
        '456 Oak Ave, Pasig City',
        'Jane Smith',
        '09199876543'
    ]);
    $orderId2 = $db->lastInsertId();

    // Order 2 items
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId2, 'Bacon Burger', 2, 110.00]);
    $stmt->execute([$orderId2, 'French Fries', 2, 50.00]);
    $stmt->execute([$orderId2, 'Carbonara', 1, 130.00]);
    $stmt->execute([$orderId2, 'Sprite', 1, 40.00]);

    // Order 3 - John's pending order
    $stmt = $db->prepare("INSERT INTO orders (user_id, branch_id, total_amount, status, address, customer_name, customer_phone) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        2, // John's user_id
        1, // SM North branch
        170.00,
        'pending',
        '123 Main St, Quezon City',
        'John Doe',
        '09187654321'
    ]);
    $orderId3 = $db->lastInsertId();

    // Order 3 items
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->execute([$orderId3, 'Sisig with Rice', 1, 110.00]);
    $stmt->execute([$orderId3, 'Coleslaw', 1, 40.00]);
    $stmt->execute([$orderId3, 'Bottled Water', 1, 25.00]);

    echo "✓ Orders seeded successfully\n\n";

    // ========================================================================
    // SUMMARY
    // ========================================================================
    echo "================================================\n";
    echo "DATABASE SEEDING COMPLETED!\n";
    echo "================================================\n\n";
    
    echo "Sample Credentials:\n";
    echo "-------------------\n";
    echo "Admin User:\n";
    echo "  Email: admin@foodorder.com\n";
    echo "  Password: admin123\n\n";
    
    echo "Regular Users:\n";
    echo "  Email: john@example.com\n";
    echo "  Password: password123\n\n";
    
    echo "  Email: jane@example.com\n";
    echo "  Password: password123\n\n";
    
    echo "Branches: 2 branches created\n";
    echo "Categories: 7 categories created\n";
    echo "Products: " . count($products) . " products created\n";
    echo "Users: 3 users created\n";
    echo "Orders: 3 sample orders created\n\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>