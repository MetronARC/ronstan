<?php

// koneksi ke database
include "../Dashboard/koneksi.php";

// Check if connection was successful
if (!$konek) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to select all rows with the Date value of "2024-07-31"
$sql = "SELECT * FROM machinehistory1 WHERE Date = '2024-08-02'";

$result = mysqli_query($konek, $sql);

// Check if there are any rows returned
if (mysqli_num_rows($result) > 0) {
    // Output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo "id: " . $row["ID"] . " - Name: " . $row["MachineID"] . " - Date: " . $row["Date"] . 
             " - Arc On: " . $row["ArcOn"] . " - Arc Off: " . $row["ArcOff"] . " - Arc Total: " . $row["ArcTotal"] . "<br>";
    }
} else {
    echo "0 results";
}

// Close the database connection
mysqli_close($konek);

?>
