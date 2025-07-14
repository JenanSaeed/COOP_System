<?php
$host = "sql103.infinityfree.com";
$username = "if0_39466845";
$password = "coopdatabase111";
$database = "if0_39466845_coop_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
