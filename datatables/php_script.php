<?php
// connect to your database
$conn = mysqli_connect("localhost", "root", "", "new-hus-db");

// select data from your table
$result = mysqli_query($conn, "SELECT * FROM crfdataview");

// fetch column names using the SHOW COLUMNS query
$columns = array();
$show_columns_result = mysqli_query($conn, "SHOW COLUMNS FROM crfdataview");
while ($row = mysqli_fetch_assoc($show_columns_result)) {
  $columns[] = $row['Field'];
}

// create an array to store the data
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

// return the data and column names as a JSON-encoded string
echo json_encode(array("data" => $data, "columns" => $columns));
?>
