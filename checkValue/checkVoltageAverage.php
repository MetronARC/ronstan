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

    // Query to get the average of Voltage within the date range and MachineID
    $avgQuery = "SELECT AVG(CAST(Voltage AS DECIMAL(10,2))) AS AvgVoltage FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate' AND MachineID = '$machineID'";
    $avgResult = mysqli_query($konek, $avgQuery);

    if ($totalResult && $avgResult) {
        // Fetch the total row count
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRows = $totalRow['TotalRows'];

        // Fetch the average of Voltage
        $avgRow = mysqli_fetch_assoc($avgResult);
        $avgVoltage = number_format($avgRow['AvgVoltage'], 2);

        echo "Total number of rows for $machineID from $startDate to $endDate: $totalRows<br>";
        echo "Average Voltage for $machineID from $startDate to $endDate: $avgVoltage<br>";
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
