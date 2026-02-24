<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=tpst_app;charset=utf8mb4',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Database connection successful!\n";
    
    // Test a query
    $result = $pdo->query('SELECT COUNT(*) as total FROM users')->fetch();
    echo "✅ Query test successful! Found " . $result['total'] . " users\n";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
