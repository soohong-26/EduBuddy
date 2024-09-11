<!-- Connecting to Database -->
<?php
    session_start();

    $conn = new mysqli('localhost', 'root', '', 'edubuddy');

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
?>
