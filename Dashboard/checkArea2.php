<?php
// Turn off all error reporting
error_reporting(0);

include "koneksi.php";

// Get the total number of rows in the table
$sql = mysqli_query($konek, "SELECT COUNT(*) as total_rows FROM area2 WHERE Status='Active'");
$data = mysqli_fetch_array($sql);
$total_rows = $data["total_rows"];

echo $total_rows;
?>