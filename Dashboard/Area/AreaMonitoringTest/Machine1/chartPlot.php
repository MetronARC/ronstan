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
    <link rel="stylesheet" href="css/Machine1.css">
    <title>SPARC Monitoring Dashboard</title>
</head>
<body>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script>
    <!-- Chart.js Date Adapter -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>

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
            <!-- END OF INSIGHTS -->

            <div class="recent-orders">
                <div id="chart-container">
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </main>
    </div>
    <script>
        // Function to convert time strings to full datetime strings
        function timeToDateTime(time) {
            return moment().startOf('day').format('YYYY-MM-DD') + ' ' + time;
        }

        // Function to convert time strings to minutes since start of the day
        function timeToMinutes(time) {
            const [hours, minutes] = time.split(':').map(Number);
            return hours * 60 + minutes;
        }

        async function fetchData() {
            const response = await fetch('fetchData.php');
            const data = await response.json();

            const dataPoints = [];
            const backgroundColors = [];
            const borderColors = [];

            // Generate data points from the fetched data
            for (let i = 0; i < 24 * 60; i++) {
                const time = moment().startOf('day').minutes(i).format('HH:mm');
                let color = 'yellow';
                data.forEach(interval => {
                    if (i >= timeToMinutes(interval.ArcOn) && i < timeToMinutes(interval.ArcOff)) {
                        color = 'green';
                    }
                });
                dataPoints.push({ x: timeToDateTime(time), y: 1 });
                backgroundColors.push(color);
                borderColors.push(color);
            }

            const ctx = document.getElementById('chart').getContext('2d');
            const chart = new Chart(ctx, {
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
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'minute',
                                displayFormats: {
                                    minute: 'HH:mm'
                                },
                                stepSize: 60
                            },
                            title: {
                                display: true,
                                text: 'Time'
                            },
                            ticks: {
                                source: 'auto',
                                autoSkip: true,
                                maxRotation: 0,
                                minRotation: 0
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

        // Fetch data and render the chart
        fetchData();
    </script>
    <script src="../../../js/index.js"></script>
</body>
</html>
