<?php

//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED
//THIS IS A REDUNDANT PAGE, IT IS NO LONGER BEING USED

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Connect to database
include("sys_page/db_connect.php");

//Import functions
include("sys_page/functions.php");

//Include the navbar
include("sys_page/header.html");

//Get info about user =====================================================================================================================================================
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
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <?php
  //running through all of the subjects and getting the level
  for($b=0;$b<sizeof($all_available_subject_array);$b++)
  { if(is_int(substr($all_available_subject_array[$b][1], -1))){
    //if it has a level set it to the level
    $all_available_subject_array[$b][5] = substr($all_available_subject_array[$b][1], -1);
  }else{
    //if it doesn't set it to 0
    $all_available_subject_array[$b][5] = 0;
  }
  }
    //this code sorts all subjects by name and level
  $all_available_subject_level_array_column = array_column($all_available_subject_array, 5);
  $all_available_subject_name_array_column = array_column($all_available_subject_array, 1);
  array_multisort($all_available_subject_name_array_column, SORT_ASC, $all_available_subject_level_array_column, SORT_ASC, $all_available_subject_array);
  
  //running through all of the tutee subjects and getting the level
  for($b=0;$b<sizeof($subject_array_tutee);$b++)
  { if(is_int(substr($subject_array_tutee[$b][1], -1))){
    //if it has a level set it to the level
    $subject_array_tutee[$b][5] = substr($subject_array_tutee[$b][1], -1);
  }else{
    //if it doesn't set it to 0
    $subject_array_tutee[$b][5] = 0;
  }
  }
  //this code sorts the tutee subjects by name and level
  $array_tutee_subject_level_array_column = array_column($subject_array_tutee, 5);
  $array_tutee_subject_name_array_column = array_column($subject_array_tutee, 2);
  array_multisort($array_tutee_subject_name_array_column, SORT_ASC, $array_tutee_subject_level_array_column, SORT_ASC, $subject_array_tutee);

  //running through all of the tutor subjects and getting the level
  for($b=0;$b<sizeof($subject_array_tutor);$b++)
  { if(is_int(substr($subject_array_tutor[$b][1], -1))){
    //if it has a level set it to the level
    $subject_array_tutor[$b][5] = substr($subject_array_tutor[$b][1], -1);
  }else{
    //if it doesn't set it to 0
    $subject_array_tutor[$b][5] = 0;
  }
  }
    //this code sorts the tutor subjects by name and level
  $array_tutor_subject_level_array_column = array_column($subject_array_tutor, 5);
  $array_tutor_subject_name_array_column = array_column($subject_array_tutor, 2);
  array_multisort($array_tutor_subject_name_array_column, SORT_ASC, $array_tutor_subject_level_array_column, SORT_ASC, $subject_array_tutor);
  ?>
    
    <!-- the header and the top of the page showing the subjects of the user -->
    <h1><p class="text-center"><?php echo $display_name."'s profile";?></p></h1>
    <div class="container text-center border border-3 border-dark extra_rounded">
              <div class="row">
                  <h3 class="col text-center py-3 m-0">Studying</h3>
                  <div class="col purple_box extra_rounded_r"></div>
              </div>
    </div>

    <div class="row" id="studying">
      <div id="tutoring_subjects_checkbox_studying" class="hide_on_start">
         <div class="card mx-auto" style="width: 25rem;">
          <div class="card-body">
            <ul class="list-group list-group-flush">
                <?php 
              for($i=0;$i<sizeof($all_available_subject_array);$i++){ //Check if subject should be ticked on start
                ?> <li class="list-group-item"> <?php
                $checkbox_id = "checkbox_" . $checkbox_id_increment;//Set what the id of the checkbox should be
                if($no_tutee_subjects) $all_available_subject_array[$i][3] = false; //If there are no subjects make sure the checkbox it unticked
                if($all_available_subject_array[$i][3] == true) {echo "<input id=" . $checkbox_id . " type='checkbox' checked>" . "  ".$all_available_subject_array[$i][1];} //Create a checked checkbox
                else {echo "<input id=" . $checkbox_id . " type='checkbox'>" . "  ".$all_available_subject_array[$i][1];} //Create an empty checkbox ?></li><?php
                $checkbox_id_increment += 1; //Increment the checkbox id by 1
                }?>
            </ul>

          </div>
         </div>
        </div>
        <div id="studying_subject_cards">
          <div class="card mx-auto" style="width: 25rem;">
            <div class="card-body">
              <ul class="list-group list-group-flush">
              <?php
            //Get number of items in subject array
            if($no_tutee_subjects == false)
            {
              for($x=0;$x<sizeof($subject_array_tutee);$x++)
              {
                ?> <li class="list-group-item"> <?php
                echo "<div ";
                echo "id=tutee_" . $subject_element_tutee_id;//Give the element a unqiue id
                echo " class='col'><p class='nowrap'>";
                echo $subject_array_tutee[$x][2];
                echo "</p></div>";
                //Increment the id?></li> <?php 
                $subject_element_tutee_id += 1;
              }
            }
          ?>
              </ul>
            </div>
          </div>
      </div>
    </div>





    <!-- the header for the part where the user can see the subjects they are willing to help with -->
    <div class="container text-center border border-3 border-dark extra_rounded">
              <div class="row">
                <h3 class="col text-center py-3 m-0">Tutoring</h3>
                  <div class="col purple_box extra_rounded_r"></div>
              </div>
    </div>
      
    <div class="row" id="tutoring">
      
      <div id="tutoring_subjects_checkbox_tutoring" class="hide_on_start">
        <div class="card mx-auto" style="width: 25rem;">
          <div class="card-body">
          <ul class="list-group list-group-flush">
          <?php for($i=0;$i<sizeof($all_available_subject_array);$i++){ //Check if subject should be ticked on start
          ?> <li class="list-group-item"> <?php
            $checkbox_id = "checkbox_" . $checkbox_id_increment;//Set what the id of the checkbox should be
            if($no_tutor_subjects) $all_available_subject_array[$i][4] = false; //If there are no subjects make sure the checkbox it unticked
            if($all_available_subject_array[$i][4] == true) {echo "<input id=" . $checkbox_id . " type='checkbox' checked>" . "  ".$all_available_subject_array[$i][1];} //Create a checked checkbox
            else {echo "<input id=" . $checkbox_id . " type='checkbox'>" . "  ".$all_available_subject_array[$i][1];} //Create an empty checkbox ?></li> <?php
            $checkbox_id_increment += 1; //Increment the checkbox id by 1
            }?>
          </ul>
          </div>
        </div>
      </div>

      <div id="tutoring_subject_cards">
        <div class="card mx-auto" style="width: 25rem;">
          <div class="card-body">
          <ul class="list-group list-group-flush">
                <?php
              //Get number of items in subject array
              if($no_tutor_subjects == false)
              {
                for($x=0;$x<sizeof($subject_array_tutor);$x++)
                {
                  ?><li class="list-group-item"><?php
                  echo "<div ";
                  echo "id=tutor_" . $subject_element_tutor_id;//Give the element a unqiue id
                  echo " class='col'><p class='nowrap'>";
                  echo $subject_array_tutor[$x][2];
                  echo "</p><img class='hide_on_start edit_cross' src='sys_img/icons8-x-100.png' alt=''></div>";
                  //Increment the id ?></li><?php
                  $subject_element_tutor_id += 1;
                }
              }
            ?>
          </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- this is the description that shows the various tutoring metrics of an individual -->
    <button class="btn btn-success btn-md" id=profile_edit_button>Edit</button>
    <div class="card mx-auto">
      <div class="card-header text-white bg-primary">       <h3>Description</h3>   </div>
        <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"> <p id="profile_desc_text"><?php echo $desc; ?></p></li>
          <li class="list-group-item text-white bg-primary">          <h3>Statistics</h3></li>
          <li class="list-group-item">          <p>Average Productivity: <?php echo $average_prod; ?>⭐</p></li>
          <li class="list-group-item">          <p>Average Experience: <?php echo $average_expe; ?>⭐</p></li>
          <li class="list-group-item">          <p>Hours spent tutoring: <?php echo $hours_tutored; ?></p></li>
          <li class="list-group-item">          <p>Sessions tutored: <?php echo $sessions_tutored; ?></p></li>
        </ul>
        </div>
    </div>
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
  </body>
</html>