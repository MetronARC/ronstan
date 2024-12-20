<?php
// Start the session
session_start();

include "Dashboard/koneksi.php";

if ($konek->connect_error) {
    die("Connection failed: " . $konek->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $remember_me = isset($_POST["remember_me"]);

    // Perform a simple query to check credentials
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $konek->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Valid credentials, set a session variable
        $_SESSION["authenticated"] = true;

        // If remember me is checked, set a cookie with a unique token
        if ($remember_me) {
            $token = bin2hex(random_bytes(16));
            setcookie("remember_me_token", $token, time() + (86400 * 30), "/"); // 30 days expiration

            // Save the token in the database
            $sql = "UPDATE users SET remember_me_token = ? WHERE email = ?";
            $stmt = $konek->prepare($sql);
            $stmt->bind_param("ss", $token, $email);
            $stmt->execute();
        }

        header("Location: Dashboard/index.php");
        exit();
    } else {
        // Invalid credentials, show a popup and redirect back to the login page
        echo '<script>alert("Invalid Credentials");';
        echo 'window.location.href = "index.php";</script>';
        exit();
    }
}

$konek->close();
?>
