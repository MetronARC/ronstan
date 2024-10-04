<?php
// Koneksi ke database
include "../Dashboard/koneksi.php";

// SQL query to check for rows with MachineID of 'RONSTAN-2'
$query = "SELECT ID FROM machinehistory1 WHERE MachineID = 'RONSTAN-2'";

// Execute the query
$result = mysqli_query($konek, $query);

// Check if there are any results
if ($result && mysqli_num_rows($result) > 0) {
    echo "Rows with a MachineID of 'RONSTAN-2' found:<br>";
    // Fetch and delete each matching row
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['ID'];
        echo "Deleting Row ID: " . $id . "<br>";
        
        // Delete query
        $deleteQuery = "DELETE FROM machinehistory1 WHERE ID = $id";
        mysqli_query($konek, $deleteQuery);
    }
    echo "All matching rows have been deleted.";
} else {
    echo "There are no rows with a MachineID of 'RONSTAN-2'.";
}

// Close the database connection
mysqli_close($konek);
?>
