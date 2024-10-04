<?php

// koneksi ke database
include "../Dashboard/koneksi.php";

// baca data
$Area = $_GET['Area'];
$UID = $_GET['UID'];
$MachineID = $_GET['MachineID'];
$Status = $_GET['Status'];
$WeldID = $_GET['weldID'];
$CurrentDC = $_GET['currentDC'];
$VoltageDC = $_GET['voltageAverage'];

date_default_timezone_set('Asia/Jakarta');
$Time = date('H:i:s');  // TIME format
$Date = date('Y-m-d');  // DATE format

$query = "SELECT Name FROM employee WHERE cardUID= '$UID'";
$result = mysqli_query($konek, $query);

if (mysqli_num_rows($result) > 0) {
    // UID exists, retrieve corresponding Name value
    $data = mysqli_fetch_array($result);
    $Name = $data['Name'];
} else {
    // UID does not exist, set Name to an empty string
    echo "Card not in the database";
    $Name = '';
}

function checkAndInsert($konek, $table, $WeldID, $MachineID, $Area, $UID, $Name, $Date, $Time) {
    $queryCheck = "SELECT * FROM $table WHERE WeldID = '$WeldID'";
    $resultCheck = mysqli_query($konek, $queryCheck);

    if (mysqli_num_rows($resultCheck) == 0) {
        $queryInsert = "INSERT INTO $table (MachineID, WeldID, Area, UID, Name, Date, Login, Status) 
                        VALUES ('$MachineID', '$WeldID', '$Area', '$UID', '$Name', '$Date', '$Time', 'Active')";
        mysqli_query($konek, $queryInsert);
    }
}

if ($Area == "1" || $Area == "2") {
    // Check and insert into area1 or area2
    $tableArea = $Area == "1" ? "area1" : "area2";
    checkAndInsert($konek, $tableArea, $WeldID, $MachineID, $Area, $UID, $Name, $Date, $Time);

    // Check and insert into recaparea1 or recaparea2
    $tableRecapArea = $Area == "1" ? "recaparea1" : "recaparea2";
    checkAndInsert($konek, $tableRecapArea, $WeldID, $MachineID, $Area, $UID, $Name, $Date, $Time);

    $tableHistory = $Area == "1" ? "machinehistory1" : "machinehistory2";

    if ($Status == "ArcOn") {
        // Insert into machinehistory1 or machinehistory2 for ArcOn
        $queryArcOn = "INSERT INTO $tableHistory (Area, MachineID, WeldID, Name, ArcOn, Date) 
                       VALUES ('$Area', '$MachineID', '$WeldID', '$Name', '$Time', '$Date')";

        if (mysqli_query($konek, $queryArcOn)) {
            // Check for previous rows with the same MachineID and NULL ArcOff
            $queryUpdateArcOff = "SELECT * FROM $tableHistory 
                                  WHERE MachineID = '$MachineID' AND Area = '$Area' 
                                  AND ArcOff IS NULL 
                                  AND id < (SELECT MAX(id) FROM $tableHistory WHERE MachineID = '$MachineID' AND Area = '$Area')";

            $resultArcOff = mysqli_query($konek, $queryUpdateArcOff);

            while ($row = mysqli_fetch_assoc($resultArcOff)) {
                $previousId = $row['id'];
                $ArcCheck = $row['ArcCheck'];

                // Update ArcOff and ArcTotal for previous rows
                $queryUpdateArcTotal = "UPDATE $tableHistory 
                                        SET ArcOff = '$ArcCheck', 
                                            ArcTotal = TIMEDIFF('$ArcCheck', ArcOn) 
                                        WHERE id = '$previousId'";

                mysqli_query($konek, $queryUpdateArcTotal);
            }

            echo "Data berhasil diupdate atau diinsert";
        } else {
            echo "Error: " . mysqli_error($konek);
        }
    } else if ($Status == "ArcOff") {
        // Update machinehistory1 or machinehistory2 for ArcOff
        $queryArcOff = "UPDATE $tableHistory 
                        SET ArcOff = '$Time', ArcTotal = TIMEDIFF('$Time', ArcOn), CurrentDC = '$CurrentDC', Voltage = '$VoltageDC' 
                        WHERE id = (SELECT MAX(id) FROM $tableHistory WHERE MachineID = '$MachineID' AND Area = '$Area')";

        if (mysqli_query($konek, $queryArcOff)) {
            echo "Data berhasil diupdate atau diinsert";
        } else {
            echo "Error: " . mysqli_error($konek);
        }
    } else if ($Status == "ArcCheck") {
        // Update machinehistory1 or machinehistory2 for ArcCheck
        $querySelect = "SELECT id, ArcTotal FROM $tableHistory WHERE MachineID = '$MachineID' AND Area = '$Area' ORDER BY id DESC LIMIT 1";
        $resultSelect = mysqli_query($konek, $querySelect);

        if (mysqli_num_rows($resultSelect) > 0) {
            $data = mysqli_fetch_assoc($resultSelect);
            $id = $data['id'];
            $ArcTotal = $data['ArcTotal'];

            // Convert ArcTotal to seconds, increment by 6, and convert back to TIME format
            $ArcTotalSeconds = strtotime($ArcTotal) - strtotime('TODAY') + 6;
            $newArcTotal = gmdate('H:i:s', $ArcTotalSeconds);

            $queryUpdate = "UPDATE $tableHistory 
                            SET ArcTotal = '$newArcTotal', ArcCheck = '$Time' 
                            WHERE id = '$id'";

            if (mysqli_query($konek, $queryUpdate)) {
                echo "ArcTotal dan ArcCheck berhasil diupdate";
            } else {
                echo "Error: " . mysqli_error($konek);
            }
        } else {
            echo "No matching records found for ArcCheck";
        }
    }
}
?>
