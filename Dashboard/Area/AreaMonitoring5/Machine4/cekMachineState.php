<?php
// Turn off all error reporting
error_reporting(0);

include "../../../koneksi.php";

// Fetch WeldID from the first row of area1 table
$sqlArea1 = mysqli_query($konek, "SELECT WeldID FROM area5 ORDER BY ID ASC LIMIT 3, 1");
$dataArea1 = mysqli_fetch_array($sqlArea1);

// Check if WeldID is empty or not found in area1 table
if (empty($dataArea1["WeldID"])) {
    // If there is no data in the first row, set status to "Inactive"
    $status = 'Inactive';
} else {
    // Fetch the most recent row from machinehistory1 table using WeldID
    $WeldID = $dataArea1["WeldID"];
    $sqlMachineHistory = mysqli_query($konek, "SELECT * FROM machinehistory5 WHERE WeldID = '$WeldID' ORDER BY ID DESC LIMIT 1");
    $dataMachineHistory = mysqli_fetch_array($sqlMachineHistory);

    if ($dataMachineHistory && $dataMachineHistory['ArcOff'] === null) {
        // If ArcOff column is NULL, set status to "Active"
        $status = 'Active';
    } else {
        // If ArcOff column is not NULL, set status to "Idle"
        $status = 'Idle';
    }
}

echo $status;
?>
