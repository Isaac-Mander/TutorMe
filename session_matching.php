<?php
//Check if the user to logged in
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


//Set card function
function create_card($available_tutee_times_data,$available_tutor_times_data,$k,$l,$potential_endtime,$potential_starttime,$y,$user_id)
{
  $name = $available_tutee_times_data[$k]['user_name'];
  $subject = $available_tutee_times_data[$k]['subject_name'][$y];
  $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $day_of_week = $days_of_week_array[$available_tutor_times_data[$l]['dayofweek']-1];
  $card_id = $available_tutee_times_data[$k]['table_id'] . "-" . $available_tutee_times_data[$k]['subject_id'][$y] . "-" . $available_tutee_times_data[$k]['student_id'] . "-" . $user_id . "-" . $available_tutee_times_data[$k]['start_time'] . "-". $available_tutee_times_data[$k]['end_time'];?>
  <div id = '<?php echo $card_id; ?>' class='card mx-auto' name="card" style="width: 18rem;"> 
  <?php ?>
  <div class="card-body">
  <div class="card-title" > <?php echo "<p id='name'>" . $name . "</p>";?> </div>
  <div> <?php echo "<p id='potential_starttime'>" . $potential_starttime . "</p>";?></div>
  <div> <?php echo "<p id='potential_endtime'>" . $potential_endtime . "</p>";?></div>
  <div> <?php echo "<p id='subject'>" . $subject . "</p>";?></div>
  <div> <?php echo "<p id='day_of_week'>" . $day_of_week . "</p>"; ?></div>
  </div>
  </div></a><?php
}


?>
  <?php
    //need to change first 4 digits of the sql for the tables to variable

      $available_session_tutor_times_sql = "SELECT 6969_student_times.id, 6969_student_times.student_id, 6969_student_times.session_start, 6969_student_times.session_end, 6969_student_times.day_of_week, 6969_students.name FROM 6969_student_times INNER JOIN 6969_students ON 6969_student_times.student_id=6969_students.id WHERE 6969_student_times.student_id=$user_id;";
      $status = TRUE;
      $available_tutor_times_data = get_session_select_data($available_session_tutor_times_sql, $conn, $status);

      
      $available_session_tutee_times_sql = "SELECT 6969_student_times.id, 6969_student_times.student_id, 6969_student_times.session_start, 6969_student_times.session_end, 6969_student_times.day_of_week, 6969_students.name FROM 6969_student_times INNER JOIN 6969_students ON 6969_student_times.student_id=6969_students.id WHERE 6969_student_times.student_id!=$user_id;";
      $status = FALSE;
      $available_tutee_times_data = get_session_select_data($available_session_tutee_times_sql, $conn, $status);
      /*check for subject matches*/

        for($k=0; $k<sizeof($available_tutee_times_data); $k++){

          for($l=0; $l<sizeof($available_tutor_times_data); $l++){
            if(isset($available_tutee_times_data[$k]['subject_id'])){
              if(isset($available_tutor_times_data[$l]['subject_id'])){
            //If the selected tutor and tutee have the same subject
              //If the selected tutor and tutee potential time has the same day of the week
              if($available_tutee_times_data[$k]['dayofweek'] == $available_tutor_times_data[$l]['dayofweek']){
                //If the selected tutee has a potential start time which is above or equal to the tutor start time
                if ($available_tutee_times_data[$k]['start_time']>= $available_tutor_times_data[$l]['start_time']){
                  //If the selected tutee has a potential end time which is below or equal to the tutor end time
                  if($available_tutee_times_data[$k]['end_time']<= $available_tutor_times_data[$l]['end_time']){
                    //If the selected tutor and tutee have the same subject
                    for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                      for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                        if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                          //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                          //Get the relivant data and create a card for a tutor to click on
                          $potential_starttime = $available_tutee_times_data[$k]['start_time'];
                          $potential_endtime = $available_tutee_times_data[$k]['end_time'];
                          create_card($available_tutee_times_data,$available_tutor_times_data,$k,$l,$potential_endtime,$potential_starttime,$y,$user_id);
                        }
                      }
                    }
                  } 
                  elseif ($available_tutee_times_data[$k]['start_time'] < $available_tutor_times_data[$l]['end_time']) {
                    if ($available_tutee_times_data[$k]['end_time'] > $available_tutor_times_data[$l]['end_time']){
                      //If the selected tutor and tutee have the same subject
                      for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                        for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                          if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                            //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                            //Get the relivant data and create a card for a tutor to click on
                          
                            $potential_starttime = $available_tutee_times_data[$k]['start_time'];
                            $potential_endtime = $available_tutor_times_data[$l]['end_time'];
                            create_card($available_tutee_times_data,$available_tutor_times_data,$k,$l,$potential_endtime,$potential_starttime,$y,$user_id);
                          }
                        }
                      }
                    } 
                    elseif($available_tutee_times_data[$k]['end_time'] < $available_tutor_times_data[$l]['end_time']){
                      //If the selected tutor and tutee have the same subject
                      for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                        for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                          if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                            //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                            //Get the relivant data and create a card for a tutor to click on
                            $potential_starttime = $available_tutee_times_data[$k]['start_time'];
                            $potential_endtime = $available_tutee_times_data[$k]['end_time'];
                            create_card($available_tutee_times_data,$available_tutor_times_data,$k,$l,$potential_endtime,$potential_starttime,$y,$user_id);
                          }
                        }
                      }
                    }      
                  }
                } 
                else if ($available_tutee_times_data[$k]['end_time'] > $available_tutor_times_data[$l]['start_time']) {
                  if ($available_tutee_times_data[$k]['end_time'] > $available_tutor_times_data[$l]['end_time']){
                    //If the selected tutor and tutee have the same subject
                    for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                      for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                        if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                          //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                          //Get the relivant data and create a card for a tutor to click on
                          $potential_starttime = $available_tutor_times_data[$l]['start_time'];
                          $potential_endtime = $available_tutor_times_data[$l]['end_time'];
                          create_card($available_tutee_times_data,$available_tutor_times_data,$k,$l,$potential_endtime,$potential_starttime,$y,$user_id);
                        }
                      }
                    }
                  } 
                  else{
                    //If the selected tutor and tutee have the same subject
                    for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                      for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                        if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                          //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                          //Get the relivant data and create a card for a tutor to click on
                          $potential_starttime = $available_tutor_times_data[$l]['start_time'];
                          $potential_endtime = $available_tutee_times_data[$k]['end_time'];
                          create_card($available_tutee_times_data,$available_tutor_times_data,$k,$l,$potential_endtime,$potential_starttime,$y,$user_id);
                        }
                      }
                    }
                  }
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
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>

    <div id="session_accept_popup" class="modal_session_match">
      <div class="modal-content_session_match">
        <p id="popup_name">Name</p>
        <p id="popup_subject_name">Subject Name</p>
        <p id="popup_day">Day of week</p>
        <p id="popup_session_length">Session length (hours)</p>

        <label for="date">Choose a date:</label>
          <select name="date" id="date">
          </select>
        <a href=""><button>Accept</button></a>
        <button id="session_match_close">Close</button>
        <span class="close_session_match">&times;</span>
      </div>
    </div>

    <div id="session_matching"></div>
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>