<?php
error_reporting(0);
// Start the session
session_start();

// Check if the user is not authenticated, redirect to login
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: ../index.php");
    exit();
}

// Set the initial timeout value (in seconds)
$timeout = 99999;

// Check the timestamp of the last activity
if (isset($_SESSION['last_activity'])) {
    $elapsedTime = time() - $_SESSION['last_activity'];

    // If more than the timeout value has passed since the last activity, redirect to the login page
    if ($elapsedTime > $timeout) {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();
?>

<?php
// Your existing PHP code

// Assuming you have a database connection established
include "koneksi.php";

// Check the connection
if ($konek->connect_error) {
    die("Connection failed: " . $konek->connect_error);
}

// Get the list of tables in the database
$tablesQuery = "SHOW TABLES";
$tablesResult = $konek->query($tablesQuery);

if ($tablesResult) {
} else {
    echo "Error retrieving tables: " . $konek->error;
}


// Array to store the area names
$areas = [];

// Fetch the table names
while ($row = $tablesResult->fetch_assoc()) {
    $tableName = $row["Tables_in_u558841402_ronstandb"];

    // Check if the table name starts with "area"
    if (preg_match('/^area/i', $tableName)) {
        $areas[] = $tableName;
    }
}

$areaRowCounts = [];

// Fetch the number of rows for each area table
foreach ($areas as $area) {
    $rowCountQuery = "SELECT COUNT(*) AS row_count FROM $area";
    $rowCountResult = $konek->query($rowCountQuery);

    if ($rowCountResult) {
        $rowCount = $rowCountResult->fetch_assoc()['row_count'];
        $areaRowCounts[$area] = $rowCount;
    } else {
        // Handle the error if needed
        $areaRowCounts[$area] = 0;
    }
}

// Close the database connection
$konek->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../Logo.png" />
    <!-- Material Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="styles/index.css">
    <title>SPARC Monitoring Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script type="text/javascript" src="jquery/jquery.min.js"></script>
    <style>
         #inside {
            display: inline-block; /* Ensures it stays in the same line as the text */
            background-color: white;
            padding: 0 5px; /* Optional: Add padding for better visual appearance */
        }
    </style>

</head>
<body>
    <script>
        var timeout = <?php echo $timeout; ?>; // Set the initial timeout value in seconds
        var countdownInterval;

        function startCountdown() {
            countdownInterval = setInterval(function() {

                if (timeout <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = "../index.php";
                }

                timeout--;
            }, 1000); // Update the countdown every 1000 milliseconds (1 second)
        }

        function resetCountdown() {
            clearInterval(countdownInterval);
            timeout = <?php echo $timeout; ?>;
            startCountdown();
        }

        document.addEventListener("mousemove", resetCountdown);
        document.addEventListener("keydown", resetCountdown);

        // Start the countdown initially
        startCountdown();
    </script>

    <div class="container" data-aos="zoom-out">
        <!-- SIDEBAR -->
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../ronstan.png" alt="Metronarc Logo">
                </div>
                <div class="close" id="close-btn">
                    <span class="material-symbols-outlined">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="index.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="recap.php">
                    <span class="material-symbols-outlined">badge</span>
                    <h3>Machine Recap</h3>
                </a>
                <a href="monitoring.php" class="active">
                    <span class="material-symbols-outlined">bar_chart_4_bars</span>
                    <h3>Monitoring</h3>
                </a>
                <a href="#">
                    <span class="material-symbols-outlined">logout</span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!-- END OF SIDEBAR -->


        <main>
            <h1>Monitoring</h1>

            <div class="date"></div>

            <div class="insights">
            <!-- Loop through the areas and generate the corresponding elements -->
            <?php foreach ($areas as $area): ?>
    <div class="sales">
        <span class="material-symbols-outlined">zoom_in_map</span>
        <div class="middle">
            <div class="left">
                <h3><?= ucfirst($area) ?></h3>
                <h2>Active Machine: <div id="inside"><h1><?= $areaRowCounts[$area] ?></h1></div></h2>
            </div>
            <?php
                // Extract the numerical part from the table name
                preg_match('/\d+/', $area, $matches);
                $areaNumber = $matches[0]; // Assuming there will always be a match
            ?>
            <a href="Area/area<?= $areaNumber ?>.php">
                <div class="progress">
                    <svg>
                        <circle cx="42" cy="42" r="36"></circle>
                    </svg>
                    <div class="number">
                        <h3>View</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>
<?php endforeach; ?>
        </div>
            <!-- END OF INSIGHTS -->
        </main>
            <!-- END OF MAIN -->
        <div class="right">
            <div class="top">
                <button id="menu-btn">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="theme-toggler">
                    <span class="material-symbols-outlined active">light_mode</span>
                    <span class="material-symbols-outlined">dark_mode</span>
                </div>
                <div class="profile">
                    <div class="info">
                        <p>Hey, <b>Ronstan</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="images/Logo.png" alt="AdminLogo">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/index.js"></script>

</body>
</html>
