<?php
$host = "localhost";
$username = "adbook";
$password = "jk3ni40ik266eu8a";
$database = "adbook_center_db";
$conn = mysqli_connect($host, $username, $password, $database);
//or die("Connection failed"); will be added later
if(!$conn){
    die("Connection failed");
}
$pdo = new PDO('mysql:host=localhost;dbname=coop_db;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>