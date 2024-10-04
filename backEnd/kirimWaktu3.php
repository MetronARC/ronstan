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
$VoltageDC = $_GET['voltageDC'];
$StaticCurrentDC = $_GET['StaticCurrentDC'];
$StaticVoltageDC = $_GET['StaticVoltageDC'];

date_default_timezone_set('Asia/Jakarta');
$Time = date('H:i:s');  // TIME format

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

if ($Area == "1") {
    if ($Status == "ArcOn") {
        // Insert into machinehistory for ArcOn
        $queryArcOn = "INSERT INTO machinehistory1 (Area, MachineID, WeldID, Name, ArcOn, Date) VALUES ('$Area', '$MachineID', '$WeldID', '$Name', '$Time', CURDATE())";
        
        if (mysqli_query($konek, $queryArcOn)) {
            echo "Data berhasil diupdate atau diinsert";
        } else {
            echo "Error: " . mysqli_error($konek);
        }
    } else if ($Status == "ArcOff") {
        // Parse CurrentDC and VoltageDC values
        $currentDCArray = explode(',', $CurrentDC);
        $voltageDCArray = explode(',', $VoltageDC);

        // Loop through each value and insert into the database
        foreach ($currentDCArray as $index => $currentDCValue) {
            $voltageDCValue = $voltageDCArray[$index];
            $queryArcOff = "UPDATE machinehistory1 SET ArcOff = '$Time', ArcTotal = TIMEDIFF('$Time', ArcOn), 
                CurrentDC = '$currentDCValue', VoltageDC = '$voltageDCValue', StaticCurrentDC = '$StaticCurrentDC', StaticVoltageDC = '$StaticVoltageDC'
                WHERE id = (SELECT MAX(id) FROM machinehistory1 WHERE MachineID = '$MachineID' AND Area = '$Area')";
                
            if (!mysqli_query($konek, $queryArcOff)) {
                echo "Error: " . mysqli_error($konek);
            }
        }
        echo "Data berhasil diupdate atau diinsert";
    }
}

if ($Area == "2") {
    if ($Status == "ArcOn") {
        // Insert into machinehistory for ArcOn
        $queryArcOn = "INSERT INTO machinehistory2 (Area, MachineID, WeldID, Name, ArcOn, Date) VALUES ('$Area', '$MachineID', '$WeldID', '$Name', '$Time', CURDATE())";
        
        if (mysqli_query($konek, $queryArcOn)) {
            echo "Data berhasil diupdate atau diinsert";
        } else {
            echo "Error: " . mysqli_error($konek);
        }
    } else if ($Status == "ArcOff") {
        // Parse CurrentDC and VoltageDC values
        $currentDCArray = explode(',', $CurrentDC);
        $voltageDCArray = explode(',', $VoltageDC);

        // Loop through each value and insert into the database
        foreach ($currentDCArray as $index => $currentDCValue) {
            $voltageDCValue = $voltageDCArray[$index];
            $queryArcOff = "UPDATE machinehistory2 SET ArcOff = '$Time', ArcTotal = TIMEDIFF('$Time', ArcOn), 
                CurrentDC = '$currentDCValue', VoltageDC = '$voltageDCValue', StaticCurrentDC = '$StaticCurrentDC', StaticVoltageDC = '$StaticVoltageDC'
                WHERE id = (SELECT MAX(id) FROM machinehistory2 WHERE MachineID = '$MachineID' AND Area = '$Area')";
                
            if (!mysqli_query($konek, $queryArcOff)) {
                echo "Error: " . mysqli_error($konek);
            }
        }
        echo "Data berhasil diupdate atau diinsert";
    }
}
?>
