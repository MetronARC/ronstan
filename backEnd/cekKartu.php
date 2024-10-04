<?php
    //koneksi ke database
    include "../Dashboard/koneksi.php";

    //baca data
    $UID = $_GET['UID'];

    // Check if UID exists in employee table
    $query = "SELECT Name FROM employee WHERE cardUID= '$UID'";
    $result = mysqli_query($konek, $query);
    if (mysqli_num_rows($result) > 0) {
        // UID exists, retrieve corresponding Name value
        echo "OK";
    } else {
        // UID does not exist, set Name to empty string
        echo "No";
    }
?>