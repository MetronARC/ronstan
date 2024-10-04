<?php
// Database connection parameters
$servername = "localhost";
$username = "u558841402_ronstan";
$password = "2468g0a7A7B7*";
$dbname = "u558841402_ronstandb";

// Input variables
$machineID = "RONSTAN-2";
$date = "2024-07-19";
$timeStart = "06:00:00";
$timeEnd = "12:00:00";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$sql = "SELECT * FROM machinehistory1 WHERE MachineID = ? AND Date = ? AND ArcOn BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $machineID, $date, $timeStart, $timeEnd);

// Execute statement
$stmt->execute();
$result = $stmt->get_result();

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=machine_history.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "MachineID\tDate\tArcOn\tArcOff\n";

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo $row["MachineID"]. "\t" . $row["Date"]. "\t" . $row["ArcOn"]. "\t" . $row["ArcOff"]. "\n";
    }
} else {
    echo "No results found\n";
}

// Close connection
$stmt->close();
$conn->close();
?>
