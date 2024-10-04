<?php

// koneksi ke database
include "../Dashboard/koneksi.php";

// baca data
$State = $_GET['State'];
$MachineID = $_GET['MachineID'];
$Area = $_GET['Area'];
$UID = $_GET['UID'];
$WeldID = $_GET['weldID'];

// if ($State === "firstHeartBeat") {
    // Set the timezone to Asia/Jakarta
    date_default_timezone_set('Asia/Jakarta');

    // Get the current date and time in DATETIME format
    $currentDateTime = date("Y-m-d H:i:s");

    // Check if $UID exists in the employee table
    $uidQuery = "SELECT Name FROM employee WHERE cardUID = '$UID'";
    $uidResult = mysqli_query($konek, $uidQuery);

    if ($uidResult && mysqli_num_rows($uidResult) > 0) {
        // Fetch the Name from the employee table
        $uidRow = mysqli_fetch_assoc($uidResult);
        $employeeName = $uidRow['Name'];

        // Prepare the insert query for heartBeatTable
        $insertQuery = "INSERT INTO heartBeatTable (MachineID, WeldID, Area, startBeat, Name) 
                        VALUES ('$MachineID', '$WeldID', '$Area', '$currentDateTime', '$employeeName')";

        if (mysqli_query($konek, $insertQuery)) {
            echo "New heartbeat record inserted successfully.";
        } else {
            echo "Error inserting heartbeat record: " . mysqli_error($konek);
        }
    } else {
        echo "No employee found with UID: $UID.";
    }
// } else {
//     // You can handle other states or default behavior here if needed
//     echo "State is not 'firstHeartBeat'.";
// }

// Close the database connection
mysqli_close($konek);
?>
