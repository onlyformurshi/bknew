<?php
// Set error reporting to maximum for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Connection Test</h1>";

try {
    // Load configuration
    echo "<div>Loading config file...</div>";
    require '../../config/config.php';
    
    echo "<div style='color:green'>Config file loaded successfully!</div>";
    
    // Test database connection
    echo "<div>Testing database connection...</div>";
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception("Database connection not available in config");
    }
    
    echo "<div style='color:green'>Database connection successful!</div>";
    
    // Check if program_media table exists
    echo "<div>Checking if program_media table exists...</div>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<div>Tables in database: " . implode(", ", $tables) . "</div>";
    
    $tableExists = in_array('program_media', array_map('strtolower', $tables));
    
    if ($tableExists) {
        echo "<div style='color:green'>program_media table exists!</div>";
        
        // Show table structure
        echo "<div>Table structure:</div>";
        $columns = $pdo->query("DESCRIBE program_media")->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>" . print_r($columns, true) . "</pre>";
        
        // Check for any existing records
        echo "<div>Checking for existing records...</div>";
        $count = $pdo->query("SELECT COUNT(*) FROM program_media")->fetchColumn();
        echo "<div>Number of records in program_media: $count</div>";
    } else {
        echo "<div style='color:orange'>program_media table does not exist!</div>";
        echo "<div>Do you want to create it? <a href='?create=true'>Create Table</a></div>";
        
        // Create table if requested
        if (isset($_GET['create']) && $_GET['create'] === 'true') {
            echo "<div>Creating program_media table...</div>";
            $pdo->exec("CREATE TABLE program_media (
                id INT AUTO_INCREMENT PRIMARY KEY,
                program_id INT NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            echo "<div style='color:green'>Table created successfully!</div>";
        }
    }
    
    // Check upload directory
    echo "<h2>Upload Directory Test</h2>";
    $uploadDirs = [
        "../../uploads/programs/",
        "../../Uploads/programs/"
    ];
    
    foreach ($uploadDirs as $dir) {
        $realPath = realpath(dirname(__FILE__) . '/' . $dir);
        echo "<div>Testing path: $dir</div>";
        echo "<div>Absolute path: " . ($realPath ?: "NOT FOUND") . "</div>";
        
        if (file_exists($dir)) {
            echo "<div style='color:green'>Directory exists!</div>";
            if (is_writable($dir)) {
                echo "<div style='color:green'>Directory is writable!</div>";
            } else {
                echo "<div style='color:red'>Directory is NOT writable! Check permissions.</div>";
            }
        } else {
            echo "<div style='color:orange'>Directory does not exist!</div>";
            echo "<div>Do you want to create it? <a href='?createDir=" . urlencode($dir) . "'>Create Directory</a></div>";
        }
        echo "<hr>";
    }
    
    // Create directory if requested
    if (isset($_GET['createDir'])) {
        $dirToCreate = $_GET['createDir'];
        echo "<div>Attempting to create directory: $dirToCreate</div>";
        
        if (mkdir($dirToCreate, 0755, true)) {
            echo "<div style='color:green'>Directory created successfully!</div>";
        } else {
            echo "<div style='color:red'>Failed to create directory! Check permissions.</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color:red'>ERROR: " . $e->getMessage() . "</div>";
}
?>