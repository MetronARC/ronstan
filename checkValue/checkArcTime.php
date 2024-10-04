<?php
    // Koneksi ke database
    include "../Dashboard/koneksi.php";

    // Define the date range
    $startDate = '2024-08-27';
    $endDate = '2024-08-27';

    // Query to get the total number of rows in the table within the date range
    $totalQuery = "SELECT COUNT(*) AS TotalRows FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate'";
    $totalResult = mysqli_query($konek, $totalQuery);

    // Initialize the total ArcTotal time
    $totalArcTimeSeconds = 0;

    // Query to get ArcOn and ArcOff times within the date range
    $arcQuery = "SELECT ArcOn, ArcOff FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate'";
    $arcResult = mysqli_query($konek, $arcQuery);

    if ($totalResult && $arcResult) {
        // Fetch the total row count
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRows = $totalRow['TotalRows'];

        // Calculate the total ArcTotal time
        while ($arcRow = mysqli_fetch_assoc($arcResult)) {
            $arcOn = strtotime($arcRow['ArcOn']);
            $arcOff = strtotime($arcRow['ArcOff']);

            // If ArcOff is before ArcOn, it means it spans midnight
            if ($arcOff < $arcOn) {
                $arcOff += 86400; // Add 24 hours (86400 seconds) to ArcOff
            }

            // Calculate the duration in seconds and add to total
            $totalArcTimeSeconds += ($arcOff - $arcOn);
        }

        // Convert total seconds to HH:MM:SS format
        $totalArcTime = gmdate("H:i:s", $totalArcTimeSeconds);

        echo "Total number of rows from $startDate to $endDate: $totalRows<br>";
        echo "Total ArcTotal time from $startDate to $endDate: $totalArcTime<br>";
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
