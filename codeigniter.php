<?php

// Load the PHPExcel library (make sure it's installed)
require 'PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


// Define the expected headers for each Excel file
$headerMap = [
    'clinical_stool' => [
        "S.NO",
        "Name",
        "Gender M/F",
        "Specimen ID",
        "Age",
        "Enrollment ID",
        "Date of Collection",
        "Town",
        "Union Council 1)UC-4 GUJRO 2)UC-8 MANGOPIR 3)UC-5 SONGAL",
        "Stool Sample Collected In Wide Mouth Container Yes/No",
        "Stool Cary-Blair Received Yes/No",
        "FIELD SITE",
        "Time of Collection (hh:mm, 24 hr clock)",
        "Date Received (dd:mm:yy)",
        "Stool Collected By",
        "BS#",
        "Organism Isolated",
        "S=≥17,I=14-16,R=≤13",
        "S=≥18,I=13-17,R=≤12",
        "S=≥13,R=≤13",
        "S=≥16,I=11-15,R=?",
        "S=≥19,I=15-18,R=≤14",
        "S=≥21,I=16-20,R=≤15",
        "S=≥17,I=14-16,R=≤13",
        "S=≥18,I=13-17,R=≤12",
        "S=≥16,I=11-15,R=≤10",
        "S=≥23,I=20-22,R=≤19",
        "S=≥31,I=21-30,R=≤20",
        "S=≥13,I=N/A,R=≤12",
        "S=≥19,I=16-18,R=≤15",
        "S=≥23,I=20-22,R=≤19",
        "S=≥23,I=20-22,R=≤19",
        "S=≥17,I=14-16,R=≤13",
        "S=≥19,I=14-18.R=≤13",
        "S=≥16,I=11-15,R=≤10",
        "S=≥26,I=23-25,R=≤22",
        "S=≥26,I=22-25,R=≤21",
        "≥16,11-15,≤10"    
    ],
    'cholera_case' => [
        "S.No.",
        "Date" ,
        "F" ,
        "M" ,
        "Grand Total"       
    ],
    'covid_layari' => [
        "Date (DD/MM/YYYY)",
        "Negative",
        "New Reported COVID-19 Cases (within 24 hours)",
        "Samples Taken"
    ],
    'covid_gulshan' => [
        "Date (DD/MM/YYYY)",
        "Negative",
        "New Reported COVID-19 Cases (within 24 hours)",
        "Samples Taken"
    ],
    'covid_gadap' => [
        "Date (DD/MM/YYYY)",
        "Negative",
        "New Reported COVID-19 Cases (within 24 hours)",
        "Samples Taken"
    ],
    'rota_case' => [
        "PARVAAN STUDY ID",
        "Child's Sex:",
        "Did you collected the stool sample:",
        "Date of stool Collection (DD/MM/YYYY)",
        "ELISA Result"
    ],
    'enteric_fever' => [
        "Town",
        "Date of blood collection",
        "Name of Lab",
        "Species"
    ]
    // add more file headers as needed
];

// header Iterator for each file
$iteratorRow = [
    'clinical_stool' => 3,
    'cholera_case' => 4,
    'covid_layari' => 4,
    'covid_gulshan' => 4,
    'covid_gadap' => 4,
    'rota_case' => 4,
    'enteric_fever' => 4
    // add more file headers as needed
];

// Get the uploaded Excel file
$excelFile = $_FILES['excelFile']['tmp_name'];

// Get the selected file name from the dropdown menu
$selectedFile = $_POST['selectedFile'];

// Check if the selected file has a defined header map
if (!isset($headerMap[$selectedFile])) {
    die("Invalid file selected");
}

// Load the Excel file
$objPHPExcel = IOFactory::load($excelFile);

// Get the first worksheet
$worksheet = $objPHPExcel->getActiveSheet();

// Get the headers from the first row of the worksheet
$headers = $worksheet->getRowIterator($iteratorRow[$selectedFile])->current()->getCellIterator();
$headerRow = [];
foreach ($headers as $header) {
    $headerRow[] = $header->getValue();
}

// Identify the date columns by checking the first two data rows
$dateColumns = [];
for ($rowIndex = $iteratorRow[$selectedFile] + 1; $rowIndex <= $iteratorRow[$selectedFile] + 2; $rowIndex++) {
    foreach ($headerRow as $columnIndex => $header) {
        if (Date::isDateTime($worksheet->getCellByColumnAndRow($columnIndex + 1, $rowIndex))) {
            $dateColumns[] = $columnIndex;
        }
    }
}
$dateColumns = array_unique($dateColumns);

// echo "<pre>";
// echo "Hardcoded";
// var_dump($headerMap[$selectedFile]);
// echo "Provided";
// var_dump($headerRow);
// echo "</pre>";

// Check if the header row matches the expected headers for the selected file
if ($headerRow != $headerMap[$selectedFile]) {
    die("Invalid header row");
}

// Get the database column count for the selected table
$db = \Config\Database::connect();
$tableName = $selectedFile;
$query = $db->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_name = ?", [$tableName]);
$columnCount = $query->getRow()->{'COUNT(*)'};

// Get the iterator row for the selected file
$iteratorRow = isset($iteratorRow[$selectedFile]) ? $iteratorRow[$selectedFile] : 1;

// Get the data rows from the worksheet
$dataRows = $worksheet->toArray(null, false, false, true);

// Remove the header rows based on the iterator row
array_splice($dataRows, 0, $iteratorRow - 0);

// Check if the number of columns in the data rows matches the table's column count
foreach ($dataRows as $row) {
    if (count($row) != $columnCount) {
        die("Invalid column count in data row");
    }
}


// Empty the database table
$query = $db->query("TRUNCATE TABLE $selectedFile");

// Insert the data rows into the database
$duplicateIds = [];
foreach ($dataRows as $row) {
    // Prepare the SQL statement to insert the data into the database
    $placeholders = implode(',', array_fill(0, count($row), '?'));
    $sql = "INSERT INTO $tableName VALUES ($placeholders)";
    $query = $db->query($sql, array_values($row));

    // Convert and format date values
    $rowData = array_values($row);
    foreach ($dateColumns as $columnIndex) {
        if (isset($rowData[$columnIndex])) {
            $value = &$rowData[$columnIndex];
            if (is_numeric($value)) {
                $value = date('d M Y', Date::excelToTimestamp($value));
            }
        }
    }

    try {
        // Insert the row data into the database
        $query->execute($rowData);
    } catch (PDOException $e) {
        // Catch the exception for duplicate entry
        if ($e->getCode() == "23000") {
            // Get the duplicate ID
            preg_match("/Duplicate entry '(.+)' for key/i", $e->getMessage(), $matches);
            $duplicateId = $matches[1];
            $duplicateIds[] = $duplicateId;
            continue; // Skip this row and move to the next one
        } else {
            // Re-throw other exceptions
            throw $e;
        }
    }
}

if (count($duplicateIds) > 0) {
    echo "The following IDs are duplicates: " . implode(", ", $duplicateIds) . ".<br>";
}

echo "Data uploaded successfully!";
