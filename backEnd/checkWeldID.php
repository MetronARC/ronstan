<?php
    // Koneksi ke database
    include "../Dashboard/koneksi.php";

    // Baca data
    $State = $_GET['State'];
    $MachineID = $_GET['MachineID'];

    // Fetch the row with the largest WeldID value for the specified MachineID
    $query = "SELECT MAX(WeldID) AS MaxWeldID FROM machine WHERE MachineID = '$MachineID'";
    $result = mysqli_query($konek, $query);

    if ($result) {
        // Check if any rows are returned
        if (mysqli_num_rows($result) > 0) {
            // Fetch the result as an associative array
            $row = mysqli_fetch_assoc($result);

            // Retrieve the value of the maximum WeldID
            $maxWeldID = $row['MaxWeldID'];
            $newWeldID = $maxWeldID + 1;

            // Output the new WeldID value
            echo $MachineID . '.' . $newWeldID;

            // Update the WeldID in the machine table
            $updateQuery = "UPDATE machine SET WeldID = $newWeldID WHERE MachineID = '$MachineID'";
            if (mysqli_query($konek, $updateQuery)) {
            } else {
                echo "Error updating WeldID: " . mysqli_error($konek);
            }
        } else {
            echo "No records found for MachineID $MachineID";
        }
    } else {
        // Handle query error
        echo "Error: " . mysqli_error($konek);
    }

    // Close the database connection
    mysqli_close($konek);
?>
