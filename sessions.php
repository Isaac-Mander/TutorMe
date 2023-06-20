
<?php

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get relevant info from session
$user_id = $_SESSION['user_id'];

//Import functions
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

$check = 0;
//Get the sessions this user is tutoring today
$session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=$user_id";  
$session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);


//Get the sessions this user is being tutored today
$session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=$user_id";  
$session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);

if (is_array($session_today_tutor_data) && is_array($session_today_tutee_data)) {
    $session_combined_data = array_merge($session_today_tutor_data, $session_today_tutee_data);
  } else {
    // Handle the case where one or both variables is not an array
    // For example:
    $session_combined_data = array();};
  if (is_array($session_combined_data)) { ?>
    <div class="upcoming_day_sessions container text-center border border-2 border-dark extra_rounded">
            <div class="row">
                <h3 class="col text-center py-3 m-0">Pending</h3>
                <div class="col red_box extra_rounded_tr"></div>
            </div>
            <div class="row row-cols-1 row-cols-md-3 gx-5"><?php
            //Show the sessions today this user is tutoring
            for($i=0; $i<sizeof($session_combined_data); $i++)
            {
              if($session_combined_data[$i][9]==0){
                //looping through all of the lines of the array
                $day = substr($session_combined_data[$i][1],0,10); //setting the day value
                $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
                $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
                $tutee =  $session_combined_data[$i][4]; //setting tutee name
                $tutor = $session_combined_data[$i][6]; //setting tutor name
                $subject = $session_combined_data[$i][8]; //setting subject name
                ?>   <div class="col"> <div id="session_card" class='card' ><?php
                ?><h5 class="card-title" >Pending</h5>
                <p class="card-text"><?php echo $tutor." tutoring ".$tutee." in ".$subject  ?></p>
                <p class="card-text"><?php echo $day."  Start time: ".$starttime ."  End time: ".$endtime  ?></p></div></div>  <?php
                $check=1;
              }
        }     ?> </div>  <?php
        ?> 
    </div></div>

    <div class="upcoming_week_sessions container text-center border border-2 border-dark extra_rounded mt-4">
        <div class="row">
            <h3 class="col text-center py-1 m-0">Weekly</h3>
            <div class="col red_box extra_rounded_tr"></div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 gx-5"><?php
    for($i=0; $i<sizeof($session_combined_data); $i++){
      if($session_combined_data[$i][9]==1){
              //looping through all of the lines of the array
            $day = substr($session_combined_data[$i][1],0,10); //setting the day value
            $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
            $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
            $tutee =  $session_combined_data[$i][4]; //setting tutee name
            $tutor = $session_combined_data[$i][6]; //setting tutor name
            $subject = $session_combined_data[$i][8]; //setting subject name
      ?>   <div class="col" > <div id="session_card" class='card'><?php
      echo ($tutor." tutoring ".$tutee."<br>"."Subject: ".$subject."<br>".$day."<br>"."Start time: ".$starttime . "<br>"."End time: ".$endtime);?> </div> </div>  <?php
      $check=2;
      }
    }
    ?> </div></div><?php
}
if ($check == 0){
  echo"there are no sessions, pleasing organise a session.";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
  <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>