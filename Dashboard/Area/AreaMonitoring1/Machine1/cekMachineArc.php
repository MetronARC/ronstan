<?php
// Include your database connection file or create a new one
include "../../../koneksi.php";

// Fetch MachineID from the "area1" table (fetching from the 1st row)
$sqlMachineID = "SELECT MachineID FROM area1 WHERE Status='Active' LIMIT 1";
$resultMachineID = $konek->query($sqlMachineID);

if (!$resultMachineID) {
    die("Error in MachineID query: " . $konek->error);
}

// Fetch the first row as an associative array
$rowMachineID = $resultMachineID->fetch_assoc();
$machineID = $rowMachineID['MachineID'];

// Use the obtained MachineID to fetch "ArcOn" from the last row in "machinehistory1"
$sqlArcOn = "SELECT ArcOn FROM machinehistory1 WHERE MachineID = '$machineID' ORDER BY id DESC LIMIT 1";
$resultArcOn = $konek->query($sqlArcOn);

if (!$resultArcOn) {
    die("Error in ArcOn query: " . $konek->error);
}

// Check if any rows were returned
if ($resultArcOn->num_rows > 0) {
    // Fetch the "ArcOn" value from the first row as an associative array
    $rowArcOn = $resultArcOn->fetch_assoc();
    $arcOnValue = $rowArcOn['ArcOn'];
    
    // Echo the ArcOn value
    echo "Latest Arc On: " . $arcOnValue;
} else {
    // No Last Arc Activities
    echo "No Last Arc Activities";
}
?>
