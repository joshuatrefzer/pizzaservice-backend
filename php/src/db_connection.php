<?php
$host = 'db';
$user = 'app_user';
$pass = 'app_password';
$dbname = 'pizza_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
