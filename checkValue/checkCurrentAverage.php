<?php
    // Koneksi ke database
    include "../Dashboard/koneksi.php";

    // Define the date range and MachineID
    $startDate = '2024-06-30';
    $endDate = '2024-06-30';
    $machineID = 'MACHINE-3';

    // Query to get the total number of rows in the table within the date range and MachineID
    $totalQuery = "SELECT COUNT(*) AS TotalRows FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate' AND MachineID = '$machineID'";
    $totalResult = mysqli_query($konek, $totalQuery);

    // Query to get the average of CurrentDC within the date range and MachineID
    $avgQuery = "SELECT AVG(CAST(CurrentDC AS DECIMAL(10,2))) AS AvgCurrentDC FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate' AND MachineID = '$machineID'";
    $avgResult = mysqli_query($konek, $avgQuery);

    if ($totalResult && $avgResult) {
        // Fetch the total row count
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRows = $totalRow['TotalRows'];

        // Fetch the average of CurrentDC
        $avgRow = mysqli_fetch_assoc($avgResult);
        $avgCurrentDC = number_format($avgRow['AvgCurrentDC'], 2);

        echo "Total number of rows for $machineID from $startDate to $endDate: $totalRows<br>";
        echo "Average CurrentDC for $machineID from $startDate to $endDate: $avgCurrentDC<br>";
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
