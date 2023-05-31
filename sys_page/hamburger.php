<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Want a hamburger</title>
</head>
<body>
  <!--  <a href="../sys_page/hamburger.php"><img src="sys_img/icons8-menu-rounded-100.png" alt="Menu Button"></a> -->
<?php
//If the user is logged in show logout button rather than login button
session_start(); ?>
<div class="card"></div>
<ul class="list-group list-group-flush">
<li class="list-group-item"><?php
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in
{
    echo "<p><a href='../login_form.php'>Login</a></p>"; //Show login button
}
else {echo "<p><a href='logout.php'>Logout</a></p>";}?></li>
<li class="list-group-item"><a href="../sys_page/hamburger.php">Menu</a> </li>
<li class="list-group-item"><a href="../user_home.php">Student Home</a> </li>
<li class="list-group-item"><a href="../user_profile.php">Profile</a> </li>
<li class="list-group-item"><a href="../session_matching.php">Tutor Others</a> </li>
<li class="list-group-item"><a href="../calendar_1.php">Set your free slots</a> </li>
<li class="list-group-item"><a href="../tutorial.php">Tutorials</a> </li>
<li class="list-group-item"><a href="../past_sessions.php">Past Sessions</a> </li>
<li class="list-group-item"><a href="../admin_view.php">Admin Dashboard</a> </li>
</ul>
    <h2><p><object data="testing" type=""></object></p></h2>
</body>
</html>