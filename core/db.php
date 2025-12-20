<?php
require_once __DIR__ . '/lang_init.php';

$host = "localhost";
$user = "root"; // username 
$pass = "";     // password 
$dbname = "epubread";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    $msg = isset($lang['db_connection_failed']) ? $lang['db_connection_failed'] : "Connection failed: ";
    die($msg . $conn->connect_error);
}

?>
