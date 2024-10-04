<?php

// koneksi ke database
include "../Dashboard/koneksi.php";

// baca data
$State = $_GET['State'];
$MachineID = $_GET['MachineID'];
$WeldID = $_GET['weldID'];

if ($State === "heartBeat") {
    // Set the timezone to Asia/Jakarta
    date_default_timezone_set('Asia/Jakarta');

    // Get the current time in hours:minutes:seconds format
    $currentTime = date("H:i:s");

    // Get the current date and time in DATETIME format
    $currentDateTime = date("Y-m-d H:i:s");

    // Update the lastSeen column with the current Date and Time unconditionally
    $updateHeartBeatQuery = "UPDATE heartBeatTable SET lastBeat = '$currentDateTime' WHERE WeldID = '$WeldID'";
    $updateMachineQuery = "UPDATE machine SET lastSeen = '$currentDateTime' WHERE MachineID = '$MachineID'";

    // Execute the first update query
    if (mysqli_query($konek, $updateHeartBeatQuery)) {
        echo "heartBeatTable lastBeat updated successfully.";
    } else {
        echo "Error updating heartBeatTable lastBeat: " . mysqli_error($konek);
    }

    // Execute the second update query
    if (mysqli_query($konek, $updateMachineQuery)) {
        echo "machine lastSeen updated successfully.";
    } else {
        echo "Error updating machine lastSeen: " . mysqli_error($konek);
    }
} else {
    // You can handle other states or default behavior here if needed
    echo "State is not 'heartBeat'.";
}

// Close the database connection
mysqli_close($konek);
?>
