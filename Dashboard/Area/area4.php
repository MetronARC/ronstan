<?php
// Start the session
session_start();

// Check if the user is not authenticated, redirect to login
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: ../../index.html");
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
        header("Location: ../../index.html");
        exit();
    }
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();

// Define the total number of divs
$totalDivs = 7; // You can change this value accordingly
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../Logo.png" />
    <script type="text/javascript" src="../jquery/jquery.min.js"></script>
    <!-- Material Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/area1.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
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
            countdownInterval = setInterval(function () {

                if (timeout <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = "../../index.html";
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

    <script>
        $(document).ready(function () {
            <?php
            // Loop to generate divs
            for ($i = 1; $i <= $totalDivs; $i++) {
                echo '
                    setInterval(function(){
                        $.get(\'AreaMonitoring4/Machine' . $i . '/cekMachineState.php\', function(data) {
                            if (data.trim() === \'Active\') {
                                $("#img' . $i . '").attr("src", "images/machineActive.png");
                            } else if (data.trim() === \'Idle\') {
                                $("#img' . $i . '").attr("src", "images/machineIDLE.png");
                            } else {
                                $("#img' . $i . '").attr("src", "images/machineInactive.png");
                            }
                        }).fail(function() {
                            console.log(\'AJAX request failed\');
                        });
                    }, 1000);
                ';
            }
            ?>
        });
    </script>

    <script>
        $(document).ready(function () {
            <?php
            // Loop to fetch machine ID
            for ($i = 1; $i <= $totalDivs; $i++) {
                echo '
                    setInterval(function(){
                        $("#cekMachine' . $i . 'ID").load(\'AreaMonitoring4/Machine' . $i . '/cekMachineID.php\');
                    }, 1000);
                ';
            }
            ?>
        });
    </script>
    <script>
        $(document).ready(function () {
            <?php
            // Loop to fetch Last Arc Activity
            for ($i = 1; $i <= $totalDivs; $i++) {
                echo '
                    setInterval(function(){
                        $("#cekMachine' . $i . 'Arc").load(\'AreaMonitoring4/Machine' . $i . '/cekMachineArc.php\');
                    }, 1000);
                ';
            }
            ?>
        });
    </script>

    <div class="container" data-aos="zoom-out">
        <!-- SIDEBAR -->
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../../Logo.png" alt="Metronarc Logo">
                    <h3>Metronarc <span class="danger">Technology</span></h3>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-symbols-outlined">close</span>
                </div>
            </div>

            <div class="sidebar">
                <a href="../index.php">
                    <span class="material-symbols-outlined">dashboard</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="../employee.php">
                    <span class="material-symbols-outlined">badge</span>
                    <h3>Users</h3>
                </a>
                <a href="../monitoring.php" class="active">
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

            <div class="date" style="background-color: white; border: 1px solid #ccc; border-radius: 10px; padding: 10px; color: #37b4fe;">
                <a href="recaparea3.php"><h1 style="color: #37b4fe">Area Recap</h1></a>
            </div>

            <div class="insights">
                <?php
                // Loop to generate divs
                for ($i = 1; $i <= $totalDivs; $i++) {
                    echo '
                        <div class="sales">
                            <span class="material-symbols-outlined">zoom_in_map</span>
                            <div class="middle">
                                <div class="left">
                                    <h3 id="cekMachine' . $i . 'Arc"></h3>
                                    <h1 id="cekMachine' . $i . 'ID"></h1>
                                </div>
                                <div class="progress">
                                    <a href="AreaMonitoring4/Machine' . $i . '/Machine.php"><img id="img' . $i . '" src="images/machineInactive.png"></a>
                                </div>
                            </div>
                        </div>';
                }
                ?>
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
                        <p>Hey, <b>Metronarc</b></p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="../images/Logo.png" alt="AdminLogo">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="../js/index.js"></script>

</body>

</html>
