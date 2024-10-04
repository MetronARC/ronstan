<?php
// Include database connection
include "../../../koneksi.php";

// Fetch WeldID from the first row of area1 table
$sql_area1 = "SELECT WeldID FROM areaTest LIMIT 1";
$result_area1 = mysqli_query($konek, $sql_area1);

if ($result_area1) {
    // Fetch the WeldID from the result
    $row_area1 = mysqli_fetch_assoc($result_area1);
    $weldID = $row_area1['WeldID'];

    // Proceed with fetching data from machinehistory1 table using the fetched WeldID
    $sql_machinehistory = "SELECT ArcOn, ArcOff, CurrentDC, Voltage FROM machinehistoryTest WHERE WeldID = ?";
    $stmt_machinehistory = mysqli_prepare($konek, $sql_machinehistory);

    // Check if statement preparation succeeded
    if ($stmt_machinehistory) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt_machinehistory, "s", $weldID);
        
        // Execute statement
        mysqli_stmt_execute($stmt_machinehistory);
        
        // Fetch data and store in an array
        $data = array();
        mysqli_stmt_bind_result($stmt_machinehistory, $arcOn, $arcOff, $currentDC, $voltage);
        while (mysqli_stmt_fetch($stmt_machinehistory)) {
            $data[] = array(
                'ArcOn' => $arcOn,
                'ArcOff' => $arcOff,
                'CurrentDC' => $currentDC,
                'Voltage' => $voltage
            );
        }
        
        // Close statement
        mysqli_stmt_close($stmt_machinehistory);
        
        // Close connection
        mysqli_close($konek);
        
        // Return data as JSON
        echo json_encode($data);
    } else {
        // Handle statement preparation failure
        die('Failed to prepare statement: ' . mysqli_error($konek));
    }
} else {
    // Handle query failure
    die('Error executing SQL query: ' . mysqli_error($konek));
}
?>
