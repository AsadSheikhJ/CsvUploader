<?php
// MySQL database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db-name";

// Get the table name from the query string parameter
$tableName = isset($_GET['table']) ? $_GET['table'] : '';
if (!$tableName) {
    die("Table name not provided <br> 
    Method Should be the Current Url with<br>
    <h3>csvExporter.php?table='Table-Name'<h3>");
}

// Connect to the database
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

// Get the data from the selected table
$statement = $pdo->prepare("SELECT * FROM $tableName");
$statement->execute();
$dataRows = $statement->fetchAll(PDO::FETCH_ASSOC);

// Create a CSV file in memory
$output = fopen("php://memory", "w");
foreach ($dataRows as $row) {
    fputcsv($output, $row);
}
rewind($output);
$outputData = stream_get_contents($output);

// Set the HTTP response headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $tableName . '.csv"');
header('Content-Length: ' . strlen($outputData));

// Output the CSV data
echo $outputData;
