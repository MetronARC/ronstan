<?php
include "../../../koneksi.php";

header('Content-Type: application/json');

$date = "2024-08-06";
$sql = "SELECT MachineID, Date, ArcOn, ArcOff FROM machinehistory1 WHERE Date = '$date'";
$result = mysqli_query($konek, $sql);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
