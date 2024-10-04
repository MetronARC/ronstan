<?php
// Turn off all error reporting
error_reporting(0);

include "../../../koneksi.php";

$sql = mysqli_query($konek, "SELECT ID, MachineID FROM area4 WHERE Status='Active' LIMIT 1 OFFSET 1");
$data = mysqli_fetch_array($sql);
$IDCount = $data["ID"];

if ($IDCount === null) {
    // If there is no second row, set $Info to some default value or handle accordingly
    $Info = 'No Data';
} else {
    $Info = $data["MachineID"];
}

echo $Info;
?>
