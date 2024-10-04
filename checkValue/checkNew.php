<?php
    // Koneksi ke database
    include "../Dashboard/koneksi.php";

    // Check the connection
    if ($konek->connect_error) {
        die("Connection failed: " . $konek->connect_error);
    }

    // Query to fetch rows from machinehistory1 table with the specified date
    $sql_machinehistory = "SELECT * FROM machinehistory1 WHERE DATE(Date) = '2024-08-27'";
    $result_machinehistory = $konek->query($sql_machinehistory);

    // Initialize total ArcTotal time to zero
    $total_arc_total_seconds = 0;

    // Loop through the rows and sum up the ArcTotal times in seconds
    while ($data_machinehistory = $result_machinehistory->fetch_assoc()) {
        $arc_total_time = $data_machinehistory["ArcTotal"];
        
        // Convert TIME to seconds (assuming TIME format is HH:MM:SS)
        list($hours, $minutes, $seconds) = explode(':', $arc_total_time);
        $total_arc_total_seconds += ($hours * 3600) + ($minutes * 60) + $seconds;
    }

    // Convert total seconds back to H:i:s format
    function formatTimeSeconds($totalSeconds) {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }

    // Format and display the total ArcTotal time
    $total_arc_total_time = formatTimeSeconds($total_arc_total_seconds);
    
    echo "Total ArcTotal time: $total_arc_total_time";

    // Close the database connection
    $konek->close();
?>