<?php
// Include your database connection file or create a new one
include "../../../koneksi.php";

// Fetch WeldID from the "area1" table (fetching from the 2nd row)
$sqlWeldID = "SELECT WeldID FROM area2 WHERE Status='Active' LIMIT 1 OFFSET 1";
$resultWeldID = $konek->query($sqlWeldID);

if (!$resultWeldID) {
    die("Error out WeldID query: " . $konek->error);
}

// Fetch the first row as an associative array
$rowWeldID = $resultWeldID->fetch_assoc();
$weldID = $rowWeldID['WeldID'];

// Use the obtained WeldID to fetch "ArcOn" from the last row in "machinehistory1"
$sqlArcOn = "SELECT ArcOn FROM machinehistory2 WHERE WeldID = '$weldID' ORDER BY id DESC LIMIT 1";
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
