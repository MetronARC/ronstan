<?php
// Start the session
session_start();

// Check if the user is not authenticated, redirect to login
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: ../index.php");
    exit();
}

// Set the initial timeout value (in seconds)
$timeout = 500;

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
include "koneksi.php";

// Check the connection
if ($konek->connect_error) {
    die("Connection failed: " . $konek->connect_error);
}

// Set the current date to today in Asia/Jakarta timezone
$current_date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$current_date = $current_date->format('Y-m-d');

// Fetch the rows from the 'machinehistory1' table with the current date
$sql_machinehistory_current_date = mysqli_query($konek, "SELECT * FROM machinehistory1 WHERE DATE(Date) = '$current_date'");

// Initialize total ArcTotal time to zero in seconds
$total_arc_total_seconds = 0;

// Loop through the rows and sum up the ArcTotal times in seconds
while ($data_machinehistory = mysqli_fetch_array($sql_machinehistory_current_date)) {
    $arc_total_time = $data_machinehistory["ArcTotal"];
    
    // Convert TIME to seconds (assuming TIME format is HH:MM:SS)
    list($hours, $minutes, $seconds) = explode(':', $arc_total_time);
    $total_arc_total_seconds += ($hours * 3600) + ($minutes * 60) + $seconds;
}

// Function to format total seconds back to H:i:s format
function formatTimeSeconds($totalSeconds) {
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;
    
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}

// Format and display the total ArcTotal time
$total_arc_total_time = formatTimeSeconds($total_arc_total_seconds);

$ceksensor1 = $total_arc_total_time;

$sql_sensor2 = mysqli_query($konek, "SELECT * FROM machine ORDER BY WeldID DESC LIMIT 1");
$data_sensor2 = mysqli_fetch_array($sql_sensor2);
$ceksensor2 = $data_sensor2 ? $data_sensor2["machineName"] : "No records";

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
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="styles/index.css">
    <script type="text/javascript" src="jquery/jquery.min.js"></script>
    <title>SPARC Monitoring Dashboard</title>
        <script>
        $(document).ready(function () {
    $.get('checkTimeArea1.php', function(data) {
        console.log('checkTimeArea1.php executed');

        // After the first request completes, execute the second request
        $("#checkMachineCount").load('checkMachineCount.php', function() {
            console.log('checkMachineCount.php executed');
        }).fail(function() {
            console.log('AJAX request to checkMachineCount.php failed');
        });

    }).fail(function() {
        console.log('AJAX request to checkTimeArea1.php failed');
    });
});

    </script>
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
                    <img src="../ronstan.png" alt="Ronstan Logo">
                </div>
                <div class="close" id="close-btn">
                    <span class="material-symbols-outlined">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="index.php" class="active">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="recap.php">
                    <span class="material-symbols-outlined">badge</span>
                    <h3>Machine Recap</h3>
                </a>
                <a href="monitoring.php">
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
            <h1>Main Dashboard</h1>

            <div class="date"></div>

            <div class="insights">
                <!-- ACTIVE AREA -->
                <div class="sales">
                    <span class="material-symbols-outlined">zoom_in_map</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Active Machine</h3>
                            <h1 id="checkMachineCount"></h1>
                        </div>
                        <a href="monitoring.php">
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
                <!-- END OF ACTIVE AREA -->
                <!-- MOST USED MACHINE -->
                <!--<div class="expenses">-->
                <!--    <span class="material-symbols-outlined">vacuum</span>-->
                <!--    <div class="middle">-->
                <!--        <div class="left">-->
                <!--            <h3>Most Used Machine</h3>-->
                <!--            <h1 id="ceksensor2"><?php echo $ceksensor2; ?></h1>-->
                <!--        </div>-->
                <!--        <div class="progress">-->
                <!--            <svg>-->
                <!--                <circle cx="42" cy="38" r="36"></circle>-->
                <!--            </svg>-->
                <!--            <div class="number">-->
                <!--                <p>Details</p>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <!-- END OF MOST USED MACHINE -->
                <!-- MOST ACTIVE WELDERS -->
                <div class="income">
                    <span class="material-symbols-outlined">engineering</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Machines Up Time Today</h3>
                            <h1 id="ceksensor1"><?php echo $ceksensor1; ?></h1>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="42" cy="38" r="36"></circle>
                            </svg>
                            <div class="number">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- END OF MOST ACTIVE WELDERS -->
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
                    <a href="Machine/Machine.php"><img src="images/Logo.png" alt="AdminLogo"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/index.js"></script>

</body>
</html>