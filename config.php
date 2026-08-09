<?php
/**
 * ការកំណត់ប្រព័ន្ធ និងការតភ្ជាប់ Database (MySQL / SQLite Fallback)
 * System Configuration & Database Connection
 */

header('Content-Type: text/html; charset=utf-8');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'military_db');
define('DB_PORT', 3306);

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // ព្យាយាមតភ្ជាប់ទៅ MySQL
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4;port=" . DB_PORT;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
    } catch (PDOException $e) {
        // ប្រសិនបើគ្មាន MySQL DB ឬបង្កើតមិនទាន់បាន ព្យាយាមបង្កើត Database ជាមុន
        try {
            $rootDsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4;port=" . DB_PORT;
            $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4;port=" . DB_PORT, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $ex) {
            // ប្រសិនបើគ្មាន MySQL Server ប្រើ SQLite Fallback សម្រាប់រត់នៅលើ Local
            $sqlitePath = __DIR__ . '/military_db.sqlite';
            $pdo = new PDO("sqlite:" . $sqlitePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }

    // បង្កើត Table ប្រសិនបើមិនទាន់មាន
    initializeTables($pdo);

    return $pdo;
}

function initializeTables($pdo) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    if ($driver === 'sqlite') {
        $query = "CREATE TABLE IF NOT EXISTS military_personnel (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            manual_id VARCHAR(50),
            rank VARCHAR(100),
            surname VARCHAR(100),
            given_name VARCHAR(100),
            name_khmer VARCHAR(150),
            name_latin VARCHAR(150),
            gender VARCHAR(10) DEFAULT 'ប្រុស',
            id_card VARCHAR(50),
            position VARCHAR(150),
            unit_group VARCHAR(150),
            unit VARCHAR(150),
            rank_date DATE,
            position_date DATE,
            dob DATE,
            enlistment_date DATE,
            framework_date DATE,
            education_level VARCHAR(50),
            study_local INTEGER DEFAULT 0,
            study_abroad INTEGER DEFAULT 0,
            children_count INTEGER DEFAULT 0,
            black_card_expiry DATE,
            blue_card_expiry DATE,
            pob_village VARCHAR(100),
            pob_commune VARCHAR(100),
            pob_district VARCHAR(100),
            pob_province VARCHAR(100),
            place_of_birth TEXT,
            addr_house VARCHAR(100),
            addr_group VARCHAR(100),
            addr_village VARCHAR(100),
            addr_commune VARCHAR(100),
            addr_district VARCHAR(100),
            addr_province VARCHAR(100),
            current_address TEXT,
            marital_status VARCHAR(50),
            phone VARCHAR(50),
            notes TEXT,
            photo TEXT,
            family_photo TEXT,
            family_name VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($query);
    } else {
        $query = "CREATE TABLE IF NOT EXISTS military_personnel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            manual_id VARCHAR(50),
            rank VARCHAR(100),
            surname VARCHAR(100),
            given_name VARCHAR(100),
            name_khmer VARCHAR(150),
            name_latin VARCHAR(150),
            gender VARCHAR(10) DEFAULT 'ប្រុស',
            id_card VARCHAR(50),
            position VARCHAR(150),
            unit_group VARCHAR(150),
            unit VARCHAR(150),
            rank_date DATE,
            position_date DATE,
            dob DATE,
            enlistment_date DATE,
            framework_date DATE,
            education_level VARCHAR(50),
            study_local TINYINT DEFAULT 0,
            study_abroad TINYINT DEFAULT 0,
            children_count INT DEFAULT 0,
            black_card_expiry DATE,
            blue_card_expiry DATE,
            pob_village VARCHAR(100),
            pob_commune VARCHAR(100),
            pob_district VARCHAR(100),
            pob_province VARCHAR(100),
            place_of_birth TEXT,
            addr_house VARCHAR(100),
            addr_group VARCHAR(100),
            addr_village VARCHAR(100),
            addr_commune VARCHAR(100),
            addr_district VARCHAR(100),
            addr_province VARCHAR(100),
            current_address TEXT,
            marital_status VARCHAR(50),
            phone VARCHAR(50),
            notes TEXT,
            photo TEXT,
            family_photo TEXT,
            family_name VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_id_card (id_card),
            INDEX idx_rank (rank),
            INDEX idx_unit (unit)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($query);
    }
}
