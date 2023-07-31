<?php
//Check if the user to logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get relevant info from session
$user_id = $_SESSION['user_id'];
$sorting = 1;
if (isset($_GET['sorting'])){
  $sorting = $_GET['sorting'];
}

//Import functions
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

if ($sorting == 1){
  ?>
    <form action='process.php' method='post'>
    <label for="sorting">Sort by:</label>
    <select name="sorting" id="sorting">
      <option value="1">By date & time</option>
      <option value="2">By student</option>
      <option value="3">By subject</option>
    </select>
    <input type="submit" name="submit" class="btn btn-success btn-md" value="Submit">
    </form> 
<?php
} elseif($sorting == 2){
  ?>
    <form action='process.php' method='post'>
    <label for="sorting">Sort by:</label>
    <select name="sorting" id="sorting">
      <option value="1">By date & time</option>
      <option value="2" selected >By student</option>
      <option value="3">By subject</option>
    </select>
    <input type="submit" name="submit" class="btn btn-success btn-md" value="Submit">
    </form> 
<?php
} elseif($sorting == 3){
  ?>
    <form action='process.php' method='post'>
    <label for="sorting">Sort by:</label>
    <select name="sorting" id="sorting">
      <option value="1">By date & time</option>
      <option value="2">By student</option>
      <option value="3" selected >By subject</option>
    </select>
    <input type="submit" name="submit" class="btn btn-success btn-md" value="Submit">
    </form> 
<?php
}


//Set card function
function create_card($potential_endtime,$potential_starttime,$name,$subject,$day_of_week,$card_id)
{
?>
  <div id = '<?php echo $card_id; ?>' class='card mx-auto' name="card" style="width: 18rem;"> 
  <?php ?>
  <div class="card-body">
  <div class="card-title" > <?php echo "<p id='name'>" . $name . "</p>";?> </div>
  <div>  <?php echo "<p id='potential_starttime'>" . $potential_starttime . "</p>";?></div>
  <div> <?php echo "<p id='potential_endtime'>" . $potential_endtime . "</p>";?></div>
  <div> <?php echo "<p id='subject'>" . $subject . "</p>";?></div>
  <div> <?php echo "<p id='day_of_week'>" . $day_of_week . "</p>"; ?></div>
  </div>
  </div></a><?php
}


function data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id)
{
  include("sys_page/db_connect.php");
  $name = $available_tutee_times_data[$k]['user_name'];
  $subject = $available_tutee_times_data[$k]['subject_name'][$y];
  $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $day_of_week = $days_of_week_array[$available_tutor_times_data[$l]['dayofweek']-1];

  $ratings_data = average_ratings($conn,$available_tutee_times_data[$k]['student_id']); 
  
  //Check if the subject is a global subject
  if(substr($available_tutee_times_data[$k]['subject_id'][$y],0,1) == "G") {
    $is_global = true;
    $subject_id = substr($available_tutee_times_data[$k]['subject_id'][$y],1,1);
  }
  else {
    $is_global = false;
    $subject_id = $available_tutee_times_data[$k]['subject_id'][$y];
  }
  $card_id = $available_tutee_times_data[$k]['table_id'] . "-" . $subject_id . "-" . $available_tutee_times_data[$k]['student_id'] . "-" . $user_id . "-" . $available_tutee_times_data[$k]['start_time'] . "-" . $available_tutee_times_data[$k]['end_time'] . "-" . $is_global;
 $info = array();
 $info = [
  "card_id" => $card_id,
  "subject" => $subject,
  "day_of_week" => $day_of_week,
  "name" => $name,
  "day_of_week_num" => $available_tutor_times_data[$l]['dayofweek']
 ];
 return $info;
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
      $array_input_number = 0;
      $session_card = array();

      if(is_array($available_tutee_times_data)){
        if(is_array($available_tutor_times_data)){
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
                            $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);

                            $session_card[$array_input_number] = [
                              "card_id" => $info['card_id'],
                              "subject" => $info['subject'],
                              "end_time" =>$potential_endtime,
                              "start_time" =>$potential_starttime,
                              "day_of_week" => $info['day_of_week'],
                              "name" => $info['name'],
                              "day_of_week_num" => $info['day_of_week_num']
                            ];
                            $array_input_number = $array_input_number + 1;
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

                              $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);


                              $session_card[$array_input_number] = [
                                "card_id" => $info['card_id'],
                                "subject" => $info['subject'],
                                "end_time" =>$potential_endtime,
                                "start_time" =>$potential_starttime,
                                "day_of_week" => $info['day_of_week'],
                                "name" => $info['name'],
                                "day_of_week_num" => $info['day_of_week_num']
                              ];
                              $array_input_number = $array_input_number + 1;
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

                              $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);


                              $session_card[$array_input_number] = [
                                "card_id" => $info['card_id'],
                                "subject" => $info['subject'],
                                "end_time" =>$potential_endtime,
                                "start_time" =>$potential_starttime,
                                "day_of_week" => $info['day_of_week'],
                                "name" => $info['name'],
                                "day_of_week_num" => $info['day_of_week_num']
                              ];
                              $array_input_number = $array_input_number + 1;
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
                            
                            $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);


                            $session_card[$array_input_number] = [
                              "card_id" => $info['card_id'],
                              "subject" => $info['subject'],
                              "end_time" =>$potential_endtime,
                              "start_time" =>$potential_starttime,
                              "day_of_week" => $info['day_of_week'],
                              "name" => $info['name'],
                              "day_of_week_num" => $info['day_of_week_num']
                            ];
                            $array_input_number = $array_input_number + 1;

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

                            $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);


                            $session_card[$array_input_number] = [
                              "name" => $info['name'],
                              "card_id" => $info['card_id'],
                              "subject" => $info['subject'],
                              "end_time" =>$potential_endtime,
                              "start_time" =>$potential_starttime,
                              "day_of_week" => $info['day_of_week'],
                              "day_of_week_num" => $info['day_of_week_num']
                            ];
                            $array_input_number = $array_input_number + 1;

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
        if(is_array($session_card)){
          if ($sorting == 1){
            $days_of_week_column_card = array_column($session_card, 'day_of_week');
            $start_time_column_card = array_column($session_card, 'start_time');
            array_multisort($days_of_week_column_card, SORT_ASC, $start_time_column_card, SORT_ASC, $session_card);
          }
          if ($sorting == 2){


            foreach ($session_card as $key => $row) {
              $name[$key]  = $row['name'];
              $subject[$key] = $row['subject'];
            }
            array_multisort($name, SORT_ASC, $subject, SORT_ASC, $session_card);
          }
          if ($sorting == 3){
            $subject_column_card = array_column($session_card, 'subject');
            array_multisort($subject_column_card, SORT_ASC, $session_card);
          }
          for ($z=0; $z<sizeof($session_card); $z++){
            create_card($session_card[$z]['end_time'],$session_card[$z]['start_time'],$session_card[$z]['name'],$session_card[$z]['subject'],$session_card[$z]['day_of_week'],$session_card[$z]['card_id']);
          }
        }else{
          echo"there are no sessions that share the same time & subject as you"."</br>";
        }

        }else{
          echo"Absolute banger, also you have no potential sessions set"."</br>";
        }
      }else{
        echo"Absolute banger, also there are no other users with a potential session set"."</br>";
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