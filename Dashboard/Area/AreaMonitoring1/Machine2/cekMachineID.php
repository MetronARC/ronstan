<?php
// Turn off all error reporting
error_reporting(0);

include "../../../koneksi.php";

// Select the first row where Status is 'Active'
$sql = mysqli_query($konek, "SELECT * FROM area1 WHERE Status='Active' ORDER BY ID ASC LIMIT 1 OFFSET 1");
$data = mysqli_fetch_array($sql);

if ($data === null) {
    // If there is no row with Status 'Active', set $Info to some default value or handle accordingly
    $Info = 'No Data';
} else {
    // Map MachineID to corresponding values
    $machineMapping = array(
        "RONSTAN-1" => "A20-2534",
        "RONSTAN-2" => "M32-2530",
    );

    // Get the MachineID from the fetched data
    $machineID = $data["MachineID"];
    
    // Check if the MachineID is in the mapping array and echo the corresponding value
    if (array_key_exists($machineID, $machineMapping)) {
        $Info = $machineMapping[$machineID];
    } else {
        // Handle cases where the MachineID is not in the mapping array
        $Info = 'No Active Data';
    }
}

echo $Info;
?>
