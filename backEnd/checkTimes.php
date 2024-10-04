<?php

// koneksi ke database
include "../Dashboard/koneksi.php";

// baca data
$State = $_GET['State'];
$MachineID = $_GET['MachineID'];

if ($State === "heartBeat") {
    // Set the timezone to Asia/Jakarta
    date_default_timezone_set('Asia/Jakarta');

    // Get the current time in hours:minutes:seconds format
    $currentTime = date("H:i:s");

    // Get the current date and time in DATETIME format
    $currentDateTime = date("Y-m-d H:i:s");

    // Update the lastSeen column with the current Date and Time unconditionally
    $updateQuery = "UPDATE machine SET lastSeen = '$currentDateTime' WHERE MachineID = '$MachineID'";
    if (mysqli_query($konek, $updateQuery)) {
        echo "lastSeen updated successfully.";
    } else {
        echo "Error updating lastSeen: " . mysqli_error($konek);
    }
} else {
    // You can handle other states or default behavior here if needed
    echo "State is not 'checkTime'.";
}

// Close the database connection
mysqli_close($konek);
?>
