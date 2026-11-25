<?php
$host = "localhost";
$username = "root";
$password = "Alvi@123";
$database = "smart_campus";
$port = 3306;

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>