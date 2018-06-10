<?php

echo "Hello from PHP1!\n";

$servername = "baza";
$username = "test";
$password = "test";
$dbname = "test";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "PHP 1 Connected successfully to MySQL server\n";
    }
catch(PDOException $e)
    {
    echo "PHP 1 Connection to MySQL server failed: " . $e->getMessage() . "\n";
    }

// close conection
$conn = null;

?>
