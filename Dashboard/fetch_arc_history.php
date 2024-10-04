<?php
include "koneksi.php";

// Check if the request is a POST request and required data is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["machineName"]) && isset($_POST["startDate"]) && isset($_POST["endDate"])) {
    $machineName = $konek->real_escape_string($_POST["machineName"]);
    $startDate = $konek->real_escape_string($_POST["startDate"]);
    $endDate = $konek->real_escape_string($_POST["endDate"]);

    // Construct SQL query to fetch ArcTotal for the specified machine and date range
    $sql = "SELECT SUM(ArcTotal) as TotalArcTime FROM machinehistory1 
            WHERE machineName = '$machineName' AND Date BETWEEN '$startDate' AND '$endDate'";

    $result = $konek->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalArcTime = $row["TotalArcTime"];

        // Convert totalArcTime from seconds to HH:MM:SS format
        $hours = floor($totalArcTime / 3600);
        $minutes = floor(($totalArcTime % 3600) / 60);
        $seconds = $totalArcTime % 60;

        $formattedTime = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

        echo $formattedTime;
    } else {
        echo "00:00:00"; // No data found
    }
} else {
    echo "Invalid request.";
}

$konek->close();
?>
