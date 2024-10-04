<?php
// Include database connection
include "../Dashboard/koneksi.php";

// Read data
$Area = $_GET['Area'];
$UID = $_GET['UID'];
$Status = $_GET['Status'];
$Mode = $_GET['Mode'];
$MachineID = $_GET['MachineID'];
$WeldID = $_GET['weldID'];

$Name = getEmployeeName($konek, $UID);

// Function to get employee name by UID
function getEmployeeName($konek, $UID)
{
    $query = "SELECT Name FROM employee WHERE cardUID= '$UID'";
    $result = mysqli_query($konek, $query);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_array($result);
        $Name = $data['Name']; // Store the fetched value to $Name
        return $Name; // Return the value of $Name
    } else {
        return ''; // Return an empty string if no data found
    }
}

// Function to execute query and handle errors
function executeQuery($konek, $query)
{
    echo "Executing query: " . $query . "\n"; // Print the query
    if (mysqli_query($konek, $query)) {
        echo "Query executed successfully\n";
    } else {
        echo "Error executing query: " . mysqli_error($konek) . "\n";
    }
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');
$Date = date('Y-m-d');
$Time = date('H:i:s');

// Debugging: Print received parameters
echo "Area: $Area, UID: $UID, Status: $Status, Mode: $Mode, MachineID: $MachineID, WeldID: $WeldID\n";

if (in_array($Area, ["1", "2", "3", "4", "5"])) {
    // Check if area is valid

    // Get maximum ID for the area
    $sql = mysqli_query($konek, "SELECT MAX(ID) as max_id FROM area$Area");
    $data = mysqli_fetch_array($sql);
    $IDCount = $data["max_id"];

    if ($IDCount == 0) {
        $i = 0;
    } else {
        $i = $IDCount;
        $ID = $i;
    }

    if ($Status == "Inactive") {
        $i++;

        // Check if a row with the same MachineID exists
        $checkQuery = "SELECT * FROM area$Area WHERE MachineID='$MachineID'";
        $checkResult = mysqli_query($konek, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            // Row with the same MachineID exists, perform an update
            $updateQuery = "UPDATE area$Area SET WeldID='$WeldID', Date='$Date' WHERE MachineID='$MachineID'";
            executeQuery($konek, $updateQuery);
        } else {
            // No row with the same MachineID, perform an insert
            $insertQuery = "INSERT INTO area$Area (Area, UID, Status, Mode, `MachineID`, WeldID, Date, Name) VALUES ('$Area', '$UID', '$Status', '$Mode', '$MachineID', '$WeldID', '$Date', '$Name')";
            executeQuery($konek, $insertQuery);
        }

        // Second INSERT query for recaparea
        $query2 = "INSERT INTO recaparea$Area (Area, UID, Status, Mode, `MachineID`, WeldID, Date, Name) VALUES ('$Area', '$UID', '$Status', '$Mode', '$MachineID', '$WeldID', '$Date', '$Name')";
        executeQuery($konek, $query2);
    } elseif ($Status == "Active") {
        // UPDATE queries for the area
        $query1 = "UPDATE area$Area SET Name='$Name', UID='$UID', Status='$Status', Mode='$Mode', `MachineID`='$MachineID', WeldID='$WeldID', Date='$Date' WHERE MachineID='$MachineID'";
        executeQuery($konek, $query1);

        // UPDATE queries for recaparea
        $query2 = "UPDATE recaparea$Area SET Name = '$Name', UID = '$UID', Status = '$Status', Mode = '$Mode', MachineID = '$MachineID', WeldID = '$WeldID', Date = '$Date' WHERE MachineID = '$MachineID' AND WeldID = '$WeldID'";
        executeQuery($konek, $query2);

        // Check Mode and update Time accordingly
        if ($Mode == "Login") {
            $query1 = "UPDATE area$Area SET Login='$Time' WHERE MachineID='$MachineID'";
            executeQuery($konek, $query1);

            $query2 = "UPDATE recaparea$Area SET Login='$Time' WHERE MachineID='$MachineID' AND WeldID = '$WeldID'";
            executeQuery($konek, $query2);
        } elseif ($Mode == "Logout") {
            $query1 = "UPDATE area$Area SET Logout='$Time' WHERE MachineID='$MachineID'";
            executeQuery($konek, $query1);

            $query2 = "UPDATE recaparea$Area SET Logout='$Time' WHERE MachineID='$MachineID' AND WeldID = '$WeldID'";
            executeQuery($konek, $query2);
        }
    } elseif ($Status == "Done") {
        // Time check for allowed intervals
        $currentTime = strtotime($Time);
        $allowedIntervals = [
            ['start' => '11:05:00', 'end' => '12:00:00'],
            ['start' => '15:05:00', 'end' => '17:00:00'],
            ['start' => '22:05:00', 'end' => '23:59:00'],
            ['start' => '04:05:00', 'end' => '05:00:00']
        ];

        $canExecute = false;
        foreach ($allowedIntervals as $interval) {
            $start = strtotime($interval['start']);
            $end = strtotime($interval['end']);
            if ($currentTime >= $start && $currentTime <= $end) {
                $canExecute = true;
                break;
            }
        }

        if ($canExecute) {
            // Retrieve WeldID from the area
            $queryWeldID = "SELECT WeldID FROM area$Area WHERE MachineID='$MachineID'";
            $resultWeldID = mysqli_query($konek, $queryWeldID);

            if ($resultWeldID) {
                $dataWeldID = mysqli_fetch_array($resultWeldID);
                $WeldID = $dataWeldID['WeldID'];

                // Delete data from the area
                $queryDeleteArea = "DELETE FROM area$Area WHERE MachineID='$MachineID'";
                executeQuery($konek, $queryDeleteArea);

                // Increment WeldID in machine table
                $queryIncrementWeldID = "UPDATE machine SET WeldID = WeldID + 1 WHERE MachineID = '$MachineID'";
                executeQuery($konek, $queryIncrementWeldID);

                // Calculate the sum of "ArcTotal" in machinehistory1
                $querySumArcTotal = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(ArcTotal))) AS totalArcTime FROM machinehistory1 WHERE WeldID='$WeldID'";
                $resultSumArcTotal = mysqli_query($konek, $querySumArcTotal);

                if ($resultSumArcTotal) {
                    $dataSumArcTotal = mysqli_fetch_array($resultSumArcTotal);
                    $totalArcTime = $dataSumArcTotal['totalArcTime'];

                    // Update the corresponding row in recaparea
                    $queryUpdateRecapArea = "UPDATE recaparea$Area SET upTime='$totalArcTime' WHERE WeldID='$WeldID'";
                    executeQuery($konek, $queryUpdateRecapArea);
                } else {
                    echo "Error calculating sum of ArcTotal: " . mysqli_error($konek);
                }
            } else {
                echo "Error retrieving WeldID from area$Area: " . mysqli_error($konek);
            }
        } else {
            echo "Execution is not allowed at this time.";
        }
    }
} else {
    echo "Invalid area!";
}
