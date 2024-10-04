<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../../Logo.png" />
    <script src="../../../jquery/jquery.min.js"></script>
    <!-- Material Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/Machine1.css">
    <title>SPARC Monitoring Dashboard</title>
    <style>
        /* Additional styles for the dropdown */
        .machine-input, .date-input {
            border: 1px solid #ccc;
            padding: 10px; /* Increased padding for more height */
            font-size: 16px;
            width: 100%;
            box-sizing: border-box; /* Ensures padding is included in width */
        }
        /* Button styling */
        #fetch-data {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 25px;
            text-align: center;
        }
        #fetch-data p {
            margin: 0; /* Remove default margin from p tag */
            color: white; /* Set text color to white */
            font-size: 18px; /* Increase font size */
        }
    </style>
</head>
<body>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
    <!-- Chart.js Date Adapter -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <!-- xlsx library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <!-- Chart.js Zoom Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.1.1/dist/chartjs-plugin-zoom.min.js"></script>

    <div class="container">
        <!-- SIDEBAR -->
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../../../../Logo.png" alt="Metronarc Logo">
                    <h3>Metronarc <span class="danger">Technology</span></h3>
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
                <a href="../../../employee.php">
                    <span class="material-symbols-outlined">badge</span>
                    <h3>Users</h3>
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
            <h1>Machine-1</h1>
            <div class="date"></div>
            <div class="insights">
                <!-- ACTIVE AREA -->
                <div class="sales">
                    <span class="material-symbols-outlined">zoom_in_map</span>
                    <div class="middle">
                        <div class="left">
                            <h2>Input Machine Name</h2>
                            <select id="machine-dropdown" class="machine-input">
                                <option value="" selected disabled>Select your Machine</option>
                                <!-- Options will be dynamically added here -->
                                <?php
                                // Database connection details
                                $servername = "localhost";
                                $username = "u558841402_ronstan";
                                $password = "2468g0a7A7B7*";
                                $dbname = "u558841402_ronstandb";

                                // Create connection
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                // Check connection
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Fetch data from the machine table
                                $sql = "SELECT realName FROM machine";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($row['realName']) . '">' . htmlspecialchars($row['realName']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No machines available</option>';
                                }

                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <div class="progress">
                            <!-- Progress bar can remain here -->
                        </div>
                    </div>
                </div>
                <div class="sales">
                    <span class="material-symbols-outlined">zoom_in_map</span>
                    <div class="middle">
                        <div class="left">
                            <h2>Input Date</h2>
                            <input type="date" id="date-input" class="date-input">
                        </div>
                        <div class="progress">
                            <a id="fetch-data" href="#">
                                <p>Enter</p>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- END OF MOST ACTIVE WELDERS -->
            </div>
            <div class="recent-orders">
                <div id="chart-container">
    <button id="zoom-in">Zoom In</button>
    <button id="zoom-out">Zoom Out</button>
    <button id="reset-zoom">Reset Zoom</button>
    <canvas id="chart"></canvas>
</div>
            </div>
        </main>
    </div>
    <script src="../../../js/index.js"></script>
    <script>
        let chartInstance = null;

        document.getElementById('fetch-data').addEventListener('click', async function(event) {
    event.preventDefault();

    const machineDropdown = document.getElementById('machine-dropdown');
    const dateInput = document.getElementById('date-input');

    const machineName = machineDropdown.value;
    const date = dateInput.value;

    if (machineName && date) {
        const response = await fetch('fetchData.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ machineName: machineName, date: date })
        });
        const data = await response.json();

        // Render the chart with the fetched data
        renderChart(data);
    } else {
        alert('Please select a machine and date.');
    }
});

function renderChart(data) {
    const dataPoints = [];
    const backgroundColors = [];
    const borderColors = [];

    for (let i = 0; i < 24 * 60; i++) {
        const time = moment().startOf('day').minutes(i).format('HH:mm');
        let color = 'yellow';
        let arcOnTime = '';
        let arcOffTime = '';
        let arcTotalTime = '';

        data.forEach(interval => {
            if (i >= timeToMinutes(interval.ArcOn) && i < timeToMinutes(interval.ArcOff)) {
                color = 'green';
                arcOnTime = interval.ArcOn;
                arcOffTime = interval.ArcOff;
                arcTotalTime = calculateArcTotal(interval.ArcOn, interval.ArcOff);
            }
        });

        dataPoints.push({ x: timeToDateTime(time), y: 1, arcOn: arcOnTime, arcOff: arcOffTime, arcTotal: arcTotalTime });
        backgroundColors.push(color);
        borderColors.push(color);
    }

    const ctx = document.getElementById('chart').getContext('2d');

    // Destroy the existing chart instance if it exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Machine On/Off',
                data: dataPoints,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    enabled: true, // Enable hover functionality
                    callbacks: {
                        label: function(context) {
                            const dataPoint = context.raw;
                            if (dataPoint.arcOn && dataPoint.arcOff) {
                                return `ArcOn: ${dataPoint.arcOn}, ArcOff: ${dataPoint.arcOff}, ArcTotal: ${dataPoint.arcTotal} minutes`;
                            } else {
                                return 'No data available';
                            }
                        }
                    }
                },
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x'
                    },
                    zoom: {
                        wheel: {
                            enabled: true
                        },
                        drag: {
                            enabled: false
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x'
                    }
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'minute',
                        displayFormats: {
                            minute: 'HH:mm'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Time'
                    },
                    ticks: {
                        source: 'data',
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        major: {
                            enabled: true
                        },
                        callback: function(value, index, values) {
                            const time = moment(value).format('HH:mm');
                            const specificTimes = ['00:01', '06:00', '12:00', '18:00', '23:59'];
                            return specificTimes.includes(time) ? time : '';
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        stepSize: 1,
                        callback: value => value === 1 ? 'On' : 'Off'
                    },
                    title: {
                        display: true,
                        text: 'Status'
                    }
                }
            }
        }
    });
}


document.getElementById('zoom-in').addEventListener('click', function() {
    chartInstance.zoom(1.1);
});

document.getElementById('zoom-out').addEventListener('click', function() {
    chartInstance.zoom(0.9);
});

document.getElementById('reset-zoom').addEventListener('click', function() {
    chartInstance.resetZoom();
});

function timeToDateTime(time) {
    return moment().startOf('day').format('YYYY-MM-DD') + ' ' + time;
}

function timeToMinutes(time) {
    if (!time) return 0; // Default to 0 if time is invalid
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
}

function calculateArcTotal(arcOn, arcOff) {
    const arcOnMinutes = timeToMinutes(arcOn);
    const arcOffMinutes = timeToMinutes(arcOff);
    return arcOffMinutes - arcOnMinutes;
}

    </script>
</body>
</html>
