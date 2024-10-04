<?php
// Turn off all error reporting
error_reporting(0);

include "koneksi.php";

// Fetch rows from "area1" table
$area1Query = "SELECT * FROM area1";
$area1Result = $konek->query($area1Query);
$area1Rows = $area1Result->num_rows;

// Fetch rows from "area2" table
// $area2Query = "SELECT * FROM area2";
// $area2Result = $konek->query($area2Query);
// $area2Rows = $area2Result->num_rows;

// Calculate the sum of rows
$totalRows = $area1Rows;

echo "$totalRows";

// Close the connection
$konek->close();

?>