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

// Set the default timezone to Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Check for rows with ArcCheck = '00:00:00' and delete them
$deleteArcCheckQuery = "DELETE FROM machinehistory1 WHERE ArcCheck = '00:00:00'";
if (mysqli_query($konek, $deleteArcCheckQuery)) {
    echo "Deleted rows with ArcCheck = '00:00:00'.\n";
} else {
    echo "Error deleting rows with ArcCheck = '00:00:00': " . mysqli_error($konek) . "\n";
}

// Get the current date and time
$currentDateTime = new DateTime();

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
        // SQL query to select rows from machinehistory1 where ArcOff is NULL
        $historyQuery = "SELECT * FROM machinehistory1 WHERE MachineID = '{$machineId['MachineID']}' AND ArcOff IS NULL ORDER BY ID ASC";
        $resultHistory = mysqli_query($konek, $historyQuery);

        // Check if the query was successful
        if ($resultHistory) {
            // Fetch all rows from the result set
            $rowsHistory = mysqli_fetch_all($resultHistory, MYSQLI_ASSOC);
            $countRows = count($rowsHistory);

            // Only proceed if there are 2 or more rows with ArcOff NULL
            if ($countRows >= 2) {
                // Process each row and update accordingly
                foreach ($rowsHistory as $index => $rowHistory) {
                    // Skip the latest row
                    if ($index === $countRows - 1) {
                        continue; // Skip the latest row
                    }

                    // Update ArcOff to be the same as ArcCheck
                    $arcOff = new DateTime($rowHistory['ArcCheck']);
                    $updateArcOffQuery = "UPDATE machinehistory1 SET ArcOff = '{$arcOff->format('H:i:s')}' WHERE ID = {$rowHistory['ID']}";
                    mysqli_query($konek, $updateArcOffQuery);

                    // Calculate and update ArcTotal as time difference between ArcOn and ArcOff
                    $arcOnTime = new DateTime($rowHistory['ArcOn']);
                    $arcTotalTime = $arcOnTime->diff($arcOff);
                    $arcTotalTimeFormatted = $arcTotalTime->format('%H:%I:%S');

                    $updateArcTotalQuery = "UPDATE machinehistory1 SET ArcTotal = '$arcTotalTimeFormatted' WHERE ID = {$rowHistory['ID']}";
                    mysqli_query($konek, $updateArcTotalQuery);

                    // Print confirmation message for each updated row
                    echo "Updated row with ID: " . $rowHistory['ID'] . "\n";
                }
            }
        } else {
            echo "Error fetching history for MachineID: " . $machineId['MachineID'] . ": " . mysqli_error($konek) . "\n";
        }
    }
} else {
    echo "Error fetching MachineIDs: " . mysqli_error($konek) . "\n";
}

// Continue with the rest of the original code to check lastSeen and delete records
$query = "SELECT MachineID, lastSeen FROM machine";
$result = mysqli_query($konek, $query);

// Check if the query was successful
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lastSeen = new DateTime($row['lastSeen']);
        $interval = $currentDateTime->getTimestamp() - $lastSeen->getTimestamp();
        $secondsDifference = $interval;

        // Check if the difference is more than 120 seconds
        if ($secondsDifference > 120) {
            $machineID = $row['MachineID'];

            // Delete the row from area1 table where MachineID matches
            $deleteQuery = "DELETE FROM area1 WHERE MachineID = ?";
            $stmt = $konek->prepare($deleteQuery);

            if ($stmt === false) {
                die("Error: Could not prepare statement: " . $konek->error);
            }

            $stmt->bind_param("s", $machineID);
            if ($stmt->execute() === false) {
                die("Error: Could not execute statement: " . $stmt->error);
            }

            $stmt->close();

            // Update the lastSeen value in the machine table to NULL
            $updateQuery = "UPDATE machine SET lastSeen = NULL, WeldID = WeldID + 1 WHERE MachineID = ?";
            $stmt = $konek->prepare($updateQuery);

            if ($stmt === false) {
                die("Error: Could not prepare update statement: " . $konek->error);
            }

            $stmt->bind_param("s", $machineID);
            if ($stmt->execute() === false) {
                die("Error: Could not execute update statement: " . $stmt->error);
            }

            $stmt->close();
        }
    }
} else {
    echo "No rows found in the machine table.";
}

// Prepare and execute the SQL query to update the ArcTotal column for all records
$sql = "UPDATE machinehistory1 SET ArcTotal = TIMEDIFF(ArcOff, ArcOn) WHERE ArcOff IS NOT NULL AND ArcOn IS NOT NULL";

// Execute the query
if (mysqli_query($konek, $sql)) {
    echo "ArcTotal records updated successfully.\n";
} else {
    echo "Error updating ArcTotal records: " . mysqli_error($konek) . "\n";
}

// Close the database connection
mysqli_close($konek);

?>
