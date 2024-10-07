<?php
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['machineName'], $input['date'], $input['startTime'], $input['endTime'])) {
        throw new Exception('Invalid input data');
    }

    $machineName = $input['machineName'];
    $date = $input['date'];
    $startTime = $input['startTime'];
    $endTime = $input['endTime'];

    // Database connection details
    $servername = "localhost";
    $username = "u558841402_ronstan";
    $password = "2468g0a7A7B7*";
    $dbname = "u558841402_ronstandb";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Step 1: Look up the MachineID using the realName
    $sql = "SELECT machineID FROM machine WHERE realName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $machineName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('No MachineID found for the given realName');
    }
    
    $row = $result->fetch_assoc();
    $machineID = $row['machineID'];
    $stmt->close();

    // Step 2: Sum ArcTotal within the specified time range
    $sql = "
    SELECT SUM(TIME_TO_SEC(ArcTotal)) AS totalArcTimeInSeconds
    FROM machinehistory1 
    WHERE MachineID = ? 
    AND DATE(Date) = ?
    AND ArcOn >= ? 
    AND ArcOff <= ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $machineID, $date, $startTime, $endTime);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalArcTimeInSeconds = (int)$row['totalArcTimeInSeconds'];
    $stmt->close();
    $conn->close();

    // Step 3: Calculate the total seconds in the given time range
    $startDateTime = new DateTime($date . ' ' . $startTime);
    $endDateTime = new DateTime($date . ' ' . $endTime);
    $timeDifferenceInSeconds = $endDateTime->getTimestamp() - $startDateTime->getTimestamp();

    // Step 4: Calculate the usage percentage
    $usagePercentage = ($totalArcTimeInSeconds / $timeDifferenceInSeconds) * 100;

    echo json_encode([
        'totalArcTime' => $totalArcTimeInSeconds,
        'usagePercentage' => $usagePercentage
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(400);
}
