<?php
function connectDB(): mysqli {
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $DBname = "Foody";
    
    $conn = new mysqli($servername, $username, $password, $DBname,null, '/path/to/mysql.sock');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

$GLOBALS['conn'] = connectDB()
?>
