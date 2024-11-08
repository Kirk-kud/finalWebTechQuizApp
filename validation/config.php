<?php
    $servername = "http://169.239.251.102/";
    $username = "kirk.kudoto";
    $password = "";
    $dbname = "webtech_fall2024_kirk_kudoto";

    $conn = mysqli_connect($servername, $username, $password, $dbname) or die ("could not connect to database");


    if ($conn->connect_error){
        echo "Connection failed";
        die("Connection failed " . $conn->connect_error);

    }
?>