<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection file
include "../Dashboard/koneksi.php";

// Check database connection
if (!$konek) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to select all rows from the table "area1" and retrieve the value in the column "MachineID"
$query = "SELECT MachineID FROM area1";

// Execute the query
$result = mysqli_query($konek, $query);

// Check if the query was successful
if ($result) {
    // Fetch all rows from the result set
    $machineIds = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Process each MachineID
    foreach ($machineIds as $machineId) {
        // SQL query to select rows from machinehistory1 where ArcOff is NULL, excluding the latest row with NULL in ArcOff
        $query = "SELECT * FROM machinehistory1 WHERE MachineID = '{$machineId['MachineID']}' AND ArcOff IS NULL AND ID NOT IN (SELECT MAX(ID) FROM machinehistory1 WHERE MachineID = '{$machineId['MachineID']}' AND ArcOff IS NULL)";
        
        // Execute the query
        $resultHistory = mysqli_query($konek, $query);

        // Check if the query was successful
        if ($resultHistory) {
            // Fetch all rows from the result set
            $rowsHistory = mysqli_fetch_all($resultHistory, MYSQLI_ASSOC);

            // Process each row and update accordingly
            foreach ($rowsHistory as $rowHistory) {
                // Update ArcOff to be the same as ArcCheck
                $updateQuery = "UPDATE machinehistory1 SET ArcOff = '{$rowHistory['ArcCheck']}' WHERE ID = {$rowHistory['ID']}";
                mysqli_query($konek, $updateQuery);

                // Calculate and update ArcTotal as time difference between ArcOn and ArcOff
                $arcOnTime = strtotime($rowHistory['ArcOn']);
                $arcOffTime = strtotime($rowHistory['ArcCheck']); // Use ArcCheck instead of ArcOff

                if ($arcOffTime && $arcOnTime) {
                    $arcTotalTime = $arcOffTime - $arcOnTime; // Calculate the time difference
                    $updateQuery = "UPDATE machinehistory1 SET ArcTotal = '$arcTotalTime' WHERE ID = {$rowHistory['ID']}";
                    mysqli_query($konek, $updateQuery);
                }

                // Print confirmation message for each updated row
                echo "Updated row with ID: " . $rowHistory['ID'] . "\n";
            }
        } else {
            echo "Error fetching history for MachineID: " . $machineId['MachineID'] . ": " . mysqli_error($konek) . "\n";
        }
    }
} else {
    echo "Error fetching MachineIDs: " . mysqli_error($konek) . "\n";
}

// Close the database connection
mysqli_close($konek);

?>