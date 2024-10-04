<?php
    // Koneksi ke database, checking if the code here works
    include "../Dashboard/koneksi.php";

    // Define the date range and MachineID
    $startDate = '2024-05-27';
    $endDate = '2024-05-31';
    $machineID = 'MACHINE-1';

    // Query to get the total number of rows in the table within the date range and MachineID
    $totalQuery = "SELECT COUNT(*) AS TotalRows FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate' AND MachineID = '$machineID'";
    $totalResult = mysqli_query($konek, $totalQuery);

    // Query to sum the ArcTotal within the date range and MachineID
    $sumQuery = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(ArcTotal))) AS TotalArcTime FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate' AND MachineID = '$machineID'";
    $sumResult = mysqli_query($konek, $sumQuery);

    if ($totalResult && $sumResult) {
        // Fetch the total row count
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRows = $totalRow['TotalRows'];

        // Fetch the sum of ArcTotal
        $sumRow = mysqli_fetch_assoc($sumResult);
        $totalArcTime = $sumRow['TotalArcTime'];

        echo "Total number of rows for $machineID from $startDate to $endDate: $totalRows<br>";
        echo "Total ArcTotal time for $machineID from $startDate to $endDate: $totalArcTime<br>";
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
