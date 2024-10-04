<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// koneksi ke database
include "../koneksi.php";

// Set the default timezone to Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Get the current date and time
$currentDateTime = new DateTime();

// Query to fetch all rows from the machine table
$query = "SELECT MachineID, lastSeen FROM machine";
$result = $konek->query($query);

// Check if the query was successful
if ($result === false) {
    die("Error: Could not execute query: " . $konek->error);
}

// Check if there are rows returned
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lastSeen = new DateTime($row['lastSeen']);
        $interval = $currentDateTime->getTimestamp() - $lastSeen->getTimestamp();
        $secondsDifference = $interval;

        // Check if the difference is more than 30 seconds
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

// Close the database connection
$konek->close();

?>
