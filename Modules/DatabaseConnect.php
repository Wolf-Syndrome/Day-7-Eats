<?php
define('USER', 'root');
define('PASSWORD', 'root1');
define('HOST', 'localhost');
define('DATABASE', 'Day7Eats');

try {
    $connection = new PDO("mysql:host=".HOST.";dbname=".DATABASE, USER, PASSWORD);
} catch (PDOException $e) {
    exit ("Error: " . $e->getMessage());
}
?>