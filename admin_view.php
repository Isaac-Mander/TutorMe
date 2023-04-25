<?php
//Import functions
include("sys_page/functions.php");

//Check if user is admin
session_start();
$_SESSION['admin_id'] = 1;
if(!isset($_SESSION['admin_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.html"); //Send to the shadow realm (login screen)
}
//Get info at school

//Connect to database
include("sys_page/db_connect.php");

//Put every student in the school into a single array
$sql = "SELECT * FROM 6969_students";
$student_results = $conn->query($sql); //Query database
if ($student_results->num_rows > 0) { //If the number of rows are not zero
    $i = 0;
    while($row = $student_results->fetch_assoc()) {
      //Array with student info linked to school/admin
      //Formatted like [index of student in array (not student table)][name, user icon file, hours spent tutoring others, number of individual tutoring sessions, user id] 
      $student_info[$i][0] = $row["name"];
      $student_info[$i][1] = $row["picture"];
      $student_info[$i][2] = $row["hours_tutored"];
      $student_info[$i][3] = $row["sessions_tutored"];
      $student_info[$i][4] = $row["id"];
      $i += 1;
    }
  }



?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php
    //Add the header
    include("sys_page/header.html");
    ?>
    <h1>View student details</h1>
    <div class="filter">
      <p>Filter by name</p>
      <input type="text">
    </div>
    
    <?php
    for($x = 0; $x <= sizeof($student_info)-1; $x++)
    {
    //Set up the info to show
    $name = $student_info[$x][0];
    $picture = $student_info[$x][1];
    $hours_tutored = $student_info[$x][2];
    $sessions_tutored = $student_info[$x][3];
    $user_id = $student_info[$x][4];
    
    echo "<a href='user_profile.php?user_id=$user_id'>";
    echo "<div class='card'>";
    echo "<img class='rounded-circle img-fluid w-25' src='sys_img/$picture' alt='the users icon photo'>";
    echo "<p>$name</p>";
    echo "<p>$hours_tutored</p>";
    echo "<p>$sessions_tutored</p>";
    echo "</div>";
    echo "</a>";
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>