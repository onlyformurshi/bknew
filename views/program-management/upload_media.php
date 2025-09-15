<?php
// Start output buffering for debugging
ob_start();
echo "<pre>DEBUG INFO:\n";


// Set error reporting to maximum for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Create log file
$logFile = __DIR__ . '/upload_debug.log';
file_put_contents($logFile, "=== Upload Debug Log " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

// Helper function to log information
function debugLog($message) {
    global $logFile;
    $formattedMessage = date('H:i:s') . " - " . $message . "\n";
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    echo $formattedMessage;
}

debugLog("Script started");

try {
    // Require config file
    debugLog("Loading config");
    require '../../config/config.php';
    debugLog("Config loaded");
    
    // Check if PDO is available
    debugLog("Checking database connection");
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception("Database connection not available");
    }
    debugLog("Database connection OK");
    
    // Check request method
    debugLog("Request method: " . $_SERVER['REQUEST_METHOD']);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }
    
    // Display received POST data
    debugLog("POST data received: " . print_r($_POST, true));
    
    // Validate program ID
    $programId = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
    debugLog("Program ID: $programId");
    
    if ($programId <= 0) {
        throw new Exception("Invalid program ID: $programId");
    }
    
    // Check for files
    debugLog("FILES data: " . print_r($_FILES, true));
    
    if (!isset($_FILES['media_files']) || empty($_FILES['media_files']['name'][0])) {
        throw new Exception("No files were uploaded");
    }
    
    // Create upload directory if it doesn't exist
    // Try both paths to see which one works
    $uploadDirPaths = [
        "../../uploads/programs/",
        "../../Uploads/programs/"
    ];
    
    $uploadDir = null;
    foreach ($uploadDirPaths as $testPath) {
        debugLog("Testing upload directory: $testPath");
        $absPath = realpath(dirname(__FILE__) . '/' . $testPath);
        debugLog("Absolute path: " . ($absPath ?: "NOT FOUND"));
        
        if (file_exists($testPath)) {
            $uploadDir = $testPath;
            debugLog("Path exists: $testPath");
            break;
        }
    }
    
    if (!$uploadDir) {
        // If none of the paths exist, try to create the first one
        $uploadDir = $uploadDirPaths[0];
        debugLog("Attempting to create directory: $uploadDir");
        
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory: $uploadDir - Check permissions");
        }
        debugLog("Directory created: $uploadDir");
    }
    
    // Check if directory is writable
    debugLog("Testing if directory is writable");
    if (!is_writable($uploadDir)) {
        throw new Exception("Upload directory is not writable: $uploadDir");
    }
    debugLog("Directory is writable");
    
    $successCount = 0;
    $totalFiles = count($_FILES['media_files']['name']);
    debugLog("Total files to upload: $totalFiles");
    
    // Process each uploaded file
    for ($i = 0; $i < $totalFiles; $i++) {
        debugLog("Processing file index: $i");
        
        // Check for errors
        if ($_FILES['media_files']['error'][$i] !== UPLOAD_ERR_OK) {
            $errorCodes = [
                UPLOAD_ERR_INI_SIZE => "File exceeds upload_max_filesize directive",
                UPLOAD_ERR_FORM_SIZE => "File exceeds MAX_FILE_SIZE directive",
                UPLOAD_ERR_PARTIAL => "File was only partially uploaded",
                UPLOAD_ERR_NO_FILE => "No file was uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
                UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload"
            ];
            
            $errorMessage = $_FILES['media_files']['error'][$i];
            if (isset($errorCodes[$_FILES['media_files']['error'][$i]])) {
                $errorMessage = $errorCodes[$_FILES['media_files']['error'][$i]];
            }
            
            debugLog("Error in file $i: $errorMessage");
            continue;
        }
        
        $tmpPath = $_FILES['media_files']['tmp_name'][$i];
        $originalName = basename($_FILES['media_files']['name'][$i]);
        
        debugLog("Original file name: $originalName");
        debugLog("Temporary path: $tmpPath");
        debugLog("File exists at temp path: " . (file_exists($tmpPath) ? "YES" : "NO"));
        
        // Generate a safe filename
        $newFileName = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $originalName);
        $targetPath = $uploadDir . $newFileName;
        
        debugLog("Target path: $targetPath");
        
        // Move the uploaded file to target location
        debugLog("Attempting to move uploaded file");
        if (move_uploaded_file($tmpPath, $targetPath)) {
            debugLog("File moved successfully");
            
            try {
                // Check if program_media table exists
                debugLog("Checking if program_media table exists");
                $tableExistsQuery = $pdo->query("SHOW TABLES LIKE 'program_media'");
                $tableExists = $tableExistsQuery->rowCount() > 0;
                debugLog("Table exists: " . ($tableExists ? "YES" : "NO"));
                
                if (!$tableExists) {
                    // Create table if it doesn't exist
                    debugLog("Creating program_media table");
                    $createTableSQL = "CREATE TABLE program_media (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        program_id INT NOT NULL,
                        file_path VARCHAR(255) NOT NULL,
                        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
                    $pdo->exec($createTableSQL);
                    debugLog("Table created successfully");
                }
                
                // Save media info to database
                debugLog("Inserting record into database");
                $stmt = $pdo->prepare("INSERT INTO program_media (program_id, file_path) VALUES (?, ?)");
                debugLog("SQL: INSERT INTO program_media (program_id, file_path) VALUES ($programId, $newFileName)");
                $stmt->execute([$programId, $newFileName]);
                $successCount++;
                debugLog("Database insert successful");
            } catch (PDOException $e) {
                debugLog("DATABASE ERROR: " . $e->getMessage());
                throw new Exception("Database error: " . $e->getMessage());
            }
        } else {
            debugLog("Failed to move uploaded file");
            throw new Exception("Failed to upload $originalName - Check file permissions");
        }
    }
    
    debugLog("Successfully uploaded $successCount of $totalFiles files");
    
    if ($successCount > 0) {
    debugLog("Operation successful");
    debugLog("Redirecting to view.php");
    ob_end_clean(); // Discard debug output
    header("Location: view.php");
    exit;
} else {
    throw new Exception("No files were successfully uploaded");
}

} catch (Exception $e) {
    debugLog("ERROR: " . $e->getMessage());
    echo "\n</pre>";
    ob_end_clean(); // Discard debug output
    echo "error:" . $e->getMessage();
}

// Final log entry
debugLog("Script ended");
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
?>
