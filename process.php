<?php
include("sys_page/db_connect.php");
include("sys_page/functions.php");

//Gets the data sent from the form from calendar_1.php
$sorting = $_POST['sorting'];
header("Location: action.php?sorting=" . $sorting);
?>
