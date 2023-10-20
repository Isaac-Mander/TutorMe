<?php
//Get relevant info from session
$user_id = $_SESSION['user_id'];

//sets the default sorting value as 1, then changes it if there it has been set to a non one value
$sorting = 1;
if (isset($_GET['sorting'])){
  $sorting = $_GET['sorting'];
}
//if sorting is one then the default value 1 is the selected sorting method and this is reflected by the value that shows on the drop down box
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
//if sorting is two then value 2 is the selected sorting method and this is reflected by the value that shows on the drop down box
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
//if sorting is three then value 3 is the selected sorting method and this is reflected by the value that shows on the drop down box
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


//this function uses the inputs to create the relevant session card
function create_card($potential_endtime,$potential_starttime,$name,$subject,$day_of_week,$card_id)
{
?>
  <div class="col">
  <!-- setting the id of the card so that it can be called in JavaScript -->
  <div id = '<?php echo $card_id; ?>' class='card mx-auto' name="pot_s_card" style="width: 18rem;"> 
  <?php ?>
  <div class="card-body">
  <!-- echoing out the information that is need for the card -->
  <div class="card-title" > <?php echo "<p id='name'>" . $name . "</p>";?> </div>
  <div>  <?php echo "<p id='potential_starttime'>" . $potential_starttime . "</p>";?></div>
  <div> <?php echo "<p id='potential_endtime'>" . $potential_endtime . "</p>";?></div>
  <div> <?php echo "<p id='subject'>" . $subject . "</p>";?></div>
  <div> <?php echo "<p id='day_of_week'>" . $day_of_week . "</p>"; ?></div>
  </div>
  </div></div><?php
}

//thiis function sorts the various inputs into an a form that is easier to use.
function data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id)
{
  //connecting to the page
  include("sys_page/db_connect.php");

  //setting the data
  $name = $available_tutee_times_data[$k]['user_name'];
  $subject = $available_tutee_times_data[$k]['subject_name'][$y];
  $ratings_data = average_ratings($conn,$available_tutee_times_data[$k]['student_id']); 
  

  //converting the day of the week to a word
  $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $day_of_week = $days_of_week_array[$available_tutor_times_data[$l]['dayofweek']-1];

  
  //Check if the subject is a global subject
  if(substr($available_tutee_times_data[$k]['subject_id'][$y],0,1) == "G") {
    //if it is global set the subject id and the is_global is set to true
    $is_global = true;
    $subject_id = substr($available_tutee_times_data[$k]['subject_id'][$y],1,10);
  }
  else {
    //if it isn't global set the subject id and the is_global is set to false
    $is_global = false;
    $subject_id = $available_tutee_times_data[$k]['subject_id'][$y];
  }
  //setting the card id to be used later
  $card_id = $available_tutee_times_data[$k]['table_id'] . "-" . $subject_id . "-" . $available_tutee_times_data[$k]['student_id'] . "-" . $user_id . "-" . $available_tutee_times_data[$k]['start_time'] . "-" . $available_tutee_times_data[$k]['end_time'] . "-" . $is_global;
 $info = array();
 //setting the now processed info
 $info = [
  "card_id" => $card_id,
  "subject" => $subject,
  "day_of_week" => $day_of_week,
  "name" => $name,
  "day_of_week_num" => $available_tutor_times_data[$l]['dayofweek']
 ];
 //returning the array
 return $info;
}


?>
  <?php
      //sql query to get all of the tutor session times, and is sent to the get_session_select data function to get the data we want.
      $available_session_tutor_times_sql = "SELECT 6969_student_times.id, 6969_student_times.student_id, 6969_student_times.session_start, 6969_student_times.session_end, 6969_student_times.day_of_week, 6969_students.name FROM 6969_student_times INNER JOIN 6969_students ON 6969_student_times.student_id=6969_students.id WHERE 6969_student_times.student_id=$user_id;";
      $status = TRUE;
      $available_tutor_times_data = get_session_select_data($available_session_tutor_times_sql, $conn, $status);
      
      //sql query to get all of the tutee session times, and is sent to the get_session_select data function to get the data we want.
      $available_session_tutee_times_sql = "SELECT 6969_student_times.id, 6969_student_times.student_id, 6969_student_times.session_start, 6969_student_times.session_end, 6969_student_times.day_of_week, 6969_students.name FROM 6969_student_times INNER JOIN 6969_students ON 6969_student_times.student_id=6969_students.id WHERE 6969_student_times.student_id!=$user_id;";
      $status = FALSE;
      $available_tutee_times_data = get_session_select_data($available_session_tutee_times_sql, $conn, $status);
      /*check for subject matches*/
      $array_input_number = 0;
      
      //if there are avaliable tutee times
      if(is_array($available_tutee_times_data)){

        //and there are avaliable tutor times
        if(is_array($available_tutor_times_data)){

          //checking across all of the potential sessions of the tutees and the tutors to see if there are any overlap
          for($k=0; $k<sizeof($available_tutee_times_data); $k++){
            for($l=0; $l<sizeof($available_tutor_times_data); $l++){

              //if the subjects have been set
              if(isset($available_tutee_times_data[$k]['subject_id'])){
                if(isset($available_tutor_times_data[$l]['subject_id'])){

                //If the selected tutor and tutee potential time has the same day of the week
                if($available_tutee_times_data[$k]['dayofweek'] == $available_tutor_times_data[$l]['dayofweek']){

                  //If the selected tutee has a potential start time which is above or equal to the tutor start time
                  if ($available_tutee_times_data[$k]['start_time']>= $available_tutor_times_data[$l]['start_time']){
                    //If the selected tutee has a potential end time which is below or equal to the tutor end time
                    if($available_tutee_times_data[$k]['end_time']<= $available_tutor_times_data[$l]['end_time']){
                      //If the selected tutor and tutee have share a subject

                      for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                        for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){

                          if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                            //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                            //Get the relivant data and create a card for a tutor to click on
                            $potential_starttime = $available_tutee_times_data[$k]['start_time'];
                            $potential_endtime = $available_tutee_times_data[$k]['end_time'];
                            $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);

                            //the data is then processed into this array
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
                        //If the selected tutor and tutee share a subject
                        for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                          for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                            if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                              //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                              //Get the relivant data and create a card for a tutor to click on
                            
                              $potential_starttime = $available_tutee_times_data[$k]['start_time'];
                              $potential_endtime = $available_tutor_times_data[$l]['end_time'];

                              //processing the data so that it is more usable
                              $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);

                              //the data is then processed into this array
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
                        //If the selected tutor and tutee share a subject
                        for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                          for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                            if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                              //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                              //Get the relivant data and create a card for a tutor to click on
                              $potential_starttime = $available_tutee_times_data[$k]['start_time'];
                              $potential_endtime = $available_tutee_times_data[$k]['end_time'];

                              //processing the data so that it is more usable
                              $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);

                              //the data is then processed into this array
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
                      //If the selected tutor and tutee share a subject
                      for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                        for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                          if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                            //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                            //Get the relivant data and create a card for a tutor to click on
                            $potential_starttime = $available_tutor_times_data[$l]['start_time'];
                            $potential_endtime = $available_tutor_times_data[$l]['end_time'];
                            
                            //processing the data so that it is more usable
                            $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);

                            //the data is then processed into this array
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
                      //If the selected tutor and tutee share a subject
                      for ($y=0; $y<sizeof($available_tutee_times_data[$k]['subject_name']); $y++){
                        for ($t=0; $t<sizeof($available_tutor_times_data[$l]['subject_name']); $t++){
                          if ($available_tutee_times_data[$k]['subject_id'][$y] == $available_tutor_times_data[$l]['subject_id'][$t]) {
                            //If the last list of if statements are true, there is a potentional session that can be formed between the current tutee and tutor
                            //Get the relivant data and create a card for a tutor to click on
                            $potential_starttime = $available_tutor_times_data[$l]['start_time'];
                            $potential_endtime = $available_tutee_times_data[$k]['end_time'];

                            //processing the data so that it is more usable
                            $info = data_sort($available_tutee_times_data,$available_tutor_times_data,$k,$l,$y,$user_id);

                            //the data is then processed into this array
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
        //if there are matched sessions
        if(is_array($session_card)){
          //if sorting is one sort by sorting method 1
          if ($sorting == 1){
            $days_of_week_column_card = array_column($session_card, 'day_of_week');
            $start_time_column_card = array_column($session_card, 'start_time');
            array_multisort($days_of_week_column_card, SORT_ASC, $start_time_column_card, SORT_ASC, $session_card);
          }
          //if sorting is two sort by sorting method 2
          if ($sorting == 2){

            $name  = array_column($session_card, 'name');
            $subject =  array_column($session_card, 'subject');

            array_multisort($name, SORT_ASC, $subject, SORT_ASC, $session_card);
          }
          //if sorting is three sort by sorting method 3
          if ($sorting == 3){
            $subject_column_card = array_column($session_card, 'subject');
            array_multisort($subject_column_card, SORT_ASC, $session_card);
          }
          //after the data has been sorted it is fed into the loop which uses the create_card function to print out all of the matched sessions
          ?><div class="row row-cols-1 row-cols-md-4" ><?php
          for ($z=0; $z<sizeof($session_card); $z++){
            create_card($session_card[$z]['end_time'],$session_card[$z]['start_time'],$session_card[$z]['name'],$session_card[$z]['subject'],$session_card[$z]['day_of_week'],$session_card[$z]['card_id']);
          }
          ?></div><?php
        }else{
          //basic message so the user knows why nothing is showing up
          echo"<p class='text-center border-bottom' >There are no sessions that share the same time & subject as you</p>"."</br>";
        }

        }else{
          //basic message so the user knows why nothing is showing up
          echo"<p class='text-center border-bottom' >You have no potential sessions set</p>"."</br>";
        }
      }else{
        //basic message so the user knows why nothing is showing up
        echo"<p class='text-center border-bottom' >There are no other users with a potential session set</p>"."</br>";
      }
      
      ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Matching</title>
    <!-- linking to the style sheet and bootstrap -->
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
  
<!-- the modal for accepting a matched session -->
<div class="modal fade " id="session_popup_test" tabindex="-1" role="dialog" aria-labelledby="session_popup_test" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <!-- the modal header -->
        <h5 class="modal-title" id="exampleModalLabel">Session info</h5>
        <!-- the button to close the modal -->
        <a type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </a>
      </div>
      <!-- the main content of the modal, is changed by the JavaScript code -->
      <div id="session_accept_popup" class="modal-content_session_match">
       <div class="modal-body"> 
        <p id="popup_name">Name</p>
        <p id="popup_subject_name">Subject Name</p>
        <p id="popup_day">Day of week</p>
        <p id="popup_session_length">Session length (hours)</p>

        <!-- allows the user to choose a date further in the future -->
        <label for="date">Choose a date:</label>
          <select name="date" id="date">
          </select>
        </div>
     <div class="modal-footer">
      <!-- the accept button for the user to accept the session -->
        <a href=""><button>Accept</button></a>
          <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
      </div>
    </div>
    </div>
  </div>
</div>

    <!-- an page identifying div tag and linking to the bootstrap JavaScript library -->
    <div id="session_matching"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>