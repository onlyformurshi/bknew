<?php
/**
 * Load environment variables from a .env file
 */
function loadEnv($envFilePath)
{
    if (!file_exists($envFilePath)) {
        error_log("⚠️ .env file not found at: " . $envFilePath);
        return;
    }

    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, "\"'");

            // Store in environment variables
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

// Load .env file (Located in project root)
loadEnv(__DIR__ . '/../.env');
?>
