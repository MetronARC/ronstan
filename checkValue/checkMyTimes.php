<?php
    // Koneksi ke database
    include "../Dashboard/koneksi.php";

    // Prepare and execute the SQL query to update the table
    $sql = "UPDATE machinehistory1 SET ArcTotal = TIMEDIFF(ArcOff, ArcOn)";
    
    // Execute the query
    if (mysqli_query($konek, $sql)) {
        echo "Records updated successfully";
    } else {
        echo "Error updating records: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>