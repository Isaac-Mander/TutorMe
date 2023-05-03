<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Want a hamburger</title>
</head>
<body>
    <a href="../sys_page/hamburger.php"><img src="sys_img/icons8-menu-rounded-100.png" alt="Menu Button"></a>
<?php
//If the user is logged in show logout button rather than login button
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in
{
    echo "<p><a href='../login_form.php'>Login</a></p>"; //Show login button
}
else {echo "<p><a href='logout.php'>Logout</a></p>";} //If logged in show logout button
?>
    <p><a href="../user_home.php">Student Home</a></p>
    <p><a href="../user_profile.php">Profile</a></p>
    <p><a href="../#">Tutor Others</a></p>
    <p><a href="../tutorial.php">Tutorials</a></p>
    <p><a href="../past_sessions.php">Past Sessions</a></p>
    <p><a href="../#">EEEEEEEE</a></p>
    <p><a href="../admin_view.php">Admin Dashboard</a></p>
    <h2><p><object data="testing" type=""></object></p></h2>
</body>
</html>