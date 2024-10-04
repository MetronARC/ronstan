<?php
    // Koneksi ke database
    include "../Dashboard/koneksi.php";

    // Define the date range
    $startDate = '2024-06-30';
    $endDate = '2024-06-30';

    // Query to get the total number of rows in the table within the date range
    $totalQuery = "SELECT COUNT(*) AS TotalRows FROM machinehistory1 WHERE `Date` BETWEEN '$startDate' AND '$endDate'";
    $totalResult = mysqli_query($konek, $totalQuery);

    // Query to get the number of rows where ArcOff is not NULL within the date range
    $successQuery = "SELECT COUNT(*) AS SuccessRows FROM machinehistory1 WHERE ArcOff IS NOT NULL AND `Date` BETWEEN '$startDate' AND '$endDate'";
    $successResult = mysqli_query($konek, $successQuery);

    if ($totalResult && $successResult) {
        // Fetch the total row count
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRows = $totalRow['TotalRows'];

        // Fetch the success row count
        $successRow = mysqli_fetch_assoc($successResult);
        $successRows = $successRow['SuccessRows'];

        // Calculate the success percentage
        if ($totalRows > 0) {
            $successPercentage = ($successRows / $totalRows) * 100;
        } else {
            $successPercentage = 0;
        }

        echo "Total number of rows from $startDate to $endDate: $totalRows<br>";
        echo "Number of successful rows (ArcOff is not NULL) from $startDate to $endDate: $successRows<br>";
        echo "Success percentage: " . number_format($successPercentage, 2) . "%";
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
