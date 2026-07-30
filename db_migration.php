<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$config = config('database.connections.mysql');

if (!$config) {
    echo "❌ Database configuration for 'mysql' not found.\n";
    exit(1);
}

$host = $config['host'] ?? '127.0.0.1';
$port = $config['port'] ?? '3306';
$username = $config['username'] ?? 'root';
$password = $config['password'] ?? '';
$defaultDb = $config['database'] ?? '';

echo "==================================================\n";
echo "    DATABASE RESET & MIGRATION HELPER SCRIPT      \n";
echo "==================================================\n";
echo "Host: $host:$port\n";
echo "User: $username\n";
echo "Default DB: $defaultDb\n";
echo "==================================================\n\n";

try {
    // Connect to MySQL server (without selecting database)
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo "❌ Gagal koneksi ke MySQL: " . $e->getMessage() . "\n";
    exit(1);
}

// Get all databases
try {
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "❌ Gagal mengambil daftar database: " . $e->getMessage() . "\n";
    exit(1);
}

// Exclude system databases
$systemDbs = ['information_schema', 'mysql', 'performance_schema', 'sys'];
$userDbs = array_values(array_filter($databases, function($db) use ($systemDbs) {
    return !in_array(strtolower($db), $systemDbs);
}));

if (empty($userDbs)) {
    echo "ℹ️ Tidak ditemukan database local (di luar database sistem).\n";
} else {
    echo "Daftar database local yang ditemukan:\n";
    foreach ($userDbs as $index => $db) {
        $isDefault = ($db === $defaultDb) ? " (default di .env)" : "";
        echo "  [" . ($index + 1) . "] $db$isDefault\n";
    }
    echo "\n";
}

echo "Pilih opsi penghapusan:\n";
echo "  [1] Hapus DATABASE DEFAULT saja ($defaultDb)\n";
echo "  [2] Hapus SEMUA database local di atas\n";
echo "  [3] Hapus database spesifik berdasarkan nomor\n";
echo "  [q] Batal / Keluar\n";
echo "Pilihan Anda: ";
$choice = trim(fgets(STDIN));

if (strtolower($choice) === 'q' || empty($choice)) {
    echo "Dibatalkan.\n";
    exit(0);
}

$dbsToDrop = [];
if ($choice === '1') {
    if (in_array($defaultDb, $userDbs)) {
        $dbsToDrop[] = $defaultDb;
    } else {
        echo "Database default ($defaultDb) tidak ditemukan di MySQL local.\n";
    }
} elseif ($choice === '2') {
    $dbsToDrop = $userDbs;
} elseif (is_numeric($choice) && isset($userDbs[$choice - 1])) {
    $dbsToDrop[] = $userDbs[$choice - 1];
} else {
    echo "❌ Pilihan tidak valid.\n";
    exit(1);
}

if (empty($dbsToDrop)) {
    echo "❌ Tidak ada database yang dipilih untuk dihapus.\n";
    exit(0);
}

echo "\n⚠️ WARNING: Database berikut akan DIHAPUS PERMANEN:\n";
foreach ($dbsToDrop as $db) {
    echo "  - $db\n";
}
echo "Apakah Anda yakin? Ketik 'yakin' untuk melanjutkan: ";
$confirm = trim(fgets(STDIN));

if (strtolower($confirm) !== 'yakin') {
    echo "Dibatalkan. Tidak ada database yang dihapus.\n";
    exit(0);
}

// Drop databases
foreach ($dbsToDrop as $db) {
    try {
        echo "Menghapus database '$db'...";
        $pdo->exec("DROP DATABASE `" . str_replace("`", "``", $db) . "`");
        echo " [OK]\n";
    } catch (PDOException $e) {
        echo " [GAGAL]: " . $e->getMessage() . "\n";
    }
}

// Recreate default database
if (!empty($defaultDb)) {
    try {
        echo "Membuat kembali database default '$defaultDb'...";
        $pdo->exec("CREATE DATABASE `" . str_replace("`", "``", $defaultDb) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo " [OK]\n";
    } catch (PDOException $e) {
        echo " [GAGAL]: " . $e->getMessage() . "\n";
    }
}

// Check for SQL dumps in workspace
$sqlFiles = glob(__DIR__ . '/*.sql');
if (!empty($sqlFiles)) {
    echo "\nDitemukan file SQL dump di folder root:\n";
    $sqlFilesList = [];
    $idx = 1;
    foreach ($sqlFiles as $file) {
        $filename = basename($file);
        $size = round(filesize($file) / (1024 * 1024), 2);
        echo "  [$idx] $filename ($size MB)\n";
        $sqlFilesList[$idx] = $file;
        $idx++;
    }
    echo "  [n] Jangan import sekarang\n";
    echo "Apakah Anda ingin meng-import salah satu file SQL di atas ke database '$defaultDb'?\n";
    echo "Pilihan Anda: ";
    $importChoice = trim(fgets(STDIN));

    if (is_numeric($importChoice) && isset($sqlFilesList[$importChoice])) {
        $selectedSql = $sqlFilesList[$importChoice];
        $selectedSqlName = basename($selectedSql);
        echo "Meng-import $selectedSqlName ke '$defaultDb'...\n";
        
        try {
            // Select the database
            $pdo->exec("USE `$defaultDb`");
            
            // Disable foreign keys
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            
            // Open file
            $file = fopen($selectedSql, 'r');
            if (!$file) {
                throw new Exception("Gagal membuka file SQL.");
            }
            
            $query = '';
            $totalLines = 0;
            $queryCount = 0;
            
            // Start transaction
            $pdo->beginTransaction();
            
            while (($line = fgets($file)) !== false) {
                $trimmed = trim($line);
                if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*')) {
                    continue;
                }
                
                $query .= $line;
                
                if (str_ends_with(rtrim($trimmed), ';')) {
                    $pdo->exec($query);
                    $query = '';
                    $queryCount++;
                    
                    // Commit and restart transaction every 1000 queries to prevent lock memory issues
                    if ($queryCount % 1000 === 0) {
                        $pdo->commit();
                        $pdo->beginTransaction();
                    }
                }
                $totalLines++;
                if ($totalLines % 5000 === 0) {
                    echo "Proses baris: $totalLines...\n";
                }
            }
            
            // Commit any remaining queries
            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
            
            // Re-enable foreign keys
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            fclose($file);
            echo "✅ Sukses meng-import SQL dump! Berhasil mengeksekusi $queryCount query.\n";
            
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Re-enable foreign keys just in case
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            echo "❌ Gagal meng-import SQL dump: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Proses import dilewati.\n";
    }
} else {
    echo "\nℹ️ Tidak ditemukan file .sql di folder root untuk di-import.\n";
    echo "Anda dapat menaruh file SQL dump di folder ini lalu jalankan ulang script ini.\n";
}

echo "\nSelesai!\n";
