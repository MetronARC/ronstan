<?php
header('Content-Type: application/json');

try {
    // Fetch the raw input data from the client
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['machineName'], $input['date'])) {
        throw new Exception('Invalid input data');
    }

    $machineName = $input['machineName'];
    $date = $input['date'];

    // Database connection details
    $servername = "localhost";
    $username = "u558841402_ronstan";
    $password = "2468g0a7A7B7*";
    $dbname = "u558841402_ronstandb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to fetch the ArcTotal for the specific MachineID and Date
    $sql = "
    SELECT ArcTotal, TIME_TO_SEC(ArcTotal) AS ArcTotalInSeconds
    FROM machinehistory1 
    WHERE MachineID = ? AND DATE(Date) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $machineName, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalArcTimeInSeconds = 0;
    $arcData = [];

    while ($row = $result->fetch_assoc()) {
        $arcData[] = $row; // Collect all rows for debugging
        $totalArcTimeInSeconds += (int)$row['ArcTotalInSeconds'];
    }

    $stmt->close();
    $conn->close();

    // Log the data for debugging
    echo json_encode([
        'arcData' => $arcData, // All rows fetched
        'totalArcTime' => $totalArcTimeInSeconds
    ]);
} catch (Exception $e) {
    // Handle error
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(400);
}
