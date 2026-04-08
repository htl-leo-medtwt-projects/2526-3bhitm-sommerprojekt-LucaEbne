<?php
$_db_host = "db_server";
$_port = 3306;
$_db_username = "Sommerprojekt25/26";
$_db_password = "sommerprojektpassword";
$_db_datenbank = "Sommerprojekt25/26";

$conn = mysqli_connect($_db_host, $_db_username,
 $_db_password, $_db_datenbank, $_port);

 if($conn->connect_error){
     die("Connection failed: " . $conn->connect_error);
 }
