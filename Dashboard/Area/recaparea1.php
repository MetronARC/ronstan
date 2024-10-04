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
?>

<?php
// Database credentials
include "../koneksi.php";

// Check the connection
if ($konek->connect_error) {
    die("Connection failed: " . $konek->connect_error);
}

// Fetch data from the database
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Modify the SQL query to use the specified date range
$sql = "SELECT * FROM recaparea1 WHERE Date BETWEEN '$startDate' AND '$endDate'"; // Replace 'your_table_name' with the actual table name
$result = $konek->query($sql);

// Close the database connection
$konek->close();
?>


<?php
// Database credentials
include "../koneksi.php";

// Check the connection
if ($konek->connect_error) {
    die("Connection failed: " . $konek->connect_error);
}

// Fetch data from the database
$startDate2 = isset($_GET['startDate2']) ? $_GET['startDate2'] : '';
$endDate2 = isset($_GET['endDate2']) ? $_GET['endDate2'] : '';

// Modify the SQL query to use the specified date range
$sql = "SELECT * FROM machinehistory1 WHERE Date BETWEEN '$startDate2' AND '$endDate2'"; // Replace 'your_table_name' with the actual table name
$result2 = $konek->query($sql);

// Close the database connection
$konek->close();
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
    <link rel="stylesheet" href="css/recaparea1.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <title>SPARC Monitoring Dashboard</title>
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
       $(document).ready(function(){
         setInterval(function(){
            console.log('Inside setInterval function');
        $.get('cekMachine1State.php', function(data) {
            console.log(data); // check the value of data
            if (data.trim() === 'Active') {
                $("#img1").attr("src", "images/machineActive.png");
            } else {
                $("#img1").attr("src", "images/machineInactive.png");
            }
        }).fail(function() {
            console.log('AJAX request failed'); // handle AJAX request failure
        });
    }, 1000);
});
    </script>
    <script>
        $(document).ready(function(){
            setInterval(function(){
                $("#cekMachine1ID").load('AreaMonitoring1/Machine1/cekMachine1ID.php');
                $("#cekMachine2ID").load('AreaMonitoring1/Machine1/cekMachine2ID.php');
            }, 1000);
        });
    </script>
    

    <div class="container">
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
            <h1>Area 1 Recap</h1>

            <div class="recent-orders">
                <div style="display: flex; align-items: center; justify-content: center; gap: 3rem">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 5rem">
    <div style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
        <h2>Area 1 Machine Usage History</h2>
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate" style="width: 100px">
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate" style="width: 100px">
        <button onclick="fetchDataByDate()">Fetch Data</button>
    </div>
</div>
                    <div id="exportBtn" style="width: 8rem; height: 2.5rem; background: green; display: flex; align-items: center; justify-content: center; box-shadow: 0 2rem 3rem rgba(132, 139, 200, 0.18); border-radius: 20%; color: white; font-weight: bold; cursor: pointer; margin-right: 1rem;">
                        Export to Excel
                    </div>
                </div>
                <table id="table1">
    <thead>
        <tr>
            <th>Machine No.</th> <!-- Add the new column header -->
            <th>Weld ID</th>
            <th>Area</th>
            <th>UID</th>
            <th>Name</th>
            <th>Date</th>
            <th>Login</th>
            <th>Logout</th>
            <th>Up Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display data in the table
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                
                // Add the new column with the conditional logic
                echo "<td>";
                switch ($row['MachineID']) {
                    case "MACHINE-1":
                        echo "WM-562";
                        break;
                    case "MACHINE-2":
                        echo "WM-688";
                        break;
                    case "MACHINE-3":
                        echo "WM-518";
                        break;
                    case "MACHINE-4":
                        echo "WM-728";
                        break;
                    case "MACHINE-5":
                        echo "WM-750";
                        break;
                    case "MACHINE-6":
                        echo "WM-684";
                        break;
                    case "MACHINE-7":
                        echo "WM-123";
                        break;
                    default:
                        echo "Unknown Machine";
                        break;
                }
                echo "</td>";
                
                echo "<td>" . $row['WeldID'] . "</td>"; 
                echo "<td>" . $row['Area'] . "</td>"; 
                echo "<td>" . $row['UID'] . "</td>"; 
                echo "<td>" . $row['Name'] . "</td>"; 
                echo "<td>" . $row['Date'] . "</td>"; 
                echo "<td>" . $row['Login'] . "</td>"; 
                echo "<td>" . $row['Logout'] . "</td>"; 
                echo "<td>" . $row['upTime'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No data available</td></tr>";
        }
        ?>
    </tbody>
</table>

            <div style="display: flex; align-items: center; justify-content: center; gap: 3rem">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10rem">
    <div style="display: flex; align-items: center; justify-content: center; gap: 1rem">
        <h2>Area 1 Machine Arc On/Off History</h2>
        <label for="startDate2">Start Date:</label>
        <input type="date" id="startDate2" name="startDate2" style="width: 100px">
        <label for="endDate2">End Date:</label>
        <input type="date" id="endDate2" name="endDate2" style="width: 100px">
        <button onclick="fetchArcHistoryByDate()">Fetch Data</button>
    </div>
</div>
                    <div id="exportBtn2" style="width: 8rem; height: 2.5rem; background: green; display: flex; align-items: center; justify-content: center; box-shadow: 0 2rem 3rem rgba(132, 139, 200, 0.18); border-radius: 20%; color: white; font-weight: bold; cursor: pointer;  margin-right: 1rem;">
                        Export to Excel
                    </div>

                </div>
            <table id="table2">
                <thead>
                    <tr>
                        <th>Machine ID</th>
                        <th>Weld ID</th>
                        <th>Area</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Arc On</th>
                        <th>Arc Off</th>
                        <th>Arc Total</th>
                        <th>Current DC</th>
                        <th>Voltage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display data in the table
                    if ($result2->num_rows > 0) {
                        while ($row = $result2->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['MachineID'] . "</td>"; 
                            echo "<td>" . $row['WeldID'] . "</td>"; 
                            echo "<td>" . $row['Area'] . "</td>"; 
                            echo "<td>" . $row['Name'] . "</td>"; 
                            echo "<td>" . $row['Date'] . "</td>"; 
                            echo "<td>" . $row['ArcOn'] . "</td>"; 
                            echo "<td>" . $row['ArcOff'] . "</td>"; 
                            echo "<td>" . $row['ArcTotal'] . "</td>"; 
                            echo "<td>" . $row['CurrentDC'] . "</td>"; 
                            echo "<td>" . $row['Voltage'] . "</td>"; 
                            // ... Repeat for other columns ...
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
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
                        <img src="../images/Logo.png" alt="AdminLogo">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/index.js"></script>
    <script>
    document.getElementById('exportBtn').addEventListener('click', function () {
        exportToExcel('table1');
    });

    function getCurrentDateTime() {
        var today = new Date();
        var day = today.getDate();
        var month = today.getMonth() + 1; // Month is zero-based
        var year = today.getFullYear();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var seconds = today.getSeconds();

        return (
            year +
            '-' +
            (month < 10 ? '0' : '') + month +
            '-' +
            (day < 10 ? '0' : '') + day +
            '_' +
            (hours < 10 ? '0' : '') + hours +
            '-' +
            (minutes < 10 ? '0' : '') + minutes +
            '-' +
            (seconds < 10 ? '0' : '') + seconds
        );
    }

    function exportToExcel(tableId) {
    var table = document.getElementById(tableId);
    var ws = XLSX.utils.table_to_sheet(table);
    
    // Specify column widths (adjust values as needed)
    var cols = [
        { wch: 15 }, // Column A
        { wch: 15 }, // Column B
        { wch: 15 }, // Column C
        { wch: 15 }, // Column D
        // Add more as needed
    ];
    
    // Use XLSX.utils.json_to_sheet to include column widths
    var ws = XLSX.utils.json_to_sheet(XLSX.utils.sheet_to_json(ws), { header: 1, cols: cols });
    
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    var fileName = 'Area 1_Machine_Usage_History_' + getCurrentDateTime() + '.xlsx';

    XLSX.writeFile(wb, fileName);
}
</script>
<script>
    document.getElementById('exportBtn2').addEventListener('click', function () {
        exportToExcel('table2');
    });

    function getCurrentDateTime() {
        var today = new Date();
        var day = today.getDate();
        var month = today.getMonth() + 1; // Month is zero-based
        var year = today.getFullYear();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var seconds = today.getSeconds();

        return (
            year +
            '-' +
            (month < 10 ? '0' : '') + month +
            '-' +
            (day < 10 ? '0' : '') + day +
            '_' +
            (hours < 10 ? '0' : '') + hours +
            '-' +
            (minutes < 10 ? '0' : '') + minutes +
            '-' +
            (seconds < 10 ? '0' : '') + seconds
        );
    }

    function exportToExcel(tableId) {
        var table = document.getElementById(tableId);
        var ws = XLSX.utils.table_to_sheet(table);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        var fileName = 'Area 1_ArcOn_ArcOff_History_' + getCurrentDateTime() + '.xlsx';

        XLSX.writeFile(wb, fileName);
    }
</script>
    <script>
        function fetchDataByDate() {
            var startDate = document.getElementById('startDate').value;
            var endDate = document.getElementById('endDate').value;
            window.location.href = 'recaparea1.php?startDate=' + startDate + '&endDate=' + endDate;
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Your JavaScript code here
        });
    </script>
<script>
    function fetchArcHistoryByDate() {
        var arcHistoryStartDate = document.getElementById('startDate2').value;
        var arcHistoryEndDate = document.getElementById('endDate2').value;
        window.location.href = 'recaparea1.php?startDate2=' + arcHistoryStartDate + '&endDate2=' + arcHistoryEndDate;
    }
</script>





</body>
</html>
