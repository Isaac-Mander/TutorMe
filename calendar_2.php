<!doctype html>
<html lang="en">
  <?php
include("sys_page/db_connect.php");
include("sys_page/functions.php");

$end_time =  $_POST["end_time"];
$start_time = $_POST["start_time"];
$day_of_week = $_POST['day_of_week'];
//Gets the data sent from the form from calendar_1.php
$student_id = $_POST['student_id'];

$query = "INSERT INTO `6969_student_times` (student_id, session_start, session_end, day_of_week) VALUES ('$student_id', '$start_time', '$end_time','$day_of_week');";
//Inserts this information into the database
$res = mysqli_query($conn, $query);
?>
<script>
  window.location.href = "calendar_1.php";
  //forces the user back to the original page
</script>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>