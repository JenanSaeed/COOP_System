<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "";
$conn = mysqli_connect($host, $username, $password, $database);
//or die("Connection failed"); will be added later
if(!$conn){
    die("Connection failed");
}
?>