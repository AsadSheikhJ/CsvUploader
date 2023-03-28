<?php

// define the hard-coded header values for each file name
// $header_values = array(
  // // "clinical_stool.csv" => array(
  //   "S.NO",
  //   "Name",
  //   "Gender M/F",
  //   "Specimen ID",
  //   "Age",
  //   "Enrollment ID",
  //   "Date of Collection",
  //   "Town",
  //   "Union Council 1)UC-4 GUJRO 2)UC-8 MANGOPIR 3)UC-5 SONGAL",
  //   "Stool Sample Collected In Wide Mouth Container Yes/No",
  //   "Stool Cary-Blair Received Yes/No",
  //   "FIELD SITE",
  //   "Time of Collection (hh:mm, 24 hr clock)",
  //   "Date Received (dd:mm:yy)",
  //   "Stool Collected By",
  //   "BS#",
  //   "Organism Isolated",
  //   "(S=≥17,I=14-16,R=≤13)",
  //   "(S=≥18,I=13-17,R=≤12)",
  //   "(S=≥13,R=≤13)",
  //   "(S=≥16,I=11-15,R=?)",
  //   "(S=≥19,I=15-18,R=≤14)",
  //   "(S=≥21,I=16-20,R=≤15)",
  //   "(S=≥17,I=14-16,R=≤13)",
  //   "(S=≥18,I=13-17,R=≤12)",
  //   "(S=≥16,I=11-15,R=≤10)",
  //   "(S=≥23,I=20-22,R=≤19)",
  //   "(S=≥31,I=21-30,R=≤20)",
  //   "(S=≥13,I=N/A,R=≤12)",
  //   "(S=≥19,I=16-18,R=≤15)",
  //   "(S=≥23,I=20-22,R=≤19)",
  //   "(S=≥23,I=20-22,R=≤19)",
  //   "(S=≥17,I=14-16,R=≤13)",
  //   "(S=≥19,I=14-18.R=≤13)",
  //   "(S=≥16,I=11-15,R=≤10)",
  //   "(S=≥26,I=23-25,R=≤22)",
  //   "(S=≥26,I=22-25,R=≤21)",
  //   "(≥16,11-15,≤10)"
  // ),
//   "file2.csv" => array("HeaderA", "HeaderB", "HeaderC", "HeaderD")
// );

// Replace the values below with your own database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'upload-db';

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
    ]
    // add more file headers as needed
];

// header Iterator for each file
$iteratorRow = [
    'clinical_stool' => 3,
    'cholera_case' => 4,
    'covid_layari' => 4,
    'covid_gulshan' => 4,
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

// Load the PHPExcel library (make sure it's installed)
// require_once 'path/to/PHPExcel.php';
require 'PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Load the Excel file
// $objPHPExcel = PHPExcel_IOFactory::load($excelFile);
$objPHPExcel = IOFactory::load($excelFile);

// Get the first worksheet
$worksheet = $objPHPExcel->getActiveSheet();

// Get the headers from the first row of the worksheet
$headers = $worksheet->getRowIterator($iteratorRow[$selectedFile])->current()->getCellIterator();
$headerRow = [];
foreach ($headers as $header) {
    $headerRow[] = $header->getValue();
}

echo "<pre>";
var_dump($headerMap[$selectedFile]);
var_dump($headerRow);
echo "</pre>";

// Check if the header row matches the expected headers for the selected file
if ($headerRow != $headerMap[$selectedFile]) {
    die("Invalid header row");
}

// Get the database column count for the selected table
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$tableName = $selectedFile;
$statement = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_name = ?");
$statement->execute([$tableName]);
$columnCount = $statement->fetchColumn();

// Get the data rows from the worksheet
$dataRows = $worksheet->toArray(null, false, false, true);

array_shift($dataRows); // remove the header row
array_shift($dataRows); // remove the header row
array_shift($dataRows); // remove the header row

    // var_dump($dataRows)  ;
// Check if the number of columns in the data rows matches the table's column count
foreach ($dataRows as $row) {
    if (count($row) != $columnCount) {
        die("Invalid column count in data row");
    }
}

// Insert the data rows into the database
$duplicateIds = [];
foreach ($dataRows as $row) {
    // Prepare the SQL statement to insert the data into the database
    $placeholders = implode(',', array_fill(0, count($row), '?'));
    $sql = "INSERT INTO $tableName VALUES ($placeholders)";
    $statement = $pdo->prepare($sql);
    
    try {
        // Insert the row data into the database
        $statement->execute(array_values($row));
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

echo "Data imported successfully";



// // Define the expected headers for each Excel file
// $headerMap = [
//     'file1' => ['ColumnA', 'ColumnB', 'ColumnC'],
//     'file2' => ['ColumnA', 'ColumnB', 'ColumnC', 'ColumnD'],
//     'file3' => ['Col1', 'Col2', 'Col3', 'Col4', 'Col5'],
//     // add more file headers as needed
// ];

// // Get the uploaded Excel file
// $excelFile = $_FILES['excelFile']['tmp_name'];

// // Get the selected file name from the dropdown menu
// $selectedFile = $_POST['selectedFile'];

// // Check if the selected file has a defined header map
// if (!isset($headerMap[$selectedFile])) {
//     die("Invalid file selected");
// }

// // Load the PHPExcel library (make sure it's installed)
// // require_once 'path/to/PHPExcel.php';
// require 'PhpSpreadsheet/vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\IOFactory;

// // Load the Excel file
// // $objPHPExcel = PHPExcel_IOFactory::load($excelFile);
// $objPHPExcel = IOFactory::load($excelFile);

// // Get the first worksheet
// $worksheet = $objPHPExcel->getActiveSheet();

// // Get the headers from the first row of the worksheet
// $headers = $worksheet->getRowIterator(3)->current()->getCellIterator();
// $headerRow = [];
// foreach ($headers as $header) {
//     $headerRow[] = $header->getValue();
// }

// // echo "<pre>";
// // var_dump($headerMap[$selectedFile]);
// // var_dump($headerRow);
// // echo "</pre>";

// // Check if the header row matches the expected headers for the selected file
// if ($headerRow != $headerMap[$selectedFile]) {
//     die("Invalid header row");
// }

// // Get the database column count for the selected table
// $db = \Config\Database::connect();
// $tableName = $selectedFile;
// $query = $db->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_name = ?", [$tableName]);
// $columnCount = $query->getRow()->{'COUNT(*)'};

// // Get the data rows from the worksheet
// $dataRows = $worksheet->toArray(null, false, false, true);

// array_shift($dataRows); // remove the header row
// array_shift($dataRows); // remove the header row
// array_shift($dataRows); // remove the header row

//     // var_dump($dataRows)  ;
// // Check if the number of columns in the data rows matches the table's column count
// foreach ($dataRows as $row) {
//     if (count($row) != $columnCount) {
//         die("Invalid column count in data row");
//     }
// }

// // Insert the data rows into the database
// foreach ($dataRows as $row) {
//     // Prepare the SQL statement to insert the data into the database
//     $placeholders = implode(',', array_fill(0, count($row), '?'));
//     $sql = "INSERT INTO $tableName VALUES ($placeholders)";
//     $query = $db->query($sql, array_values($row));
// }

// echo "Data imported successfully";