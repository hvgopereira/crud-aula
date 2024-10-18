<?php
$host = 'localhost';
$user = 'root'; // seu usuário do MySQL
$password = ''; // sua senha do MySQL
$database = 'crud_php';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
