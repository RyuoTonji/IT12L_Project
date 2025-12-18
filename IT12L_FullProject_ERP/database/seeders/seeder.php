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
        [1, 1, 'Chicken BBQ with Rice', 99.00, 'products/chickenrice.png', 1],
        [1, 1, 'Pork BBQ with Rice', 99.00, 'products/porkrice.png', 1],
        [1, 1, 'Beef Tapa with Rice', 120.00, 'products/taparice.png', 1],
        [1, 1, 'Sisig with Rice', 110.00, 'products/sisig.png', 1],
        
        [2, 1, 'Chicken BBQ with Rice', 99.00, 'products/chickenrice.png', 1],
        [2, 1, 'Pork BBQ with Rice', 99.00, 'products/porkrice.png', 1],
        [2, 1, 'Beef Tapa with Rice', 120.00, 'products/taparice.png', 1],
        [2, 1, 'Sisig with Rice', 110.00, 'products/sisig.png', 1],
        
        // Barbecue (category_id: 2)
        [1, 2, 'Pork Isaw (Grilled pork intestines)', 65.00, 'products/isaw.png', 1],
        [1, 2, 'Adidas (Grilled chicken feet)', 120.00, 'products/adidas.png', 1],
        [1, 2, 'Chicken Isaw (Grilled chicken intestines)', 150.00, 'products/chickenisaw.png', 1],
        
        [2, 2, 'Pork Isaw (Grilled pork intestines)', 65.00, 'products/isaw.png', 1],
        [2, 2, 'Adidas (Grilled chicken feet)', 120.00, 'products/adidas.png', 1],
        [2, 2, 'Chicken Isaw (Grilled chicken intestines)', 150.00, 'products/chickenisaw.png', 1],
        
        // Pares (category_id: 3)
        [1, 3, 'Pares Classic', 85.00, 'products/paresclassic.png', 1],
        [1, 3, 'Beef Pares (House Special)', 95.00, 'products/paresspecial.png', 1],
        [1, 3, 'Pares Overload', 110.00, 'products/overload.png', 1],
        
        [2, 3, 'Pares Classic', 85.00, 'products/paresclassic.png', 1],
        [2, 3, 'Beef Pares (House Special)', 95.00, 'products/paresspecial.png', 1],
        [2, 3, 'Pares Overload', 110.00, 'products/overload.png', 1],
        
        // Beverages (category_id: 4)
        [1, 4, 'Iced Tea', 35.00, 'products/icedtea.png', 1],
        [1, 4, 'Coke', 40.00, 'products/coke.png', 1],
        [1, 4, 'Sprite', 40.00, 'products/sprite.png', 1],
        [1, 4, 'Bottled Water', 25.00, 'products/water.png', 1],

        [2, 4, 'Iced Tea', 35.00, 'products/icedtea.png', 1],
        [2, 4, 'Coke', 40.00, 'products/coke.png', 1],
        [2, 4, 'Sprite', 40.00, 'products/sprite.png', 1],
        [2, 4, 'Bottled Water', 25.00, 'products/water.png', 1],
        
        // Sides (category_id: 5)
        [1, 5, 'French Fries', 50.00, 'products/fries.png', 1],
        [1, 5, 'Onion Rings', 60.00, 'products/onion.png', 1],
        [1, 5, 'Coleslaw', 40.00, 'products/coleslaw.png', 1],
        
        [2, 5, 'French Fries', 50.00, 'products/fries.png', 1],
        [2, 5, 'Onion Rings', 60.00, 'products/onion.png', 1],
        [2, 5, 'Coleslaw', 40.00, 'products/coleslaw.png', 1],
               
        // Desserts (category_id: 6)
        [1, 6, 'Halo-Halo', 80.00, 'products/halohalo.png', 1],
        [1, 6, 'Ice Cream Sundae', 70.00, 'products/sundae.png', 1],
        [1, 6, 'Leche Flan', 60.00, 'products/leche.png', 1],

        [2, 6, 'Halo-Halo', 80.00, 'products/halohalo.png', 1],
        [2, 6, 'Ice Cream Sundae', 70.00, 'products/sundae.png', 1],
        [2, 6, 'Leche Flan', 60.00, 'products/leche.png', 1]
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
            'name' => 'lola lol',
            'email' => 'lola@example.com',
            'password' => password_hash('lola12345', PASSWORD_BCRYPT),
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
    // SUMMARY
    // ========================================================================
    echo "================================================\n";
    echo "DATABASE SEEDING COMPLETED!\n";
    echo "================================================\n\n";
    
    echo "Sample Credentials:\n";
    echo "-------------------\n";
    echo "Admin User:\n";
    echo "  Email: admin@foodorder.com\n";
    echo "  Password: admin12345\n\n";
    
    echo "Regular Users:\n";
    echo "  Email: lola@example.com\n";
    echo "  Password: lola12345\n\n";
    
    echo "  Email: jane@example.com\n";
    echo "  Password: password123\n\n";
    
    echo "Branches: 2 branches created\n";
    echo "Categories: 6 categories created\n";
    echo "Products: " . count($products) . " products created\n";
    echo "Users: 3 users created\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>