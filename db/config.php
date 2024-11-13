<?php
    $servername = "localhost";
    $username = "kirk.kudoto";
    $password = "Mawuyram1";
    $dbname = "webtech_fall2024_kirk_kudoto";

    $conn = mysqli_connect($servername, $username, $password, $dbname) or die ("could not connect to database");


    if ($conn->connect_error){
        echo "Connection failed";
        die("Connection failed " . $conn->connect_error);

    }
?>