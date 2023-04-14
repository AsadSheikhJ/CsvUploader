<?php


$table_name = $_POST['table_name'];
error_log("Table name: $table_name");
$data = get_table_data($table_name);
echo $data;
function get_table_data($table_name) {
    error_log("Inside get_table_data()");
    // Connect to database
    $db = new mysqli("localhost", "root", "", "new-hus-db");

    // Check for errors
    if ($db->connect_error) {
        error_log("Connection failed: " . $db->connect_error);
        die("Connection failed: " . $db->connect_error);
    }

    // Retrieve data from table
    $query = "SELECT * FROM $table_name";
    $result = $db->query($query);

    // Check for errors
    if (!$result) {
        error_log("Query failed: " . $db->error);
        die("Query failed: " . $db->error);
    }

    // Create array to hold table data
    $table_data = array();
    while ($row = $result->fetch_assoc()) {
        $table_data[] = $row;
    }

    // Close database connection
    $db->close();

    // Return table data as JSON string
    return json_encode($table_data);
}