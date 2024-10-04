<?php
// Turn off all error reporting
error_reporting(0);

include "../../../koneksi.php";

// Select the first row where Status is 'Active'
$sql = mysqli_query($konek, "SELECT * FROM area5 WHERE Status='Active' ORDER BY ID ASC LIMIT 1");
$data = mysqli_fetch_array($sql);

if ($data === null) {
    // If there is no row with Status 'Active', set $Info to some default value or handle accordingly
    $Info = 'No Data';
} else {
    $Info = $data["MachineID"];
}

echo $Info;
?>
