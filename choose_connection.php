<?php
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    include 'db_connect_local.php';
} else {
    include 'db_connect_online.php';
}
?>
