<?php
require '../../config/config.php';

require '../../helpers/security.php';
require_once '../../config/functions.php';
checkModuleAccess($pdo, 'Program Management');
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

    // Validate file type
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

    // Read and validate header
    $header = fgetcsv($handle);
    $expectedColumns = ["centre Name", "Country ID", "Coordinator ID", "Address", "City", "Phone", "Email"];
    
    if (!$header || count(array_intersect($header, $expectedColumns)) {
        die("error: Invalid CSV format! Expected columns: centre Name, Country ID, Coordinator ID, Address, City, Phone, Email");
    }

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO centres 
                              (centre_name, country_id, coordinator_id, address, city, phone, email, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                              ON DUPLICATE KEY UPDATE 
                              centre_name=VALUES(centre_name), 
                              country_id=VALUES(country_id),
                              coordinator_id=VALUES(coordinator_id),
                              address=VALUES(address),
                              city=VALUES(city),
                              phone=VALUES(phone),
                              email=VALUES(email)");

        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            // Skip empty rows
            if (count($data) < 7 || empty(trim(implode("", $data)))) {
                $skipped++;
                continue;
            }

            $centreName = trim($data[0]);
            $countryId = (int)trim($data[1]);
            $coordinatorId = (int)trim($data[2]);
            $address = trim($data[3]);
            $city = trim($data[4]);
            $phone = trim($data[5]);
            $email = trim($data[6]);
            $status = "active"; // Default status

            // Validate required fields
            if (empty($centreName) || $countryId <= 0 || empty($address) || empty($city)) {
                $skipped++;
                continue;
            }

            // Validate email format if provided
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            try {
                $stmt->execute([$centreName, $countryId, $coordinatorId, $address, $city, $phone, $email, $status]);
                
                if ($stmt->rowCount() == 1) {
                    $inserted++;
                } else {
                    $updated++;
                }
            } catch (PDOException $e) {
                $skipped++;
                error_log("Skipped row: " . $e->getMessage());
            }
        }

        fclose($handle);
        $pdo->commit();

        if ($inserted > 0 || $updated > 0) {
            echo "success: centres imported successfully! New: $inserted, Updated: $updated, Skipped: $skipped";
        } else {
            echo "error: No centres imported! Please check your CSV format. Skipped: $skipped";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("error: Database Error - " . $e->getMessage());
    }
} else {
    die("error: Invalid request!");
}
?>