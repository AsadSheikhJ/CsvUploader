<?php
// Define the expected headers for each Excel file
$headerMap = [
    'file1' => ['ColumnA', 'ColumnB', 'ColumnC'],
    'file2' => ['ColumnA', 'ColumnB', 'ColumnC', 'ColumnD'],
    'file3' => ['Col1', 'Col2', 'Col3', 'Col4', 'Col5'],
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
$headers = $worksheet->getRowIterator(3)->current()->getCellIterator();
$headerRow = [];
foreach ($headers as $header) {
    $headerRow[] = $header->getValue();
}

// echo "<pre>";
// var_dump($headerMap[$selectedFile]);
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
foreach ($dataRows as $row) {
    // Prepare the SQL statement to insert the data into the database
    $placeholders = implode(',', array_fill(0, count($row), '?'));
    $sql = "INSERT INTO $tableName VALUES ($placeholders)";
    $query = $db->query($sql, array_values($row));
}

echo "Data imported successfully";