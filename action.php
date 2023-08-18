<?php
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}


//Import dependencies
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");
?>
<h1 class="text-center">Action page</h1>
<p class="text-center">On this page you can view your pending, scheduled, and past tutoring sessions, as well as propose new tutoring sessions.</p>
<?php

//Import session page
include("sessions.php");

//Add visual separation between components
echo "<br>";
echo "<br>";
echo "<br>";
?>
<h1 class="text-center">Tutor someone</h1>
<div class="flex-row d-flex justify-content-centre"><div class="card mx-auto justify-content-center p-5"><p class="border-bottom"><strong>Please note this will only show possible sessions with people who have the same subject and have an overlapping free time</strong></p></div></div></div>
<p class="text-center">Not enough choices? Try increasing the times you are free <a href="info_setting.php">here</a></p>
<?php
//Import session matching component
include("session_matching.php");
?>
<script src="content.js"></script>