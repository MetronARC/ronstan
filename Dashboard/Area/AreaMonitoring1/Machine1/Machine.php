<?php
// Start the session
session_start();

// Check if the user is not authenticated, redirect to login
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: ../../../../index.html");
    exit();
}

// Include database connection
include "../../../koneksi.php";

// Set the initial timeout value (in seconds)
$timeout = 500;

// Define mapping array for machine labels
$machineMapping = [
    "RONSTAN-1" => "A20-2534",
    "RONSTAN-2" => "M32-2530",
];

// Fetch data from database for sensor 1
$sql_sensor1 = mysqli_query($konek, "SELECT * FROM area1 WHERE Status='Active' ORDER BY ID ASC LIMIT 1");
$data_sensor1 = mysqli_fetch_array($sql_sensor1);
$ceksensor1 = $data_sensor1 ? $data_sensor1["Name"] : "No active records found in the 'area1' table.";

// Map the value to the corresponding machine label
if (isset($machineMapping[$ceksensor1])) {
    $ceksensor1 = $machineMapping[$ceksensor1];
}

// Fetch data from database for sensor 2
$sql_sensor2 = mysqli_query($konek, "SELECT * FROM area1 WHERE Status='Active' ORDER BY ID ASC LIMIT 1");
$data_sensor2 = mysqli_fetch_array($sql_sensor2);
$ceksensor2 = $data_sensor2 ? $data_sensor2["UID"] : "No active records found in 'area1'.";

// Map the value to the corresponding machine label
if (isset($machineMapping[$ceksensor2])) {
    $ceksensor2 = $machineMapping[$ceksensor2];
}

// Fetch data from database for sensor 3
$sql_sensor3 = mysqli_query($konek, "SELECT WeldID FROM area1 WHERE Status='Active' LIMIT 1");
$data_sensor3 = mysqli_fetch_array($sql_sensor3);
$weldID = $data_sensor3["WeldID"];

$sql_sensor4 = mysqli_query($konek, "SELECT * FROM area1 WHERE Status='Active' ORDER BY ID ASC LIMIT 1");
$data_sensor4 = mysqli_fetch_array($sql_sensor4);
$ceksensor4 = $data_sensor4 ? $data_sensor4["MachineID"] : "No active records found in 'area1'.";

// Map the value to the corresponding machine label
if (isset($machineMapping[$ceksensor4])) {
    $ceksensor4 = $machineMapping[$ceksensor4];
}

$sql_sensor5 = mysqli_query($konek, "SELECT * FROM area1 WHERE Status='Active' ORDER BY ID ASC LIMIT 1");
$data_sensor5 = mysqli_fetch_array($sql_sensor5);
$ceksensor5 = $data_sensor5 ? $data_sensor5["Login"] : "No active records found in the 'area1' table.";

if (empty($weldID)) {
    $ceksensor3 = "No active WeldID found.";
} else {
    $sql_machinehistory = mysqli_query($konek, "SELECT * FROM machinehistory1 WHERE WeldID = '$weldID'");
    $sumArcTotal = 0;

    while ($row_machinehistory = mysqli_fetch_array($sql_machinehistory)) {
        $arcTotal = $row_machinehistory["ArcTotal"];
        list($hours, $minutes, $seconds) = explode(':', $arcTotal);
        $sumArcTotal += $hours * 3600 + $minutes * 60 + $seconds;
    }

    $ceksensor3 = gmdate('H:i:s', $sumArcTotal);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../../Logo.png" />
    <script type="text/javascript" src="../../../jquery/jquery.min.js"></script>
    <!-- Material Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="../../../styles/index.css">
    <title>SPARC Monitoring Dashboard</title>

    <style>
        #inside {
            display: inline-block;
            /* Ensures it stays in the same line as the text */
            background-color: white;
            padding: 0 5px;
            /* Optional: Add padding for better visual appearance */
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
                    window.location.href = "../../../../index.html";
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
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <div class="container">
        <!-- SIDEBAR -->
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../../../../ronstan.png" alt="Metronarc Logo">
                </div>
                <div class="close" id="close-btn">
                    <span class="material-symbols-outlined">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="../../../index.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="../recap.php">
                    <span class="material-symbols-outlined">badge</span>
                    <h3>Machine Recap</h3>
                </a>
                <a href="../../../monitoring.php" class="active">
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
            <h1 id="ceksensor4"><?php echo $ceksensor4; ?></h1>

            <div class="date"></div>

            <div class="insights">
                <!-- ACTIVE AREA -->
                <div class="sales">
                    <span class="material-symbols-outlined">badge</span>
                    <div class="middle">
                        <div class="left">
                            <h3>UID</h3>
                            <h1 id="ceksensor2"><?php echo $ceksensor2; ?></h1>
                        </div>
                        <div class="progress">
                            <img src="images/badge.png">
                            <div class="number">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END OF ACTIVE AREA -->
                <!-- MOST USED MACHINE -->
                <div class="expenses">
                    <span class="material-symbols-outlined">schedule</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Up Time</h3>
                            <h1 id="ceksensor3"><?php echo $ceksensor3; ?></h1>
                        </div>
                        <div class="progress">
                            <img src="images/time.png">
                            <div class="number">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END OF MOST USED MACHINE -->
                <!-- MOST ACTIVE WELDERS -->
                <div class="income">
                    <span class="material-symbols-outlined">vibration</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Name</h3>
                            <h1 id="ceksensor1"><?php echo $ceksensor1; ?></h1>
                        </div>
                        <div class="progress">
                            <img src="images/mode.png">
                            <div class="number">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END OF MOST ACTIVE WELDERS -->
            </div>
            <!-- END OF INSIGHTS -->

            <div class="recent-orders">
                <div>
                    <!--<canvas id="myChart" style="height: 130px;"></canvas>-->

                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    $(document).ready(function() {
                        var ctx = document.getElementById('myChart');
                        var myChart;
                        var initialData;

                        $.ajax({
                            url: 'getArcOnOff.php',
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                console.log('Received data:', data);
                                initialData = data;
                                createChart(data);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching chart data:', error);
                            }
                        });

                        function createChart(data) {
                            var maxValues = data.map(entry => Math.max(entry.CurrentDC, entry.Voltage));

                            myChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: data.map(entry => entry.WeldID),
                                    datasets: [{
                                        label: 'Current and Voltage',
                                        data: maxValues,
                                        backgroundColor: 'transparent' // Transparent background, we'll draw manually
                                    }]
                                },
                                options: {
                                    scales: {
                                        x: {
                                            stacked: true
                                        },
                                        y: {
                                            stacked: true,
                                            beginAtZero: true
                                        }
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    var dataIndex = context.dataIndex;
                                                    var dataEntry = data[dataIndex];
                                                    return [
                                                        `Current DC: ${dataEntry.CurrentDC}`,
                                                        `Voltage: ${dataEntry.Voltage}`,
                                                        `WeldID: ${dataEntry.WeldID}`,
                                                        `ArcOn: ${dataEntry.ArcOn}`,
                                                        `ArcOff: ${dataEntry.ArcOff}`
                                                    ];
                                                }
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 0
                                    }
                                },
                                plugins: [{
                                    beforeDraw: function(chart) {
                                        var ctx = chart.ctx;
                                        var yAxis = chart.scales['y'];
                                        var xAxis = chart.scales['x'];

                                        chart.data.datasets[0].data.forEach((value, index) => {
                                            var meta = chart.getDatasetMeta(0).data[index];
                                            var barBottom = yAxis.getPixelForValue(0);
                                            var barTop = yAxis.getPixelForValue(value);

                                            // Draw Current DC
                                            var currentHeight = yAxis.getPixelForValue(0) - yAxis.getPixelForValue(data[index].CurrentDC);
                                            ctx.fillStyle = 'blue';
                                            ctx.fillRect(meta.x - meta.width / 2, barBottom - currentHeight, meta.width, currentHeight);

                                            // Draw Voltage
                                            var voltageHeight = yAxis.getPixelForValue(0) - yAxis.getPixelForValue(data[index].Voltage);
                                            ctx.fillStyle = 'yellow';
                                            ctx.fillRect(meta.x - meta.width / 2, barBottom - voltageHeight, meta.width, voltageHeight);
                                        });
                                    }
                                }]
                            });
                        }
                    });
                </script>

            </div>
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
                        <p>Hey, <b>Metronarc</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="../../../images/Logo.png" alt="AdminLogo">
                    </div>
                </div>
            </div>
            <!-- END OF TOP -->
            <div class="recent-updates">
                <h2>Arc On/Off</h2>
                <div class="updates" id="welder-updates">
                    <!-- Data will be dynamically added here using JavaScript -->
                </div>
                <script>
                    $(document).ready(function() {
                        // Function to fetch data from PHP script and update the updates div
                        function fetchWelderData() {
                            $.ajax({
                                url: 'getArcOnOffRight.php',
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    // Clear existing content
                                    $('#welder-updates').empty();

                                    // Iterate through data and append to updates div
                                    data.forEach(function(entry) {
                                        $('#welder-updates').append(`
                            <div class="update">
                                <div class="profile-photo">
                                    <img src="../../../images/welder.png">
                                </div>
                                <div class="message">
                                    <p>Arc On: <b>${entry.ArcOn}</b></p>
                                    <small class="text-muted">Arc Off: <b>${entry.ArcOff}</b></small>
                                    <p>Arc Total: <b>${entry.ArcTotal}</b></p>
                                </div>
                            </div>
                        `);
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error fetching data:', error);
                                }
                            });
                        }

                        // Fetch data initially and set an interval to fetch data every second
                        fetchWelderData();
                        setInterval(fetchWelderData, 1000); // Update every 1 second
                    });
                </script>
            </div>
            <div class="recent-updates">
                <div class="updates" id="welder-updates">
                    <h2>Login Time: <div id="inside">
                            <h2><?php echo $ceksensor5; ?></h2>
                        </div>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../js/index.js"></script>

</body>

</html>