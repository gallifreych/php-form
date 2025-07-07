<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "iletisim";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


?>