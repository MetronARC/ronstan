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

    // Step 1: Look up the MachineID using the realName (machineName)
    $sql = "SELECT machineID FROM machine WHERE realName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $machineName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('No MachineID found for the given realName');
    }
    
    $row = $result->fetch_assoc();
    $machineID = $row['machineID']; // Extract the machineID

    $stmt->close();

    // Step 2: Use the retrieved MachineID to find the ArcTotal for that specific machine and date
    $sql = "
    SELECT SUM(TIME_TO_SEC(ArcTotal)) AS totalArcTimeInSeconds
    FROM machinehistory1 
    WHERE MachineID = ? AND DATE(Date) = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $machineID, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalArcTimeInSeconds = (int)$row['totalArcTimeInSeconds'];

    $stmt->close();
    $conn->close();

    // Return the totalArcTimeInSeconds to the front-end
    echo json_encode([
        'totalArcTime' => $totalArcTimeInSeconds
    ]);
} catch (Exception $e) {
    // Handle error
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(400);
}
