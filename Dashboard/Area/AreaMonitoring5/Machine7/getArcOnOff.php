<?php
// Include your database connection file or create a new one
include "../../../koneksi.php";

// Fetch WeldID from the "area1" table
$sqlWeldID = "SELECT WeldID FROM area5 WHERE Status='Active' LIMIT 6, 1";
$resultWeldID = $konek->query($sqlWeldID);

if (!$resultWeldID) {
    die("Error in WeldID query: " . $konek->error);
}

$chartData = [];

if ($resultWeldID->num_rows > 0) {
    $row = $resultWeldID->fetch_assoc();
    $weldID = $row['WeldID'];

    // Fetch ArcOn and ArcOff data for each WeldID from the "machinehistory1" table using prepared statement
    $stmt = $konek->prepare("SELECT ArcOn, ArcOff, ArcTotal, CurrentDC FROM machinehistory5 WHERE WeldID = ?");
    $stmt->bind_param("s", $weldID); // assuming WeldID is a string, use "i" if it's an integer
    $stmt->execute();
    $resultData = $stmt->get_result();
    $stmt->close();

    if ($resultData) {
        while ($rowData = $resultData->fetch_assoc()) {
            $chartData[] = [
                'WeldID' => $weldID,
                'ArcOn' => $rowData['ArcOn'],
                'ArcOff' => $rowData['ArcOff'],
                'ArcTotal' => $rowData['ArcTotal'],
                'CurrentDC' => $rowData['CurrentDC'],
            ];
        }
    } else {
        die("Error in machinehistory1 query: " . $konek->error);
    }
} else {
    die("No active WeldID found.");
}

// Close the database connection
$konek->close();

// Return the data as JSON
echo json_encode($chartData);
?>
