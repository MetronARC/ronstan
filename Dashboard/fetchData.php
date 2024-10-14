<?php
header('Content-Type: application/json');

// Database connection parameters
include "koneksi.php";

// Check connection
if ($konek->connect_error) {
    die("Connection failed: " . $konek->connect_error);
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$machineName = $input['machineName'] ?? '';
$date = $input['date'] ?? '';

// Lookup machine ID based on machine name
$sql = "SELECT machineID FROM machine WHERE realName = ?";
$stmt = $konek->prepare($sql);
$stmt->bind_param("s", $machineName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $machineID = $row['machineID'];

    // Query to fetch data based on machineID and date
    $sql = "SELECT ArcOn, ArcOff FROM machinehistory1 WHERE MachineID = ? AND Date = ?";
    $stmt = $konek->prepare($sql);
    $stmt->bind_param("ss", $machineID, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $data = ['error' => 'Machine not found'];
}

$stmt->close();
$konek->close();

echo json_encode($data);
?>