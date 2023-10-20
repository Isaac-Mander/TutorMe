<?php
//connection information
$servername = "localhost";
$username = "root";
$password = "";
$database = "tutorme";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  //error message if connetion failed
  die("Connection failed: " . $conn->connect_error);
}
?>