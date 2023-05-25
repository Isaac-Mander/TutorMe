<?php
//Import functions
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

?>
  <?php
      $available_session_tutor_times_sql = "SELECT * FROM 6969_student_times INNER JOIN 6969_students ON 6969_student_times.student_id=6969_students.id WHERE 6969_student_times.student_id=3;";
      $status = TRUE;
      $available_tutor_times_data = get_session_select_data($available_session_tutor_times_sql, $conn, $status);

      
      $available_session_tutee_times_sql = "SELECT * FROM 6969_student_times INNER JOIN 6969_students ON 6969_student_times.student_id=6969_students.id WHERE 6969_student_times.student_id!=3;";
      $status = FALSE;
      $available_tutee_times_data = get_session_select_data($available_session_tutee_times_sql, $conn, $status);
      
      /*check for subject matches*/


        for($k=0; $k<sizeof($available_tutee_times_data); $k++){
          for($l=0; $l<sizeof($available_tutor_times_data); $l++){
            //If the selected tutor and tutee have the same subject
            if ($available_tutee_times_data[$k][6]== $available_tutor_times_data[$l][6]); {
              //If the selected tutor and tutee potential time has the same day of the week
              if($available_tutee_times_data[$k][3] == $available_tutor_times_data[$l][3]){
                //If the selected tutee has a potential start time which is above or equal to the tutor start time
                if ($available_tutee_times_data[$k][1]>= $available_tutor_times_data[$l][1]){
                  //If the selected tutee has a potential end time which is below or equal to the tutor end time
                  if($available_tutee_times_data[$k][2]<= $available_tutor_times_data[$l][2]){

                    //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor

                    //Get the relivant data and create a card for a tutor to click on
                    $potential_starttime = $available_tutee_times_data[$k][1];
                    $potential_endtime = $available_tutee_times_data[$k][2];
                    $name = $available_tutee_times_data[$k][4];
                    
                    $subject = $available_tutee_times_data[$k][6];
                    $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
                    $day_of_week = $days_of_week_array[$available_tutor_times_data[$l][3]-1];
                    $card_id = $available_tutee_times_data[$k][7];?>

                    <div id = '<?php echo $card_id; ?>' class='card' style="width: 18rem;"> <?php echo $name."     ".$potential_starttime."       ".$potential_endtime."    ".$subject."     ".$day_of_week; ?></div></a><?php
                 }
               }
              }
          }
        }
      }
      ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Matching</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>