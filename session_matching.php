<?php
//Import functions\
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

?>
  <?php
      $avaliable_session_tutor_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_subjects_tutor ON 6969_subjects_tutor.tutor_id=6969_students.id WHERE 6969_students.id=3";
      $avaliable_tutor_times_data = get_session_select_data($avaliable_session_times_sql, $conn);

      
      $avaliable_session_tutee_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_subjects_tutee ON 6969_subjects_tutee.tutee_id=6969_students.id WHERE 6969_students_id!=3";
      $avaliable_tutee_times_data = get_session_select_data($avaliable_session_times_sql, $conn);
      
      
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
    <h1>Hello, world!</h1>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>