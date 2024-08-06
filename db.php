<?php
function getcon() {
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'prod';

    $con = mysqli_connect($hostname, $username, $password, $database, 8002);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit();
    }

    return $con;
}









?>