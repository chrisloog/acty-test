<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "acty-test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>