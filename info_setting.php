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

//Import needed files
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
  $email = $data['email'];
  $phone = $data['phone'];
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
  $no_tutor_subjects = TRUE;
  $subject_array_tutor = array();
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
  $no_tutee_subjects = TRUE;
  $subject_array_tutee = array();
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
for($b=0;$b<sizeof($all_available_subject_array);$b++){
  if (is_numeric(substr($all_available_subject_array[$b][1],-1,1))){
    $all_available_subject_array[$b][5] = substr($all_available_subject_array[$b][1],-1,1);
  }else{
    $all_available_subject_array[$b][5] = 0;
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="content.js"></script>


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
    
  <link rel="stylesheet" href="sys_page/styles.css">
  <?php

  $level = 0;
  $level_tutee =0;
    if (isset($_GET['level'])){
      $level = $_GET['level'];
    }
    if (isset($_GET['level_tutee'])){
      $level_tutee = $_GET['level_tutee'];
    }

  for($b=0;$b<sizeof($all_available_subject_array);$b++)
  { 
    $all_available_subject_array[$b][1000] = $b;
    if(is_numeric(substr($all_available_subject_array[$b][1], -1))){
    $all_available_subject_array[$b][5] = substr($all_available_subject_array[$b][1], -1);
  }
  else
  {
    $all_available_subject_array[$b][5] = 0;
  }
  }
  $all_available_subject_level_array_column = array_column($all_available_subject_array, 5);
  $all_available_subject_name_array_column = array_column($all_available_subject_array, 1);
  array_multisort($all_available_subject_name_array_column, SORT_ASC, $all_available_subject_level_array_column, SORT_ASC, $all_available_subject_array);

  for($b=0;$b<sizeof($subject_array_tutee);$b++)
  { if(is_numeric(substr($subject_array_tutee[$b][2], -1))){
    $subject_array_tutee[$b][5] = substr($subject_array_tutee[$b][2], -1);
  }else{
    $subject_array_tutee[$b][5] = 0;
  }
  }
  $array_tutee_subject_level_array_column = array_column($subject_array_tutee, 5);
  $array_tutee_subject_name_array_column = array_column($subject_array_tutee, 2);
  array_multisort($array_tutee_subject_name_array_column, SORT_ASC, $array_tutee_subject_level_array_column, SORT_ASC, $subject_array_tutee);

  for($b=0;$b<sizeof($subject_array_tutor);$b++)
  { if(is_numeric(substr($subject_array_tutor[$b][2], -1))){
    $subject_array_tutor[$b][5] = substr($subject_array_tutor[$b][2], -1);
  }else{
    $subject_array_tutor[$b][5] = 0;
  }
  }
  $array_tutor_subject_level_array_column = array_column($subject_array_tutor, 5);
  $array_tutor_subject_name_array_column = array_column($subject_array_tutor, 2);
  array_multisort($array_tutor_subject_name_array_column, SORT_ASC, $array_tutor_subject_level_array_column, SORT_ASC, $subject_array_tutor);

  ?>



    <h1 class="text-center">Setup page</h1>
    <p class="text-center">On this page you can set your subjects and the times in which you are free</p>
  
    

<div class="container">
    <div class="main-body">
          <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex flex-column align-items-center text-center">
                    <img src="sys_img\dev_icon.jpg" alt="Admin" class="rounded-circle" width="150">
                    <div class="mt-3">
                      <h4><?php echo $display_name ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="card mb-3">
                <div class="card-body">
                  
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Email</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                      <?php echo $email; ?>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Phone</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                    <?php echo $phone; ?>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Tutoring subjects:</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                    <?php
                      //Get number of items in subject array
                      if($no_tutor_subjects == false)
                      {
                        for($x=0;$x<sizeof($subject_array_tutor);$x++)
                        {
                          ?><button type="button" class="btn btn-outline-light btn-rounded btn-success"
                          <?php echo " id='" . $subject_array_tutor[$x][2]."' ";//Give the element a unqiue id"; ?> data-bs-toggle="modal" data-bs-target="#remove_subject" data-is-global='<?php echo $subject_array_tutor[$x][1]?>' data-subject-id='<?php echo $subject_array_tutor[$x][0]?>' data-subject-status='0' data-user-id='<?php echo $user_id ?>'><?php
                          echo $subject_array_tutor[$x][2];
                          //Increment the id ?></button><?php
                          $subject_element_tutor_id += 1;
                        }
                      }
                    ?>
                    <a class="btn btn-info btn-md" data-bs-toggle="modal" data-bs-target="#add_subject_tutor">Add subjects</a>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Need help with:</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                    <?php
                      //Get number of items in subject array
                      if($no_tutee_subjects == false)
                      {
                        for($x=0;$x<sizeof($subject_array_tutee);$x++)
                        {
                          ?><button type="button" class="btn btn-outline-light btn-rounded btn-success"
                          <?php echo " id='" . $subject_array_tutee[$x][2]."' ";//Give the element a unqiue id"; ?> data-bs-toggle="modal" data-bs-target="#remove_subject" data-is-global='<?php echo $subject_array_tutee[$x][1]?>' data-subject-id='<?php echo $subject_array_tutee[$x][0]?>' data-subject-status='1' data-user-id='<?php echo $user_id ?>'><?php
                          echo $subject_array_tutee[$x][2];
                          //Increment the id ?></button><?php
                          $subject_element_tutee_id += 1;
                        }
                      }
                    ?>
                    <a class="btn btn-info btn-md" data-bs-toggle="modal" data-bs-target="#add_subject_tutee">Add subjects</a>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-12">
                      <a class="btn btn-success btn-md" id=profile_edit_button>Edit</a>
                    </div>
                  </div>
                </div>
              </div>       
              </div>
    </div>
<?php
?>
  <div class="modal fade" id="add_subject_tutor" tabindex="-1" role="dialog" aria-labelledby="add_subject_tutor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select subjects</h5>
        <form action="info_setting.php" method="get">
            <label for="Level">Select level:</label>
            <select name="level" id="level">
<?php if ($level == 0){
                 ?>
                <option value="0" selected>Any level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif ($level == 1){
                 ?>
                <option value="0">Any level</option>
                <option value="1" selected>Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif($level == 2){
  ?>
                <option value="0">Any level</option>
                <option value="1">Level 1</option>
                <option value="2" selected>Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif($level == 3){
  ?>
                <option value="0">Any level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3" selected>Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif($level == 4){
?>
                <option value="0">Any level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4" selected>Non-NCEA</option>

<?php } ?>
            </select>
            <input type="hidden" id="modal_sorting_id" name="modal_sorting_id" value="1">
            <input type="submit" name="submit" class="btn btn-success btn-md" value="Sort!">
        </form>
        <a class="close btn" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>
      </div>
      <div class="modal-body"> 
      <div id="tutoring_subjects_checkbox_tutoring">
      <div class="row row-cols-1 row-cols-md-3">
          <?php for($i=0;$i<sizeof($all_available_subject_array);$i++){ //Check if subject should be ticked on start
            if($level == 0){
              ?> <div class="col" ><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
            }elseif($level == 1){
              if ($all_available_subject_array[$i][5] == 1){
                ?> <div class="col" ><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none"><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }
            }elseif($level == 2){
              if ($all_available_subject_array[$i][5] == 2){
                ?> <div class="col" ><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none"><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }
            }elseif($level == 3){
              if ($all_available_subject_array[$i][5] == 3){
                ?> <div class="col" ><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none"><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }
            }elseif($level == 4){
              if ($all_available_subject_array[$i][5] == 0){
                ?> <div class="col" ><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none" ><div class="card mx-auto border border-secondary" style="width: auto;"><div class="card-body"><?php
              }
            }
            $checkbox_id = "checkbox_" . $checkbox_id_increment;//Set what the id of the checkbox should be
            if($no_tutor_subjects) $all_available_subject_array[$i][4] = false; //If there are no subjects make sure the checkbox it unticked
            if($all_available_subject_array[$i][4] == true) 
            {
              //echo "<img class='subject_icon card-image-top img-fluid' src='sys_img\subject_icon.jpg' alt=''>";
              ?>
              <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div></div>
              <div class="card-footer border border-danger border-5"> <?php echo "<input id=checkbox_tutor_" . $all_available_subject_array[$i][1000] . " type='checkbox' checked>   already selected" ?> </div> <?php
            } //Create a checked checkbox
            else 
            {
              //echo "<img class='subject_icon card-image-top img-fluid' src='sys_img\subject_icon.jpg' alt=''>";
              ?>
              <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div></div>
              <div class="card-footer"> <?php echo "<input id=checkbox_tutor_" . $all_available_subject_array[$i][1000] . " type='checkbox'>" ?> </div> <?php
              } //Create an empty checkbox 
            ?></div></div>
            <?php
            $checkbox_id_increment += 1; //Increment the checkbox id by 1
            }?>
      </div></div></div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
        <a id='tutor_save_subjects_btn' class="btn btn-primary">Add subjects</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_subject_tutee" tabindex="-1" role="dialog" aria-labelledby="add_subject_tutee" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select subjects</h5>
        <form action="info_setting.php" method="get">
            <label for="Level_tutee">Select level:</label>
            <select name="level_tutee" id="level_tutee">
<?php if ($level_tutee == 0){
                 ?>
                <option value="0" selected>Any level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif ($level_tutee == 1){
                 ?>
                <option value="0">Any level</option>
                <option value="1" selected>Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif($level_tutee == 2){
  ?>
                <option value="0">Any level</option>
                <option value="1">Level 1</option>
                <option value="2" selected>Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif($level_tutee == 3){
  ?>
                <option value="0">Any level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3" selected>Level 3</option>
                <option value="4">Non-NCEA</option>
<?php
} elseif($level_tutee == 4){
?>
                <option value="0">Any level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4" selected>Non-NCEA</option>

<?php } ?>
            </select>
            <input type="hidden" id="modal_sorting_id" name="modal_sorting_id" value="2">
            <input type="submit" name="submit" class="btn btn-success btn-md" value="Sort!">
        </form>
        <a class="close btn" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
                    </a>
      </div>
      <div class="modal-body">
      <div id="tutoring_subjects_checkbox_studying">
      <div class="row row-cols-1 row-cols-md-3">
            <?php 
          for($i=0;$i<sizeof($all_available_subject_array);$i++){ //Check if subject should be ticked on start
                 if($level_tutee == 0){
              ?> <div class="col" ><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
            }elseif($level_tutee == 1){
              if ($all_available_subject_array[$i][5] == 1){
                ?> <div class="col" ><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none"><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }
            }elseif($level_tutee == 2){
              if ($all_available_subject_array[$i][5] == 2){
                ?> <div class="col" ><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none"><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }
            }elseif($level_tutee == 3){
              if ($all_available_subject_array[$i][5] == 3){
                ?> <div class="col" ><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none"><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }
            }elseif($level_tutee == 4){
              if ($all_available_subject_array[$i][5] == 0){
                ?> <div class="col" ><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }else{
                ?> <div class="col" style="display: none" ><div class="card mx-auto border border-warning" style="width: auto;"> <div class="card-body"><?php
              }
            }
            $checkbox_id = "checkbox_" . $checkbox_id_increment;//Set what the id of the checkbox should be
            if($no_tutee_subjects) $all_available_subject_array[$i][3] = false; //If there are no subjects make sure the checkbox it unticked
            if($all_available_subject_array[$i][3] == true) 
            {
              //echo "<img class='subject_icon card-image-top img-fluid' src='sys_img\subject_icon.jpg' alt=''>";
              ?>
              <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div>
              <div class="card-footer border border-danger border-5"> <?php echo "<input id=checkbox_tutee_" . $all_available_subject_array[$i][1000] . " type='checkbox' checked>   already selected" ?></div> <?php
            } //Create a checked checkbox
            else {
              //echo "<img class='subject_icon card-image-top img-fluid' src='sys_img\subject_icon.jpg' alt=''>";
              ?>
              <div class="card-text"> <?php echo $all_available_subject_array[$i][1];?></div>
              <div class="card-footer"> <?php echo "<input id=checkbox_tutee_" . $all_available_subject_array[$i][1000] . " type='checkbox'>" ?> </div> <?php
             } //Create an empty checkbox 
            ?></div></div></div><?php
            $checkbox_id_increment += 1; //Increment the checkbox id by 1
            }?>
        </div>
        </div>
        </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
        <a id='tutee_save_subjects_btn' class="btn btn-primary">Add subjects</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="remove_subject" tabindex="-1" role="dialog" aria-labelledby="remove_subject" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Remove Subject</h5>
        <a type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </a>
      </div>
      <div class="modal-body">
        <h3>Are you sure you want to remove this subject?</h3>
        <?php
          $user_id = $_COOKIE['user_id'];
          $subject_status = $_COOKIE['subject_status'];
          $is_global = $_COOKIE['is_global'];
          $subject_id = $_COOKIE['subject_id'];
          ?>
        <?php
        echo $user_id."<br>";
        echo $subject_status."<br>";
        echo $is_global."<br>";
        echo $subject_id."<br>";
        ?>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</a>
        <form action="process.php" method="post">
        <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id?>">
        <input type="hidden" id="subject_status" name="subject_status" value="<?php echo $subject_status?>">
        <input type="hidden" id="is_global" name="is_global" value="<?php echo $is_global?>">
        <input type="hidden" id="subject_id" name="subject_id" value="<?php echo $subject_id?>">
        <input type="submit" class="btn btn-danger" value="remove subject">
        </form>
      </div>
    </div>
  </div>
</div>
<?php

if (isset($_GET['modal_sorting_id'])){
  $modal_sorting_id = $_GET['modal_sorting_id'];
  if ($modal_sorting_id == 1){
    ?>
    <script>
    const add_subject_tutor = new bootstrap.Modal(document.getElementById('add_subject_tutor'))
    add_subject_tutor.show()
    </script>
    
    <?php
    $modal_sorting_id = 0;
  }elseif ($modal_sorting_id == 2){
    ?>
    <script>
    const add_subject_tutee = new bootstrap.Modal(document.getElementById('add_subject_tutee'))
    add_subject_tutee.show()
    </script>
    <?php
    $modal_sorting_id = 0;
  }
}
?>
<script>
          const remove_subject = document.getElementById('remove_subject')
          if (remove_subject) {
            remove_subject.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            const user_id = button.getAttribute('data-user-id')
            const is_global = button.getAttribute('data-is-global')
            const subject_id = button.getAttribute('data-subject-id')
            const subject_status = button.getAttribute('data-subject-status')

            console.log(user_id)
            console.log(is_global)
            console.log(subject_id)
            console.log(subject_status)

            document.cookie = "user_id=" + user_id
            document.cookie = "is_global=" + is_global
            document.cookie = "subject_id=" + subject_id
            document.cookie = "subject_status=" + subject_status
            
            javascript:alert(document.cookie)
          })
          }
</script>

  </body>
</html>
<style>
  body{
    margin-top:20px;
    color: #1a202c;
    text-align: left; 
}
.main-body {
    padding: 15px;
}
.card {
    box-shadow: 0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px 0 rgba(0,0,0,.06);
}

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid rgba(0,0,0,.125);
    border-radius: .25rem;
}

.card-body {
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1rem;
}

.gutters-sm {
    margin-right: -8px;
    margin-left: -8px;
}
button:hover {
  background-color: #FF2400 !important;
  transition: 0.5s;
}

.gutters-sm>.col, .gutters-sm>[class*=col-] {
    padding-right: 8px;
    padding-left: 8px;
}
.mb-3, .my-3 {
    margin-bottom: 1rem!important;
}

.bg-gray-300 {
    background-color: #e2e8f0;
}
.h-100 {
    height: 100%!important;
}
.shadow-none {
    box-shadow: none!important;
}
</style>
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
    <div class="card mx-auto w-75">
    <form action='calendar_2.php' method='post'>
    <div class="card-body">
    <div class="form-group row">
        <label for="start_time" class="col-sm-2 col-form-label"><b>Earliest Start time</b></label>
        <div class="col-sm-10">
        <input type="time" class="form-control" id="start_time" placeholder="Start time" name="start_time" required><br>
        </div>
        </div>

        <div class="form-group row">
        <label for="end_time" class="col-sm-2 col-form-label"><b>Latest End time</b></label>
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
      ?><div class="row row-cols-1 row-cols-md-5">   <?php
      $day =  $time_year . "-" . $time_month . "-" . $time_day;
      for($i=0; $i<sizeof($available_session_times_data); $i++){
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
  
        ?> <div class="col">   <div id=<?php echo $card_id; ?> class='card mx-auto border border-grey p-3' style="width: 15rem;"><?php
        echo (date("l h:i:s A", $potential_starttime) . "<br>");
        echo date("l h:i:s A", $potential_endtime); //prints out the cards of the time sessions.
        ?> <a href="delete_calendar_time.php?id=<?php echo $card_id; ?>">Remove</a></div></div>  <?php }?> </div>  <?php } ?>


    <div id='calendar'></div>
    <div id='profile'></div>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script> -->

  </body>
</html>