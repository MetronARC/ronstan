<?php
    //koneksi ke database
    include "../Dashboard/koneksi.php";

    //baca data
    $State = $_GET['State'];
    $MachineID = "MACHINE-6.10";

    // Sum the ArcTotal for the specified machineID
    $query = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(ArcTotal))) AS TotalArcTime, COUNT(*) AS RowCount FROM machinehistory1 WHERE WeldID = '$MachineID'";
    $result = mysqli_query($konek, $query);

    if ($result) {
        // Check if any rows are returned
        if (mysqli_num_rows($result) > 0) {
            // Fetch the result as an associative array
            $row = mysqli_fetch_assoc($result);

            // Retrieve the value of the total ArcTotal and row count
            $totalArcTime = $row['TotalArcTime'];
            $rowCount = $row['RowCount'];

            echo "WeldID: $MachineID<br>";
            echo "Number of rows: $rowCount<br>";
            echo "Total Arc Time: $totalArcTime";
        } else {
            echo "No records found for WeldID $MachineID";
        }
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
