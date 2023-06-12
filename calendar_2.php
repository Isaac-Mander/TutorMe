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
<<<<<<< Updated upstream
$query = "INSERT INTO `6969_student_times` (student_id, session_start, session_end, day_of_week) VALUES ('$student_id', '$start_time', '$end_time','$day_of_week');";
//Inserts this information into the database
$res = mysqli_query($conn, $query);
=======


//Check through several error states
//If start time is before end time (else invalid session)
if ($end_time > $start_time){

  //Check if a session overlaps the new one
  $check_sql = "SELECT * FROM `6969_student_times` WHERE `student_id`=$student_id AND `day_of_week`=$day_of_week AND (('$start_time' < `session_end` AND '$end_time' > `session_end`) OR ('$start_time' < `session_start` AND '$end_time' > `session_start`) OR ('$start_time' < `session_start` AND '$end_time' > `session_end`))";
  $check_results = mysqli_query($conn, $check_sql);
  //If any sessions were found that overlap, send error msg
  if($check_results->num_rows > 0)
  {
    $invalid_time = 2;
  }
  else
  {
    $query = "INSERT INTO `6969_student_times` (student_id, session_start, session_end, day_of_week) VALUES ('$student_id', '$start_time', '$end_time','$day_of_week');";
    //Inserts this information into the database
      $res = mysqli_query($conn, $query);
      $invalid_time = 0;
  }
}
else
{
  $invalid_time = 1;
}

//Send user back to frontend calendar page
header("Location: calendar_1.php?invalid_time=" . $invalid_time);
>>>>>>> Stashed changes
?>

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