<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPARC Monitoring Dashboard Login</title>
    <link rel="icon" type="image/svg+xml" href="Logo.png" />
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body data-aos="zoom-in">

    <?php
    // Start the session
    session_start();

    // Assuming you have a database connection
    $servername = "localhost";
    $username = "u558841402_ronstan";
    $password = "2468g0a7A7B7*";
    $dbname = "u558841402_ronstandb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user is already authenticated or if a valid remember me token exists
    if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
        header("Location: Dashboard/index.php");
        exit();
    } elseif (isset($_COOKIE["remember_me_token"])) {
        $token = $_COOKIE["remember_me_token"];
        $sql = "SELECT * FROM users WHERE remember_me_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Valid token, set session and redirect to the Dashboard
            $_SESSION["authenticated"] = true;
            header("Location: Dashboard/index.php");
            exit();
        }
    }

    $conn->close();
    ?>

    <!-- Navigation Bar -->
    <header>
        <nav class="navbar">
            <a href="#" class="logo">
                <img src="./Logo.png" alt="logo">
            </a>
        </nav>
    </header>
    <!-- Navigation Bar -->

    <!-- Form Popup -->
    <div class="form-popup">
        <div class="form-box">
            <div class="form-details">
                <h2>Welcome</h2>
                <p>Please Log in Using Correct Credentials</p>
            </div>
            <div class="form-content">
                <h2>LOGIN</h2>
                <form action="login.php" method="post">
                    <div class="input-field">
                        <input type="text" name="email" required>
                        <label>Username</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <div class="policy-text" style="margin-top: 20px;">
                        <input type="checkbox" id="policy" name="remember_me">
                        <label for="policy">Remember me</label>
                    </div>
                    <button type="submit">Log in</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Form Popup -->

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
