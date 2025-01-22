<?php

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'moja_strona';

$login = "admin";
$pass = "haslo";


// łączenie z bazą
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
	
}


?>