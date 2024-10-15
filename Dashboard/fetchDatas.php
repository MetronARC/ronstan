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
$date = $input['date'] ?? '';

// Query to fetch data for all machines based on the provided date
$sql = "SELECT m.realName, mh.ArcOn, mh.ArcOff 
        FROM machine m
        JOIN machinehistory1 mh ON m.machineID = mh.MachineID 
        WHERE mh.Date = ?";
$stmt = $konek->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['realName']][] = [
        'ArcOn' => $row['ArcOn'],
        'ArcOff' => $row['ArcOff']
    ];
}

$stmt->close();
$konek->close();

echo json_encode($data);
