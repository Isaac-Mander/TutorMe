<?php
//Check if user is logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get the user info from the session cookie
$user_id = $_SESSION['user_id'];
$school_code = $_SESSION['school_code'];

//Import functions
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");
$user_id = $_SESSION['user_id'];
$school_code = $_SESSION['school_code'];
$sql = "SELECT * FROM " . $school_code . "_students WHERE id=" . $user_id;
$result = $conn->query($sql); //Query database
if ($result->num_rows > 0) { //If the number of rows are not zero
  $data = $result->fetch_assoc();
  $display_name = $_SESSION['user'];
  $desc = $data['description'];
  $hours_tutored = $data['hours_tutored'];
  $sessions_tutored = $data['sessions_tutored'];
}
//End of section ==========================================================================================================================================================



//Get the info about the users subjects that they tutor ===================================================================================================================
$subject_tutor_sql = "SELECT * FROM `6969_subjects_tutor` WHERE tutor_id=$user_id";
$subject_tutor_result = $conn->query($subject_tutor_sql); //Query database
if ($subject_tutor_result->num_rows > 0) { //If the number of rows are not zero
  $subject_array_tutor = [];
  $i = 0;
  while($row = $subject_tutor_result->fetch_assoc()) {
    //Check which subject ID to use (if = 0 ignore ID)
    $is_global = false;
    if($row['global_subject_id'] == "0") {$is_global = false;}
    else {$is_global = true;}

    if($is_global) {$subject_tutor_id = $row['global_subject_id'];}
    else {$subject_tutor_id = $row['local_subject_id'];}
    //Add the id to an array
    $subject_array_tutor[$i][0] = $subject_tutor_id;
    //Add the current state of is_global
    $subject_array_tutor[$i][1] = $is_global;

    $i += 1;
  }
  $no_tutor_subjects = false;

  //Get the subject names as an array
  for($i=0;$i<sizeof($subject_array_tutor);$i++)
  {

    $subject_id = $subject_array_tutor[$i][0];
    //Change the query type depending if the subject is global or local
    //Global
    if($subject_array_tutor[$i][1] == True) {$subject_name_sql = "SELECT * FROM `6969_subjects_tutor` INNER JOIN `subjects` ON `6969_subjects_tutor`.`global_subject_id`=`subjects`.`id` WHERE `6969_subjects_tutor`.`tutor_id`=$user_id AND `global_subject_id`=$subject_id;";}
    
    //Local
    else {$subject_name_sql = "SELECT * FROM `6969_subjects_tutor` INNER JOIN `6969_subjects` ON `6969_subjects_tutor`.`local_subject_id`=`6969_subjects`.`id` WHERE `6969_subjects_tutor`.`tutor_id`=$user_id AND `local_subject_id`=$subject_id;";}

    //Query the database to find the subject name
    $subject_name_result = $conn->query($subject_name_sql); //Query database
    if ($subject_name_result->num_rows > 0) { //If the number of rows are not zero
      $row = $subject_name_result->fetch_assoc();
      $subject_array_tutor[$i][2] = $row['name'];
    }
  }
  
}
else
{
  //If no subjects are found
  $no_tutor_subjects = true;
}




//Get the info about the users subjects that they are tutored in ===================================================================================================================
$subject_tutee_sql = "SELECT * FROM `6969_subjects_tutee` WHERE tutee_id=$user_id";
$subject_tutee_result = $conn->query($subject_tutee_sql); //Query database
if ($subject_tutee_result->num_rows > 0) { //If the number of rows are not zero
  $subject_array_tutee = []; //Array is formatted as [index][subject id in table, subject is in global table?, subject name in english]
  $i = 0;

  while($row = $subject_tutee_result->fetch_assoc()) {
    //Check which subject ID to use (if = 0 ignore ID)
    $is_global = false;
    if($row['global_subject_id'] == "0") {$is_global = false;}
    else {$is_global = true;}

    if($is_global) {$subject_tutee_id = $row['global_subject_id'];}
    else {$subject_tutee_id = $row['local_subject_id'];}
    //Add the id to an array
    $subject_array_tutee[$i][0] = $subject_tutee_id;
    //Add the current state of is_global
    $subject_array_tutee[$i][1] = $is_global;

    $i += 1;
  }

  $no_tutee_subjects = false;

  //Get the subject names as an array
  for($i=0;$i<sizeof($subject_array_tutee);$i++)
  {

    $subject_id = $subject_array_tutee[$i][0];
    //Change the query type depending if the subject is global or local
    //Global
    if($subject_array_tutee[$i][1] == True) {$subject_name_sql = "SELECT * FROM `6969_subjects_tutee` INNER JOIN `subjects` ON `6969_subjects_tutee`.`global_subject_id`=`subjects`.`id` WHERE `6969_subjects_tutee`.`tutee_id`=$user_id AND `global_subject_id`=$subject_id;";}
    
    //Local
    else {$subject_name_sql = "SELECT * FROM `6969_subjects_tutee` INNER JOIN `6969_subjects` ON `6969_subjects_tutee`.`local_subject_id`=`6969_subjects`.`id` WHERE `6969_subjects_tutee`.`tutee_id`=$user_id AND `local_subject_id`=$subject_id;";}

    //Query the database to find the subject name
    $subject_name_result = $conn->query($subject_name_sql); //Query database
    if ($subject_name_result->num_rows > 0) { //If the number of rows are not zero
      $row = $subject_name_result->fetch_assoc();
      $subject_array_tutee[$i][2] = $row['name'];
    }
  }
}
else
{
  //If no subjects are found
  $no_tutee_subjects = true;
}
//End of section =========================================================================================================================================================

//Get the subjects of the school =========================================================================================================================================
$all_available_subject_array = get_available_subjects($school_code);
if(!$no_tutee_subjects)
{
  //Check if the subject is currently selected by the user as a subject they wish to be tutored in
  for($b=0;$b<sizeof($all_available_subject_array);$b++)
  {
    
    //Loop though the subject names in the array to find a match
    for($x=0;$x<sizeof($subject_array_tutee);$x++)
    {
      //If a match is found, set [2] to be true and exit loop
      if(trim($subject_array_tutee[$x][2]) == trim($all_available_subject_array[$b][1])) 
      {
        $all_available_subject_array[$b][3] = 1;
        break;
      }
      else 
      {
        $all_available_subject_array[$b][3] = 0;
      }
    }
  }
}

if(!$no_tutor_subjects)
{
  //Check if the subject is currently selected by the user as a subject they wish to be tutored in
  for($b=0;$b<sizeof($all_available_subject_array);$b++)
  {
    
    //Loop though the subject names in the array to find a match
    for($x=0;$x<sizeof($subject_array_tutor);$x++)
    {
      //If a match is found, set [2] to be true and exit loop
      if(trim($subject_array_tutor[$x][2]) == trim($all_available_subject_array[$b][1])) 
      {
        $all_available_subject_array[$b][4] = 1;
        break;
      }
      else 
      {
        $all_available_subject_array[$b][4] = 0;
      }
    }
  }
}


// Get a user's average ratings ==============================================================================================================================
$ratings_data = average_ratings($conn,$user_id);
$average_prod = $ratings_data[0];
$average_expe = $ratings_data[1];

$subject_element_tutee_id = 0; //This id allows for a unique id to be set to each subject for js purposes 
$subject_element_tutor_id = 0;
$checkbox_id_increment = 0; //Checkbox id for js

$tz = new DateTimeZone('NZ');
  $dt = new DateTime('now',$tz);
  $time_day = $dt->format('d'); // output: '1' - '31'
  $time_month = $dt->format('m'); // output: '1' - '12'cc
  $time_year = $dt->format('Y'); // output: '2023'
  $time_hour = $dt ->format('h');// output: '09'
  $time_minute = $dt ->format('i');// out: ':46'
  $time =  $time_year . "-" . $time_month . "-" . $time_day ." " . $time_hour . ":" . $time_minute;
  $date =  $time_year . "-" . $time_month . "-" . $time_day;
  
  //calling the events function, and setting the events?>
  
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <?php
    $tz = new DateTimeZone('NZ');
    $dt = new DateTime('now',$tz);
    $time_day = $dt->format('d'); // output: '1' - '31'
    $time_month = $dt->format('m'); // output: '1' - '12'cc
    $time_year = $dt->format('Y'); // output: '2023'
    $time_hour = $dt ->format('h');// output: '09'
    $time_minute = $dt ->format('i');// out: ':46'
    $time =  $time_year . "-" . $time_month . "-" . $time_day ." " . $time_hour . ":" . $time_minute;
    $date =  $time_year . "-" . $time_month . "-" . $time_day;

    $events = grab_events($conn,$user_id);

    $available_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=$user_id";
    $available_session_times_data = get_available_session_data($available_session_times_sql, $conn);
    //pulls all the potential times from the database, runs through function
    //$events = grab_events($conn);
    //calling the events function, and setting the events?>
        <link rel="stylesheet" href="sys_page/styles.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src='fullcalendar-6.1.5\fullcalendar-6.1.5\dist\index.global.js'></script>
<script>
//loading in the calender
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    <?php 

    $JsonEvents = json_encode($events); //putting the array into a format the calendar can read ?>
    var calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      headerToolbar: {
        left:'',
        center: 'title',
        right:''
      },
      //setting the tool bar
      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        timeGridWeek: { buttonText: 'grid week' }
      },

      //only having the initial view
      initialView: 'timeGridWeek',
      initialDate:  '<?php echo $date?>',
      allDaySlot: false,
      navLinks: false, // can click day/week names to navigate views
      dayMaxEvents: true, // allow "more" link when too many events
      events: <?php echo $JsonEvents ?> //uploading all of the events
    });
    calendar.render();
    //rendering calendar
  });
</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1100px;
    margin: 0 auto;
  }

</style>
  </head>
  <body>
  <?php
  for($b=0;$b<sizeof($all_available_subject_array);$b++)
  { if(is_int(substr($all_available_subject_array[$b][1], -1))){
    $all_available_subject_array[$b][5] = substr($all_available_subject_array[$b][1], -1);
  }else{
    $all_available_subject_array[$b][5] = 0;
  }
  }
  $all_available_subject_level_array_column = array_column($all_available_subject_array, 5);
  $all_available_subject_name_array_column = array_column($all_available_subject_array, 1);
  array_multisort($all_available_subject_name_array_column, SORT_ASC, $all_available_subject_level_array_column, SORT_ASC, $all_available_subject_array);

  for($b=0;$b<sizeof($subject_array_tutee);$b++)
  { if(is_int(substr($subject_array_tutee[$b][1], -1))){
    $subject_array_tutee[$b][5] = substr($subject_array_tutee[$b][1], -1);
  }else{
    $subject_array_tutee[$b][5] = 0;
  }
  }
  $array_tutee_subject_level_array_column = array_column($subject_array_tutee, 5);
  $array_tutee_subject_name_array_column = array_column($subject_array_tutee, 2);
  array_multisort($array_tutee_subject_name_array_column, SORT_ASC, $array_tutee_subject_level_array_column, SORT_ASC, $subject_array_tutee);

  for($b=0;$b<sizeof($subject_array_tutor);$b++)
  { if(is_int(substr($subject_array_tutor[$b][1], -1))){
    $subject_array_tutor[$b][5] = substr($subject_array_tutor[$b][1], -1);
  }else{
    $subject_array_tutor[$b][5] = 0;
  }
  }
  $array_tutor_subject_level_array_column = array_column($subject_array_tutor, 5);
  $array_tutor_subject_name_array_column = array_column($subject_array_tutor, 2);
  array_multisort($array_tutor_subject_name_array_column, SORT_ASC, $array_tutor_subject_level_array_column, SORT_ASC, $subject_array_tutor);
  ?>
    
    <h1><p class="text-center"><?php echo $display_name."'s profile";?></p></h1>

    <div class="row">
      <div class ="col" >
      <div id="tutoring_subjects_checkbox_studying" class="hide_on_start">
                <?php 
              for($i=0;$i<sizeof($all_available_subject_array);$i++){ //Check if subject should be ticked on start
                ?> <div class="card mx-auto" style="width: 25rem;"> <div class="card-body"> <?php
                $checkbox_id = "checkbox_" . $checkbox_id_increment;//Set what the id of the checkbox should be
                if($no_tutee_subjects) $all_available_subject_array[$i][3] = false; //If there are no subjects make sure the checkbox it unticked
                if($all_available_subject_array[$i][3] == true) 
                {
                  ?>
                  <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div>
                  <div class="card-footer"> <?php echo "<input id=" . $checkbox_id . " type='checkbox' checked>" ?> </div> <?php
                } //Create a checked checkbox
                else {
                  echo "<input id=" . $checkbox_id . " type='checkbox'>" . "  ".$all_available_subject_array[$i][1];
                } //Create an empty checkbox 
                ?></div></div><?php
                $checkbox_id_increment += 1; //Increment the checkbox id by 1
                }?>
        </div>
        <div id="studying_subject_cards">
              <?php
            //Get number of items in subject array
            if($no_tutee_subjects == false)
            {
              for($x=0;$x<sizeof($subject_array_tutee);$x++)
              {
                  ?><div class="card mx-auto" style="width: 25rem;"><div class="card-body"><?php
                  echo "<div ";
                  echo "id=tutee_" . $subject_element_tutee_id;//Give the element a unqiue id
                  echo " class='col'><p class='nowrap'>";
                  echo $subject_array_tutee[$x][2];
                  echo "";
                  echo "</p></div>";
                  //<img class='hide_on_start edit_cross' src='sys_img/icons8-x-100.png' alt=''>
                  //Increment the id ?></div></div><?php
                  $subject_element_tutee_id += 1;
              }
            }
          ?>
      </div>
    </div>
          
    <div class="col" id="tutoring">
      <div id="tutoring_subjects_checkbox_tutoring" class="hide_on_start">
          <?php for($i=0;$i<sizeof($all_available_subject_array);$i++){ //Check if subject should be ticked on start
          ?> <div class="card mx-auto" style="width: 25rem;"> <div class="card-body"> <?php
            $checkbox_id = "checkbox_" . $checkbox_id_increment;//Set what the id of the checkbox should be
            if($no_tutor_subjects) $all_available_subject_array[$i][4] = false; //If there are no subjects make sure the checkbox it unticked
            if($all_available_subject_array[$i][4] == true) 
            {
              ?>
              <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div>
              <div class="card-footer"> <?php echo "<input id=" . $checkbox_id . " type='checkbox' checked>" ?> </div> <?php
              
            } //Create a checked checkbox
            else 
            {
              ?>
              <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div>
              <div class="card-footer"> <?php echo "<input id=" . $checkbox_id . " type='checkbox' checked>" ?> </div> <?php
              } //Create an empty checkbox 

            ?></div> </div>
            <?php
            $checkbox_id_increment += 1; //Increment the checkbox id by 1
            }?>
      </div>

      <div id="tutoring_subject_cards">
                <?php
              //Get number of items in subject array
              if($no_tutor_subjects == false)
              {
                for($x=0;$x<sizeof($subject_array_tutor);$x++)
                {
                  ?><div class="card mx-auto" style="width: 25rem;"><div class="card-body"><?php
                  echo "<div ";
                  echo "id=tutor_" . $subject_element_tutor_id;//Give the element a unqiue id
                  echo " class='col'><p class='nowrap'>";
                  echo $subject_array_tutor[$x][2];
                  echo "";
                  echo "</p></div>";
                  //<img class='hide_on_start edit_cross' src='sys_img/icons8-x-100.png' alt=''>
                  //Increment the id ?></div></div><?php
                  $subject_element_tutor_id += 1;
                }
              }
            ?>
      </div>
    </div>
  </div>
            
    <button class="btn btn-success btn-md" id=profile_edit_button>Edit</button>
    <div class="card mx-auto">
      <!-- <div class="card-header text-white bg-primary">       <h3>Description</h3>   </div> -->
        <div class="card-body">
        <ul class="list-group list-group-flush">
          <!-- <li class="list-group-item"> <p id="profile_desc_text"><?php echo $desc; ?></p></li> -->
          <li class="list-group-item text-white bg-primary">          <h3>Statistics</h3></li>
          <li class="list-group-item">          <p>Average Productivity: <?php echo $average_prod; ?>⭐</p></li>
          <li class="list-group-item">          <p>Average Experience: <?php echo $average_expe; ?>⭐</p></li>
          <li class="list-group-item">          <p>Hours spent tutoring: <?php echo $hours_tutored; ?></p></li>
          <li class="list-group-item">          <p>Sessions tutored: <?php echo $sessions_tutored; ?></p></li>
        </ul>
        </div>
    </div>
    <div class="card mx-auto w-75">
    <form action='calendar_2.php' method='post'>
    <div class="card-body">
    <div class="form-group row">
        <label for="start_time" class="col-sm-2 col-form-label"><b>Start time</b></label>
        <div class="col-sm-10">
        <input type="time" class="form-control" id="start_time" placeholder="Start time" name="start_time" required><br>
        </div>
        </div>

        <div class="form-group row">
        <label for="end_time" class="col-sm-2 col-form-label"><b>End time</b></label>
        <div class="col-sm-10">
        <input type="time" class="form-control" id="end_time" placeholder="End time" name="end_time" required><br>
        </div>
        </div>

        <fieldset class="form-group">
          <div class="row">
            <legend class="col-form-label col-sm-2 pt-0">Day of week</legend>
            <div class="col-sm-10">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week1" value=1>
                <label class="form-check-label" for="gridRadios1">
                  Monday
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week2" value=2>
                <label class="form-check-label" for="gridRadios2">
                  Tuesday
                </label>
              </div>
              <div class="form-check disabled">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week3" value=3>
                <label class="form-check-label" for="gridRadios3">
                  Wednesday
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week4" value=4>
                <label class="form-check-label" for="gridRadios4">
                  Thursday
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week5" value=5>
                <label class="form-check-label" for="gridRadios5">
                  Friday
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week6" value=6>
                <label class="form-check-label" for="gridRadios6">
                  Saturday
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="day_of_week" id="day_of_week7" value=7>
                <label class="form-check-label" for="gridRadios7">
                  Sunday
                </label>
              </div>
            </div>
          </div>
  </fieldset>
        <input type="hidden" id="student_id" name="student_id" value="<?php echo $user_id ?>">
        <input type="submit">
    </div>
    </form> 
    </div>
    </div>

    <?php
    if (is_array($available_session_times_data)) {
      //if is array
      for($i=0; $i<sizeof($available_session_times_data); $i++){
        //running through all of the array
        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $day =  $time_year . "-" . $time_month . "-" . $time_day;
        $name = $available_session_times_data[$i][0]; //setting name
        $day_of_week = $available_session_times_data[$i][3]; //setting day of the week

        $potential_start_time_session = $available_session_times_data[$i][1]; //setting potential start time
        $potential_end_time_session = $available_session_times_data[$i][2]; //setting potential end time
        $potential_starttime_rough = strtotime($day.$potential_start_time_session); //getting a time value
        $potential_endtime_rough = strtotime($day.$potential_end_time_session); //getting a time value
        if (date('N') == $day_of_week){ //checking if the date is the same
          $potential_starttime = $potential_starttime_rough; //if so the times need no change
          $potential_endtime = $potential_endtime_rough;

        } elseif (date('N') > $day_of_week){ //if it is different 
          $time_diff = date('N') - $day_of_week; //find the time difference
          $potential_starttime = $potential_starttime_rough - ($time_diff * 86400);//accounts for the diffence
          $potential_endtime = $potential_endtime_rough - ($time_diff * 86400);

        } elseif(date('N') < $day_of_week){//if it is different
          $time_diff = $day_of_week - date('N'); //finds the time difference 
          $potential_starttime = $potential_starttime_rough + ($time_diff * 86400); //acounts for the difference
          $potential_endtime = $potential_endtime_rough + ($time_diff * 86400);
        }


        $card_id = $available_session_times_data[$i][4];
  
        ?>    <div id=<?php echo $card_id; ?> class='card mx-auto' style="width: 35rem;"><?php
        echo ($name."<br>".date("l h:i:s A", $potential_starttime) . "<br>");
        echo date("l h:i:s A", $potential_endtime); //prints out the cards of the time sessions.
        ?> <a href="delete_calendar_time.php?id=<?php echo $card_id; ?>">Remove</a>     </div>   <?php      }
    }?>


    <div id='calendar'></div>
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    
  </body>
</html>