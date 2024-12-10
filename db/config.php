<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "quest";

    $conn = mysqli_connect($servername, $username, $password, $dbname) or die ("could not connect to database");


    if ($conn->connect_error){
        echo "Connection failed";
        die("Connection failed " . $conn->connect_error);

    }
?>