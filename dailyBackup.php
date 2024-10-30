<?php
$database = "u558841402_ronstandb";
$user = "u558841402_ronstan";
$password = "2468g0a7A7B7*";
$backup_file = "/backup/" . $database . "_" . date("Y-m-d_H-i-s") . ".sql";

// Run the mysqldump command to back up the database
$command = "mysqldump --user=$user --password=$password $database > $backup_file";
system($command, $output);

if ($output === 0) {
    echo "Backup created successfully: $backup_file";
} else {
    echo "Error creating backup.";
}
?>