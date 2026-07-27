<?php

session_start();

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ticketing_db';

$conn = new mysqli($servername, $username, $password, $dbname);
// $conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn->connect_error){
    die("connection error:".$conn->error);
}
?>
