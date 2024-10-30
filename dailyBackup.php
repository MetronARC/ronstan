<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$database = "u558841402_ronstandb";
$user = "u558841402_ronstan";
$password = "2468g0a7A7B7*";

// Absolute path to the backup directory
$backup_directory = "/home/u558841402/public_html/backup/";
$backup_file = $backup_directory . $database . "_" . date("Y-m-d_H-i-s") . ".sql";

// Check if the backup directory exists and is writable
if (!is_dir($backup_directory)) {
    mkdir($backup_directory, 0755, true);
}

if (!is_writable($backup_directory)) {
    die("Backup directory is not writable.");
}

// Path to mysqldump (change this if necessary)
$mysqldump_path = "/usr/bin/mysqldump";

// Construct the command to back up the database
$command = "$mysqldump_path --user=$user --password=$password $database > $backup_file 2>&1";

// Execute the command and capture the output
$output = shell_exec($command);

// Check if the backup was created successfully
if (file_exists($backup_file) && filesize($backup_file) > 0) {
    echo "Backup created successfully: $backup_file";
} else {
    echo "Error creating backup. Command output: " . $output;
}
?>
