<?php
// Get the date from the URL query string
$date = isset($_GET['date']) ? $_GET['date'] : null;

if (!$date) {
    echo "<h2>No date provided. Please go back and select a date.</h2>";
    exit;
}
?>

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
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
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

        .datetime-input {
            border: 1px solid #ccc;
            padding: 7px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
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
    <script
        type="text/javascript"
        src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script
        type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link
        rel="stylesheet"
        type="text/css"
        href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Time Range Picker</title>
    <style>
        input[type="text"] {
            cursor: text;
        }
    </style>

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
            <h1>Machine Recapitulation</h1>
            <div class="date"></div>
            <div class="recent-orders">
                <div id="charts-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <!-- Charts will be dynamically inserted here -->
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
                        <p>Hey, <b>Ronstan</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <a href="Machine/Machine.php"><img src="images/Logo.png" alt="AdminLogo"></a>
                    </div>
                </div>
            </div>
            <div class="recent-updates">

            </div>
        </div>
    </div>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/index.js"></script>
    <script type="text/javascript">
        var selectedDate = '<?php echo $date; ?>'; // Get the date from PHP
        console.log("Selected date for chart: ", selectedDate);

        // Use the selected date to generate charts below
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // After loading, trigger the chart generation using the selected date
            if (selectedDate) {
                // Assume you have a function or logic to fetch chart data
                fetchChartData(selectedDate); // Use the selected date
            }
        });

        function fetchChartData(date) {
            // Replace this with your AJAX call or logic to fetch data for charts based on the date
            fetch('fetchDatas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        date: date // Send the selected date to the backend
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Clear previous charts
                    const chartsContainer = document.getElementById('charts-container');
                    chartsContainer.innerHTML = ''; // Clear previous charts

                    // Loop through machines and create a new canvas for each
                    for (const machineName in data) {
                        const machineData = data[machineName];

                        const canvas = document.createElement('canvas');
                        canvas.id = `chart-${machineName}`; // Unique ID for each chart
                        canvas.style.width = '30%'; // Ensures 3 charts per row
                        canvas.style.height = '100px';
                        chartsContainer.appendChild(canvas);

                        renderChart(machineData, date, machineName, canvas); // Generate chart
                    }
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                });
        }

        function renderChart(data, date, machineName, canvas) {
            const dataPoints = [];
            const backgroundColors = [];
            const borderColors = [];
            const hoverLabels = [];

            for (let i = 0; i < 24 * 60; i++) {
                const time = moment().startOf('day').minutes(i).format('HH:mm');
                let color = '#ebd234';
                let hoverLabel = '';

                data.forEach(interval => {
                    if (interval.ArcOn && interval.ArcOff) {
                        const arcOnTime = timeToMinutes(interval.ArcOn);
                        const arcOffTime = timeToMinutes(interval.ArcOff);

                        if (arcOnTime !== null && arcOffTime !== null) {
                            if (i >= arcOnTime && i < arcOffTime) {
                                color = '#008000';
                                if (i === arcOnTime) {
                                    hoverLabel = `ArcOn: ${interval.ArcOn}, ArcOff: ${interval.ArcOff}, ArcTotal: ${arcOffTime - arcOnTime} minutes`;
                                }
                            }
                        }
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

            const ctx = canvas.getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: `Usage for ${machineName}`,
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
                return null;
            }
            const [hours, minutes] = time.split(':').map(Number);
            return hours * 60 + minutes;
        }

        function timeToDateTime(time, date) {
            return moment(date + ' ' + time, 'YYYY-MM-DD HH:mm').toDate();
        }
    </script>

</body>

</html>