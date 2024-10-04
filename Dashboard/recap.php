<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../Logo.png" />
    <script type="text/javascript" src="jquery/jquery.min.js"></script>
    <!-- Material Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="styles/index.css">
    <title>SPARC Monitoring Dashboard</title>
    <style>
        /* Additional styles for the dropdown */
        .machine-input,
        .date-input {
            border: 1px solid #ccc;
            padding: 10px;
            /* Increased padding for more height */
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            /* Ensures padding is included in width */
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
            margin: 0;
            /* Remove default margin from p tag */
            color: white;
            /* Set text color to white */
            font-size: 18px;
            /* Increase font size */
        }

        /* Chart Container and Buttons styling */
        #chart-container {
            position: relative;
            /* Make the container a positioned element */
        }

        #reset-zoom,
        #move-left,
        #move-right {
            position: absolute;
            top: 10px;
            /* Adjust as needed */
            z-index: 10;
            /* Ensure it appears above other elements */
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #reset-zoom {
            position: absolute;
            top: 0px;
            /* Adjust as needed */
            right: 10px;
            /* Adjust to position it correctly */
        }

        #move-left {
            position: absolute;
            top: 0px;
            /* Adjust as needed */
            right: 205px;
            /* Adjust to position it correctly */
        }

        #move-right {
            position: absolute;
            top: 0px;
            /* Adjust as needed */
            right: 110px;
            /* Adjust to position it correctly */
        }
    </style>
</head>

<body>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
    <!-- Chart.js Zoom Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.0/dist/chartjs-plugin-zoom.min.js"></script>

    <!-- Chart.js Date Adapter -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    <!-- xlsx library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <div class="container">
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
                <a href="index.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="recap.php" class="active">
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
            <h1>Machine Recap</h1>
            <div class="date"></div>
            <div class="insights">
                <!-- ACTIVE AREA -->
                <div class="sales">
                    <span class="material-symbols-outlined">zoom_in_map</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Input Machine Name</h3>
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
                            <h3>Input Date</h3>
                            <input type="date" id="date-input" class="date-input">
                        </div>
                        <div class="progress">
                            <a id="fetch-data" href="#">
                                <p>Enter</p>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="sales">
                    <span class="material-symbols-outlined">zoom_in_map</span>
                    <div class="middle">
                        <div class="left">
                            <h3>Usage Percentage (24h)</h3>
                            <h2>50%</h2>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="42" cy="42" r="36"></circle>
                            </svg>
                            <div class="number">
                                <h3></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="recent-orders">
                <div id="chart-container">
                    <canvas id="chart"></canvas>
                    <!-- Reset Zoom and Move Buttons -->
                    <button id="reset-zoom">Reset Zoom</button>
                    <button id="move-left">Move Left</button>
                    <button id="move-right">Move Right</button>
                </div>
            </div>
        </main>
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
                        <a href="Machine/Machine.php"><img src="images/Logo.png" alt="AdminLogo"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/index.js"></script>
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
                    body: JSON.stringify({
                        machineName: machineName,
                        date: date
                    })
                });
                const data = await response.json();

                // Render the chart with the fetched data and user-selected date
                renderChart(data, date);
            } else {
                alert('Please select a machine and date.');
            }
        });

        document.getElementById('reset-zoom').addEventListener('click', function() {
            if (chartInstance) {
                chartInstance.resetZoom();
            }
        });

        document.getElementById('move-left').addEventListener('click', function() {
            if (chartInstance) {
                chartInstance.pan({
                    x: 100
                });
            }
        });

        document.getElementById('move-right').addEventListener('click', function() {
            if (chartInstance) {
                chartInstance.pan({
                    x: -100
                });
            }
        });

        function renderChart(data, date) {
            const dataPoints = [];
            const backgroundColors = [];
            const borderColors = [];
            const hoverLabels = [];

            for (let i = 0; i < 24 * 60; i++) {
                const time = moment().startOf('day').minutes(i).format('HH:mm');
                let color = '#ebd234';
                let hoverLabel = '';

                data.forEach(interval => {
                    if (interval.ArcOn && interval.ArcOff) { // Check if both times are defined
                        const arcOnTime = timeToMinutes(interval.ArcOn);
                        const arcOffTime = timeToMinutes(interval.ArcOff);

                        if (arcOnTime !== null && arcOffTime !== null) { // Check if conversion was successful
                            if (i >= arcOnTime && i < arcOffTime) {
                                color = '#008000';
                                if (i === arcOnTime) {
                                    hoverLabel = `ArcOn: ${interval.ArcOn}, ArcOff: ${interval.ArcOff}, ArcTotal: ${arcOffTime - arcOnTime} minutes`;
                                }
                            }
                        }
                    } else {
                        console.error("Missing ArcOn or ArcOff time in data:", interval);
                    }
                });

                dataPoints.push({
                    x: timeToDateTime(time, date),
                    y: 1,
                    label: hoverLabel
                });
                backgroundColors.push(color);
                borderColors.push(color);
                hoverLabels.push(hoverLabel);
            }

            const ctx = document.getElementById('chart').getContext('2d');

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
                            enabled: true,
                            callbacks: {
                                label: function(tooltipItem) {
                                    const label = tooltipItem.raw.label;
                                    return label ? label : '';
                                }
                            }
                        },
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'x',
                                modifierKey: 'ctrl',
                            },
                            zoom: {
                                enabled: true,
                                mode: 'x',
                                drag: {
                                    enabled: true,
                                    backgroundColor: 'rgba(225,225,225,0.3)',
                                },
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true,
                                }
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
                                    const specificTimes = ['00:01', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00', '23:59'];
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

        function timeToMinutes(time) {
            if (!time) {
                console.error("Invalid time value:", time);
                return null; // or return a default value like 0 if that makes sense in your context
            }
            const [hours, minutes] = time.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function timeToDateTime(time, date) {
            return moment(date + ' ' + time, 'YYYY-MM-DD HH:mm').toDate();
        }
    </script>

    <script>
        // Example in your JavaScript
        document.getElementById('fetch-data').addEventListener('click', async function(event) {
            event.preventDefault();

            const machineDropdown = document.getElementById('machine-dropdown');
            const dateInput = document.getElementById('date-input');

            const machineName = machineDropdown.value;
            const date = dateInput.value;

            console.log('Selected Date:', date);  // Should print YYYY-MM-DD

            if (machineName && date) {
                try {
                    const response = await fetch('fetchMachineData.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            machineName: machineName,
                            date: date
                        })
                    });

                    if (!response.ok) throw new Error('Error fetching data');

                    const data = await response.json();

                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Convert totalArcTime (in seconds) to usage percentage
                        const totalArcTimeInSeconds = data.totalArcTime;
                        const totalSecondsInDay = 24 * 60 * 60; // 86,400 seconds in 24 hours
                        const usagePercentage = (totalArcTimeInSeconds / totalSecondsInDay) * 100;

                        // Update the usage percentage in the UI
                        document.querySelector('.sales h2').textContent = `${(totalArcTimeInSeconds / 86400 * 100).toFixed(2)}%`;
                        console.log('Total Arc Time in Seconds:', data.totalArcTime);
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to fetch or process the data.');
                }
            } else {
                alert('Please select a machine and date.');
            }
        });
    </script>
</body>

</html>