<?php
require '../../config/config.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Country Management');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {
    $fileTmp = $_FILES["csvFile"]["tmp_name"];
    $fileName = $_FILES["csvFile"]["name"];

    // Validate file upload error
    if ($_FILES["csvFile"]["error"] !== UPLOAD_ERR_OK) {
        die("error: File upload error (" . $_FILES["csvFile"]["error"] . ")");
    }

    // Check if file exists
    if (!file_exists($fileTmp)) {
        die("error: Uploaded file is missing!");
    }

    // Validate file type (ensure it's a CSV)
    $fileType = mime_content_type($fileTmp);
    $allowedTypes = ["text/plain", "text/csv", "application/vnd.ms-excel", "application/octet-stream"];

    if (!in_array($fileType, $allowedTypes) && pathinfo($fileName, PATHINFO_EXTENSION) !== "csv") {
        die("error: Invalid file format! Please upload a valid CSV file.");
    }

    // Open file for reading
    $handle = fopen($fileTmp, "r");
    if (!$handle) {
        die("error: Unable to open the CSV file.");
    }

    // Read the header row and validate column structure
    $header = fgetcsv($handle);
    if (!$header || count($header) < 4) {
        die("error: Invalid CSV format! Expected columns: Country Name, Country Code, Currency, Language.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO countries (country_name, country_code, currency, language, status) 
                               VALUES (?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE country_name=VALUES(country_name), currency=VALUES(currency), language=VALUES(language)");

        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Ensure the row has exactly 4 columns
            if (count($data) < 4) {
                $skipped++;
                continue;
            }

            $countryName = trim($data[0]);
            $countryCode = trim($data[1]);
            $currency = trim($data[2]);
            $language = trim($data[3]);
            $status = "active"; // Default status

            // Check if any required field is missing or empty
            if (empty($countryName) || empty($countryCode) || empty($currency) || empty($language)) {
                $skipped++;
                continue;
            }

            try {
                $stmt->execute([$countryName, $countryCode, $currency, $language, $status]);
                if ($stmt->rowCount() == 1) {
                    $inserted++; // New record
                } else {
                    $updated++; // Existing record updated
                }
            } catch (PDOException $e) {
                $skipped++; // Skip invalid rows
            }
        }

        fclose($handle);

        if ($inserted > 0 || $updated > 0) {
            echo "success: File imported successfully! New: $inserted, Updated: $updated, Skipped: $skipped";
        } else {
            echo "error: No new data inserted or updated! Skipped: $skipped";
        }
    } catch (PDOException $e) {
        die("error: Database Error - " . $e->getMessage());
    }
} else {
    die("error: Invalid request!");
}
?>
